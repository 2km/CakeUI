<?php
App::uses('AppShell', 'Console/Command');
App::uses('Controller', 'Controller');
App::uses('ViewTask', 'Console/Command/Task');


class DkmViewTask extends ViewTask {
	protected function _loadController() {
		if (!$this->controllerName) {
			$this->err(__d('cake_console', 'Controller not found'));
		}

		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}

		$controllerClassName = $this->controllerName . 'Controller';
		App::uses($controllerClassName, $plugin . 'Controller');
		if (!class_exists($controllerClassName)) {
			$file = $controllerClassName . '.php';
			$this->err(__d('cake_console', "The file '%s' could not be found.\nIn order to bake a view, you'll need to first create the controller.", $file));
			return $this->_stop();
		}
		$controllerObj = new $controllerClassName();
		$controllerObj->plugin = $this->plugin;
		$controllerObj->constructClasses();
		$modelClass = $controllerObj->modelClass;
		$modelObj = $controllerObj->{$controllerObj->modelClass};

		if ($modelObj) {
			$primaryKey = $modelObj->primaryKey;
			$displayField = $modelObj->displayField;
			$singularVar = Inflector::variable($modelClass);
			$singularHumanName = $this->_singularHumanName($this->controllerName);
			$schema = $modelObj->schema(true);
			$fields = array_keys($schema);
			$associations = $this->_associations($modelObj);
		} else {
			$primaryKey = $displayField = null;
			$singularVar = Inflector::variable(Inflector::singularize($this->controllerName));
			$singularHumanName = $this->_singularHumanName($this->controllerName);
			$fields = $schema = $associations = array();
		}
		$pluralVar = Inflector::variable($this->controllerName);
		$pluralHumanName = $this->_pluralHumanName($this->controllerName);

		$artigo = $info = null;
		if(isset($modelObj->genero)){
			$artigo = $modelObj->genero == 'm' ? 'o' : 'a';	
		}
		if(isset($modelObj->info)){
			$info = $modelObj->info;
		}

		return compact('artigo','info','modelClass', 'schema', 'primaryKey', 'displayField', 'singularVar', 'pluralVar',
				'singularHumanName', 'pluralHumanName', 'fields', 'associations');
	}
}
?>