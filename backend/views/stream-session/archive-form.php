<?php
/**
 * Copyright © 2021 GBKSOFT. Web and Mobile Software Development.
 * See LICENSE.txt for license details.
 */
use backend\models\Stream\UploadArchiveInterface;
use backend\models\Stream\UploadRecordedShowForm;
use kartik\widgets\FileInput;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model UploadArchiveInterface */


$maxVideoSizeInKB = Yii::$app->params['maxUploadVideoSize'] / 1024;
$maxVideoSizeInGB = $maxVideoSizeInKB / 1024 / 1024;

$this->registerJs('var directUrlInputId = "#' . Html::getInputId($model, UploadArchiveInterface::FIELD_DIRECT_URL) . '";
    var videoFileInputId = "#' . Html::getInputId($model, UploadArchiveInterface::FIELD_VIDEO_FILE) . '";
    ', View::POS_BEGIN);

$this->registerJsFile('/backend/web/js/upload-archive.js', [
    'position' => View::POS_END,
    'depends' => [
        JqueryAsset::class
    ],
]);
?>

<?=
$form->field($model, UploadArchiveInterface::FIELD_UPLOAD_TYPE)->radio([
    'id' => 'type-link',
    'value' => UploadRecordedShowForm::TYPE_LINK,
    'uncheck' => null,
    'label' => $model->getAttributeLabel('directUrl'),
    'checked' => 'checked',
]);
?>

<?=
    $form->field($model, UploadArchiveInterface::FIELD_DIRECT_URL)
    ->textInput(['placeholder' => Yii::t('app', 'Please put the direct URL link to the file with the video')])
    ->label(false);
?>

<?=
$form->field($model, UploadArchiveInterface::FIELD_UPLOAD_TYPE)->radio([
    'id' => 'type-upload',
    'value' => UploadRecordedShowForm::TYPE_UPLOAD,
    'uncheck' => null,
    'label' => $model->getAttributeLabel('videoFile'),
]);
?>

<div id="kv-error-file-upload" style="margin:0;margin-bottom: 5px;display:none"></div>
<?=
    $form->field($model, UploadArchiveInterface::FIELD_VIDEO_FILE)
    ->hint(Yii::t('app', 'max size {sizeinGb}GB', ['sizeinGb' => $maxVideoSizeInGB]))
    ->widget(FileInput::class, [
        'options' => [
            'multiple' => false,
            'disabled' => true
        ],
        'pluginOptions' => [
            'showPreview' => false,
            'maxFileCount' => 1,
            'showUpload' => false,
            'showRemove' => false,
            // the maximum file size for upload in KB
            'maxFileSize' => $maxVideoSizeInKB,
            'msgSizeTooLarge' => Yii::t(
                'app',
                'The file "{name}" is too big. Its size cannot exceed <b>{maxSize} GB</b>.',
                [
                    'maxSize' => $maxVideoSizeInGB,
                ]
            ),
            'elErrorContainer' => '#kv-error-file-upload',
        ],
    ])->label(false);
?>

