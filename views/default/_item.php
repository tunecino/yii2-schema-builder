<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<a class="btn btn-block btn-inverse palette-custom-asphalt" href="<?= Url::to(['view', 'id' => $model->id]) ?>">

    <h2>
	    <?= Html::encode($model->name) ?>
    </h2>

    <p data-toggle="tooltip" data-placement="right" title="Number of contained Entities"><?= Html::encode($model->getEntities()->count()) ?> <span class="fui-folder"></span></p> 
</a>