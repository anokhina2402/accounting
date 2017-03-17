<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OutgoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $category String */

//footer sum
$sum = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $sum += $val->sum;
    }
}

$this->title = 'Outgos Category ' . $category;
?>
<div class="outgo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Back', Yii::$app->getUrlManager()->createUrl(['main/index','date'=>date('Y-m-02', strtotime($dataProvider->getModels()[0]['date']))]), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create Outgo', Yii::$app->getUrlManager()->createUrl(['outgo/create','category'=>$category]), ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            [   'attribute' => 'category2',
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
                                Yii::$app->getUrlManager()->createUrl(['outgo/updatecategory2','id'=>$model['id']]),
                                ['title' => Yii::t('yii', 'View all outgoes in category 2 or update if one'), 'data-pjax' => '0']);
                        },
                        'delete'=>function ($url, $model) {
                            return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                                Yii::$app->getUrlManager()->createUrl(['outgo/deletecategory2','id'=>$model['id']]),
                                ['title' => Yii::t('yii', 'Delete all outgoes in category 2'), 'data-pjax' => '0']);
                        }
                    ],
                    'template'=>'{update}   {delete}',
            ],
        ],
        'showFooter'=>true,
    ]); ?>
</div>
