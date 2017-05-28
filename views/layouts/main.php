<?php
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\widgets\Pjax;

use tunecino\builder\BuilderAsset;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = BuilderAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
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
            'brandLabel' => 'Schema Builder',
            'brandUrl' => ['default/index'],
            'options' => ['class' => 'navbar-inverse navbar-fixed-top'],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'nav navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Home', 'url' => ['default/index']],
                ['label' => 'Application', 'url' => Yii::$app->homeUrl],
                ['label' => 'Github Repo', 'url' => 'https://github.com/tunecino/yii2-schema-builder']
            ],
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?php Pjax::begin(['id' => 'breadcrumbs', 'options' => ['tag' => 'span']]); ?>
                <?= Breadcrumbs::widget([
                    'homeLink' => ['label' => 'Home', 'url' => ['default/index']],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            <?php Pjax::end(); ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; SchemaBuilder <?= date('Y') ?></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
