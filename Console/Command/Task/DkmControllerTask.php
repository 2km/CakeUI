<?php
App::uses('AppShell', 'Console/Command');
App::uses('ControllerTask', 'Console/Command/Task');
App::uses('AppModel', 'Model');


class DkmControllerTask extends ControllerTask {
	
	public function bakeActions($controllerName, $admin = null, $wannaUseSession = true) {
		$currentModelName = $modelImport = $this->_modelName($controllerName);
		$modelObj = ClassRegistry::init($currentModelName);

		$info = $artigo = null;
		if(isset($modelObj->genero)){
			$artigo = $modelObj->genero == 'm' ? 'o' : 'a';
		}
		if(isset($modelObj->info)){
			$info = $modelObj->info;
		}
		$this->Template->set(compact('artigo','info'));
		return parent::bakeActions($controllerName, $admin, $wannaUseSession);
	}
}
?>