<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $schemaProvider yii\data\ActiveDataProvider */

$this->title = 'Schemas';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schema-index">
    
<div class="col-xs-12">
    <h2 style="display:inline"><?= Html::encode($this->title) ?></h2>
    <?php Pjax::begin(['id' => 'schema-form', 'options' => ['tag' => 'span']]); ?>
        <?= $this->render('_form', ['schema' => $schema]) ?>
    <?php Pjax::end(); ?>

</div>


    <?php Pjax::begin(['id' => 'schema-list']); ?> 
        <?= ListView::widget([
            'dataProvider' => $schemaProvider,
            'layout' => 
                '<div class="col-xs-12">
                    <div class="pull-left mb20">{summary}</div>
                    <div class="pull-right mt20">{pager}</div>
                </div>
                {items}',
            'itemOptions' => ['class' => 'schema-item col-xs-6 col-sm-4 col-md-3'],
            'itemView' => '_item',
        ]) ?>
    <?php Pjax::end(); ?>

</div>

