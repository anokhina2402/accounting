<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Income */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array('label') of categories */

?>

<div class="income-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'income-form',
        'options' => ['class' => 'form-horizontal'],
    ]); ?>
    <?= $form->field($model, 'category')->widget(
        AutoComplete::className(), [
        'clientOptions' => [
            'source' => $categories,
        ],
        'options'=>[
            'class'=>'form-control',
            'autoFocus' => true,
        ]
    ]);
    ?>
    <?= $form->field($model, 'sum')->textInput(); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
        'clientOptions' => [
            'firstDay' => '1',
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::Button(
                $model->isNewRecord ? 'Create' : 'Update',
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'id' => 'save-income'
            ]) ?>
        <?= Html::a('Cancel', Yii::$app->request->referrer, ['class'=>'btn btn-warning']) ?>
    </div>
    <?php    Modal::begin([
        'id' => 'income-modal-confirm',
        'header' => '<h2>Confirm</h2>',
        'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                     <input type="submit" class="btn btn-primary" id="dataConfirmOK" value="OK" />',
    ]);

    Modal::end();
    ?>
    <?php ActiveForm::end() ?>

</div>
<?php
$script = <<< JS

$('#save-income').click(function() {
    var date = new Date();
    var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    var date_val = new Date($('#income-date').val());
    if (date_val < firstDay || date_val > lastDay) {
        $("#income-modal-confirm").modal({show:true}).find('.modal-body').html('Selected day of not current month!');
    }
    else {
        $('#income-form').submit();
    }
});

JS;
$this->registerJS($script);

