<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sum_income float */
/* @var $current_date String */

use yii\helpers\Html;
use yii\grid\GridView;

?>
    <p>
    <?= Html::a('Create Income', ['income/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [   'attribute' => 'category',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => ['class' => 'income-category'],
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum_income . '</strong>',
            'contentOptions' => ['class' => 'income-sum'],
        ],
        [
                'class' => 'yii\grid\ActionColumn',
                'buttons'=> [
                    'update'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->getUrlManager()->createUrl(['income/updatecategory','id'=>$model['id']]),
                            ['title' => Yii::t('yii', 'View all incomes in category or update'), 'data-pjax' => '0']);
                    },
                    'delete'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                            Yii::$app->getUrlManager()->createUrl(['income/deletecategory','id'=>$model['id']]),
                            ['title' => Yii::t('yii', 'Delete all incomes in category'), 'data-pjax' => '0']);
                    }
                ],
                'template'=>'{update}   {delete}',
            ],
        ],
    'showFooter'=>true,
    ]); ?>