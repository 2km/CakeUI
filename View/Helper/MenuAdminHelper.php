<?php
class MenuAdminHelper extends AppHelper{
	public $helpers = array('Html');

	private $activeItem = array();

	private $menu = null;

	private $processed = array();

	private $urlActive = null;

	public function hasChild($item){
		return ($item['Menu']['rght']-$item['Menu']['lft'])>1;
	}

	public function generate($menu,$urlActiveItem=null){
		$this->menu = $menu;
		$this->urlActive = $urlActiveItem;
		$html='<ul class="sidebar-menu">';
		$html.=$this->generate_menu(0,$menu[0]['Menu']['parent_id']);
		$html.='</ul>';
		return $html;
	}
	public function generate_menu($pos=0,$parent_id = null){
		$this->processed[$pos]=true;
		if($this->hasChild($this->menu[$pos])){
			if(!isset($html)){$html=null;}
			$mother_node = $this->menu[$pos];
			$children = $this->generate_menu($pos+1,$mother_node['Menu']['id']);
			$html .='<li class="treeview">'.$this->Html->link(__d('dkmadmin',$mother_node['Menu']['name']),$mother_node['Menu']['link']).
				'<ul class="treeview-menu">'.
					$children;
		} else{
			if(!isset($html)){$html=null;}
			if(strstr($this->menu[$pos]['Menu']['link'],$this->urlActive)){
				$html .= '<li class="active">'.$this->Html->link(__d('dkmadmin',$this->menu[$pos]['Menu']['name']),$this->menu[$pos]['Menu']['link']).'</li>';
			} else {
				$html .= '<li>'.$this->Html->link(__d('dkmadmin',$this->menu[$pos]['Menu']['name']),$this->menu[$pos]['Menu']['link']).'</li>';
			}
			if(isset($this->menu[$pos+1])){
				if($this->menu[$pos+1]['Menu']['parent_id']!=$parent_id){
					$html.='</ul></li>';
				} else{
					$html.='</li>';
				}
			}
			else {
				$html.='</ul></li>';
			}
		}
		if(isset($this->menu[($pos+1)]) && !(isset($this->processed[($pos+1)]))){
			$html .= $this->generate_menu($pos+1,$this->menu[($pos+1)]['Menu']['parent_id']);
		}
		return $html;
	}
}
?>