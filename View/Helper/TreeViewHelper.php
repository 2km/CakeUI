<?php
App::uses('AppHelper', 'View/Helper');
class TreeViewHelper extends AppHelper {
	var $helpers = array('Html');
	public function beforeRender($viewFile) {
		$this->Html->script(array('/CakeUI/js/jstree/jstree'),array('inline'=>false)); 
		$this->Html->css('/CakeUI/js/jstree/themes/default/style.min',null,array('inline'=>false));
	}
	public function generate($div,$urls = array()){
		$scriptNovo = '$(function () { 
			$("'.$div.'").jstree({ 
				"plugins" : [ "contextmenu", "dnd", "search", "state", "types", "wholerow" ],
				"core" : {
					"check_callback" : true,
	    			"data" : {
	    				"url" : "'.$this->Html->url($urls['actions']).'",
						"data" : function (n) { 
							return { 
								"operation" : "get_tree", 
								"id" : n.attr ? n.attr("id").replace("node_","") : -1
							}; 
						}
					}
				},';
		$scriptNovo .= '
			"contextmenu" : {
				"items" : {
					"create" : {
						"label" : "Create",';
						if(isset($urls['create'])){
							$scriptNovo.='"action" : function() {
								window.location = "'.$this->Html->url($urls['create']).'"
							},';
						} else{
							$scriptNovo.='"action" :function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						inst.create_node(obj, {}, "last", function (new_node) {
							setTimeout(function () { inst.edit(new_node); },0);
						});
					},';
						}
						$scriptNovo.='"seperator_after" : true,
							"seperator_before" : false,
							"icon" : "glyphicon glyphicon-plus",
						},
					"rename":{
						"label":"Edit",
						"icon" : "glyphicon glyphicon-pencil",';
						if(isset($urls['edit'])){
							$scriptNovo.='"action": function(obj){window.location = "'.$this->Html->url($urls['edit']).'/"+obj.reference.context.id.replace("node_","")},';
						} else{
							 $scriptNovo.='"action": function (data) {var inst = $.jstree.reference(data.reference),obj = inst.get_node(data.reference);inst.edit(obj);}';
						}
						$scriptNovo.='},
					"delete_node":{
						"label":"Delete",
						"icon" : "glyphicon glyphicon-minus",';
						if(isset($urls['delete'])){
							$scriptNovo.='"action": function(obj){window.location = "'.$this->Html->url($urls['delete']).'/"+obj.reference.context.id.replace("node_","")},';
						} else{
							$scriptNovo.='"action":function (data) {
						var inst = $.jstree.reference(data.reference),
							obj = inst.get_node(data.reference);
						if(inst.is_selected(obj)) {
							inst.delete_node(inst.get_selected());
						}
						else {
							inst.delete_node(obj);
						}
					}';
						}
						$scriptNovo.='}
					}
				}
				})
				.bind("create_node.jstree", function (e, data) {
					$.post(
						"'.$this->Html->url($urls['actions']).'", 
						{ 
							"operation" : "create_node", 
							"id" : data.parent.replace("node_",""), //parent_id
							"position" : data.position,
							"title" : data.node.text,
						}, 
						function (r) {
							if(r.status) {
								data.node.id = "node_" + r.id;
							}
							else {
								alert("'.__d('dkmadmin',"Problem creating the node").'")
								data.instance.refresh();
							}
						}
					);
				})
				.bind("delete_node.jstree", function (e, data) {
					$.ajax({
						async : false,
						type: "POST",
						url: "'.$this->Html->url($urls['actions']).'",
						data : { 
							"operation" : "remove_node", 
							"id" : data.node.id.replace("node_","")
						}, 
						success : function (r) {
							if(!r.status) {
								alert("'.__d('dkmadmin',"Problem deleting the node").'")
								data.instance.refresh();
							}
						}
					});
				})
				.bind("rename_node.jstree", function (e, data) {
					$.post(
						"'.$this->Html->url($urls['actions']).'", 
						{ 
							"operation" : "rename_node", 
							"id" : data.node.id.replace("node_",""),
							"title" : data.text
						}, 
						function (r) {
							if(r.status){
								data.instance.refresh();
							}
							else {
								alert("'.__d('dkmadmin',"Problem moving the node").'")
								data.instance.refresh();
							}
						}
					);
				})
				.bind("move_node.jstree", function (e, data) {
					$.ajax({
						async : false,
						type: "POST",
						url:  "'.$this->Html->url($urls['actions']).'",
						data : { 
							"operation" : "move_node", 
							"id" : data.node.id.replace("node_",""), //id do node atual
							"ref" : data.node.parent.replace("node_",""), //parent_id
							"position" : data.position, //nova posição
						},
						success : function (r) {
							if(!r.status) {
								alert("'.__d('dkmadmin',"Problem moving the node").'")
								data.instance.refresh();
							}
							else {
								data.instance.refresh();
							}
						}
					});
				});';
		$scriptNovo .= '});';
	return $this->Html->scriptBlock($scriptNovo,array('inline'=>false));
	}
}	
?>