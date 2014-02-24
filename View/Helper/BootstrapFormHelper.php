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
	private $included = false;
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
		//put placeholders
		if(isset($this->_models[$this->defaultModel]->placeholder) && !isset($options['placeholder'])){
			$text = $this->_getOnlyField($fieldName);
			if(isset($this->_models[$this->defaultModel]->placeholder[$text])){
				$options['placeholder']=$this->_models[$this->defaultModel]->placeholder[$text];
			}
		}
		//put tooltips
		if(isset($this->_models[$this->defaultModel]->tooltips)){
			$text = $this->_getOnlyField($fieldName);
			if(isset($this->_models[$this->defaultModel]->tooltips[$text])){
				if(isset($options['label']) && $options['label']===false){
					$options['label']=false;
				}
				else if(!empty($options['label'])){
					//$options['label']= $options['label'].' <span data-tooltip class="has-tip tip-top" data-width="250" title="'.$this->_models[$this->defaultModel]->tooltips[$text].'">'.$this->Html->image('/DkmUI/img/icon_interrogation.png').'</span>';
					//$options['label']= $options['label'].' <button type="button" class="btn btn-xs btn-default cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.$this->_models[$this->defaultModel]->tooltips[$text].'">?</button>';
					$options['label']= $options['label'].'<span class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.$this->_models[$this->defaultModel]->tooltips[$text].'">?</button>';
				} else{
					$label = __(Inflector::humanize(Inflector::underscore($fieldName)));
					//$options['label']=$label.' <span data-tooltip class="has-tip tip-top noradius" data-width="210" title="'.$this->_models[$this->defaultModel]->tooltips[$text].'">'.$this->Html->image('/DkmUI/img/icon_interrogation.png').'</span>';
					$options['label']= $label.'<button type="button" class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.$this->_models[$this->defaultModel]->tooltips[$text].'">?</button>';
				}
			}
		}
		if($this->isFieldError($fieldName)){
			$options['div']['class']='has-error has-feedback';
			$options['after']='<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
		}
		
		return parent::input($fieldName, $options);
	}
	private function _getOnlyField($fieldName){
		if (strpos($fieldName, '.') !== false) {
			$fieldElements = explode('.', $fieldName);
			$text = array_pop($fieldElements);
		} else {
			$text = $fieldName;
		}
		return $text;
	}
	public function inlineForm($model,$fieldName,$buttonName,$options){
		if(!isset($options['Button'])){$options['Button']=null;}
		if(!isset($options['Field'])){$options['Field']=null;}
		if(!isset($options['Form'])){$options['Form']=null;}

		$defaultConfig['Field']['label']=false;
		$defaultConfig['Field']['div']=false;		
		$defaultConfig['Button']['place']='after';
		$defaultConfig['Button']['class']='btn btn-default';
		$defaultConfig['Form']=null;

		$buttonOptions = am($defaultConfig['Button'],$options['Button']);
		$fieldOptions = am($defaultConfig['Field'],$options['Field']);
		$formOptions = am($defaultConfig['Form'],$options['Form']);

		$button = '<span class="input-group-btn">'.$this->button($buttonName,$buttonOptions).'</span>';
		if($buttonOptions['place']=='after'){
			$fieldOptions['after']=$button;	
		} else{
			$fieldOptions['before']=$button;	
		}

		$html = $this->create($model,$formOptions);
		$html .='<div class="input-group">';
		$html .=$this->input($fieldName,$fieldOptions);     
		$html.='</div>';
		$html.='</form>';
		return $html;
	}
}
?>