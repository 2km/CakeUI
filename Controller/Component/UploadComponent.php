<?php
class UploadComponent extends Object {
	public $options = null;
	public $settings = null;
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
	    parent::__construct($collection, $settings);
	}	
	public function initialize($controller) { 
		if(!empty($controller->uses[0])){
			$this->model = ClassRegistry::init($controller->uses[0]);
		}else{
			$this->model = ClassRegistry::init($controller->modelClass);
		}
	}
    public function startup(&$controller) {
    	if(isset($controller->request->form["qqfile"]) || isset($controller->request->form["qqfile"])){
	    	$fieldToUpload = $controller->request->data["qqFieldName"];
	    	if(!isset($this->settings[$fieldToUpload]['allow'])){
				$this->settings[$fieldToUpload]['allow']=array('jpg','jpeg','gif','png');
			}
			if(!isset($this->settings[$fieldToUpload]['size'])){
				$this->settings[$fieldToUpload]['size']=5248880;
			}
			if(!isset($this->settings[$fieldToUpload]['path'])){
				$this->settings[$fieldToUpload]['path']='uploads';
			}		
			if($controller->RequestHandler->isAjax()){
				$controller->autoRender=false;
				echo $this->_do_upload($controller->request->data["qqFieldName"]);
				die();
			}
		}
	}

	public function beforeRender($controller) {}
	public function beforeRedirect($controller) {}
	public function shutdown($controller) {}
	
	private function _do_upload($fieldName){
		App::import('Vendor', 'CakeUI.ajaxUploader', array('file' => 'fineuploader'.DS.'php.php'));
		$allowedExtensions = $this->settings[$fieldName]['allow'];
		$sizeLimit = $this->settings[$fieldName]['size']; //até 5 megas
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$caminho = WWW_ROOT.$this->settings[$fieldName]['path'].'/';
		$result = $uploader->handleUpload($caminho,true);
		if(isset($result['success'])){
			if(isset($this->settings[$fieldName]['resize'])){
				foreach ($this->settings[$fieldName]['resize'] as $name => $width) {
					$target = WWW_ROOT.$this->settings[$fieldName]['path'].'/'.$name.'/';
					$this->_resize($caminho.$result['filename'],$target,$result['filename'],$width);
				}
			}
		}
		return htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
	private function _resize($sourceFilename,$target,$fileName,$width){
		App::import('Vendor', 'CakeUI.phpthumb', array('file' => 'phpthumb'.DS.'ThumbLib.inc.php'));
		if(is_readable($sourceFilename)){
			$options = array('resizeUp' => false, 'jpegQuality' => 100);
			$thumb = PhpThumbFactory::create($sourceFilename,$options);
			$thumb->resize($width);
			if(!$thumb->save($target.$fileName)){
				return false;
			}
			unset($thumb);
		} else {
			return false;
		}
		return true;
	}
}
?>