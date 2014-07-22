<?php echo $this->Form->create($options['model']); ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title"><?php echo __('Tipo Classificacao'); ?></h4>
</div>
<div class="modalForm form modal-body">
<?php 
	echo $this->Session->flash('modalMsg'); 
	echo $this->Form->input('id');
	echo $this->Form->input('CakeUITemp.key',array('type'=>'hidden'));
	foreach ($options['table'] as $key=>$field) {
		$customForm='input';
		if (isset($field['customForm'])) {
			$customForm=$field['customForm'];
		}
		if(!isset($field['form'])){
			$field['form']=array();
		}
		// pr($field['form']);
		echo $this->Form->{$customForm}($field['field'],$field['form']);
	}
	?>
</div>
<?php
	echo '<div class="modal-footer">';
	echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
	echo $this->Form->button('<span class="glyphicon glyphicon-ok"></span> '.__('Save'),array('class'=>'btn btn-primary','id'=>'formSendModal'));
	echo $this->Form->end();
	echo '</div>';
	echo '</form>';

	$tableId = '#'.$options['table_id'];
	echo $this->Html->scriptBlock('
		if($("'.$tableId.'").size()==0){
			if($("#CakeUITempKey").val()==""){
				$("#CakeUITempKey").val("0");	
			}
		} else{
			if($("#CakeUITempKey").val()==""){
				last = parseInt($("'.$tableId.' tbody tr:last").attr("id").substr(4));
				$("#CakeUITempKey").val(last+1);	
			}
		}
	', array('inline'=>'false'));
	$this->Js->get('#formSendModal')->event('click', 
		$this->Js->request(
			//"/".$this->request->url."/?dkmStart=".$modalData["dkmStart"]."&dkmPos=".$modalData["dkmPos"]."&dkmModal=2&dkmModalId=".$modalData["dkmModalId"]."&dkmElement=".$modalData["dkmElement"]."&dkmModel=".$modalData['dkmModel']."&dkmPosition=".$modalData['dkmPosition']."&dkmTable=".$modalData['dkmTable'],
			array(
				'action'=>$this->action,
				'CakeUIOperation'=>2,
				'CakeUICookie'=>$cakeUICookie,
				// 'CakeUIModel'=>$modalData['CakeUIModel'],
				// 'CakeUIElement'=>$modalData['CakeUIElement']
			),
			array(
				'update'=>'#modal-content',
				'method'=>'post',
				'dataExpression' => true,
				'data'=> $this->Js->serializeForm(array('isForm' => false, 'inline' => true)),
	)));
	echo $this->Js->writeBuffer();
	echo $this->fetch('script');
	echo $this->fetch('css');
?>