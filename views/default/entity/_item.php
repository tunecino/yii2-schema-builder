<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= Html::a(
	Html::encode($model->name), 
	['view-entity', 'id' => $model->id],
	['data-toggle' => 'tooltip', 'data-placement' => 'bottom', 'title' => 'Edit']
)?>

<div class="pull-right">
	<?= Html::a(
		'<span class="fui-new"></span>', 
		['view-entity', 'id' => $model->id], 
		['data-toggle' => 'tooltip', 'data-placement' => 'left', 'title' => 'Edit']
	)?>
	<a onclick="window.open(this.href,'_blank');return false;" href="<?= $model->previewUrl ?>" data-toggle="tooltip" data-placement="right" title="Preview"><span class="fui-export"></span></a>

</div>


