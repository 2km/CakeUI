<?php
/**
 * Bake Template for Controller action generation.
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
 * @package       Cake.Console.Templates.default.actions
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>

/**
 * <?php echo $admin ?>index method
 *
 * @return void
 */
	public function <?php echo $admin ?>index() {
		$this-><?php echo $currentModelName ?>->recursive = 0;
		//$this->Prg->commonProcess();
		//$this->Paginator->settings['conditions'] = $this-><?php echo $currentModelName ?>->parseCriteria($this->passedArgs);
		$this->set('<?php echo $pluralName ?>', $this->Paginator->paginate());
<?php
	foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach ($modelObj->{$assoc} as $associationName => $relation):
			if (!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\t\t//\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
				$compact[] = "'{$otherPluralName}'";
			endif;
		endforeach;
	endforeach;
	if (!empty($compact)):
		echo "\t\t//\$this->set(compact(".join(', ', $compact)."));\n";
	endif;
?>
	}

/**
 * <?php echo $admin ?>view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function <?php echo $admin ?>view($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
<?php if(empty($artigo)): ?>
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
<?php else: ?>
			throw new NotFoundException(__('<?php echo ucfirst(mb_strtolower($singularHumanName,"UTF8")); ?> inválid<?php echo $artigo;?>!'));
<?php endif; ?>
		}
		$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
		$this->set('<?php echo $singularName; ?>', $this-><?php echo $currentModelName; ?>->find('first', $options));
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin ?>add method
 *
 * @return void
 */
	public function <?php echo $admin ?>add() {
		if ($this->request->is('post')) {
			$this-><?php echo $currentModelName; ?>->create();
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
<?php if(empty($artigo)): ?>
				$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> has been saved.'),'default',array('class'=>'alert alert-success'));
<?php else: ?>
				$this->Session->setFlash(__('<?php echo ucfirst($artigo), ' ', ucfirst($singularHumanName); ?> foi criad<?php echo $artigo;?>.'),'default',array('class'=>'alert alert-success'));
<?php endif; ?>
				return $this->redirect(array('action' => 'index'));
			} else {
<?php if(empty($artigo)): ?>
				$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
<?php else: ?>
				$this->Session->setFlash(__('Não foi possível salvar <?php echo $artigo .' '. mb_strtolower($singularHumanName,"UTF-8"); ?>. Por favor, tente novamente.'),'default',array('class'=>'alert alert-danger'));
<?php endif; ?>

<?php else: ?>
				return $this->flash(__('The <?php echo strtolower($singularHumanName); ?> has been saved.'), array('action' => 'index'));
<?php endif; ?>
			}
		}
<?php
	foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
		foreach ($modelObj->{$assoc} as $associationName => $relation):
			if (!empty($associationName)):
				$otherModelName = $this->_modelName($associationName);
				$otherPluralName = $this->_pluralName($associationName);
				echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
				$compact[] = "'{$otherPluralName}'";
			endif;
		endforeach;
	endforeach;
	if (!empty($compact)):
		echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
	endif;
?>
	}

<?php $compact = array(); ?>
/**
 * <?php echo $admin ?>edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function <?php echo $admin; ?>edit($id = null) {
		if (!$this-><?php echo $currentModelName; ?>->exists($id)) {
<?php if(empty($artigo)): ?>
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
<?php else: ?>
			throw new NotFoundException(__('<?php echo ucfirst($singularHumanName); ?> inválid<?php echo $artigo;?>!'));
<?php endif; ?>
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this-><?php echo $currentModelName; ?>->save($this->request->data)) {
<?php if ($wannaUseSession): ?>
<?php if(empty($artigo)): ?>
				$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> has been saved.'),'default',array('class'=>'alert alert-success'));
<?php else: ?>
				$this->Session->setFlash(__('<?php echo ucfirst($artigo), ' ', ucfirst($singularHumanName); ?> foi criad<?php echo $artigo;?>.'),'default',array('class'=>'alert alert-success'));
<?php endif; ?>
				return $this->redirect(array('action' => 'index'));
			} else {
<?php if(empty($artigo)): ?>
				$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> could not be saved. Please, try again.'),'default',array('class'=>'alert alert-danger'));
<?php else: ?>
				$this->Session->setFlash(__('Não foi possível salvar <?php echo $artigo .' '. mb_strtolower($singularHumanName,"UTF-8"); ?>. Por favor, tente novamente.'),'default',array('class'=>'alert alert-danger'));
<?php endif; ?>
<?php else: ?>
				return $this->flash(__('The <?php echo strtolower($singularHumanName); ?> has been saved.'), array('action' => 'index'));
<?php endif; ?>
			}
		} else {
			$options = array('conditions' => array('<?php echo $currentModelName; ?>.' . $this-><?php echo $currentModelName; ?>->primaryKey => $id));
			$this->request->data = $this-><?php echo $currentModelName; ?>->find('first', $options);
		}
<?php
		foreach (array('belongsTo', 'hasAndBelongsToMany') as $assoc):
			foreach ($modelObj->{$assoc} as $associationName => $relation):
				if (!empty($associationName)):
					$otherModelName = $this->_modelName($associationName);
					$otherPluralName = $this->_pluralName($associationName);
					echo "\t\t\${$otherPluralName} = \$this->{$currentModelName}->{$otherModelName}->find('list');\n";
					$compact[] = "'{$otherPluralName}'";
				endif;
			endforeach;
		endforeach;
		if (!empty($compact)):
			echo "\t\t\$this->set(compact(".join(', ', $compact)."));\n";
		endif;
	?>
	}

/**
 * <?php echo $admin ?>delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function <?php echo $admin; ?>delete($id = null) {
		$this-><?php echo $currentModelName; ?>->id = $id;
		if (!$this-><?php echo $currentModelName; ?>->exists()) {
<?php if(empty($artigo)): ?>
			throw new NotFoundException(__('Invalid <?php echo strtolower($singularHumanName); ?>'));
<?php else: ?>
			throw new NotFoundException(__('<?php echo ucfirst($singularHumanName); ?> inválid<?php echo $artigo;?>!'));
<?php endif; ?>
		}
		$this->request->allowMethod('post', 'delete');
		if ($this-><?php echo $currentModelName; ?>->delete()) {
<?php if ($wannaUseSession): ?>
<?php if(empty($artigo)): ?>
			$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> has been deleted.'),'default',array('class'=>'alert alert-success'));
<?php else: ?>
			$this->Session->setFlash(__('<?php echo ucfirst($artigo), ' ', ucfirst($singularHumanName); ?> foi excluíd<?php echo $artigo;?>.'),'default',array('class'=>'alert alert-success'));
<?php endif; ?>
		} else {
<?php if(empty($artigo)): ?>
			$this->Session->setFlash(__('The <?php echo strtolower($singularHumanName); ?> could not be deleted. Please, try again.'),'default',array('class'=>'alert alert-danger'));
<?php else: ?>
			$this->Session->setFlash(__('Não foi possível excluir <?php echo $artigo;?> <?php echo mb_strtolower($singularHumanName,"UTF-8"); ?>.'),'default',array('class'=>'alert alert-danger'));
<?php endif; ?>
		}
		return $this->redirect(array('action' => 'index'));
<?php else: ?>
			return $this->flash(__('The <?php echo strtolower($singularHumanName); ?> has been deleted.'), array('action' => 'index'));
		} else {
			return $this->flash(__('The <?php echo strtolower($singularHumanName); ?> could not be deleted. Please, try again.'), array('action' => 'index'));
		}
<?php endif; ?>
	}
