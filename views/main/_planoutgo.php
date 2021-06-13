<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sum_planoutgo float */

use yii\helpers\Html;
use yii\grid\GridView;

?>
    <p>
    <?= Html::a('Create Plan Outgo', ['plan-outgo/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [   'attribute' => 'category',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => ['class' => 'planoutgo-category'],
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum_planoutgo . '</strong>',
            'contentOptions' => ['class' => 'planoutgo-sum'],
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'buttons'=> [
                'update'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->getUrlManager()->createUrl(['plan-outgo/updatecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'View all plan outgoes in category or update'), 'data-pjax' => '0']);
                },
                'delete'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->getUrlManager()->createUrl(['plan-outgo/deletecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Delete plan outgoes in category'), 'data-pjax' => '0']);
                }
            ],
            'template'=>'{update}   {delete}',
        ],
        ],
    'showFooter'=>true,
    ]); ?>