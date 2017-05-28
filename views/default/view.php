<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $schema tunecino\models\Schema */

$this->title = $schema->name;
$this->params['breadcrumbs'][] = ['label' => 'Schemas', 'url' => ['index'], 'data-pjax'=> 0];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="schema-view">

    <?php Pjax::begin(['id' => 'schema-title', 'options' => ['tag' => 'span']]); ?>
        <h2 style="display:inline"><?= Html::encode($this->title) ?></h2>
    <?php Pjax::end(); ?>
    
    <span class="pull-right">

        <?php Pjax::begin(['id' => 'schema-form', 'options' => ['tag' => 'span']]); ?>
            <?= $this->render('_form', ['schema' => $schema]) ?>
        <?php Pjax::end(); ?>

        <?= Html::a('<span class="fui-trash"></span> DELETE', ['delete', 'id' => $schema->id], [
            'class' => 'btn btn-danger mt20 mb20',
            'data' => [
                'confirm' => 'Are you sure you want to completely delete Schema "'.$schema->name.'" ?',
                'method' => 'post',
            ],
        ]) ?>

        <?php Pjax::begin(['id' => 'schema-generate', 'options' => ['tag' => 'span']]); ?>
            <?= $this->render('generate', ['schema' => $schema]) ?>
        <?php Pjax::end(); ?>

        <a class="btn btn-default mt20 mb20" onclick="$('#schema-info').toggle();" data-toggle="tooltip" data-placement="bottom" title="Show/Hide Schema details">
            <span class="fui-info-circle"></span>
        </a>
    </span>

    <?php Pjax::begin(['id' => 'schema-info', 'options' => ['style' => ['display' => 'none']]]); ?> 
        <?= DetailView::widget([
            'model' => $schema,
            'attributes' => array_diff($schema->attributes(), ['id']),
        ]) ?>

        <?php foreach ($schema->loadForms() as $fname => $fobj) {
            echo '<hr>';
            echo '<h6>' . $fname . '</h6>';
            echo DetailView::widget(['model' => $fobj['model'], 'attributes' => array_diff($fobj['model']->attributes(), ['id', 'schema_id'])]);
        } ?>

    <?php Pjax::end(); ?>

</div>

<hr/>

<div class="entity-index">
    <div >
        <h4>
            Entities
            <div class="pull-right">
                <?php Pjax::begin(['id' => 'entity-form', 'options' => ['tag' => 'span']]); ?>
                    <?= $this->render('entity/_form', ['model' => $entity]) ?>
                <?php Pjax::end(); ?>

                <span class="search-container">
                    <a class="btn btn-default" onclick="$('.entity-search').toggle();"><span class="fui-search"></span></a>
                    <?= $this->render('entity/_search', ['model' => $entitySearchModel, 'schema' => $schema]) ?>
                </span>
            </div>
        </h4>

        <?php Pjax::begin(['id' => 'entity-list']); ?> 
            <?= ListView::widget([
                'dataProvider' => $entityProvider,
                'layout' => 
                    '<div>
                        <div class="pull-left mb20">{summary}</div>
                        <div class="pull-right mt20">{pager}</div>
                    </div>
                    <div class="clearfix"></div>
                    {items}',
                'itemOptions' => ['class' => 'entity-item panel'],
                'itemView' => 'entity/_item',
            ]) ?>
        <?php Pjax::end(); ?>
    </div>
</div>