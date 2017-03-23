<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $sum_outgo float */

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\bootstrap\Modal;

?>
    <p>
    <?= Html::a('Create Outgo', ['outgo/create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [   'attribute' => 'category',
            'footer' => '<strong>In Total</strong>',
            'contentOptions' => ['class' => 'outgo-category'],
        ],
        [   'attribute' => 'sum',
            'footer' => '<strong>' . $sum_outgo . '</strong>',
            'contentOptions' => ['class' => 'outgo-sum'],
        ],
        'date',
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons'=> [
                'update'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/updatecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'View all outgoes in category or update'), 'data-pjax' => '0']);
                },
                'delete'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/deletecategory','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Delete all outgoes in category'), 'data-pjax' => '0']);
                },
                'copy'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-copy"></span>',
                        Yii::$app->getUrlManager()->createUrl(['outgo/copy','id'=>$model['id']]),
                        ['title' => Yii::t('yii', 'Copy outgo'), 'data-pjax' => '0']);
                },
                'stat'=>function ($url, $model) {
                    return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>',
                        '',
                        [
                            'title' => Yii::t('yii', 'Show category static'),
                            'data-pjax' => '0',
                            'data-toggle' => 'modal',
                            'data-target' => '#category-static',
                            'data-whatever' => $model['category']
                        ]);
                }

            ],
            'template'=>'{update}   {delete}   {copy}   {stat}',
        ],
        ],
    'showFooter'=>true,
    ]); ?>
<?php
Modal::begin([
'header' => '<h2>Category static</h2>',
    'id' => 'category-static'
]);
?>
<?=
$this->render('/modal/modal-category', array(
));
?>
<?php
Modal::end();
?>

<?php
$script = <<< JS

$('#category-static').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var recipient = button.data('whatever');
  var modal = $(this);
  modal.find('.modal-header').text(recipient + ' category static');
})
JS;
$this->registerJS($script);

