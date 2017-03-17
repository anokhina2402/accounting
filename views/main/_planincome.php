<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sum_planincome float */

use yii\helpers\Html;
use yii\grid\GridView;

?>
    <p>
    <?= Html::a('Create Plan Income', ['plan-income/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [   'attribute' => 'category',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => ['class' => 'planincome-category'],
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum_planincome . '</strong>',
            'contentOptions' => ['class' => 'planincome-sum'],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons'=> [
                'update'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->getUrlManager()->createUrl(['plan-income/updatecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'View all plan incomes in category or update'), 'data-pjax' => '0']);
                },
                'delete'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->getUrlManager()->createUrl(['plan-income/deletecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Delete plan incomes in category'), 'data-pjax' => '0']);
                }
            ],
            'template'=>'{update}   {delete}',
        ],
        ],
    'showFooter'=>true,
    ]); ?>