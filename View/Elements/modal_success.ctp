<?php
App::uses('String', 'Utility');
function returnTableHtml($requestData,$options){
	if($requestData['CakeUITemp']['key']==0 ){ //&& não ta sendo editado
		$html="<table class='table' id='".$options['table_id']."'>";
		$html.=returnHeaderHtml($options);
		$html.='<tbody>';
		$html.='</tbody>';
		$html.='</table>';
		return $html;
	}
}
function returnHeaderHtml($options){
	$tableHeader="<thead>";
	$tableHeader.="<tr>";
	foreach($options['table'] as $key=>$value){
		$value['display'] = isset($value['display'])?$value['display']:true;
		if($value['display']===true){
			$tableHeader.= "<th>".$value['label']."</th>";
		}
	}
	$tableHeader.="<th class='actions'>".__("Actions")."</th>";
	$tableHeader.="</tr>";
	$tableHeader.="</thead>";
	return $tableHeader;
}
$row_td='';
foreach($options['table'] as $key=>$field){
	$field['display'] = isset($field['display'])?$field['display']:true;
	if($field['display']===true){
		if(isset($field['form']['options']) && is_array($field['form']['options'])){
			$row_td .= "<td>".$field['form']['options'][$requestData[$options['model']][$field['field']]]."</td>";
		}else{
			$row_td .= "<td>".$requestData[$options['model']][$field['field']]."</td>";
		}
	}
}
$formFields = null;
foreach($requestData[$options['model']] as $field=>$value){
	if(is_array($value)){
		foreach ($value as $keyTemp => $valueTemp) {
			$formFields .= $this->Form->input($options["model"].".".$requestData['CakeUITemp']['key'].".".$field.".",array('value'=>$valueTemp,'type'=>'hidden'));
		}
	}else{
		$formFields .= $this->Form->input($options["model"].".".$requestData['CakeUITemp']['key'].".".$field,array('value'=>str_replace(["\n","\r","\n\r"], "", nl2br($value)),'type'=>'hidden'));
	}
}
if(isset($options['extra_model'])){
	foreach($options['extra_model'] as $k=>$extraModel){
		foreach($requestData[$extraModel] as $reqKey=>$reqValue){
			foreach($reqValue as $extraK=>$extraValue){
				$formFields .= $this->Form->input($options["model"].".".$requestData['CakeUITemp']['key'].".".$extraModel.'.'.$reqKey.'.'.$extraK,array('value'=>$extraValue,'type'=>'hidden'));
			}
		}
	}
}
$editUrl = $this->Html->url(array('action'=>$this->action,'CakeUIOperation'=>1,'CakeUILocalStorageName'=>$CakeUILocalStorageName,'CakeUIRowId'=>$requestData['CakeUITemp']['key'],String::toList($this->request->params['pass'],',')));
if(!empty($requestData[$options['model']]['id'])){ //Actions will be in database
	$row_td .=
		"<td class='actions'>".
			addslashes($formFields).
			addslashes(
				$this->Js->link("Delete",
					array('action'=>$this->action,'CakeUIOperation'=>3,'CakeUILocalStorageName'=>$CakeUILocalStorageName,'CakeUIRecordId'=>$requestData[$options['model']]['id'],String::toList($this->request->params['pass'],',')),
					array('method'=>'post',
						'dataExpression'=>true,
						'data'=>'$("#row-'.$requestData['CakeUITemp']['key'].'").closest("form").serialize()+"&"+$.param(JSON.parse(localStorage.getItem("'.$CakeUILocalStorageName.'")))',
						'success' => '$("#row-'.$requestData['CakeUITemp']['key'].'").remove();if($("#'.$options['table_id'].' tbody tr").size()==0){$("#'.$options['table_id'].'").remove();}','error'=>'alert("'.__("Problema ao tentar apagar o item").'")', 'class'=>'btn btn-xs btn-danger','confirm'=>__('Deseja apagar o item?'))))." ".
			addslashes($this->Html->link(__("Editar"),"#",array('class'=>'btn btn-xs btn-warning','onclick'=>'cakeUIEditRow("'.'row-'.$requestData['CakeUITemp']['key'].'","'.$editUrl .'","'.$options['model'].'","'.$CakeUILocalStorageName.'")'))).
		"</td>";
} else { //Actions will be only in window
	$row_td .=
		"<td class='actions'>".
			addslashes($formFields).
			addslashes($this->Html->link(__("Delete"),"#",array('class'=>'btn btn-xs btn-danger', 'onclick'=>'cakeUIDeleteRow("'.'row-'.$requestData['CakeUITemp']['key'].'","'.$options['table_id'].'")')))." ".
			addslashes($this->Html->link(__("Editar"),"#",array('class'=>'btn btn-xs btn-warning','onclick'=>'cakeUIEditRow("'.'row-'.$requestData['CakeUITemp']['key'].'","'.$editUrl .'","'.$options['model'].'","'.$CakeUILocalStorageName.'")'))).
		"</td>";
}

