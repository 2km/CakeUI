CakeUI
======
<h2>Introduction</h2>
<p>Plugin to improve user interface of cakephp applications.</p>
<p>This plugin is based on <a href='http://getbootstrap.com/' target='_blank'>Bootstrap</a>.</p>
<h2>Installation</h2>
<p>1 - Clone this repo to your plugin directory:</p>
```
cd projectName/Plugin/
git clone https://github.com/golgher/CakeUI.git
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
<h2>Using</h2>
<h3>Tooltips</h3>
<p>This plugin provide a way to define tooltips for any form field.</p>
<p>You have to set the tooltip text in the model class, 
	after doing this an question mark will appear after the label, 
	and the tooltip text will be displayed on mouseover.</p>
```
public $tooltips = array(
	'field'			=>	'Tooltip text for this field',
	'other_field'	=>	'Other text'
);
```
<p>Note: Tooltips and placeholder will only work if you're using correctly the FormHelper.</p>
<h3>Placeholder</h3>
<p>Works like the tooltips, defining an array in the model class.<p>
```
public $placeholder = array(
	'field_name'=>'placeholder text'
);
```
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
```
<h3>Alerts</h3>
<h3>Pagination</h3>
<h3>Dropdown Button</h3>
<h3>Breadcrumb</h3>
<h3>Inline Form (field + button)</h3>
<h3>Tree</h3>
<h3>Css Grid</h3>