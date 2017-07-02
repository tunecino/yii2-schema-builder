<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\gii\generators\model\Generator;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $schema tunecino\models\Schema */
/* @var $form yii\widgets\ActiveForm */

?>

<?php Modal::begin([
    'id' => 'generate-modal',
    'options' => ['class' => 'modal modal-fullscreen fade', 'data-backdrop' => 'static'],
    'header' =>  '<h4>Terminal</h4>',
    'toggleButton' => $schema->readyToGenerate() ? 
    	['label' => '<span class="fui-play"></span> GENERATE', 'class' => 'btn btn-inverse mt20 mb20'] :
    	['label' => '<span class="fui-play"></span> GENERATE', 'class' => 'btn btn-inverse mt20 mb20', 'disabled' => true, 'data-toggle'=> 'tooltip', 'title' => 'Either there is no created entities or there is an entity without any attribute'],
]); ?>

<p class="jumbotron alert alert-danger text-center">
	By hitting the <strong>GENERATE</strong> button you are goinig to <strong class="text-uppercase">destroy</strong> your <strong class="text-uppercase">database</strong> and most of <strong class="text-uppercase">your working files</strong>. This extension was designed to work on new builds. Use it with precaution.
	<a href="#" onclick="$('#commands').toggle()">Click here to show/hide the full list of commands to execute.</a>
</p>

<pre id="commands" style="display:none">
	<ol>
		<?php foreach ($schema->consoleCommands as $cmd) {
			echo '<li>' . $cmd . '</li>';
		} ?>	
	</ol>
</pre>

<div class="text-center">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button 
		id="generateBtn"
		type="button" 
		class="btn btn-inverse" 
		<?php if ($schema->readyToGenerate() === false) echo 'disabled="" data-toggle="tooltip" title="Either there is no created entities or there is an entity without any attribute"'; ?> 
		data-cmd-path="<?= Url::to(['default/get-commands', 'id' => $schema->id], true) ?>"
		data-console-path="<?= Url::to(['default/std'], true) ?>"
	><span class="fui-play"></span> GENERATE</button>
</div>


<pre id="console" class="terminal palette palette-midnight-blue" style="display:none"></pre>

<?php Modal::end(); ?>