echo $this->Session->flash('modalMsg');
list($name,$number) = explode('-',$options['table_id']);
$divElement = "#table-".$number;
$table = returnTableHtml($requestData,$options);
// $row = returnHtmlRow($requestData,$options);
$completeTr = "<tr id='row-".$requestData['CakeUITemp']['key']."'>".$row_td."</tr>";
$js = '$(document).ready(function(){
if($("'.$divElement.' table").size()==0){
	$("'.$divElement.'").append("'.$table.'");
	$("#'.$options['table_id'].' tbody").append("'.$completeTr.'");
} else{
	if($("#row-'.$requestData['CakeUITemp']['key'].'").size()==0){
		$("#'.$options['table_id'].' tbody").append("'.$completeTr.'");
	} else {
		$("#row-'.$requestData['CakeUITemp']['key'].'").html("'.$row_td.'");
	}
}
$(".'.$options['model'].'-modal-content").parents(".modalWindow:first").modal("hide");
$("body").removeClass("modal-open");
$(".modal-backdrop").remove();
});

';
echo $this->Html->scriptBlock($js);
echo $this->Js->writeBuffer();
die();


// $tableElement = "#".$options['table_id'];

// $tableHeader = "<thead>";
// $tableHeader .= "<tr>";
// foreach($options['table'] as $key=>$value){
// 	$tableHeader .= "<th>".$value['label']."</th>";
// }
// $tableHeader .= "<td class='actions'>".__("Actions")."</td>";
// $tableHeader .= "</tr>";
// $tableHeader .= "</thead>";
//Não sei o que isso faz
// if(isset($modalData["dkmEditLink"]) && $modalData["dkmEditLink"]){
// 	$tableRow ="";
// 	$position = $modalData["dkmPosition"];
// } else{
// 	if($modalData["dkmPosition"] > 0){
// 		$position = $modalData["dkmPosition"];
// 	} else{
// 		$position = $modalData["dkmPosition"]+$modalData["dkmStart"];
// 	}
// 	$tableRow ="<tr id='row-".$position."'>";
// }
$formFields = null;
$formCounter = 0;
list($containerName,$idContainer) = explode("-",$modalData["dkmModalId"]);
$tabelaId = $options['table_id'];
foreach($table['field'] as $field=>$options){
	if(!isset($options['display'])){
		$options['display']=true;
	}
	if(isset($options['editable']) && $options['editable']===true){
		$customForm = 'input';
		$label = false;
		$opts = null;
		if(isset($options['customForm'])){
			$customForm = $options['customForm'];
			if($options['customForm'] == 'select2Basic'){
				$opts = $options['options'];
			}
		}
		if(isset($options['label'])){
			$label = $options['label'];
		}

		if($customForm == 'select2Basic'){
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'label'=>false,'options'=>$opts), true)."</td>"));
			$script .= '$("#'.Inflector::camelize($modalData["dkmModel"]).$position.Inflector::camelize($field).'").select2();';
		}
		else if($customForm == 'datePicker'){
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'label'=>false), true)."</td>"));
			$script .= '$("#'.Inflector::camelize($modalData["dkmModel"]).$position.Inflector::camelize($field).'").datepicker({dateFormat: "dd/mm/yy",});';
		}
		else if($customForm == 'dateTimePicker'){
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'label'=>false), $otherOptions, true)."</td>"));
			$script .= '$("#'.Inflector::camelize($modalData["dkmModel"]).$position.Inflector::camelize($field).'").dateTimePicker({dateFormat: "dd/mm/yy",});';
		}
		else if($customForm == 'wysiwyg'){
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'label'=>false), true)."</td>"));
			$script .= '$(".wysiwyg").wysiwyg({initialContent:""});';
		}
		else if($customForm == 'ajaxUpload'){
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'resizedPath'=>$options['resizedPath']), true)."</td>"));
			$script .= $this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'resizedPath'=>$options['resizedPath']), -1);
		}else{
			$tableRow .= str_replace("\n","",addslashes("<td>".$this->DkmForm->{$customForm}($modalData["dkmModel"].".".$position.".".$field,array('value'=>$dataToSave[$field],'label'=>false,'options'=>$opts), true)."</td>"));
		}
		unset($dataToSave[$field]);
	}else{
		if(isset($options['display']) && $options['display']==true){
			if(isset($options['options']) && is_array($options['options'])){
				$tableRow .= "<td>".$options['options'][$dataToSave[$field]]."</td>";
			}else{
				$tableRow .= "<td>".addslashes($dataToSave[$field])."</td>";
			}
		}
	}
}
$formFields = null;
foreach($dataToSave as $field=>$value){
	$formFields .= $this->DkmForm->input($modalData["dkmModel"].".".$position.".".$field,array('value'=>$value,'type'=>'hidden'));
}

