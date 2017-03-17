<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Income */
/* @var $categories array('label') of categories */


$this->title = 'Create Income';
?>
<div class="income-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?=

    $this->render('_form', array(
        'model' => $model,
        'categories' => $categories,
    ));
    ?>


</div>
