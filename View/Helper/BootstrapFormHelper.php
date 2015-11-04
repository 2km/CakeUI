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
				$options['placeholder']=__($currentModel->placeholder[$field]);
			}
		}
		//put tooltips
		if(isset($currentModel->tooltips)){
			if(isset($currentModel->tooltips[$field])){
				if(isset($options['label']) && $options['label']===false){
					$options['label']=false;
				}
				else if(!empty($options['label'])){
					$options['label']= $options['label'].'<span class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.__($currentModel->tooltips[$field]).'">?</button>';
				} else{
					$label = __(Inflector::humanize(Inflector::underscore($field)));
					$options['label']= $label.'<button type="button" class="btn btn-xs btn-link cakeui-tooltip" data-toggle="tooltip" data-placement="right" title="'.__($currentModel->tooltips[$field]).'">?</button>';
				}
			}
		}
		//put mask
		if(isset($currentModel->mask) && !isset($mask['data-inputmask'])){
			if(isset($currentModel->mask[$field])){
				if(is_array($currentModel->mask[$field])){
					if(!isset($options['class'])){$options['class']=null;}
					$options['class'].='form-control '.$currentModel->mask[$field]['class'];
				}
				else{
					$options['data-inputmask']=$currentModel->mask[$field];
				}
			}
		}
		if($this->isFieldError($fieldName)){
			if(isset($options['div'])){
				if (is_array($options['div'])) {
					if(!isset($options['div']['class'])){
						$options['div']['class'] = ' has-error has-feedback';
					} else {
						$options['div']['class'] .= ' has-error has-feedback';
					}
				} else {
					$options['div'] .=' has-error has-feedback';
				}
			} else{
				$options['div']['class'] = ' has-error has-feedback';
			}


			if(!isset($options['after'])){$options['after']=null;}
			if($temp_options['type']=='text' || $temp_options['type']=='password' || $temp_options['type']=='email'){
				if($temp_options['label']==false){
					$options['after'].='<span class="glyphicon glyphicon-remove form-control-feedback no-label"></span>';
				}else{
					$options['after'].='<span class="glyphicon glyphicon-remove form-control-feedback"></span>';
				}
			}
		}
		if(isset($options['multiple']) && $options['multiple']==='checkbox'){
			$options['class']=null;
			$options['between']='<div class="checkbox-container">';
			$options['after']='</div>';
		}
		$options = $this->_parseOptions($options);
		if($options['type']=='checkbox'){
			if(empty($options['div']['class'])){
				$options['div']['class']='checkbox';
			} else {
				$options['div']['class'].=' checkbox';
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
		if(!isset($attributes['multiple']) || (isset($attributes['multiple']) && $attributes['multiple']!=='checkbox')){
			$js = '$("#'.$this->domId($fieldName).'").select2();';

			echo $this->Html->script('/CakeUI/js/select2-3.4.8/select2.min',array('inline'=>false));
			if(empty($this->once['/CakeUI/css/select2-3.4.8/select2.css']) && empty($this->once['/CakeUI/css/select2-3.4.8/select2-bootstrap.css'])){
				$this->once['/CakeUI/css/select2-3.4.8/select2.css']=true;
				$this->once['/CakeUI/css/select2-3.4.8/select2-bootstrap.css']=true;
				echo $this->Html->css(array('/CakeUI/css/select2-3.4.8/select2.css','/CakeUI/css/select2-3.4.8/select2-bootstrap.css'),null,array('inline'=>false));
			}

			echo $this->Html->scriptBlock($js,array('inline'=>false));
		}
		return $select_source;
	}
	public function checkbox($fieldName,$options = array()){
		if($options['class']=='form-control'){
			$options['class']=false;
		}
		return parent::checkbox($fieldName,$options);
	}
	public function ajaxUpload($fieldName, $uploadOptions = array()){
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
			'displayFile'=>true,
			'editPath'=>false
		);
//-----------------------------------------------------js script for success callback---------------------------------------------------
		if(!isset($uploadOptions['success'])){
			if($uploadOptions['multiple']=='true'){
				$uploadOptions['success'] = 'if(responseJSON.success){
					$("<input>").attr("type","hidden").attr("name","data\['.$model.'\]\["+position+"\]\['.$field.'\]").attr("value",responseJSON.filename).appendTo("#'.$jsId.'-'.$this->ajaxUploadCounter.'");';
					if($uploadOptions['displayFile']==true){
						$uploadOptions['success'] .= '$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<p><img src=\"'.$this->request->webroot.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/"+responseJSON.filename+"\" border=0 /></p>");';
					}
					if(isset($uploadOptions['original_name'])){
						$originalNameField= '\['.$model.'\]\["+position+"\]\['.$uploadOptions['original_name'].'\]';
						$uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<input type=\"hidden\" name=\"data'.$originalNameField.'\" value=\""+responseJSON.original_filename+"\" />");';
					}

					$uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<div id=\"img-"+id+"\" class=\"alert alert-success\"></div>");';
					$uploadOptions['success'] .='$("#img-"+id).html($(".qq-file-id-"+id).html());$(".qq-file-id-"+id).hide();';
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
					// $uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<div id=\"img-0\"></div>");';
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
					// $uploadOptions['success'] .='$("#'.$jsId.'-'.$this->ajaxUploadCounter.'").append("<div id=\"img-0\"></div>");';
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
							if($uploadOptions['editPath']!==false){
								$htmlImageArea .= '<p>'.$this->Html->image($uploadOptions['editPath'].'/'.$reqField[$field]).'</p>';
							} else {
								$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$reqField[$field]).'</p>';
							}
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
						if($uploadOptions['editPath']!==false){
							$htmlImageArea .= '<p>'.$this->Html->image($uploadOptions['editPath'].'/'.$this->request->data[$model][$key][$field]).'</p>';
						} else{
							$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$this->request->data[$model][$key][$field]).'</p>';
						}
						$divId = '#'.$jsId.'-'.$this->ajaxUploadCounter;
						$fieldId = '#'.$this->domId($model.'.'.$key.'.id');
						$htmlImageArea .= $this->Html->link('Apagar','#',['onclick'=>'$("'.$divId.' p").html("");$("'.$fieldId.'").val("");$(this).remove();return false;']);
					}
					if(isset($uploadOptions['original_name'])){
						$htmlImageArea .= $this->input($model.'.'.$key.'.'.$uploadOptions['original_name'],array('type'=>'hidden'));
						$htmlImageArea .= '<p>'.$this->value($model.'.'.$key.'.'.$uploadOptions['original_name']).'</p>';
					}

				}
			} else {
				$htmlImageArea .= $this->input($model.'.'.$field,array('type'=>'hidden'));
				if(!empty($this->request->data[$model][$field]) && $uploadOptions['displayFile']==true){
					if($uploadOptions['editPath']!==false){
						$htmlImageArea .= '<p>'.$this->Html->image($uploadOptions['editPath'].'/'.$this->request->data[$model][$field]).'</p>';
					} else {
						$htmlImageArea .= '<p>'.$this->Html->image('../'.$uploadOptions['path'].'/'.$uploadOptions['resizedPath'].'/'.$this->request->data[$model][$field]).'</p>';
					}
					$divId = '#'.$jsId.'-'.$this->ajaxUploadCounter;
					$fieldId = '#'.$this->domId($model.'.'.$field);
					$htmlImageArea .= $this->Html->link('Apagar','#',['onclick'=>'$("'.$divId.' p").html("");$("'.$fieldId.'").val("");$(this).remove();return false;']);
				}
				if(isset($uploadOptions['original_name'])){
					$htmlImageArea .= $this->input($model.'.'.$uploadOptions['original_name'],array('type'=>'hidden'));
					$htmlImageArea .= '<p>'.$this->value($model.'.'.$uploadOptions['original_name']).'</p>';
				}

			}
		}

		$htmlImageArea .= '</div>';
		$html='
