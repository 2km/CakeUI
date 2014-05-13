<?php
if($hasChildren){
	$this->Tree->addItemAttribute('class', 'dropdown');
	if($this->Tree->__settings['depth']==0){
		echo $this->Html->link(__($data['Menu']['name']).'<b class="caret"></b>','#',array("class"=>"trigger","data-toggle"=>"dropdown", 'escape'=>false));	
	}else{
		echo $this->Html->link(__($data['Menu']['name']),'#',array("class"=>"trigger right-caret","data-toggle"=>"dropdown", 'escape'=>false));
	}
	
}else{
	$data['Menu']['url']=empty($data['Menu']['url'])?'#':$data['Menu']['url'];
	echo $this->Html->link(__($data['Menu']['name']),$data['Menu']['url']);
}
?>