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
use yii\widgets\Pjax;

?>
<?php
//select month
?>
<?php Pjax::begin(['enablePushState' => true,'id' => 'modal_pjax']);?>

<?= Html::label('Date'); ?>
<?php echo DatePicker::widget([
    'name' => 'date',
    'clientOptions' => [
        'showOn' => 'button',
        'buttonImage' => '/images/calendar.gif',
        'changeMonth' => true,
        'changeYear' => true,
        'firstDay' => '1',
    ],
    'id' => 'date',
    'value' => $date,
    'dateFormat' => 'yyyy-MM-dd',
]); ?>

<?php echo Html::checkbox('by_category',$by_category, ['class' => 'checkbox', 'id' => 'by_category', 'label' => 'By category']); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [

        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'category',
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],
        [   'attribute' => 'category2',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],
        [
            'attribute' => 'name',
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum . '</strong>',
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],
        [
            'attribute' => 'date',
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],
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
            'contentOptions' => function($model)
            {
                return ['class' => ($model['sum'] > 0 ? 'green_back' : 'red_back')];
            }
        ],

    ],
    'showFooter'=>true,
]); ?>
<?php Pjax::end()?>


<?php
$script = <<< JS

$('#date').on('change', function (event) {
  
  getAllStatistic($(this).val(), $('#by_category').prop('checked'));

});

$('#by_category').on('click', function (event) {
   console.log($(this).prop('checked'));
    getAllStatistic($('#date').val(), $(this).prop('checked'));

});
JS;
$this->registerJS($script);

