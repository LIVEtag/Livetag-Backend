<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace common\components\queue\stream;

use common\exception\FfmpegException;
use common\helpers\LogHelper;
use common\helpers\FileHelper;
use common\models\Stream\StreamSessionArchive;
use Exception;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Create Entity and generate psoter
 * phpcs:disable PHPCS_SecurityAudit.BadFunctions
 * Class CreatePlaylistJob
 */
class CreatePlaylistJob extends BaseObject implements JobInterface
{
    /**
     * ArchiveVideo id
     * @var int
     */
    public $id;

    /**
     * Relative path to store playlist.
     * Very important to store all playlist files in separate folder.
     * @var string
     */
    protected $relativePath;

    /**
     * Category for logs
     */
    const LOG_CATEGORY = 'archive';

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        LogHelper::info('Create playlist for archive', self::LOG_CATEGORY, [], $this->logTags());

        $archive = StreamSessionArchive::find()->byId($this->id)->one();
        if (!$archive) {
            LogHelper::error('Archive not found', self::LOG_CATEGORY, [], $this->logTags());
            return;
        }
        if (!$archive->isInQueue()) {
            LogHelper::error(
                'Invalid status to start archive processing',
                self::LOG_CATEGORY,
                LogHelper::extraForModelError($archive),
                $this->logTags()
            );
            return;
        }
        // Set processing
        $archive->setProcessing();
        $archive->save(false, ['status', 'updatedAt']);

        // Change relative path to save all files in separate folder. Use `{id}-playlist` format
        $this->relativePath = $archive->getRelativePath() . '/' . $archive->id . '-playlist';

        // Create Playlist
        try {
            $playlist = $this->createPlaylist($archive);
        } catch (Throwable $ex) {
            $extra = LogHelper::extraForException($archive, $ex);
            if ($ex instanceof FfmpegException) {
                $extra['command'] = $ex->getCommand();
                $extra['ffmpegErrors'] = $ex->getFfmpegErros();
            }
            LogHelper::error(
                'Failed to process Archive',
                self::LOG_CATEGORY,
                $extra,
                $this->logTags()
            );
            $archive->setFailed();
            $archive->save(false, ['status', 'updatedAt']);
            return;
        }

        //Save url to model and change status
        $archive->setReady();
        $archive->setAttribute('playlist', $playlist);
        if (!$archive->save(true, ['status', 'playlist', 'updatedAt'])) {
            LogHelper::error(
                'Failed to save playlist',
                self::LOG_CATEGORY,
                LogHelper::extraForModelError($archive),
                $this->logTags()
            );
        }
        LogHelper::info('Playlist added', self::LOG_CATEGORY, [], $this->logTags());
    }

    /**
     * Cut video in small chunks, create playlist and upload all to s3
     *
     * @param StreamSessionArchive $archive
     * @return string
     * @throws \Exception
     * @throws FfmpegException
     */
    protected function createPlaylist(StreamSessionArchive $archive)
    {
        $url = $archive->getUrl();
        // check video exist
        if (!FileHelper::fileFromUrlExists($url)) {
            throw new Exception('Video file not exist: ' . $url);
        }
        $ffmpeg = Yii::$app->params['ffmpeg'];
        $tmpPlaylistPath = Yii::getAlias('@runtime/' . uniqid() . '_' . time() . '.m3u8');

        // Step 1. Create playlist + files from original file.
        // m3u8 file + some files
        $errors = [];
        $cmd = $ffmpeg . ' -i ' . $url . ' -crf 0 -preset veryslow -hls_playlist_type vod -hls_time 4 -hls_list_size 0 '
            . '-segment_format mpegts -threads 4 -codec copy -acodec copy ' . $tmpPlaylistPath . ' -hide_banner -loglevel error 2>&1';
        exec($cmd, $errors);
        //very simple check: if file created - operation successful
        if (!is_file($tmpPlaylistPath)) {
            throw new FfmpegException('Failed to create m3u8 file', $cmd, $errors);
        }

        try {
            $this->uploadTsPartsToS3($tmpPlaylistPath);
            // Upload m3u8 to s3 and save path in archive entity
            return FileHelper::uploadFileToPath($tmpPlaylistPath, $this->relativePath, 'm3u8');
        } finally {
            @unlink($tmpPlaylistPath); //Remove temp files after s3 upload (or fail)
        }
    }

    /**
     * Open m3u8 file. Extract all file names and upload related files to s3
     * Replace each file in m3u8 to s3 link
     * Unlink each file
     * @param type $path
     */
    protected function uploadTsPartsToS3($path)
    {
        $content = file_get_contents($path);
        $files = array_filter(array_map('trim', file($path)), function ($element) {
            return strpos($element, '#') === false;
        });
        foreach ($files as $file) {
            try {
                //upload file to s3 and store FULL link
                $s3Path = FileHelper::uploadFileToPath(Yii::getAlias('@runtime/' . $file), $this->relativePath, 'ts');
                $url = FileHelper::getUrlByPath($s3Path);

                //Replace link in playlist
                $pos = strpos($content, $file);
                if ($pos !== false) {
                    $content = substr_replace($content, $url, $pos, strlen($file));
                }
            } finally {
                @unlink(Yii::getAlias('@runtime/' . $file)); //Remove temp files after s3 upload (or fail)
            }
        }
        file_put_contents($path, $content);
    }

    /**
     * log tags (set id)
     * @return array
     */
    protected function logTags()
    {
        return [LogHelper::TAG_ARCHIVE_ID => $this->id];
    }
}
