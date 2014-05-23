<?php
class ZipcodeBRComponent extends Object {
	public $options = null;
	public $settings = null;
	private $modelName = null;
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = $settings;
	    parent::__construct($collection, $settings);
	}	
	public function initialize($controller) { 
		if(!empty($controller->uses[0])){
			$this->modelName = $controller->uses[0];
		}else{
			$this->modelName = $controller->modelClass;
		}
	}
    public function startup(&$controller) {
    	if(isset($controller->request->data["zipCodeComp"]) || isset($controller->request->data["zipCodeComp"])){
			echo $this->get_address($controller->request->data[$this->modelName][$this->settings['fieldName']]);
			die();
		}
	}

	public function beforeRender($controller) {}
	public function beforeRedirect($controller) {}
	public function shutdown($controller) {}
	
	private function get_address($zipcode){
		App::uses('Sanitize','Utility');
		$zipcode = Sanitize::paranoid($zipcode);
		App::uses('HttpSocket', 'Network/Http');
		$HttpSocket = new HttpSocket();
		$post['cepEntrada']=$zipcode; $post['tipoCep']=''; $post['cepTemp']=''; $post['metodo']='buscarCep';
		$resposta = $HttpSocket->post("http://m.correios.com.br/movel/buscaCepConfirma.do",$post);
		$resposta = str_replace("\n","",$resposta);$resposta = str_replace("\r","",$resposta);$resposta = str_replace("\t","",$resposta);
		preg_match_all('/<span class="respostadestaque">([^<]+)<\/span>/i',$resposta,$match);
		if(isset($match[1][0])){
			$endereco = utf8_encode(trim($match[1][0]));
			if(stripos($endereco, '- até')!==false){
				preg_match_all('/(.*)(- até)(.*)/i',$endereco,$matchRua);
				$endereco = $matchRua[1][0];
			} else if(stripos($endereco, '- de')!==false){
				preg_match_all('/(.*)(- de)(.*)/i',$endereco,$matchRua);
				$endereco = $matchRua[1][0];
			} else if(stripos($endereco, '- lado')!==false){
				preg_match_all('/(.*)(- lado)(.*)/i',$endereco,$matchRua);
				$endereco = $matchRua[1][0];
			}
			$this->request->data['Zipcode']['logradouro']=trim($endereco);
			$this->request->data['Zipcode']['bairro']=utf8_encode(trim($match[1][1]));
			$cidadeUF = explode('/',$match[1][2]);
			$this->request->data['Zipcode']['cidade']=utf8_encode(trim($cidadeUF[0]));
			$this->request->data['Zipcode']['estado']=trim($cidadeUF[1]);
		}
		return json_encode($this->request->data);
	}
	
}
?>