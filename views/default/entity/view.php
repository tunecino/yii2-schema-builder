<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model tunecino\models\Entity */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Schemas', 'url' => ['index'], 'data-pjax'=> 0];
$this->params['breadcrumbs'][] = ['label' => $model->schema->name, 'url' => ['view', 'id' => $model->schema->id], 'data-pjax'=> 0];
$this->params['breadcrumbs'][] = $this->title;

$entity_id = $model->id;
?>

<div class="entity-view">

    <h2>
        <?php Pjax::begin(['id' => 'entity-title', 'options' => ['tag' => 'span']]); ?>
            <?= Html::encode($this->title) ?>
        <?php Pjax::end(); ?>

        <span class="pull-right">
            <?php Pjax::begin(['id' => 'entity-form', 'options' => ['tag' => 'span']]); ?>
                <?= $this->render('_form', ['model' => $model, 'id' => $model->id]) ?>
            <?php Pjax::end(); ?>

            <?= Html::a('<span class="fui-trash"></span> DELETE', ['delete-entity', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the entity "'.$model->name.'" ?',
                    'method' => 'post',
                ],
            ]) ?>
            <a class="btn btn-default" onclick="$('#entity-info').toggle();" data-toggle="tooltip" data-placement="bottom" title="Show/Hide Entity details">
                <span class="fui-info-circle"></span>
            </a>
        </span>

    </h2>

    <?php Pjax::begin(['id' => 'entity-info', 'options' => ['style' => ['display' => 'none']]]); ?> 
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name:ntext',
            ],
        ]) ?>
    <?php Pjax::end(); ?>

</div>

<hr/>

<div class="attribute-index">

    <h4>
        Attributes
        <div class="pull-right">
            <?php Pjax::begin(['id' => 'attribute-form', 'options' => ['tag' => 'span']]); ?>
                <?= $this->render('attribute/_form', ['model' => $attribute]) ?>
            <?php Pjax::end(); ?>
        </div>
    </h4>

    <?php Pjax::begin(['id' => 'attribute-list']); ?> 
        <?= GridView::widget([
            'dataProvider' => $attributeProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '<span class="text-silent">&Oslash;</span>'],
            'showOnEmpty' => false,
            'emptyText' => 'Has no attributes. click the <b>(+)</b> button to start creating them.',
            'summaryOptions' => ['class' => 'summary small'],
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                'name',
                [
                    'attribute' => 'type',
                    'value' => function ($model, $key, $index, $column) {
                        return $model->typeLabel;
                    },
                ],
                [
                    'attribute' => 'required',
                    'format' => 'html',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                    'value' => function ($model, $key, $index, $column) {
                        return $model->required ? '<span class="fui-check"></span>' : '';
                    }
                ],
                [
                    'attribute' => 'unique',
                    'format' => 'html',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                    'value' => function ($model, $key, $index, $column) {
                        return $model->unique ? '<span class="fui-check"></span>' : '';
                    }
                ],
                [
                    'attribute' => 'length',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                ],
                [
                    'attribute' => 'precision',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                ],
                [
                    'attribute' => 'scale',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                ],
                [
                    'attribute' => 'default',
                    'headerOptions' => ['style' => ['text-align' => 'center']],
                    'contentOptions' => ['style' => ['text-align' => 'center']],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => ['text-align'=>'right']],
                    'template' => '{update} {delete}',
                    'buttonOptions' => ['style' => ['margin-left'=>'8px']],
                    'buttons' => [
                        'update' => function ($url, $model) {
                            //return Html::a('<span class="glyphicon glyphicon-info-sign"></span>', $url, ['title' => 'aaa']);
                            return $this->render('attribute/_form', ['model' => $model]);
                        },
                        'delete' => function($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-attribute', 'id' => $model->id], [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                    'pjax' => 0,
                                ],
                            ]);
                        }
                    ],
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>

<hr>


