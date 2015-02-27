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
        $(":input").inputmask();
        $(".money-inputmask").maskMoney({thousands:".", decimal:","});
        $("input[type=\'checkbox\'], input[type=\'radio\']").iCheck({
            checkboxClass: "icheckbox_minimal",
            radioClass: "iradio_minimal"
        });
        $(".cel-inputmask").keyup(function(){
            v = $(this).val();
            v = v.replace(/\D/g,"");
            if(v.length<=10){
                changeMask(this,"(99)9999-9999[9]");
            } else{
                changeMask(this,"(99)9999[9]-9999");
            }
            return false;
        });
    ', array('inline'=>false));
    $this->Js->get('#formSendModal')->event('click',
        $this->Js->request(
            array(
                'action'=>$this->action,
                'CakeUIOperation'=>2,
                'CakeUILocalStorageName'=>$CakeUILocalStorageName
            ),
            array(
                'update'=>'#modal-content',
                'method'=>'post',
                'dataExpression' => true,
                'data'=>'$("#formSendModal").closest("form").serialize()+"&"+$.param(JSON.parse(localStorage.getItem("'.$CakeUILocalStorageName.'")))'
    )));
?>