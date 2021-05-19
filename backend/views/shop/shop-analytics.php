<?php
use backend\models\Shop\Shop;
use yii\web\View;

/* @var $this View */
/* @var $shop Shop */
$analytics = $shop->getAnalytics();
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header">
                <h4 class="box-title">Summary analytics</h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <!--/.box-header -->
            <div class="box-body">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3><?= $analytics['totalViewCount']; ?></h3>
                                <p>Views</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-gray">
                            <div class="inner">
                                <h3><?= $analytics['totalAddToCartCount']; ?></h3>
                                <p>“Add to cart” clicks</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-cart-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?= $analytics['totalAddToCartRate']; ?><sup style="font-size: 20px">%</sup></h3>
                                <p>“Add to cart” rate</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-percent"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?= $analytics['uniqueViews']; ?></h3>
                                <p>Unique views</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?= $analytics['likesCount']; ?></h3>
                                <p>Likes</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-heart"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-4 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?= $analytics['commentsCount']; ?></h3>
                                <p>Comments</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-comment"></i>
                            </div>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

