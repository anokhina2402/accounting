<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sum_outgo float */

use yii\helpers\Html;
use yii\grid\GridView;

?>
    <p>
    <?= Html::a('Create Outgo', ['outgo/create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a( 'Statistic',
        '',
        [
            'title' => Yii::t('yii', 'Show outgo statistic'),
            'class' => 'btn btn-info',
            'data-pjax' => '0',
            'data-toggle' => 'modal',
            'data-target' => '#statistic',
            'data-type' => '',
            'data-whatever' => '',
        ]); ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [   'attribute' => 'category',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => ['class' => 'outgo-category'],
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum_outgo . '</strong>',
            'contentOptions' => ['class' => 'outgo-sum'],
        ],
        'date',
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons'=> [
                'update'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/updatecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'View all outgoes in category or update'), 'data-pjax' => '0']);
                },
                'delete'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/deletecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Delete all outgoes in category'), 'data-pjax' => '0']);
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
                            'title' => Yii::t('yii', 'Show category statistic'),
                            'data-pjax' => '0',
                            'data-toggle' => 'modal',
                            'data-target' => '#statistic',
                            'data-whatever' => $model['category'],
                            'data-type' => 'category',
                        ]);
                }

            ],
            'template'=>'{update}   {delete}   {copy}   {stat}',
        ],
        ],
    'showFooter'=>true,
    ]); ?>

