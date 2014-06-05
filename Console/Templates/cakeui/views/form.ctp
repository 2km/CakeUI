<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.views
 * @since         CakePHP(tm) v 1.2.0.5234
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<div class="panel panel-default page-header <?php echo $pluralVar; ?> form">
	<div class="panel-heading">
		<h3 class="panel-title">
	<?php 
	if(empty($artigo)){
		printf("<?php echo __('%s %s'); ?>", Inflector::humanize($action), $singularHumanName);
	}else{
		if (strpos($action, 'add') !== false):
			printf("<?php echo __('%s %s'); ?>", 'Adicionar', $singularHumanName);
		else: 
			printf("<?php echo __('%s %s'); ?>", 'Editar', $singularHumanName); 
		endif;
	}
	echo "\n";
	?>
		</h3>
	</div>
	<div class="panel-body">
<?php 
	echo "\t<?php \n\t\techo \$this->Form->create('{$modelClass}');\n";
	foreach ($fields as $field) {
		if (strpos($action, 'add') !== false && $field === $primaryKey) {
			continue;
		} elseif (!in_array($field, array('created', 'modified', 'updated','created_by','modified_by','slug'))) {
			if ($schema[$field]['type']=='date') {
				echo "\t\techo \$this->Form->datePicker('{$field}');\n";
			} elseif($schema[$field]['type']=='text'){
				echo "\t\techo \$this->Form->wysiwyg('{$field}');\n";
			}else{
				echo "\t\techo \$this->Form->input('{$field}');\n";	
			}
			
		}
	}
	if (!empty($associations['hasAndBelongsToMany'])) {
		foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
			echo "\t\techo \$this->Form->input('{$assocName}');\n";
		}
	}
	if(empty($artigo)):
		echo "\t\techo \$this->Form->button('<span class=\"glyphicon glyphicon-ok\"></span> '.__('Save'),array('class'=>'btn btn-success'));\n";
	else:
		echo "\t\techo \$this->Form->button('<span class=\"glyphicon glyphicon-ok\"></span> '.__('Salvar'),array('class'=>'btn btn-success'));\n";
	endif;
	echo "\t\techo \$this->Form->end();\n";
	echo "\t?>\n";
?>
	</div>
</div>
<div class="actions">
	<ul>
<?php if (strpos($action, 'add') === false): ?>
		<li><?php 
			if(empty($artigo)):
				echo "<?php echo \$this->Form->postLink(__('Delete').' <span class=\"glyphicon glyphicon-trash\"></span>', array('action' => 'delete', \$this->Form->value('{$modelClass}.{$primaryKey}')), array('class'=>'btn btn-danger','escape'=>false), __('Are you sure you want to delete # %s?', \$this->Form->value('{$modelClass}.{$primaryKey}'))); ?>"; 
			else:
				echo "<?php echo \$this->Form->postLink(__('Excluir').' <span class=\"glyphicon glyphicon-trash\"></span>', array('action' => 'delete', \$this->Form->value('{$modelClass}.{$primaryKey}')), array('class'=>'btn btn-danger','escape'=>false), __('Are you sure you want to delete # %s?', \$this->Form->value('{$modelClass}.{$primaryKey}'))); ?>";
			endif;
			?>
		</li>
<?php endif; ?>
		<li><?php 
			if(empty($artigo)):
				echo "<?php echo \$this->Html->link(__('List " . $pluralHumanName . "').' <span class=\"glyphicon glyphicon-th-list\"></span>', array('action' => 'index'),array('class'=>'btn btn-info','escape'=>false)); ?>";
			else:
				echo "<?php echo \$this->Html->link(__('Listar " . $pluralHumanName . "').' <span class=\"glyphicon glyphicon-th-list\"></span>', array('action' => 'index'),array('class'=>'btn btn-info','escape'=>false)); ?>";
			 endif;
			 ?>
		</li>
	</ul>
</div>
