<?php
/*
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace backend\models\Stream;

/**
 * @author Roman Oriekhov orekhov.ry@gbksoft.com
 */
interface UploadArchiveInterface
{
    const FIELD_UPLOAD_TYPE = 'uploadType';
    const FIELD_DIRECT_URL = 'directUrl';
    const FIELD_VIDEO_FILE = 'videoFile';

    /**
     * To add url link to the video
     */
    const TYPE_LINK = 'link';

    /**
     * To upload video from device storage
     */
    const TYPE_UPLOAD = 'upload';

    /**
     * Available types
     */
    const UPLOAD_TYPES = [
        self::TYPE_LINK,
        self::TYPE_UPLOAD,
    ];
    const RESPONSE_CODE_SUCCESS = 200;

    /**
     * @return array
     */
    public function getArchiveValidationRules(): array;

    /**
     * Save archive from file or url
     * @return bool
     */
    public function saveArchive(): bool;

    /**
     * @return StreamSession
     */
    public function getStreamSession(): StreamSession;
}
