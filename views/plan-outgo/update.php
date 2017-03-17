<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PlanOutgo */
/* @var $categories array('label') of categories */

$this->title = 'Update Plan Outgo: ' . $model->category;
?>
<div class="plan-outgo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
    ]) ?>

</div>
