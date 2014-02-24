<?php
class TreeListHelper extends AppHelper {
	public function generate($data,$model,$displayField){
		$i = 0;
		$anterior=null;
		$totalDeVenda=0;
		$last_rght=0;
		$stack = array();
		$spacer="___";
		foreach ($data as $chave=>$register){
			while ($stack && ($stack[count($stack) - 1] < $register[$model]['rght'])) {
				array_pop($stack);
			}
			if($last_rght!=$register[$model]['rght']){
				$tree_prefix = str_repeat($spacer,count($stack));
			}
			$stack[] = $register[$model]['rght'];
			$last_rght=$register[$model]['rght'];
			$data[$chave][$model][$displayField]=$tree_prefix.$data[$chave][$model][$displayField];
		}
		return $data;
	}
}		
?>