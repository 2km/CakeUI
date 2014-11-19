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
	echo $this->element('CakeUI.modal_footer');
	echo $this->fetch('css');
    echo $this->fetch('script');
    echo $this->Js->writeBuffer();
?>