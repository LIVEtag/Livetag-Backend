<?php
use backend\models\Shop\Shop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */

$this->title = 'Centrifugo debug page';
?>

<div class="category-form">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <?php $form = ActiveForm::begin(); ?>
                <div class="box-header"></div>
                <!--/.box-header -->
                <div class="box-body table-responsive">
                    <?= $form->field($model, 'shopId')->widget(Select2::class, [
                        'data' => Shop::getIndexedArray(),
                        'options' => ['placeholder' => 'Select shop ...'],
                        'pluginOptions' => ['allowClear' => false],
                    ])->label('Shop'); ?>
                    <?= $form->field($model, 'centrifugoUrl'); ?>
                    <?= $form->field($model, 'signEndpoint'); ?>
                    <?= $form->field($model, 'centrifugoToken')->textInput(['readonly' => true])->label('Centrifugo Token (/POST ​/v1​/centrifugo​/sign)'); ?>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Go'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                <!--/.box-footer -->
                <?php ActiveForm::end(); ?>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/centrifugal/centrifuge-js@2.6.0/dist/centrifuge.min.js"></script>

<?php if ($validated) : ?>
    <div class="callout callout-success">
        <h4>Validated!</h4>
        <p>Open Console for output</p>
    </div>

    <script type="text/javascript">
        const shopChannel = "shop_<?= $model->shopId; ?>";

        //formats: https://github.com/centrifugal/centrifuge-js#subscription-event-context-formats
        var callbacks = {
          "publish": function (message) {
            console.log('publish:', message.data);
          },
          "join": function (message) {
            console.log('join:', message);
          },
          "leave": function (message) {

            console.log('leave:', message);
          },
          "subscribe": function (context) {
            console.log('subscribe:', context);
          },
          "error": function (err) {
            console.log('error:', err);
          },
          "unsubscribe": function (context) {
            console.log('unsubscribe:', context);
          }
        };


        const centrifuge = new Centrifuge("<?= $model->centrifugoUrl; ?>", {"debug": true});
        centrifuge.setToken("<?= $model->centrifugoToken; ?>");

        centrifuge.on('connect', function (ctx) {
          console.log("connected", ctx);
        });
        centrifuge.on('disconnect', function (ctx) {
          console.log("disconnected", ctx);
        });
        centrifuge.subscribe(shopChannel, callbacks);

        centrifuge.connect();
    </script>
<?php endif; ?>