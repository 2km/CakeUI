<?php
App::uses('AppShell', 'Console/Command');
App::uses('ModelTask', 'Console/Command/Task');

class DkmModelTask extends ModelTask {

	public function bake($name, $data = array()) {
		if ($name instanceof Model) {
			$object = new Model(array('name' => $name->name));
			$fields = $object->schema();
			$data['fields'] = $fields;
		} else {
			//Changed By Dkmadmin
			$object = new Model(array('name' => $name));
			$fields = $object->schema();
			$data['fields'] = $fields;
		}
		return parent::bake($name,$data);
	}
	protected function _interactive() {
		$this->hr();
		$this->out(__d('cake_console', "Bake Model\nPath: %s", $this->getPath()));
		$this->hr();
		$this->interactive = true;

		$primaryKey = 'id';
		$validate = $associations = array();

		if (empty($this->connection)) {
			$this->connection = $this->DbConfig->getConfig();
		}
		$currentModelName = $this->getName();
		$useTable = $this->getTable($currentModelName);
		$db = ConnectionManager::getDataSource($this->connection);
		$fullTableName = $db->fullTableName($useTable);
		if (!in_array($useTable, $this->_tables)) {
			$prompt = __d('cake_console', "The table %s doesn't exist or could not be automatically detected\ncontinue anyway?", $useTable);
			$continue = $this->in($prompt, array('y', 'n'));
			if (strtolower($continue) === 'n') {
				return false;
			}
		}

		$tempModel = new Model(array('name' => $currentModelName, 'table' => $useTable, 'ds' => $this->connection));

		$knownToExist = false;
		try {
			$fields = $tempModel->schema(true);
			$knownToExist = true;
		} catch (Exception $e) {
			$fields = array($tempModel->primaryKey);
		}
		if (!array_key_exists('id', $fields)) {
			$primaryKey = $this->findPrimaryKey($fields);
		}

		if ($knownToExist) {
			$displayField = $tempModel->hasField(array('name', 'title', 'nome', 'titulo', 'descricao'));
			if (!$displayField) {
				$displayField = $this->findDisplayField($tempModel->schema());
			}

			$prompt = __d('cake_console', "Would you like to supply validation criteria \nfor the fields in your model?");
			$wannaDoValidation = $this->in($prompt, array('y', 'n'), 'y');
			if (array_search($useTable, $this->_tables) !== false && strtolower($wannaDoValidation) === 'y') {
				$validate = $this->doValidation($tempModel);
			}

			$prompt = __d('cake_console', "Would you like to define model associations\n(hasMany, hasOne, belongsTo, etc.)?");
			$wannaDoAssoc = $this->in($prompt, array('y', 'n'), 'y');
			if (strtolower($wannaDoAssoc) === 'y') {
				$associations = $this->doAssociations($tempModel);
			}
		}

		$this->out();
		$this->hr();
		$this->out(__d('cake_console', 'The following Model will be created:'));
		$this->hr();
		$this->out(__d('cake_console', "Name:       %s", $currentModelName));

		if ($this->connection !== 'default') {
			$this->out(__d('cake_console', "DB Config:  %s", $this->connection));
		}
		if ($fullTableName !== Inflector::tableize($currentModelName)) {
			$this->out(__d('cake_console', 'DB Table:   %s', $fullTableName));
		}
		if ($primaryKey !== 'id') {
			$this->out(__d('cake_console', 'Primary Key: %s', $primaryKey));
		}
		if (!empty($validate)) {
			$this->out(__d('cake_console', 'Validation: %s', print_r($validate, true)));
		}
		if (!empty($associations)) {
			$this->out(__d('cake_console', 'Associations:'));
			$assocKeys = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
			foreach ($assocKeys as $assocKey) {
				$this->_printAssociation($currentModelName, $assocKey, $associations);
			}
		}

		$this->hr();
		$looksGood = $this->in(__d('cake_console', 'Look okay?'), array('y', 'n'), 'y');

		if (strtolower($looksGood) === 'y') {
			$vars = compact('associations', 'validate', 'primaryKey', 'useTable', 'displayField');
			$vars['useDbConfig'] = $this->connection;
			if ($this->bake($currentModelName, $vars)) {
				if ($this->_checkUnitTest()) {
					$this->bakeFixture($currentModelName, $useTable);
					$this->bakeTest($currentModelName, $useTable, $associations);
				}
			}
		} else {
			return false;
		}
	}
}
?>