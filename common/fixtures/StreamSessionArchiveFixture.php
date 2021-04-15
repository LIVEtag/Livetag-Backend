<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */

namespace common\fixtures;

use common\components\FileSystem\media\MediaTypeEnum;
use common\components\test\ActiveFixture;
use common\models\Stream\StreamSessionArchive;

class StreamSessionArchiveFixture extends ActiveFixture
{
    const ARCHIVE_1_SESSION_7_NEW = 1;

    public $tableName = '{{%stream_session_archive}}';

    public $depends = [
        StreamSessionFixture::class,
    ];

    /**
     * @inheritdoc
     */
    protected function getTemplate(): array
    {
        return [
            'type' => MediaTypeEnum::TYPE_VIDEO,
            'createdAt' => $this->generator->incrementalTime,
            'updatedAt' => $this->generator->incrementalTime,
        ];
    }

    /**
     * @param string $videoName
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function generateVideo(string $videoName): string
    {
        // phpcs:disable
        $temp = tempnam(null, null) . ".mp4";
        copy(__DIR__ . "/data/archiveVideo/$videoName.mp4", $temp);
        // phpcs:enable
        return $this->createS3UploadedData(
            (new StreamSessionArchive())->getRelativePath(),
            $temp,
        );
    }
}
