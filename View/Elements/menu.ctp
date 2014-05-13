<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header floatLeft">
			<button type="button" class="navbar-toggle navbar-left" data-toggle="collapse" data-target=".navbar-collapse">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
			</button>
		</div>
<?php
if(isset($menu)){ 
?>		
		<div class="navbar-left navbar-collapse collapse">
	<?php echo $this->Tree->generate($menu,array('model'=>'Menu','alias'=>'nome','element'=>'CakeUI.item-menu','class'=>'nav navbar-nav dropdown-toggle'));?>
		</div>
		
<?php
}
?>
	</div>
</div>