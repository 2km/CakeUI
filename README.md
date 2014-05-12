CakeUI
======
<h2>Introduction</h2>
<p>Plugin to improve user interface of cakephp applications.</p>
<p>This plugin is based on <a href='http://getbootstrap.com/' target='_blank'>Bootstrap</a>.</p>
<h2>Installation</h2>
<p>1 - Clone this repo to your plugin directory:</p>
```
cd projectName/Plugin/
git clone https://github.com/2km/CakeUI.git
```
<p>2 - Configure projectName/Config/bootstrap.php:</p>
```
CakePlugin::load('CakeUI');
```
<p>3 - Configure projectName/Controller/AppController.php:</p>
```
public $helpers = array(
	'Form'=> array('className' => 'CakeUI.BootstrapForm'), 
	'Html'=> array('className' => 'CakeUI.BootstrapHtml')
);
//Note: if you redeclare helpers array in other Controller, remember to define className
```
<p>4 - Using the CakeUI layout</p>
<p>Define in AppController what layout will be used:</p>
```
public function beforeFilter(){
	$this->layout='CakeUI.default';
}
```
<h2>Using</h2>
<h3>Tooltips</h3>
<p>This plugin provide a way to define tooltips for any form field.</p>
<p>You have to set the tooltip text in the model class, after doing this an question mark will appear after the label, and the tooltip text will be displayed on mouseover.</p>
```
public $tooltips = array(
	'field'			=>	'Tooltip text for this field',
	'other_field'	=>	'Other text'
);
```
<p>Note: Tooltips and placeholder will only work if you're using correctly the FormHelper.</p>
<h3>Placeholder</h3>
<p>Works like the tooltips, defining an array in the model class.</p>
```
public $placeholder = array(
	'field_name'=>'placeholder text'
);
```
<h3>Masks</h3>
<p>Works like the tooltips, defining an array in the model class.</p>
<p>We use this lib for mask inputs http://github.com/RobinHerbots/jquery.inputmask</p>
```
public $mask = array(
	'phone' => "'mask': '(99)9999-9999'",  
	'zipcode' => array('class'=>'zipcode'),
	'money' => array('class'=>'money-inputmask'),
);
```
<p>In the second example, we have defined a class, this way, we can use other lib to mask the input.</p>
<p>We also included https://github.com/plentz/jquery-maskmoney, to mask money fields.</p>

<h3>Tabs and Pills</h3>
```
$data[0]['title']='tab1';
$data[0]['content']='Lorem ipsum dollar';
$data[1]['title']='tab2';
$data[1]['content']='/users/index';
$data[2]['title']='Lorem ipsum dollar';
$data[2]['content']='Lorem ipsum dollar';
$data[2]['class']='disabled'; //Option to disable access to tab

//Options array
//$config['type'] = (tabs | tabs nav-justified | pills | pills nav-stacked | pills nav-justified)
//$config['selected']=2 //define which tab will be displayed

echo $this->Html->tabs($data);

//echo $this->Html->tabs($data,$config);
```
<h3>Alerts</h3>
<p>Displaying alerts using the <a href="http://getbootstrap.com/components/#alerts" target="_blank">bootstrap</a> style.</p>
```
//Success alert
$this->Session->setFlash('The User has been saved','default',array('class'=>'alert alert-success'));

//Error alert
$this->Session->setFlash('The User could not be saved. Please, try again.','default',
	array('class'=>'alert alert-danger')
);
```
<h3>Pagination</h3>
<p>Element to display the pagination message and buttons with bootstrap style.</p>
```
<?php
	echo $this->element('CakeUI.paging');
?>
```
<h3>Table</h3>
<p>To use bootstrap style you must add this class in table declaration:</p>
```
	<table class='table table-striped table-bordered table-condensed'>
		...
	</table>
```
<p>Learn more about <a href="http://getbootstrap.com/css/#tables" target="_blank">bootstrap tables</a>.</a>
<h3>Buttons</h3>
<p>Applying bootstrap style in application buttons:</p>
```
//Actions
echo $this->Html->link('View',array('action'=>'view',$user['User']['id']),
	<strong>array('class'=>'btn btn-xs btn-info')</strong>
);
echo $this->Html->link('Edit',array('action'=>'edit',$user['User']['id']),
	<strong>array('class'=>'btn btn-xs btn-warning')</strong>
);
echo $this->Form->postLink('Delete',array('action'=>'delete', $user['User']['id']), 
	<strong>array('class'=>'btn btn-xs btn-danger'), </strong>
	sprintf('Are you sure you want to delete "%s"?', $user['User']['email'])
);
//New
echo $this->Html->link('New User',array('action'=>'add'),
	<strong>array('class'=>'btn btn-primary')</strong>
);
//Submit
echo $this->Form->end(array("label"=>__('Save'), <strong>"class"=>"btn btn-success")</strong>);
```
<p>Learn more about <a href="http://getbootstrap.com/css/#buttons" target="_blank">bootstrap buttons</a>.</a>

