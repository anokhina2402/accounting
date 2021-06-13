<?php
/* @var $this yii\web\View */
/* @var $sum_replace float */
/* @var $day_replace int */
/* @var $class_sum string */
/* @var $info string */
/* @var $class_info string */
/* @var $dataProviderPlanIncome yii\data\ActiveDataProvider */
/* @var $dataProviderPlanOutgo yii\data\ActiveDataProvider */
/* @var $dataProviderIncome yii\data\ActiveDataProvider */
/* @var $dataProviderOutgo yii\data\ActiveDataProvider */
/* @var $sum_planincome float */
/* @var $sum_planoutgo float */
/* @var $sum_income float */
/* @var $sum_outgo float */
/* @var $current_date String */

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\grid\GridView;

?>
<?php
//select month
?>
<?= Html::label('Date start'); ?>
<?php echo DatePicker::widget([
    'name' => 'date-start',
    'clientOptions' => [
        'showOn' => 'button',
        'buttonImage' => '/images/calendar.gif',
        'changeMonth' => true,
        'changeYear' => true,
        'firstDay' => '1',
    ],
    'id' => 'date-start',
    'value' => $date_start,
    'dateFormat' => 'yyyy-MM-dd',
]); ?>
<?= Html::label('Date end'); ?>
<?php echo DatePicker::widget([
    'name' => 'date-end',
    'clientOptions' => [
        'showOn' => 'button',
        'buttonImage' => '/images/calendar.gif',
        'changeMonth' => true,
        'changeYear' => true,
        'firstDay' => '1',
    ],
    'id' => 'date-end',
    'value' => $date_end,
    'dateFormat' => 'yyyy-MM-dd',
]); ?>
<?= Html::button(
    'Show',
    [
        'class' => 'btn btn-success',
        'id' => 'show-category-statistic'
    ]) ?>
<?= Html::a('Cancel', Yii::$app->request->referrer, ['class'=>'btn btn-warning']) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'category',
        [   'attribute' => 'category2',
            'footer' => '<strong>In Total</strong>',
        ],
        'name',
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
                },
                'copy'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-copy"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/copy','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Copy outgo'), 'data-pjax' => '0']);
                }

            ],
            'template'=>'{update}   {delete}   {copy}',
        ],
    ],
    'showFooter'=>true,
]); ?>


<?php
$script = <<< JS

$('#show-category-statistic').on('click', function (event) {
 var date_start = $('input[name=date-start]').val();
 var date_end = $('input[name=date-end]').val();
  

  getStatistic('$type', '$category', date_start, date_end);

});
JS;
$this->registerJS($script);

