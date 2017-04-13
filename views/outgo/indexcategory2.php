<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OutgoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $category String */
/* @var $category2 String */

//footer sum
$sum = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $sum += $val->sum;
    }
}

$this->title = 'Outgos Category ' . $category. ' ' . $category2;
?>
<div class="outgo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Back', Yii::$app->getUrlManager()->createUrl(['outgo/indexcategory','id'=>$dataProvider->getModels()[0]->id]), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create Outgo', Yii::$app->getUrlManager()->createUrl(['outgo/create','category'=>$category,'category2'=>$category2]), ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [   'attribute' => 'name',
                'footer' => '<strong>In Total</strong>',
            ],
            [   'attribute' => 'sum',
                'footer' => '<strong>' . $sum . '</strong>',
            ],
             'date',

            [
                    'class' => 'yii\grid\ActionColumn',
                    'buttons'=> [
                        'update'=>function ($url, $model) {
                            return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                                Yii::$app->getUrlManager()->createUrl(['outgo/update','id'=>$model['id']]),
                                ['title' => Yii::t('yii', 'View all outgoes in category 2 or update if one'), 'data-pjax' => '0']);
                        },
                        'delete'=>function ($url, $model) {
                            return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                                Yii::$app->getUrlManager()->createUrl(['outgo/delete','id'=>$model['id']]),
                                ['title' => Yii::t('yii', 'Delete all outgoes in category 2'), 'data-pjax' => '0', 'data-method'=>'post']);
                        },
                        'copy'=>function ($url, $model) {
                            return Html::a( '<span class="glyphicon glyphicon-copy"></span>',
                                Yii::$app->getUrlManager()->createUrl(['outgo/copy','id'=>$model['id']]),
                                ['title' => Yii::t('yii', 'Copy outgo'), 'data-pjax' => '0']);
                        },
                        'stat'=>function ($url, $model) {
                            return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>',
                                '',
                                [
                                    'title' => Yii::t('yii', 'Show name statistic'),
                                    'data-pjax' => '0',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#statistic',
                                    'data-whatever' => $model['name'],
                                    'data-type' => 'name',

                                ]);
                        }


                    ],
                    'template'=>'{update}   {delete}   {copy}   {stat}',
            ],
        ],
        'showFooter'=>true,
    ]); ?>
</div>
