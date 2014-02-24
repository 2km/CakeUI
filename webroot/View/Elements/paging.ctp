<small><p>
<?php
	echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));
?>
</p></small>
<ul class="pagination">
<?php
	echo $this->Paginator->first('<<', array('tag'=>'li','disabledTag'=>'a'));
	echo $this->Paginator->prev('<', array('tag'=>'li','disabledTag'=>'a'),null,array('tag'=>'li','disabledTag'=>'a','class'=>'disabled'));
	echo $this->Paginator->numbers(array('tag'=>'li','currentTag'=>'a','separator'=>false,'currentClass'=>'disabled'));
	echo $this->Paginator->next('>', array('tag'=>'li','disabledTag'=>'a'),null,array('tag'=>'li','disabledTag'=>'a','class'=>'disabled'));
	echo $this->Paginator->last('>>', array('tag'=>'li','disabledTag'=>'a'));
?>
</ul>