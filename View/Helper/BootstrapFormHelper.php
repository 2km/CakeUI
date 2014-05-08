<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 *
 * Licensed under The MIT License
 *
 * Copyright (c) La Pâtisserie, Inc. (http://patisserie.keensoftware.com/)
 * @license     MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('FormHelper', 'View/Helper');

class BootstrapFormHelper extends FormHelper {
	
	public $helpers = array('Html', 'Js'=>array('Jquery'));
	private $once = array();
	private $counter = 0;

	protected $_inputDefaults = array(
		'div' => array('class'=>'form-group'),
		'label' => array('class' => 'control-label'),
		'class' => 'form-control',
		'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-block')),
	);

	public function __construct($View, $options = array()) {
		if(isset($options['counter'])){
			$this->counter = $options['counter'];
		}
		parent::__construct($View, $options);
	}

	public function create($model = null, $options = array()) {
		if(!isset($options['novalidate'])){
			$options['novalidate']=true;
		}
		$options['role']='form';
		$options += array('class' => 'custom');
		return parent::create($model, $options);
	}
	protected function _parseOptions($options) {
		if(!empty($options['label'])) {
			//manage case 'label' => 'your label' as well as 'label' => array('text' => 'your label') before array_merge()
			if(!is_array($options['label'])) {
				$options['label'] = array('text' => $options['label']);
			}
			$options['label'] = array_merge_recursive($options['label'], $this->_inputDefaults['label']);
		}
		$options = array_merge(
			array('before' => null),
			$this->_inputDefaults,
			$options
		);
		return parent::_parseOptions($options);
	}
	public function inputDefaults($defaults = null, $merge = false) {
		if (!empty($defaults)) {
			if ($merge) {
				$this->_inputDefaults = array_merge($this->_inputDefaults, (array)$defaults);
			} else {
				$this->_inputDefaults = (array)$defaults;
			}
		}
		return $this->_inputDefaults;
	}
	public function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);
		$field_array = $this->entity();
		$currentModel = $this->_getModel($field_array[0]);
		$field = array_pop($field_array);
		//put placeholders
		if(isset($currentModel->placeholder) && !isset($options['placeholder'])){
			if(isset($currentModel->placeholder[$field])){
				$options['placeholder']=$currentModel->placeholder[$field];
			}
		}
		//put tooltips
		if(isset($currentModel->tooltips)){
			if(isset($currentModel->tooltips[$field])){
				if(isset($options['label']) && $options['label']===false){
					$options['label']=false;
				}
				else if(!empty($options['label'])){
					$options['label']= $options['label'].'<span class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.$currentModel->tooltips[$field].'">?</button>';
				} else{
					$label = __(Inflector::humanize(Inflector::underscore($fieldName)));
					$options['label']= $label.'<button type="button" class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.$currentModel->tooltips[$field].'">?</button>';
				}
			}
		}
		//put mask
		if(isset($this->_models[$this->defaultModel]->mask) && !isset($mask['data-inputmask'])){
			if(isset($this->_models[$this->defaultModel]->mask[$field])){
				if(is_array($this->_models[$this->defaultModel]->mask[$field])){
					if(!isset($options['class'])){$options['class']=null;}
					$options['class'].='form-control '.$this->_models[$this->defaultModel]->mask[$field]['class'];
				}
				else{
					$options['data-inputmask']=$this->_models[$this->defaultModel]->mask[$field];
				}
			}
		}
		if($this->isFieldError($fieldName)){
			if(!isset($options['div']['class'])){$options['div']['class']=null;}
			if(!isset($options['after'])){$options['after']=null;}
			$options['div']['class'].=' has-error has-feedback';
			$options['after'].='<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
		}
		
		return parent::input($fieldName, $options);
	}
	
	public function inlineForm($model,$fieldName,$buttonName,$options){
		if(!isset($options['Button'])){$options['Button']=null;}
		if(!isset($options['Field'])){$options['Field']=null;}
		if(!isset($options['Form'])){$options['Form']=null;}

		$defaultConfig['Field']['label']=false;		
		$defaultConfig['Button']['place']='after';
		$defaultConfig['Button']['class']='btn btn-default';
		$defaultConfig['Form']=null;

		$buttonOptions = am($defaultConfig['Button'],$options['Button']);
		$fieldOptions = am($defaultConfig['Field'],$options['Field']);
		$formOptions = am($defaultConfig['Form'],$options['Form']);

		if(isset($fieldOptions['div']['class'])){
			$fieldOptions['div']['class'].=" input-group";
		}else{
			$fieldOptions['div']['class']="input-group";
		}
		$button = '<span class="input-group-btn">'.$this->button($buttonName,$buttonOptions).'</span>';
		if($buttonOptions['place']=='after'){
			$fieldOptions['after']=$button;	
		} else{
			$fieldOptions['before']=$button;	
		}

		$html = $this->create($model,$formOptions);
		$html .=$this->input($fieldName,$fieldOptions);
		$html.='</form>';
		return $html;
	}

	public function select($fieldName, $options = array(), $attributes = array()){
		$select_source = parent::select($fieldName, $options, $attributes);
		
		$js = '$("#'.$this->domId($fieldName).'").select2();';

		echo $this->Html->script('/CakeUI/js/select2-3.4.8/select2.min',array('inline'=>false));
		if(empty($this->once['/CakeUI/css/select2-3.4.8/select2.css']) && empty($this->once['/CakeUI/css/select2-3.4.8/select2-bootstrap.css'])){
			$this->once['/CakeUI/css/select2-3.4.8/select2.css']=true;
			$this->once['/CakeUI/css/select2-3.4.8/select2-bootstrap.css']=true;
			echo $this->Html->css(array('/CakeUI/css/select2-3.4.8/select2.css','/CakeUI/css/select2-3.4.8/select2-bootstrap.css'),null,array('inline'=>false));	
		}
		
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		return $select_source;
	}
}
?>