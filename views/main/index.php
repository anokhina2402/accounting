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
use \yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use \yii\bootstrap\Alert;
use \yii\bootstrap\Modal;

?>
<h1><span class>Current month: <?php echo date('M Y', strtotime($current_date)); ?></br>You have <span class="big_text <?= Html::encode($class_sum) ?>"><?= Html::encode($sum_replace) ?></span> money for <span class="big_text blue"><?= Html::encode($day_replace) ?></span> days</span></h1>
<h2 class="info <?= Html::encode($class_info) ?>"><?= Html::encode($info) ?></h2>

<?php
//select month
$form = ActiveForm::begin([
    'id' => 'date-form',
    'options' => ['class' => 'form-horizontal'],
    'method' => 'GET',
    'action' => ['main/index'],
]); ?>
<?= Html::label('Select month'); ?>
<?php echo DatePicker::widget([
    'name' => 'date',
    'clientOptions' => [
            'showOn' => 'button',
    'buttonImage' => '/images/calendar.gif',
        'changeMonth' => true,
      'changeYear' => true,
        ],
    //'language' => 'ru',
    'dateFormat' => 'yyyy-MM-dd',
]); ?>
<?php ActiveForm::end() ?>

<?php
//export plan from current month to the next
?>
<?= Alert::widget([
        'options' =>
            [
                    'id' => 'alert-export-month-plan',
                    'style' => 'display:none'
            ],
        'body' => '<div></div>',
        ]
    ) ?>
<?= Html::Input('button', 'export-month-plan', 'Export plan from current month to the next', ['id' => 'export-month-plan']); ?>
<?= Html::a( 'All Statistic',
    '',
    [
        'title' => Yii::t('yii', 'Show income outgo statistic'),
        'class' => 'btn btn-info',
        'data-pjax' => '0',
        'data-toggle' => 'modal',
        'data-target' => '#all-statistic',
    ]); ?>


<?php echo Tabs::widget([
        'encodeLabels' => false,
        'items' => [
         [
             'label' => 'Previous',
             'url' => Yii::$app->getUrlManager()->createUrl(['main/index','date'=>date('Y-m-02',strtotime('last month', strtotime($current_date)))]),
         ],
         [
             'label' => 'Income Plan (' . $sum_planincome . ')',
             'content' => $this->render('_planincome', array(
                 'dataProvider' => $dataProviderPlanIncome,
                 'sum_planincome' => $sum_planincome,
                 'current_date' => $current_date,
             )),
         ],
         [
             'label' => 'Outgo Plan (' . ( $sum_planincome < $sum_planoutgo ? '<span class="red">' . $sum_planoutgo . '</span>' :  ( $sum_planincome +100 < $sum_planoutgo ? '<span class="orange">' . $sum_planoutgo . '</span>' : $sum_planoutgo ) ) . ')',
             'content' => $this->render('_planoutgo', array(
                 'dataProvider' => $dataProviderPlanOutgo,
                 'sum_planoutgo' => $sum_planoutgo,
                 'current_date' => $current_date,
             )),
         ],
         [
             'label' => 'Income (' . $sum_income . ')',
             'content' => $this->render('_income', array(
                 'dataProvider' => $dataProviderIncome,
                 'sum_income' => $sum_income,
                 'current_date' => $current_date,
             )),
         ],
         [
             'label' => 'Outgo (' . $sum_outgo . ')',
             'content' => $this->render('_outgo', array(
                 'dataProvider' => $dataProviderOutgo,
                 'sum_outgo' => $sum_outgo,
                 'current_date' => $current_date,
             )),
             'active' => true
         ],
         [
             'label' => 'Next',
             'url' => Yii::$app->getUrlManager()->createUrl(['main/index','date'=>date('Y-m-02',strtotime('next month', strtotime($current_date)))]),
         ],
     ],
 ]); ?>
<p>

</p>

<?php

Modal::begin([
    'header' => '<h2>Statistic</h2>',
    'id' => 'all-statistic',
    'size' => "modal-lg",
]);
Modal::end();
?>


<?php
$script = <<< JS

$('#export-month-plan').click(function() {

    $.ajax({
                type: "GET",
                url: '/main/export-month-plan',
                data: {},
                success: function (data) {
                    $("#alert-export-month-plan")
                    .slideDown(1000)
                    .addClass(data.class)
                    .delay( 1000 )
                    .slideUp(1000)
                    .find('div')
                    .html(data.message);

                },
                error: function (exception) {
                    alert('error'+exception);
                }
            });
    });


JS;
$this->registerJS($script);
