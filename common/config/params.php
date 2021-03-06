<?php
/**
 * Copyright © 2018 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
return [
    'user.passwordResetTokenExpire' => 3600,
    'supportEmail' => getenv('SUPPORT_EMAIL'),
    'adminEmail' => getenv('ADMIN_EMAIL'),
    'ffmpeg' => Yii::getAlias('@root') . '/bin/ffmpeg',
    'ffprobe' => Yii::getAlias('@root') . '/bin/ffprobe',
    'maxUploadLogoSize' => 102400, // 1024 * 100 -> 100 Kb
    'maxUploadImageSize' => 15728640, //1024 * 1024 * 15 -> 15 Mb
    'maxUploadVideoSize' => 5368709120,  // 1024*1024*1024*5
    'maxUploadCoverSize' => 15728640, //1024 * 1024 * 15 -> 15 Mb
];
