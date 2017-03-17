<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
/* @var $model app\models\Outgo */
/* @var $form yii\widgets\ActiveForm */
/* @var $categories array('label') of categories */
/* @var $categories2 array('label') of categories2 */
/* @var $names array('label') of names */
?>

<div class="outgo-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'outgo-form',
        'options' => ['class' => 'form-horizontal'],
    ]); ?>
    <?= $form->field($model, 'category')->widget(
        AutoComplete::className(), [
        'clientOptions' => [
            'source' => $categories,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ]);
    ?>
    <?= $form->field($model, 'category2')->widget(
        AutoComplete::className(), [
        'clientOptions' => [
            'source' => $categories2,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ]);
    ?>
    <?= $form->field($model, 'name')->widget(
        AutoComplete::className(), [
        'clientOptions' => [
            'source' => $names,
        ],
        'options'=>[
            'class'=>'form-control'
        ]
    ]);
    ?>
    <?= $form->field($model, 'sum')->textInput(); ?>
    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
    ]) ?>

    <div class="form-group">
        <?= Html::Button(
                $model->isNewRecord ? 'Create' : 'Update',
                [
                        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'id' => 'save-outgo'
                ]) ?>
        <?= Html::a('Cancel', Yii::$app->request->referrer, ['class'=>'btn btn-warning']) ?>
    </div>
    <?php    Modal::begin([
        'id' => 'outgo-modal-confirm',
        'header' => '<h2>Confirm</h2>',
        'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                     <input type="submit" class="btn btn-primary" id="dataConfirmOK" value="OK" />',
    ]);

    Modal::end();
    ?>
    <?php ActiveForm::end() ?>

</div>

<?php
$id = ($model->isNewRecord ? 0 : $model->id );
$script = <<< JS

$('#save-outgo').click(function() {

    
   $.ajax({
                type: "GET",
                url: '/outgo/validate',
                dataType: 'json',
                data: {
                    'category': $('#outgo-category').val(),
                    'sum': $('#outgo-sum').val(),
                    'date': $('#outgo-date').val(),
                    'id': $id
                    },
                success: function (data) {
                    if (data.result == 'error'){
                        
                        $("#outgo-modal-confirm").modal({show:true}).find('.modal-body').html(data.message);
                    }
                    else {
                        $('#outgo-form').submit();
                    }

                },
                error: function (exception) {
                    alert('error'+exception);
                }
            });
    });

JS;
$this->registerJS($script);

