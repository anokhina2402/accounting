<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Accounting',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            localStorage.setItem('activeTab', $(e.target).attr('href'));
        });
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        }
        $('.planoutgo-category').each(function(){
            // If more than planned
            if ($(this).parent().find('td.planoutgo-sum').html()*1 < $('.outgo-category:contains("'+$(this).html()+'")').parent().find('.outgo-sum').html()*1) {
                $(this).parent().addClass('red');
                $('.outgo-category:contains("'+$(this).html()+'")').parent().addClass('red');
            }
            // If there is 10%
            else if (($(this).parent().find('td.planoutgo-sum').html()*1 + $(this).parent().find('td.planoutgo-sum').html()*0.1) >= $('.outgo-category:contains("'+$(this).html()+'")').parent().find('.outgo-sum').html()*1) {
                $(this).parent().addClass('purple');
                $('.outgo-category:contains("'+$(this).html()+'")').parent().addClass('purple');
            }
        });

        $('#date-form input[name="date"]').on('change', function () { $('#date-form').submit(); });


    });
</script>
</body>
</html>
<?php $this->endPage() ?>
