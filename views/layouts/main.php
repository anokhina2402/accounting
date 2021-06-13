<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;
use \yii\bootstrap\Modal;


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

<?php

Modal::begin([
    'header' => '<h2>Statistic</h2>',
    'id' => 'statistic',
    'size' => "modal-lg",
]);
Modal::end();
?>

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
            // If as planned
            else if ($(this).parent().find('td.planoutgo-sum').html()*1 == $('.outgo-category:contains("'+$(this).html()+'")').parent().find('.outgo-sum').html()*1) {
                $(this).parent().addClass('purple');
                $('.outgo-category:contains("'+$(this).html()+'")').parent().addClass('purple');
            }
            // If there is 10%
            else if ($(this).parent().find('td.planoutgo-sum').html()*1 <= $('.outgo-category:contains("'+$(this).html()+'")').parent().find('.outgo-sum').html()*1 + $('.outgo-category:contains("'+$(this).html()+'")').parent().find('.outgo-sum').html()*0.1) {
                $(this).parent().addClass('blue');
                $('.outgo-category:contains("'+$(this).html()+'")').parent().addClass('blue');
            }
        });

        $('#date-form input[name="date"]').on('change', function () { $('#date-form').submit(); });


    });

    $('#statistic').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var recipient = button.data('whatever');
        var type = button.data('type');
        var modal = $(this);
        var date = new Date();
        var string_date = date.toISOString().substring(0, 10);

        modal.find('.modal-header').text(recipient + ' statistic');

        getStatistic(type, recipient, string_date, string_date);


    });


    function getStatistic(type, category, date_start, date_end)
    {
        jQuery.ajax({
            type: "GET",
            url: '/outgo/statistic',
            dataType: 'html',
            data: {
                'category': category,
                'date_start': date_start,
                'date_end': date_end,
                'type': type
            },
            success: function (data) {
                jQuery('#statistic').find('.modal-body').html(data);

            },
            error: function (exception) {
                alert('error'+JSON.stringify(exception));
            }
        });
    }

    $(document).on('ready pjax:success', function() {
    $('#all-statistic').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        var date = new Date();
        var string_date = date.toISOString().substring(0, 10);

        getAllStatistic(string_date, 1);


    });
    });


    function getAllStatistic(date, by_category)
    {
        jQuery.ajax({
            type: "GET",
            url: '/outgo/all-statistic',
            dataType: 'html',
            data: {
                'date': date,
                'by_category': (by_category ? '1' : '0')
            },
            success: function (data) {
                jQuery('#all-statistic').find('.modal-body').html(data);

            },
            error: function (exception) {
                alert('error'+JSON.stringify(exception));
            }
        });
    }


</script>
</body>
</html>
<?php $this->endPage() ?>
