<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Outgo */
/* @var $categories array('label') of categories */
/* @var $categories2 array('label') of categories2 */
/* @var $names array('label') of names */

$this->title = 'Create Outgo';
?>
<div class="outgo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=

    $this->render('_form', array(
        'model' => $model,
        'categories' => $categories,
        'categories2' => $categories2,
        'names' => $names,
    ));
    ?>

</div>
