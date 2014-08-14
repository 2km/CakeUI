<?php
App::uses('Component', 'Controller');
App::uses('CakeSession', 'Model/Datasource');
App::uses('CookieComponent', 'Controller/Component');
class childFormComponent extends Component {
	public $options = null;
	public $settings = null;

	public $components = array('Session','Cookie');

	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
	    parent::__construct($collection, $settings);
	}
	public function initialize(Controller $controller) {
		if(!empty($controller->uses[0])){
			$this->model = ClassRegistry::init($controller->uses[0]);
		}else{
			$this->model = ClassRegistry::init($controller->modelClass);
		}
	}
/*
CakeUIOperation = 1 (display modal window)
CakeUIOperation = 2 (add or update form)
CakeUIOperation = 3 delete
*/
    public function startup(Controller $controller) {
    	if(!isset($controller->request->params['named']['CakeUICookie'])){
    		return ;
    	}
    	$options = json_decode($_COOKIE[$controller->request->params['named']["CakeUICookie"]],true);

    	$model = $options['model'];
    	if($controller->request->params['named']['CakeUIOperation'] == 1){ //Display modal window
    		if(isset($controller->request->data[$model])){
    			$data[$model] = $controller->request->data[$model][$controller->request->params['named']["CakeUIRowId"]];
    			$controller->request->data = $data;
    			$controller->request->data['CakeUITemp']['key']=$controller->request->params['named']["CakeUIRowId"];
    		}
    		$controller->set(compact('options'));
    		$controller->set("cakeUICookie",$controller->request->params['named']["CakeUICookie"]);
	    	echo $controller->render($options["element"]);
	    	die();
		} else if($controller->request->params['named']['CakeUIOperation'] == 2){ //Add or update
    		$controller->set(compact('options'));
    		$controller->set("cakeUICookie",$controller->request->params['named']["CakeUICookie"]);
			if ($this->model->{$model}->saveAll($controller->request->data[$model],array('validate'=>'only'))) {
				$controller->set('requestData',$controller->request->data);
			    echo $controller->render($options["return_element"]);
			} else {
				$controller->Session->setFlash(__('Não foi possível adicionar este item'),'default',array('class'=>'alert alert-danger'),'modalMsg');
			    echo $controller->render($options["element"]);
			}
	    	die();
		} else if($controller->request->params['named']['CakeUIOperation'] == 3){ //Delete
			$id = $controller->request->params['named']["CakeUIRecordId"];
			$this->model->{$model}->id = $id;
			if (!$this->model->{$model}->exists()) {
				throw new NotFoundException(__('Item inválido 1'));
			}
			if ($this->model->{$model}->delete($id,true)) {
				die();
			}
			throw new NotFoundException(__('Item inválido 2'));
		}
	}
    public function shutdown(Controller $controller){
        if(isset($controller->request->params['named']['CakeUICookie'])){
            unset($_COOKIE[$controller->request->params['named']['CakeUICookie']]);
            setcookie($controller->request->params['named']['CakeUICookie'], "", time()-3600);
        }
    }
}
?>