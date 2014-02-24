<?php
class GridHelper extends AppHelper {
	private $types = array(
		'xs'=>'phones', 
		'sm'=>'tablets', 
		'md'=>'desktops', 
		'lg'=>'larger desktops'
	);
	public function grid($columns){
		$html = "<div class = 'row'>\n";
		foreach($columns as $key => $colNumber){
			$html .= "<div class = 'col-md-".key($colNumber)."'>\n";
			$html .= current($colNumber);
			$html .= "</div>\n";
		}
		$html .= "</div>\n";
		return $html;
	}
}
?>