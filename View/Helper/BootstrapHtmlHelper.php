<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 *
 * Licensed under The MIT License
 *
 * Copyright (c) La Pâtisserie, Inc. (http://patisserie.keensoftware.com/)
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('HtmlHelper', 'View/Helper');
App::uses('Inflector', 'Utility');

class BootstrapHtmlHelper extends HtmlHelper {
	public $helpers = array('Form');
	private $counter=0;

/*
	$data[0]['title']='Home'
	$data[0]['link']='/'
	$data[1]['title']='Users'
	$data[1]['link']='/users/index'
	$data[2]['title']='Profile'
	$data[2]['link']='#' //this action will create a last item (disabled, without link)
*/
	public function breadcrumb($data = array()){
		$html = '<ol class="breadcrumb">';
		foreach ($data as $key => $value) {
			if($value['link']!='#'){
				$html .= '<li>'.$this->link($value['title'],$value['link']).'</li>';	
			} else{
				$html .= '<li>'.$value['title'].'</li>';	
			}
		}
		$html .= '</ol>';	
		return $html;
	}
/*
	$data[0]['title']='tab1'
	$data[0]['content']='Lorem ipsum dollar'
	$data[0]['class']='disabled' //optional
	$data[1]['title']='tab2'
	$data[1]['content']='/users/index'
	$data[2]['title']='Lorem ipsum dollar'
	$data[2]['content']='Lorem ipsum dollar'

	$config['type'] = (tabs | tabs nav-justified | pills | pills nav-stacked | pills nav-justified)
	$config['selected']=2 //key position
*/
	public function tabs($data = array(),$config = array()){
		$defaultConfig = array('type'=>'tabs');
		$config = am($defaultConfig,$config);
		$tabId = "tab-".$this->counter++;
		//Nav tabs
		$html = '<ul id="'.$tabId.'" class="nav nav-'.$config['type'].'">';
		$firstKey = key($data);
		foreach ($data as $key => $value) {
			$class = null;
			if(isset($value['class'])){
				$class = $value['class'];
			}
			$link = $this->link($value['title'],'#'.Inflector::slug($value['title']).$this->counter.$key,array("data-toggle"=>"tab"));
			if(isset($value['class']) && $value['class']=='disabled'){
				$link = $this->link($value['title'],'#');
			}
			
			if($firstKey == $key){
				$html.='<li class="active '.$class.'">'.$link.'</li>';
			} else {
				$html.='<li class="'.$class.'">'.$link.'</li>';
			}
		}
		$html .= '</ul>';
		$html .= '<div class="tab-content">';
		foreach ($data as $key => $value) {
			if($firstKey == $key){
				$html.='<div class="tab-pane active" id="'.(Inflector::slug($value['title']).$this->counter.$key).'">'.$value['content'].'</div>';
			} else {
				$html.='<div class="tab-pane" id="'.(Inflector::slug($value['title']).$this->counter.$key).'">'.$value['content'].'</div>';
			}
		}
		$html.='</div><div class="clearAny"></div>';
		if(isset($config['selected'])){
			echo $this->scriptBlock('$("#'.$tabId.' a[href=\"#'.(Inflector::slug($data[$config['selected']]['title']).$this->counter.$config['selected']).'\"]").tab("show");',array('inline'=>false));	
		}

		return $html;
	}
	/*
	$options[0]['title']='Action';
	$options[0]['link']='/';
	$options[1]['title']='Other Action';
	$options[1]['link']='/users';

	$config['size']=(btn-xs | btn-sm | btn-lg) //default size without any class
	$config['color']=(btn-default | btn-primary | btn-success | btn-info | btn-warning | btn-danger | btn-link)
	$config['split']=true;
	$config['dropup']=true;

	$config['form']['submit']=true;
	$config['form']['field']='Tmp.operation'
	*/
	public function dropdownButton($title=null,$options=array(),$config=array()){
		$defaultConfig = array(
			'size'=>null,
			'color'=>'btn-default',
			'split'=>false,
			'dropup'=>false,
			'form'=>array('submit'=>false,'field'=>'Tmp.operation')
		);
		$config['form'] = am($defaultConfig['form'],$config['form']);
		$config = am($defaultConfig,$config);
		$html = null;

		if($config['form']['submit']){
			$html .=$this->Form->input($config['form']['field'],array('type'=>'hidden','value'=>key($options)));
		}
		if($config['dropup']){
			$html .= '<div class="btn-group dropup">';
		}else{
			$html .= '<div class="btn-group">';	
		}
		$js = 'return false;';
		if($config['form']['submit']){
			$fieldId = $this->_getJSIDName($config['form']['field']);
			$js = '$("#'.$fieldId.'").val("'.key($options).'");$(this).parents("form").submit();';
		}
		
		if(!$config['split']){
			$html .='<button type="button" onclick=\''.$js.'\' class="btn '.$config['size'].' '.$config['color'].' dropdown-toggle" data-toggle="dropdown">'.$title.'<span class="caret"></span></button>';	
		} else{
			$html .='<button type="button" onclick=\''.$js.'\' class="btn '.$config['size'].' '.$config['color'].'">'.$title.'</button>';
			$html .='<button type="button" class="btn '.$config['size'].' '.$config['color'].' dropdown-toggle" data-toggle="dropdown">';
			$html .='<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
		}
		
		$html .='<ul class="dropdown-menu" role="menu">';
		foreach ($options as $key => $value) {
			if($config['form']['submit']){
				$link = '#';
				$fieldId = $this->_getJSIDName($config['form']['field']);
				$js = '$("#'.$fieldId.'").val("'.$key.'");$(this).parents("form").submit();';
				$html .='<li>'.$this->link($value['title'],$link,array('onclick'=>$js)).'</li>';	
			} else{
				$link = isset($value['link'])?$value['link']:"#";
				$html .='<li>'.$this->link($value['title'],$link).'</li>';	
			}
			
		}
		$html .='</ul>';
		$html .='</div>';
		if($config['form']['submit']){
			$html .='</form>';
		}
			
		return $html;
	}
	private function _getJSIDName($fieldName){
		$position = null;
		if(strpos($fieldName,'.')===false){
			$model = $this->defaultModel;
			$name = $fieldName;
		} else{
			$fieldElements = explode('.', $fieldName);
			if(count($fieldElements)>2){
				$position = $fieldElements[1];
			}
			$name = array_pop($fieldElements);
			$model = array_shift($fieldElements);
		}
		return Inflector::camelize($model).$position.Inflector::camelize($name);
	}
	public function grid($columns){
		$html = "<div class = 'row'>\n";
		foreach($columns as $key => $colNumber){
			$html .= "<div class = 'col-md-".key($colNumber)."'>\n";
			$html .= current($colNumber);
			$html .= "</div>\n";
		}
		$html .= "</div>\n";
		return $html;
	}
}
?>