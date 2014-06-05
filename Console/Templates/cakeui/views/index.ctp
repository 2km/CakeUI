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
<?php
$singularHumanName = isset($info['singular']) ? $info['singular'] : $singularHumanName;
?>
<div class="<?php echo $pluralVar; ?> index">
	<h2><?php echo "<?php echo __('{$pluralHumanName}'); ?>"; ?></h2>
	<div class="actions">
		<ul>
			<li><?php 
			if(empty($artigo)):
				echo "<?php echo \$this->Html->newLink(__('New " . $singularHumanName . "'), array('action'=>'add')); ?>";
			else:
				echo "<?php echo \$this->Html->link(__('Nov".$artigo ." " .$singularHumanName . "'), array('action' => 'add'),array('class'=>'btn btn-primary')); ?>";
			endif;
			?></li>
			<li><?php 
			if(empty($artigo)):
				echo "<?php //echo \$this->Html->link(\"<span class='glyphicon glyphicon-search'></span> \".__('Filter'),'#',array('id'=>'filter-button', 'class'=>'btn btn-default','escape'=>false));?>";
			else:
				echo "<?php //echo \$this->Html->link(\"<span class='glyphicon glyphicon-search'></span> \".__('Filtro'),'#',array('id'=>'filter-button', 'class'=>'btn btn-default','escape'=>false));?>";
			endif;		
			?></li>
		</ul>
	</div>
	<!-- <div id="form-search" class="hidden"> -->
<?php
	if(empty($artigo)):
		echo "\t<?php\n";
		echo "/*\n";
		echo "\t\techo \$this->Form->create('{$modelClass}');\n";
		echo "\t\t\$gridForm[][12]=\$this->Form->input('".$fields[1]."');\n\n";
		echo "\t\t\$gridActions[][5]=\$this->Form->end(array('label'=>__('Search'),'class'=>'btn btn-success topAlign'));\n";
		echo "\t\t\$gridActions[][7]=\$this->Html->link(__('Show All'),array('action'=>'index'),array('class'=>'btn btn-default topAlign'));\n\n";	
		echo "\t\t\$grid[][10]=\$this->Html->grid(\$gridForm);\n";
		echo "\t\t\$grid[][2]=\$this->Html->grid(\$gridActions);\n";
		echo "\t\techo \$this->Html->grid(\$grid);\n";
		echo "*/\n";
		echo "\t?>\n";
	else:
		echo "\t<?php\n";
		echo "/*\n";
		echo "\t\techo \$this->Form->create('{$modelClass}');\n";
		echo "\t\t\$gridForm[][12]=\$this->Form->input('".$fields[1]."');\n\n";
		echo "\t\t\$gridActions[][5]=\$this->Form->end(array('label'=>__('Buscar'),'class'=>'btn btn-success topAlign'));\n";
		echo "\t\t\$gridActions[][7]=\$this->Html->link(__('Limpar'),array('action'=>'index'),array('class'=>'btn btn-default topAlign'));\n\n";	
		echo "\t\t\$grid[][10]=\$this->Html->grid(\$gridForm);\n";
		echo "\t\t\$grid[][2]=\$this->Html->grid(\$gridActions);\n";
		echo "\t\techo \$this->Html->grid(\$grid);\n";
		echo "*/\n";
		echo "\t?>\n";
	endif;
?>
	<!-- </div> -->
	<table class='table table-striped table-hover table-condensed'>
	<thead>
	<tr>
	<?php foreach ($fields as $field): 
			if ($field=='created' || $field=='modified' || $field=='created_by' || $field=='modified_by'): 
				continue;
			elseif ($pos = strpos($field,'_id') === false):
	?>
		<th><?php echo "<?php echo \$this->Paginator->sort('{$field}',__('".ucfirst($field)."')); ?>"; ?></th>
	<?php else: ?>
		<th><?php echo "<?php echo \$this->Paginator->sort('{$field}',__('".ucfirst(strstr($field,'_id',true))."')); ?>"; ?></th>
	<?php
			endif;
	endforeach; 
	?>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php
	echo "<?php foreach (\${$pluralVar} as \${$singularVar}): ?>\n";
	echo "\t<tr>\n";
		foreach ($fields as $field) {
			if ($field=='created' || $field=='modified' || $field=='created_by' || $field=='modified_by'): 
				continue;
			endif;
			$isKey = false;
			if (!empty($associations['belongsTo'])) {
				foreach ($associations['belongsTo'] as $alias => $details) {
					if ($field === $details['foreignKey']) {
						$isKey = true;
						echo "\t\t<td>\n\t\t\t<?php echo \$this->Html->link(\${$singularVar}['{$alias}']['{$details['displayField']}'], array('controller' => '{$details['controller']}', 'action' => 'view', \${$singularVar}['{$alias}']['{$details['primaryKey']}'])); ?>\n\t\t</td>\n";
						break;
					}
				}
			}
			if ($isKey !== true) {
				echo "\t\t<td><?php echo h(\${$singularVar}['{$modelClass}']['{$field}']); ?>&nbsp;</td>\n";
			}
		}

		echo "\t\t<td class=\"actions\">\n";
		echo "\t\t\t<?php\n";
		if(empty($artigo)):
			echo "\t\t\t\techo \$this->Html->viewLink(__('View'),array('action'=>'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])).' ';\n";
			echo "\t\t\t\techo \$this->Html->editLink(__('Edit'), array('action'=>'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])).' ';\n";
			echo "\t\t\t\techo \$this->Form->deleteLink(__('Delete'),array('action'=>'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']),null,sprintf(__('Are you sure you want to delete # %s?'), \${$singularVar}['{$modelClass}']['{$primaryKey}']));\n";
		else:
			echo "\t\t\t\techo \$this->Html->viewLink(__('Ver'),array('action'=>'view', \${$singularVar}['{$modelClass}']['{$primaryKey}'])).' ';\n";
			echo "\t\t\t\techo \$this->Html->editLink(__('Editar'), array('action'=>'edit', \${$singularVar}['{$modelClass}']['{$primaryKey}'])).' ';\n";
			echo "\t\t\t\techo \$this->Form->deleteLink(__('Excluir'),array('action'=>'delete', \${$singularVar}['{$modelClass}']['{$primaryKey}']),null,sprintf(__('Tem certeza que deseja excluir o registro # %s?'), \${$singularVar}['{$modelClass}']['{$primaryKey}']));\n";
		endif;
		echo "\t\t\t?>\n";
		echo "\t\t</td>\n";
		echo "\t</tr>\n";
		echo "<?php endforeach; ?>\n";
	?>
	</tbody>
	</table>
</div>
<?php echo "<?php echo \$this->element('CakeUI.paging'); ?>";?>