<div id="div-'.$jsId.'"></div>
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
$("#div-'.$jsId.'").fineUploader({
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
	public function textareaCounter($fieldName, $options = array()){
		$jsId = $this->domId($fieldName);
		$options += array(
			'maxChars'=>200,
			'maxCharsWarning'=>180,
			'msgFontSize'=> '12px',
			'msgFontColor'=> '#000000',
			'msgFontFamily'=> 'Arial',
			'msgTextAlign'=> 'right',
			'msgWarningColor'=> '#F00',
			'msgAppendMethod'=> 'insertAfter',
			'msgTextBefore'=> 'Characters: ',
			'msgTextAfter'=> '',
			'textConnector'=>'/'
		);
		$json_options = json_encode($options);
		// $json_options = '{';
		// if(isset($options['maxChars'])){
		// 	$json_options .="'maxChars':".$options['maxChars'].",";
		// 	unset($options['maxChars']);
		// }
		// if(isset($options['maxCharsWarning'])){
		// 	$json_options .="'maxCharsWarning':".$options['maxCharsWarning'].",";
		// 	unset($options['maxCharsWarning']);
		// }
		// $json_options .= '}';

		echo $this->Html->script('/CakeUI/js/jqCharCounter/jquery.jqEasyCharCounter.js',array('inline'=>false));
		$js = '$("#'.$jsId.'").jqEasyCounter('.$json_options.');';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		$options['type']='textarea';
		return $this->input($fieldName,$options);
	}
	public function starRating($fieldName, $options = array()){
		$formName = $this->_name($fieldName);
		$jsId = $this->domId($fieldName);
		$this->setEntity($fieldName);
		$field_array = $this->entity();
		$model = $field_array[0];
		$field = array_pop($field_array);

		$html = null;

		if(isset($options['label'])){
			$html=$this->label($fieldName,$options['label']);
		} else{
			$html=$this->label($fieldName,$label = __(Inflector::humanize(Inflector::underscore($field))));
		}

		$html .= '<div id="star-'.$jsId.'" class="starRating"></div>';
		if ($this->isFieldError($fieldName)) {
    		$html .= '<div class="has-error has-feedback"><span class="help-block">'.$this->error($fieldName,array('div'=>true)).'</span></div>';
		}

		echo $this->Html->script('/CakeUI/js/raty/jquery.raty.min.js',array('inline'=>false));

		$readonly = $score = null;
		if($this->value($fieldName)){
			$score = 'score: '.$this->value($fieldName);
		}
		if(isset($options['disabled']) && $options['disabled']==true){
			$readonly = 'readOnly: true,';
		}
		$js = '$("#star-'.$jsId.'").raty({
			starHalf    : "'.$this->Html->url("/CakeUI/js/raty/img/star-half.png").'",
			starOff     : "'.$this->Html->url("/CakeUI/js/raty/img/star-off.png").'",
			starOn      : "'.$this->Html->url("/CakeUI/js/raty/img/star-on.png").'",
			scoreName      : "'.$formName.'",
			targetType	: "number",
			'.$readonly.$score.'
		});';
		$test = '$("#'.$jsId.'").raty';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		return $html;
	}
	public function wysiwyg($fieldName, $options = array(), $js_options = array()){
		$jsId = $this->domId($fieldName);
		$scripts = array('//tinymce.cachefly.net/4.0/tinymce.min.js');
		echo $this->Html->script($scripts,array('inline'=>false));
		if (!array_key_exists('entity_encoding',$js_options)) {
			$js_options['entity_encoding']='raw';
		}
		if (!array_key_exists('toolbar',$js_options)) {
			$js_options['toolbar']="bold italic alignleft aligncenter alignright alignjustify bullist numlist outdent indent link code";
		}
		if (!array_key_exists('plugins',$js_options)) {
			$js_options['plugins']=array(
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
			);
		}
		if (!array_key_exists('menubar',$js_options)) {
			$js_options['menubar']=false;
		}
		if(isset($options['disabled']) && $options['disabled']==true){
			$js_options['readonly']=1;
		}
		$js_options = json_encode($js_options);
		$js_options = substr($js_options, 1,strlen($js_options)-2);
		$js = 'tinymce.init({
    			selector: "#'.$jsId.'",
    			'.$js_options.'
		});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		if(!isset($options['rows'])){
			$options['rows']=5;
		}
		if(!isset($options['cols'])){
			$options['cols']=48;
		}
		return $this->input($fieldName,$options)."<br />";
	}
	public function datePicker($fieldName, $options = array(), $jsOptions=array()){
		$jsId = $this->domId($fieldName);
		$jsOptions+=array(
			'format'=> 'dd/mm/yyyy',
			'autoclose'=> 'true',
			'todayHighlight'=>'true',
			'startView'=> 0,
			'orientation'=>'left',
			'locale'=>'pt-BR'
		);
		$scripts[] = '/CakeUI/js/bootstrap-datepicker/bootstrap-datepicker';
		if($jsOptions['locale']!==false){
			$scripts[] ='/CakeUI/js/bootstrap-datepicker/locales/bootstrap-datepicker.'.$jsOptions['locale'];
		}

		echo $this->Html->script($scripts,array('inline'=>false));
		$css = '/CakeUI/css/bootstrap-datepicker/datepicker3';
		if(!isset($this->once[$css])){
			$this->once[$css]=true;
			echo $this->Html->css($css,null,array('inline'=>false));
		}

		$js = '$("#'.$jsId.'").datepicker({
					format: "'.$jsOptions['format'].'",
					autoclose: "'.$jsOptions['autoclose'].'",
					todayHighlight: "'.$jsOptions['todayHighlight'].'",
					startView: "'.$jsOptions['startView'].'",
					orientation: "'.$jsOptions['orientation'].'",';
		if($jsOptions['locale']!==false){
					$js .= 'language:"'.$jsOptions['locale'].'"';
		}
				$js .= '});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		if (!isset($options['type'])) {
			$options['type']='text';
		}
		return $this->input($fieldName,$options);
	}
	public function datePickerRange($fieldName1, $fieldName2, $options1 = array(),$options2 = array(), $jsOptions = array()){
		$this->setEntity($fieldName1);
		$field_array = $this->entity();
		$model = $field_array[0];
		$field = array_pop($field_array);
		if(!is_array($options1)){
			$options1 = array();
		}
		if(!is_array($options2)){
			$options2=array();
		}
		if(!is_array($jsOptions)){
			$jsOptions = array();
		}
		$jsOptions+=array(
			'format'=> 'dd/mm/yyyy',
			'autoclose'=> 'true',
			'todayHighlight'=>'true',
			'startView'=> 0,
			'orientation'=>'left',
			'locale'=>'pt-BR',
			'rangeLabel'=>'até'
		);
		if(isset($options1['label'])){
			$label = $this->label($fieldName1,$options1['label']);
		} else{
			$label = $this->label($fieldName1,__(Inflector::humanize(Inflector::underscore($field))));
		}
		$options1+=array(
			'div'=>array('class'=>'input-group form-group input-daterange','id'=>'datepicker-'.$this->counter),
		);
		$options1['label']=false;
		$options2+=array(
			'div'=>false,
			'label'=>false
		);
		$scripts[] = '/CakeUI/js/bootstrap-datepicker/bootstrap-datepicker';
		if($jsOptions['locale']!==false){
			$scripts[] ='/CakeUI/js/bootstrap-datepicker/locales/bootstrap-datepicker.'.$jsOptions['locale'];
		}
		echo $this->Html->script($scripts,array('inline'=>false));
		$css = '/CakeUI/css/bootstrap-datepicker/datepicker3';
		if(!isset($this->once[$css])){
			$this->once[$css]=true;
			echo $this->Html->css($css,null,array('inline'=>false));
		}

		$js = '$("#datepicker-'.$this->counter.'").datepicker({
					format: "'.$jsOptions['format'].'",
					autoclose: "'.$jsOptions['autoclose'].'",
					todayHighlight: "'.$jsOptions['todayHighlight'].'",
					startView: "'.$jsOptions['startView'].'",
					orientation: "'.$jsOptions['orientation'].'",';
		if($jsOptions['locale']!==false){
					$js .= 'language:"'.$jsOptions['locale'].'"';
		}
		$js .= '});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		if (!isset($options['type'])) {
			$options['type']='text';
		}
		$options1['after']='<span class="input-group-addon">'.$jsOptions['rangeLabel'].'</span>';
		$options1['after'].=$this->input($fieldName2,$options2);
		$this->counter++;
		return $label.$this->input($fieldName1,$options1);
	}
	public function inputBrZipcode($fieldName, $options = array(),$ajaxOptions = array()){
		$this->setEntity($fieldName);
		$field_array = $this->entity();
		$model = $field_array[0];
		$field = array_pop($field_array);
		$jsId = $this->domId($fieldName);

		$ajaxOptions+=array(
			'streetField'=>$this->domId('rua'),
			'numberField'=>$this->domId('numero'),
			'districtField'=>$this->domId('bairro'),
			'cityField'=>$this->domId('cidade'),
			'stateField'=>$this->domId('estado'),
			'callback'=>'updateField(data);'
		);
		$js = null;
		if(isset($this->request->data[$model][$field])){
			$js = '$("#zipcodeContainer").hide().removeClass("hidden").slideDown("fast");';
		}
		echo $this->Html->scriptBlock('
			var updateField = function(zipcodeJson){
				if(zipcodeJson != null){
					$("#'.$ajaxOptions['streetField'].'").val(zipcodeJson.logradouro);
					$("#'.$ajaxOptions['districtField'].'").val(zipcodeJson.bairro);
					$("#'.$ajaxOptions['cityField'].'").val(zipcodeJson.localidade);
					$("#'.$ajaxOptions['stateField'].'").val(zipcodeJson.uf);
					$("#zipcodeContainer").hide().removeClass("hidden").slideDown("fast");
					$("#'.$ajaxOptions['numberField'].'").focus();
				} else {
					$("#'.$ajaxOptions['streetField'].'").val("");
					$("#'.$ajaxOptions['districtField'].'").val("");
					$("#'.$ajaxOptions['cityField'].'").val("");
					$("#'.$ajaxOptions['stateField'].'").val("");
					$("#zipcodeContainer").hide().removeClass("hidden").slideDown("fast");
					$("#'.$ajaxOptions['streetField'].'").focus();
				}
			}
			$(function(){
				var status=0;
				$("#'.$jsId.'").keyup(function() {
					var cep = $("#'.$jsId.'").val().replace(/\D/g,"");
					if(cep.length<8){
						status=0;
					}
					if(cep.length==8 && status==0){
						status=1;
			  			$.ajax({
							beforeSend:function (XMLHttpRequest) {$("#zipcodeIndicator").hide().removeClass("hidden").show()},
							complete:function (XMLHttpRequest, textStatus) {$("#zipcodeIndicator").hide();},
							dataType:"html",
							success:function (data, textStatus){
								try{
									data= $.parseJSON(data);
								}
								catch(err){
									data = null
								}
								'.$ajaxOptions['callback'].'},
							error: function(XMLHttpRequest, textStatus){
								$("#zipcodeIndicator").hide();
								data = null;
								'.$ajaxOptions['callback'].'
							},
							type:"get",
							url: "http://cep.correiocontrol.com.br/"+cep+".json"
						});
					}
				});
			'.$js.'
			});
		',array('inline'=>false));
		$options['after']=$this->Html->image('/CakeUI/img/indicator.gif',array('id'=>'zipcodeIndicator','class'=>'hidden'));
		return $this->input($fieldName,$options);
	}
	public function deleteLink($title, $url = null, $options = array(), $confirmMessage = false) {
		$title.=' <span class="glyphicon glyphicon-trash"></span>';
		$options['escape']=false;
		if(!isset($options['class'])){$options['class']=null;}
		$options['class'].=" btn btn-xs btn-danger";
		return $this->postLink($title,$url,$options,$confirmMessage);
	}
	public function childAddForm($label,$options = array()){
		App::uses('String', 'Utility');
		$options+=array(
			'model'=>null,
			'element'=>'CakeUI./Elements/modal_form',
			'return_element'=>'CakeUI./Elements/modal_success',
			'table_id'=>"CakeUI".$options["model"]."-".$this->counter,
			'table'=>null,
			'key'=>0
		);
		$html = null;
		$localStorageName = $options['model'].'-'.$this->counter;
		$html .='<div id="modal-'.$this->counter.'" class="modal fade modalWindow" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content '.$options['model'].'-modal-content" id="modal-content"></div>
            </div>
        </div>';
        $url = array('action'=>$this->action,'CakeUIOperation'=>1,'CakeUILocalStorageName'=>$localStorageName, String::toList($this->request->params['pass'],','));
		$html .= $this->Html->newLink($label,'#',array('id'=>'modalButton-'.$this->counter)
			//array('data-remote'=>$this->Html->url($url), 'data-toggle'=>'modal','data-target'=>'#modal-'.$this->counter)
		);
		$html .= "<div id='table-".$this->counter."' class='topAlign' style='overflow:auto'>";
		if (isset($this->request->data[$options['model']]) && count($this->request->data[$options['model']])) {
			$html .= $this->tableCreate($options,$localStorageName);
		}
		$html .= "</div>";
		$request = $this->Js->request($url,
            array(
                // 'update'=>'#LogisticaItemContratoId',
                'method'=>'post',
                'success'=>'$("#modal-'.$this->counter.' .modal-content").html(data);$("#modal-'.$this->counter.'").modal("show")',
                'dataExpression' => true,
                // 'data'=> $this->Js->serializeForm(array('isForm' => false, 'inline' => true))
                'data'=>'JSON.parse(localStorage.getItem("'.$localStorageName.'"))'
        ));
		echo $this->Html->scriptBlock('
			localStorage.setItem("'.$localStorageName.'", JSON.stringify('.json_encode(array('CakeUILocalStorage'=>$options)).'));
			$("#modalButton-'.$this->counter.'").click(function(e){
				'.$request.'
			});
		',array('inline'=>false));
		$this->counter++;
		return $html;
	}
	private function tableCreate($options,$local_storage_name){
		App::uses('String', 'Utility');
		$html = null;
		$html .= "<table class='table' id='"."CakeUI".$options["model"].'-'.$this->counter."'><thead><tr>";
		foreach($options['table'] as $key=>$table){
			$table['display'] = isset($table['display'])?$table['display']:true;
			if($table['display']){
				$html .= "<th>".$table['label']."</th>";
			}
		}
		$html .= "<th class='actions'>".__("Actions")."</th>";
		$html .= "</tr></thead><tbody>";
		foreach ($this->request->data[$options['model']] as $key => $fields) {
			$formFields = null;
			foreach ($fields as $name => $value) {
				if($name=='created'||$name=='modified'){
					continue;
				}
				if(is_array($value)){
					if(!in_array($name,$options['extra_model'])){
						foreach ($value as $keyTemp => $valueTemp) {
							$formFields .= $this->input($options["model"].".".$key.".".$name.".",array('value'=>$valueTemp['id'],'type'=>'hidden'));
						}
					} else{
						foreach ($value as $keyTemp => $valueTemp) {
							foreach($valueTemp as $extraModelKey=>$extraModelValue){
								if($extraModelKey=='created' || $extraModelKey=='modified'){
									continue;
								}
								$formFields .= $this->input($options["model"].".".$key.".".$name.".".$keyTemp.".".$extraModelKey,array('value'=>$extraModelValue,'type'=>'hidden'));
							}

						}
					}
				}else{
					$formFields .= $this->input($options["model"].".".$key.".".$name,array('value'=>$value,'type'=>'hidden'));
				}
			}
			$formFields .=$this->input('CakeUITemp.'.$key.'.key',array('type'=>'hidden','value'=>$key));
			$html .= "<tr id='row-".$key."'>";
			foreach($options['table'] as $k=>$table){
				$table['display'] = isset($table['display'])?$table['display']:true;
				if($table['display']){
					if(isset($this->request->data[$options['model']][$key])){
						if(isset($options['table'][$k]['form']['options'])){
							$html .= "<td>".$options['table'][$k]['form']['options'][$fields[$table['field']]]."</td>";
						} else{
							$html .= "<td>".$fields[$table['field']]."</td>";
						}
					}
				}
			}
			$editUrl = $this->Html->url(array('action'=>$this->action,'CakeUIOperation'=>1,'CakeUILocalStorageName'=>$local_storage_name,'CakeUIRowId'=>$key,String::toList($this->request->params['pass'],',')));
			if(empty($fields['id'])){
				$html .=
					"<td class='actions'>".
						$formFields.
						$this->Html->link(__("Delete"),"#",array('class'=>'btn btn-xs btn-danger', 'onclick'=>'cakeUIDeleteRow("'.'row-'.$key.'","'.$options['table_id'].'")'))." ".
						$this->Html->link(__("Editar"),"#",array('class'=>'btn btn-xs btn-warning','onclick'=>'cakeUIEditRow("'.'row-'.$key.'","'.$editUrl .'","'.$options['model'].'","'.$local_storage_name.'")')).
					"</td>";
			} else{
				$html .=
					"<td class='actions'>".
						$formFields.
						$this->Js->link("Delete",
							array('action'=>$this->action,'CakeUIOperation'=>3,'CakeUILocalStorageName'=>$local_storage_name,'CakeUIRecordId'=>$fields['id'],String::toList($this->request->params['pass'],',')),
							array('method'=>'post',
								'dataExpression'=>true,
								'data'=>'$("#row-'.$key.'").closest("form").serialize()+"&"+$.param(JSON.parse(localStorage.getItem("'.$local_storage_name.'")))',
								'success' => '$("#row-'.$key.'").remove();if($("#'.$options['table_id'].' tbody tr").size()==0){$("#'.$options['table_id'].'").remove();}','error'=>'alert("'.__("Problema ao tentar apagar o item").'")', 'class'=>'btn btn-xs btn-danger','confirm'=>__('Deseja apagar o item?')))." ".

						$this->Html->link(__("Editar"),"#",array(
							'class'=>'btn btn-xs btn-warning',
							'onclick'=>'cakeUIEditRow("'.'row-'.$key.'","'.$editUrl .'","'.$options['model'].'","'.$local_storage_name.'")')).
					"</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";
		echo $this->Js->writeBuffer(array('inline'=>false));
		return $html;
	}
	public function addItem($label,$model,$fields){
		$html = $this->Html->newLink($label,'#',array('id'=>'addItem-'.$this->counter));
		$div_id = $model.'-'.$this->counter;
		$html .='<div id="'.$div_id.'" class="table-responsive">';
		if (isset($this->request->data[$model]) && count($this->request->data[$model])) {
			$html .=$this->addItemTableCreate($model,$fields);
		}
		$html .='</div>';
		$this->addItemJs($model,$div_id,$fields);
		echo $this->Js->writeBuffer(array('inline'=>false));
		$this->counter++;
		return $html;
	}
	private function addItemTableCreate($model,$fields){
		App::uses('Inflector', 'Utility');
		$html = null;
		$table_id = "CakeUI".$model.'-'.$this->counter;
		$html .= "<table class='table' id='".$table_id."'><thead><tr>";
		foreach($fields as $fieldName=>$options){
			if(isset($options['form']['label'])){
				$html .= "<th>".$options['form']['label']."</th>";
			} else{
				$html.= "<th width='0'></th>";
			}
		}
		$html .= "<th class='actions'>".__("Actions")."</th>";
		$html .= "</tr></thead><tbody>";
		foreach ($this->request->data[$model] as $key => $req_values) {
			$html .= "<tr id='".$model."-row-".$key."'>";
			foreach($req_values as $fieldName=>$fieldValue){
				if(isset($fields[$fieldName])){
					$fields[$fieldName]['form']['label']=false;
					$html .= "<td>".$this->input($model.".".$key.".".$fieldName,$fields[$fieldName]['form'])."</td>";
				} else{
					$html .=$this->input($model.".".$key.".".$fieldName,array('type'=>'hidden'));
				}
			}
			if(empty($req_values['id'])){
				$html .=
					"<td class='actions'>".
						$this->Html->link(__("Delete"),"#",array('class'=>'btn btn-danger', 'onclick'=>'cakeUIDeleteRow("'.$model.'-row-'.$key.'","'.$table_id.'")'))." ".
					"</td>";
			} else{
				$html .=
					"<td class='actions'>".
						// $this->Js->link("Delete",array('action'=>$this->action,'CakeUIModel'=>$model,'CakeUIRecord'=>$req_values['id']),array('success' => 'cakeUIDeleteRow("'.'row-'.$key.'","'.$table_id.'")','error'=>'alert("'.__("Problema ao tentar apagar o item").'")', 'class'=>'btn btn-danger','confirm'=>__('Deseja apagar o item?')))." ".
					$this->Js->link("Delete",array('controller'=>Inflector::pluralize($model),'action'=>'delete',$req_values['id']),array('success' => '$("#'.$model.'-row-'.$key.'").remove();if($("#'.$table_id.' tbody tr").size()==0){$("#'.$table_id.'").remove();}','error'=>'alert("'.__("Problema ao tentar apagar o item").'")', 'class'=>'btn btn-danger','confirm'=>__('Deseja apagar o item?')))." ".
					"</td>";
			}
			$html .= "</tr>";
		}
		$html .= "</tbody></table>";
		return $html;
	}
	private function addItemJs($model,$div_id,$fields){
		App::uses('Inflector', 'Utility');
		$table_id = "CakeUI".$model.'-'.$this->counter;
		$table_html="<table class='table' id='".$table_id."'>";
		$table_html.="<thead>";
		$table_html.="<tr>";
		foreach($fields as $key=>$value){
			if(isset($value['form']['label'])){
				$table_html.= "<th>".$value['form']['label']."</th>";
			} else{
				$table_html.= "<th width='0'></th>";
			}
		}
		$table_html.="<th class='actions'>".__("Actions")."</th>";
		$table_html.="</tr>";
		$table_html.="</thead>";
		$table_html.='<tbody>';
		$table_html.='</tbody>';
		$table_html.='</table>';

		$fieldJs=$completeTd='';
		foreach ($fields as $field => $options) {
			$options['form']['label']=false;
			$completeTd.='<td>'.$this->input($model.'.CakeUITempPosition.'.$field,$options['form']).'</td>';
			if(isset($options['js_callback'])){
				$fieldName = $model.'CakeUITempPosition'.Inflector::camelize($field);
				$fieldJs .= str_replace('fieldToChange', $fieldName, $options['js_callback']);
			}
		}
		$completeTd .="<td class='actions'>".
						$this->Html->link(__("Delete"),"#",array('class'=>'btn btn-danger', 'onclick'=>'$(this).parents("tr:first").remove();if($("#'.$table_id.' tbody tr").size()==0){$("#'.$table_id.'").remove();}'))." ".
					"</td>";
		$completeTd = str_replace("\n","",addslashes($completeTd));


		$js = '$("#addItem-'.$this->counter.'").click(function(){
					var position = 0;
					if($("#'.$div_id.' table").size()==0){
						$("#'.$div_id.'").append("'.$table_html.'");
						tr = "<tr id=\"'.$model.'-row-0\"></tr>"
						$("#'.$table_id.' tbody").append(tr);
						completeTd = "'.$completeTd.'";
						completeTd = completeTd.replace(/CakeUITempPosition/g,position);
						$("#'.$table_id.' #'.$model.'-row-0").append(completeTd);
					} else{
						row = $("#'.$table_id.' tr:last").attr("id");
						vals = row.split("-");
						position = (parseInt(vals[2])+1)
						new_id = "'.$model.'-row-"+position;
						tr = "<tr id=\""+new_id+"\"></tr>"
						$("#'.$table_id.' tbody").append(tr);
						completeTd = "'.$completeTd.'";
						completeTd = completeTd.replace(/CakeUITempPosition/g,position);
						$("#'.$table_id.' #"+new_id).append(completeTd);
					}
					fieldToChange = "'.$fieldJs.'";
					fieldToChange = fieldToChange.replace(/CakeUITempPosition/g,position);
					eval(fieldToChange);
				});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
	}
}
?>