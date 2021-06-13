<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PlanOutgo */
/* @var $categories array('label') of categories */

$this->title = 'Create Plan Outgo';
?>
<div class="plan-outgo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
    ]) ?>

</div>
