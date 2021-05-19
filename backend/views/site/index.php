<?php
/* @var $this yii\web\View */

$this->title = 'Summary analytics';
?>
<section class="content">
    <?php foreach ($shops as $shop): ?>
        <?= $this->render('shop-analytics', ['shop' => $shop, 'displayHeader' => $displayHeader ]); ?>
    <?php endforeach; ?>
</section>