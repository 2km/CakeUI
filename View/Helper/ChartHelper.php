<?php
class ChartHelper extends AppHelper {

	private $once = array();
	private $counter = 0;
	public $helpers = array('Html', 'Js'=>array('Jquery'));

	private function includeMorrisLibs(){
		$css = '/CakeUI/css/morris/morris';
		if(!isset($this->once[$css])){
			$this->once[$css]=true;
			echo $this->Html->css($css,null,array('inline'=>false));	
		}
		$js[]='//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js';
		$js[]='/CakeUI/js/morris/morris.min';
		echo $this->Html->script($js,array('inline'=>false));
	}
	/*
	$data[0]['year']=2008;
	$data[0]['value'] = 20;
	$data[1]['year']=2009;
	$data[1]['value'] = 10;
	$data[2]['year']=2010;
	$data[2]['value'] = 5;
	$data[3]['year']=2011;
	$data[3]['value'] = 5;
	$data[4]['year']=2012;
	$data[4]['value'] = 20;
	*/
	public function morrisLineChartOld($data = array(),$options=array()){
		$options+=array(
			'labels'=>'Value'
		);
		$json_data = json_encode($data);
		$this->includeMorrisLibs();
		$html='<div id="chart-'.$this->counter.'"></div>';
		$axes = array_keys($data[0]);
		$js='new Morris.Line({
				element: "chart-'.$this->counter.'",
				data: '.$json_data.',
				xkey: "'.$axes[0].'",
				ykeys: ["'.$axes[1].'"],
				labels: ["'.$options['labels'].'"]
			});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		$this->counter++;
		return $html;
	}
	public function morrisLineChart($data = array(),$options=array()){
		$options+=array(
			'labels'=>'Value'
		);
		$json_data = json_encode($data);
		$this->includeMorrisLibs();
		$html='<div id="chart-'.$this->counter.'"></div>';
		$bars = array_keys($data[0]);
		$js='new Morris.Line({
			element: "chart-'.$this->counter.'",
			data: '.$json_data.',
			xkey: "'.$bars[0].'",';
		unset($bars[0]);
		$json_bars = json_encode(array_values($bars));
		$js.='ykeys: '.$json_bars.',
			labels: '.json_encode($options['labels']).'
			});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		$this->counter++;
		return $html;
	}
	public function morrisBarsChart($data=array(),$options=array()){
		$options+=array(
			'labels'=>null
		);
		$json_data = json_encode($data);
		$this->includeMorrisLibs();
		$html='<div id="chart-'.$this->counter.'"></div>';
		$bars = array_keys($data[0]);
		$js='Morris.Bar({
			element: "chart-'.$this->counter.'",
			data: '.$json_data.',
			xkey: "'.$bars[0].'",';
		unset($bars[0]);
		$json_bars = json_encode(array_values($bars));
		$js.='ykeys: '.$json_bars.',
			labels: '.json_encode($options['labels']).'
			});';
		echo $this->Html->scriptBlock($js,array('inline'=>false));
		$this->counter++;
		return $html;
	}
}
?>