if(!empty($dataToSave['id'])){
	$tableRow .= "<td class='actions'>".
		addslashes($formFields).
		addslashes($this->Js->link("Delete","/".$this->request->url."/?dkmModal=3&dkmModel=".$modalData['dkmModel']."&dkmId=".$dataToSave['id'],array('success' => '$("#row-'.$position.'").remove();if($("#'.$tabelaId.' tbody tr").size()==0){$("#'.$tabelaId.'").html("");}','error'=>'alert("'.__("Problema ao tentar apagar o item").'")', 'class'=>'alert delete button tiny round','confirm'=>'Deseja apagar o item?')))." ".
		addslashes($this->Html->link("Editar","#",array('id'=>'rowEdit-'.$position,'class'=>'button tiny round'))).
		"</td>";
	$tableRow .="";
} else {
	$tableRow .= "<td class='actions'>".
		addslashes($formFields).
		addslashes($this->DkmForm->niceLink(__("Delete"),"#",array('class'=>'delete alert tiny DkmDelete','confirm'=>'Deseja apagar o item?')))." ".
		addslashes($this->Html->link("Editar","#",array('id'=>'rowEdit-'.$position,'class'=>'button tiny round'))).
		"</td>";
	$tableRow .="</tr>";
}
//$tabelaId = "DKM".$modalData["dkmModel"];
if(isset($modalData["dkmEditLink"]) && $modalData["dkmEditLink"]){
	$js='
	row = "'.$tableRow.'";
	position = '.$position.';
	$("#row-'.$position.'").html(row);
	$("#'.$modalData["dkmModalId"].'").foundation("reveal","close")
	dkmDeleteRow("'.$tableElement.'");
	urlEdit = "'.$this->request->here.'/?dkmEditLink=1&dkmModal=1&dkmModalId='.$modalData['dkmModalId'].'&dkmElement='.$modalData['dkmElement'].'&dkmModel='.$modalData['dkmModel'].'&dkmPosition="+position+"&dkmTable='.$modalData['dkmTable'].'";
	dkmEditRow(position,urlEdit,"'.$modalData['dkmModalId'].'");
	'.$script;
} else{
	$js='
	row = "'.$tableRow.'";
	if($("'.$divElement.' table thead").size()==0){
		$("'.$divElement.'").html("<table id=\"'.$tabelaId.'\">'.$tableHeader.'</table>");
		id='.$position.';
		$("#'.$tabelaId.'").append("<tbody>"+row+"</tbody>");
	} else{
		last = $("#'.$tabelaId.' tbody tr:last").attr("id");
		id = '.$position.';
		$("#'.$tabelaId.'").append(row);
	}
	$("#'.$modalData["dkmModalId"].'").foundation("reveal","close")
	dkmDeleteRow("'.$tableElement.'");
	urlEdit = "'.$this->request->here.'/?dkmEditLink=1&dkmModal=1&dkmModalId='.$modalData['dkmModalId'].'&dkmElement='.$modalData['dkmElement'].'&dkmModel='.$modalData['dkmModel'].'&dkmPosition="+id+"&dkmTable='.$modalData['dkmTable'].'";
	dkmEditRow(id,urlEdit,"'.$modalData['dkmModalId'].'");
	'.$script;
}
echo $this->Html->scriptBlock($js);
echo $this->Js->writeBuffer();
?>