<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlanIncome */
/* @var $categories array('label') of categories */

$this->title = 'Update Plan Income: ' . $model->category;
?>
<div class="plan-income-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
    ]) ?>

</div>
