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
	private $ajaxUploadCounter = 0;

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
		$options += array(
			'class' => 'custom',
			'role'=>'form',
			'novalidate'=>true
		);
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
		$temp_options = $this->_parseOptions($options);
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
					$label = __(Inflector::humanize(Inflector::underscore($field)));
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
			if($temp_options['type']=='text'){
				$options['after'].='<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
			}
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
	public function ajaxUpload($fieldName, $uploadOptions = array(),$onlyHtml = false){
		$jsId = $this->domId($fieldName);
		$this->setEntity($fieldName);
		$field_array = $this->entity();
		$model = $field_array[0];
		$field = array_pop($field_array);
		$key = null;
		
		if(isset($field_array[1]) && is_numeric($field_array[1])){
			$key = $field_array[1];
		}

		$uploadOptions += array(
			'fileType'=>'"jpeg","jpg","gif","png"',
			'size'=>5242880,
			'width'=>'150px',
			'multiple'=>'false',
			'path'=>'uploads',
			'resizedPath'=>'small',
			'label'=>Inflector::humanize($field),
			'url'=>$this->request->here,
			'class'=>'btn btn-default',
			'displayFile'=>true
		);
//-----------------------------------------------------js script for success callback---------------------------------------------------
		if(!isset($uploadOptions['success'])){
			if($uploadOptions['multiple']=='true'){
				$uploadOptions['success'] = 'if(responseJSON.success){
					$("<input>").attr("type","hidden").attr("name","data\['.$model.'\]\["+position+"\]\['.$field.'\]").attr("value",responseJSON.filename).appendTo("#'.$jsId.'-'.$this->ajaxUploadCounter.'");';
					if($uploadOptions['displayFile']==true){
						//$uploadOptions['success'] .= '$("<img>").attr("src","'.$this->webroot.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/"+responseJSON.filename).attr("id","target"+position).appendTo("#'.$jsId.'-'.$this->ajaxUploadCounter.'");';
						$uploadOptions['success'] .= '$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<p><img src=\"'.$this->request->webroot.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/"+responseJSON.filename+"\" border=0 /></p>");';
					}
					if(isset($uploadOptions['original_name'])){
						$originalNameField= '\['.$model.'\]\["+position+"\]\['.$uploadOptions['original_name'].'\]';
						$uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<input type=\"hidden\" name=\"data'.$originalNameField.'\" value=\""+responseJSON.original_filename+"\" />");';
					}
				$uploadOptions['success'] .='position++;}';
			}else{
				if(strlen($key)>0){
					$uploadOptions['success'] = 'if(responseJSON.success){
						$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").html("<input type=\"hidden\" name=\"data\['.$model.'\]\['.$key.'\]\['.$field.'\]\" value=\""+responseJSON.filename+"\" />");';
					if($uploadOptions['displayFile']==true){
						$uploadOptions['success'] .= '$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<p><img src=\"'.$this->request->webroot.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/"+responseJSON.filename+"\" border=0 /></p>");';
					}
						
					if(isset($uploadOptions['original_name'])){
						$originalNameField=str_replace($lastValue, $uploadOptions['original_name'], $campo);
						$uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<input type=\"hidden\" name=\"data'.$originalNameField.'\" value=\""+responseJSON.original_filename+"\" />");';
					}
					$uploadOptions['success'] .='}';
				} else{
					$uploadOptions['success'] = 'if(responseJSON.success){
						$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").empty().html("<input type=\"hidden\" name=\"data['.$model.']['.$field.']\" value=\""+responseJSON.filename+"\" />");';
					if($uploadOptions['displayFile']==true){
						$uploadOptions['success'] .= '$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<img src=\"'.$this->request->webroot.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/"+responseJSON.filename+"\" border=0 />");';
					}
					if(isset($uploadOptions['original_name'])){
						$uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<input type=\"hidden\" name=\"data['.$model.']['.$uploadOptions['original_name'].']\" value=\""+responseJSON.original_filename+"\" />");';
					}
					$uploadOptions['success'] .='}';
				}
			}
		}
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//----------------------------------------------------------- HTML BLOCK -----------------------------------------------------------------------------------------
		$html = '';
		$htmlImageArea = '<div id="'.$jsId.'-'.$this->ajaxUploadCounter.'">';
		if($uploadOptions['multiple'] == 'true'){
			if (isset($this->request->data[$model])) {
				foreach($this->request->data[$model] as $reqFieldKey => $reqField){
					if(is_array($reqField)){
						$htmlImageArea .= $this->input($model.'.'.$reqFieldKey.'.'.$field,array('type'=>'hidden')).
							$this->input($model.'.'.$reqFieldKey.'.id',array('type'=>'hidden'));
						if(!empty($reqField[$field]) && $uploadOptions['displayFile']==true){
							$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$reqField[$field]).'</p>';
						}
						if(isset($uploadOptions['original_name'])){
							$htmlImageArea .= $this->input($model.'.'.$reqFieldKey.'.'.$uploadOptions['original_name'],array('type'=>'hidden'));
							$htmlImageArea .= '<p>'.$this->value($model.'.'.$reqFieldKey.'.'.$uploadOptions['original_name']).'</p>';
						}	
					}
				$key = $reqFieldKey+1;	
				}	
			}
		}else{
			if(strlen($key)>0){
				if(!empty($this->request->data[$model][$key][$field])) {
					$htmlImageArea .= $this->input($fieldName,array('type'=>'hidden')).
						$this->input($model.'.'.$key.'.id',array('type'=>'hidden'));
					if(!empty($this->request->data[$model][$key][$field]) && $uploadOptions['displayFile']==true){
						$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$this->request->data[$model][$key][$field]).'</p>';
					}
					if(isset($uploadOptions['original_name'])){
						$htmlImageArea .= $this->input($model.'.'.$key.'.'.$uploadOptions['original_name'],array('type'=>'hidden'));
						$htmlImageArea .= '<p>'.$this->value($model.'.'.$key.'.'.$uploadOptions['original_name']).'</p>';
					}
				}
			} else {
				$htmlImageArea .= $this->input($model.'.'.$field,array('type'=>'hidden'));
				if(!empty($this->request->data[$model][$field]) && $uploadOptions['displayFile']==true){
					$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$this->request->data[$model][$field]).'</p>';
				}
				if(isset($uploadOptions['original_name'])){
					$htmlImageArea .= $this->input($model.'.'.$uploadOptions['original_name'],array('type'=>'hidden'));
					$htmlImageArea .= '<p>'.$this->value($model.'.'.$uploadOptions['original_name']).'</p>';
				}
			}	
		}
		
		$htmlImageArea .= '</div>';
		$html='