<h3>Dropdown Button</h3>
<p>Creating dropdown buttons:</p>
```
$options[0]['title']='Action';
$options[0]['link']='/';
$options[1]['title']='Other Action';
$options[1]['link']='/users';

$config['size']=(btn-xs | btn-sm | btn-lg) //default size without any class
$config['color']=(btn-default | btn-primary | btn-success | btn-info | btn-warning | btn-danger | btn-link)
$config['split']=false;
$config['dropup']=false;

$config['form']['submit']=false;
$config['form']['field']='Tmp.operation'

$this->Html->dropdownButton('Options',$options,$config);
```
<p>Example: submit a form with an action value.</p>
```
$options[]['title']='Save and exit';		//Action Value - 0
$options[]['title']='Save and continue';	//Action Value - 1
$config['color'] = 'btn-primary';
$config['split'] = true;
$config['dropup'] = true;
$config['form']['submit']=true;

echo $this->Html->dropdownButton('Save',$options,$config);
```
<p>Note: the first key of array will be used for the value of action.</p>
<h3>Breadcrumb</h3>
```
$data[0]['title']='Home';
$data[0]['link']='/';
$data[1]['title']='Users';
$data[1]['link']='/users/index';
$data[2]['title']='Profile';
$data[2]['link']='#'; //this action will create a last item (disabled, without link)

echo $this->Html->breadcrumb($data);
```
<h3>Inline Form (field + button)</h3>
```
/*
Params:
	- $model
	- $fieldName
	- $buttonLabel
	- $options:
		$options['Form']['url']='/other/action';
		
		$options['Field']['placeholder']='placeholder text';
		$options['Field']['class']='btn-xs';

		$options['Button']['place']='before';
		$options['Button']['class']='btn btn-xs btn-primary'

*/
$this->Form->inlineForm('User','email','Send',array(
		'Field'=>array('placeholder'=>'Type your e-mail'),
		'Button'=>array('place'=>'after','class'=>'btn btn-success')));
```
<h3>Grid</h3>
<p>Helper to generate html using the bootstrap grid system.</p>
```
//In Controller Class
public $helpers = array('CakeUI.Grid');

//In View
$grid[][2]='Html Content';
$grid[][4]=$this->Form->input('name');
$grid[][4]=$this->Form->input('telephone');
$grid[][2]=$this->Form->end(array("label"=>__('Save'), "class"=>"btn btn-success"));

echo $this->Grid->grid($grid);

//Note: The second key of array is the grid size.
```
<h3>Tree</h3>
<p>This Helper uses the <a href='http://jstree.com/' target='_blank'>jstree</a> lib.</p>
<p>In model:</p>
```
public $actsAs = array('Tree');
```
<p>In controller:</p>
```
public $helpers = array('CakeUI.TreeView');
public $components = array('CakeUI.Tree'=>array('treeName'=>'Menus'));
```
<p>In View:</p>
```
<div id="tree-div"></div>
<?php 
echo $this->TreeView->generate('#tree-div',array(
	'actions'=>'/admin/menus/index',
	'create'=>'/admin/menus/add', //if you don't define, new itens will be generate at the tree
	'edit'=>'/admin/menus/edit', //if you don't define, itens will be edited at the tree
	'delete'=>'/admin/menus/delete'));
?>
```
<h3>Select2</h3>
<p>All select fields will automatic changed to select2</p>
<p>You can learn more about <a href='https://github.com/ivaynberg/select2/'>select2 here</a>.</p>

<h3>Ajax File Upload</h3>
<p>Generate a button to upload one or multiple files.</p>
<p>In View:</p>
```
//Single file:
echo $this->Form->ajaxUpload('photo',array('size'=>'"200px"','label'=>'Upload photo'));
//Multiple
echo $this->Form->ajaxUpload('photo',array('size'=>'"200px"','label'=>'Upload photos','multiple'=>'true'));
```
<p>In Controller:</p>
```
public $components = array(
		'CakeUI.Upload'=>array(
			'photo'=>array(
				'size'=>5248880,
				'resize'=>array('small'=>240),
				'allow'=>array('jpg','jpeg','gif','png')
			)
		)
	);
```
<p>You can learn more about <a href='http://docs.fineuploader.com/'>fineuploader here</a>.</p>