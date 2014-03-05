<?php

class netColorpicker extends netBaseElem {
	function netColorpicker($params) {
		$this->netBaseElem($params);
	}

	function draw($type, $can, $linenum) {
		if($can=="write") {
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$content.=$this->getVal();
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$name=$this->getName();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		$content='<div class="colorSelector" style="background-color: '.$value.';"></div><input type="text" class="cpkr" name="'.$name.$linenum.'" value="'.$value.'" maxlength="7"';
		if($this->getWidth()!='')
		{
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			$content.=' style="height: '.$this->getHeight().';"';
		}
		$content.=' />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}
?>