<div class="relationship-index">
    
    <h4>
        Relations
        <div class="pull-right">
            <?php Pjax::begin(['id' => 'relationship-form', 'options' => ['tag' => 'span']]); ?>
                <?= $this->render('relationship/_form', ['model' => $relationship]) ?>
            <?php Pjax::end(); ?>
        </div>
    </h4>

     <?php Pjax::begin(['id' => 'relationship-list']); ?> 
        <?= GridView::widget([
            'dataProvider' => $relationshipProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'showOnEmpty' => false,
            'emptyText' => 'Has no relations.',
            'summaryOptions' => ['class' => 'summary small'],
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                'rel_type',
                [
                    'attribute' => null,
                    'value' => 'relatedTo.name',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => ['text-align'=>'right']],
                    'template' => '{update} {delete}',
                    'buttonOptions' => ['style' => ['margin-left'=>'8px']],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return $this->render('relationship/_form', ['model' => $model]);
                        },
                        'delete' => function($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-relationship', 'id' => $model->id], [
                                'class' => '',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this item?',
                                    'method' => 'post',
                                    'pjax' => 0,
                                ],
                            ]);
                        }
                    ],
                ],
            ],
        ]); ?>

        <?php if(count($juctionAttributes) > 0) { ?>
            <hr>
            <h4>Shared Attributes <span class="lead">(Junction Table)</span></h4>

            <?php foreach ($juctionAttributes as $relation_name => $juction) { ?>
              <div class="breadcrumb">
                <h6>
                    <span class="fui-link"></span> <b><?= $relation_name ?></b>
                    <div class="pull-right">
                        <?php Pjax::begin(['id' => $relation_name . '-attribute-form', 'options' => ['tag' => 'span']]); ?>
                            <?= $this->render('attribute/_form', ['model' => $juction['attribute']]) ?>
                        <?php Pjax::end(); ?>
                    </div>
                </h6>

                <?php Pjax::begin(['id' => $relation_name . '-attribute-list']); ?> 
                    <?= GridView::widget([
                        'dataProvider' => $juction['provider'],
                        'tableOptions' => ['class' => 'table table-hover'],
                        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '<span class="text-silent">&Oslash;</span>'],
                        'showOnEmpty' => false,
                        'emptyText' => $model->name === $relation_name 
                                        ? '<i><b>' . $model->name . '</b></i> has no shared attributes with itself.'
                                        : '<i><b>' . $model->name . '</b></i> has no shared attributes with <i><b>' . $relation_name . '</b></i>.',
                        'summaryOptions' => ['class' => 'summary small'],
                        'layout' => "{items}\n{summary}\n{pager}",
                        'columns' => [
                            'name',
                            [
                                'attribute' => 'type',
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->typeLabel;
                                },
                            ],
                            [
                                'attribute' => 'required',
                                'format' => 'html',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->required ? '<span class="fui-check"></span>' : '';
                                }
                            ],
                            [
                                'attribute' => 'unique',
                                'format' => 'html',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                                'value' => function ($model, $key, $index, $column) {
                                    return $model->unique ? '<span class="fui-check"></span>' : '';
                                }
                            ],
                            [
                                'attribute' => 'length',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                            ],
                            [
                                'attribute' => 'precision',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                            ],
                            [
                                'attribute' => 'scale',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                            ],
                            [
                                'attribute' => 'default',
                                'headerOptions' => ['style' => ['text-align' => 'center']],
                                'contentOptions' => ['style' => ['text-align' => 'center']],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'contentOptions' => ['style' => ['text-align'=>'right']],
                                'template' => '{update} {delete}',
                                'buttonOptions' => ['style' => ['margin-left'=>'8px']],
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return $this->render('attribute/_form', ['model' => $model]);
                                    },
                                    'delete' => function($url, $model) use ($entity_id) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-attribute', 'id' => $model->id, 'entity_id' => $entity_id], [
                                            'class' => '',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this item?',
                                                'method' => 'post',
                                                'pjax' => 0,
                                            ],
                                        ]);
                                    }
                                ],
                            ],
                        ],
                    ]); ?>
                <?php Pjax::end(); ?>

              </div>
            <?php } // end foreach $juctionAttributes ?>

        <?php } // end if count($juctionAttributes) ?>

    <?php Pjax::end(); ?>

</div>