<div id="'.$jsId.'"></div>
<script type="text/template" id="template-'.$jsId.'">
  <div class="qq-uploader-selector qq-uploader span12">
    <div class="qq-upload-drop-area-selector qq-upload-drop-area span12" qq-hide-dropzone>
      <span>Drop files here to upload</span>
    </div>
    <div class="qq-upload-button-selector '.$uploadOptions['class'].'" style="width: '.$uploadOptions['width'].';">
      <div>'.$uploadOptions['label'].'</div>
    </div>
    <div id="restricted-fine-uploader"></div>
    '.$htmlImageArea.'
    <span class="qq-drop-processing-selector qq-drop-processing">
      <span>Processing dropped files...</span>
      <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
    </span>
    <ul class="qq-upload-list-selector qq-upload-list" style="margin-top: 10px; text-align: center;">
      <li>
        <div class="qq-progress-bar-container-selector">
          <div class="qq-progress-bar-selector qq-progress-bar"></div>
        </div>
        <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
        <span class="qq-upload-file-selector qq-upload-file"></span>
        <span class="qq-upload-size-selector qq-upload-size"></span>
        <a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Cancel</a>
        <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
      </li>
    </ul>
  </div>
</script>';
		if ($this->isFieldError($fieldName)) {
    		$html .= '<div class="has-error has-feedback"><span class="help-block">'.$this->error($fieldName,array('div'=>false)).'</span></div>';
		}
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------Component JS---------------------------------------------------------------------------------------
		$js = '
var position='.(strlen($key)==0?0:$key).';
$("#'.$jsId.'").fineUploader({
	template: "template-'.$jsId.'",
	multiple: '.$uploadOptions['multiple'].',
	request: {
		endpoint: "'.$uploadOptions['url'].'",
		params: {
        	qqFieldName: "'.$field.'"
    	}
	},
	classes: {
		success: "alert alert-success",
		fail: "alert alert-error"
	},
	validation: {
	  allowedExtensions: ['.$uploadOptions['fileType'].'], 
	  sizeLimit: '.$uploadOptions['size'].', 
	},
	showMessage: function(message) {
		$("#restricted-fine-uploader").append("<div class=\"alert alert-danger\">" + message + "</div>");
	},
	callbacks: {
		onComplete: function(id, fileName, responseJSON){
			'.$uploadOptions['success'].'
		}
	}
});';
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		
		$css = '/CakeUI/css/fineuploader/fineuploader.css';
		echo $this->Html->script('/CakeUI/js/fineuploader/all.fineuploader',array('inline'=>false));
		if(!isset($this->once[$css])){
			$this->once[$css]=true;
			echo $this->Html->css($css,null,array('inline'=>false));	
		}
		
		echo $this->Html->scriptBlock($js,array('inline'=>false));		
		$this->ajaxUploadCounter++;

		return $html;
	}
}
?>