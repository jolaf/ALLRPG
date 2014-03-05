<?php

class netTextarea extends netBaseElem {
	var $rows; //количество рядов textarea
	var $minchar; //минимальное количество символов
	var $maxchar; //максимальное количество символов

	function setRows($rows) {
		$this->rows=$rows;
	}

	function setMaxchar($maxchar)
	{
		$this->maxchar=$maxchar;
	}

	function setMinchar($minchar)
	{
		$this->minchar=$minchar;
	}

	function getRows() {
		return ($this->rows);
	}

	function getMaxchar()
	{
		return ($this->maxchar);
	}

	function getMinchar()
	{
		return ($this->minchar);
	}

	function netTextarea($params) {
		$this->netBaseElem($params);
		$this->setRows($params["rows"]);
		$this->setMaxchar($params["maxchar"]);
		$this->setMinchar($params["minchar"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$this->setValue(decodesafe(encode($this->getVal())));
			$content.=$this->getVal();
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$name=$this->getName();
		$rows=$this->getRows();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		if($rows!='' && $rows!=0)
		{
			$content='<textarea name="'.$name.$linenum.'" rows="'.$rows.'"';
		}
		else
		{
			$content='<textarea name="'.$name.$linenum.'"';
		}
		if($this->getWidth()!='')
		{
			if(stripos($this->getWidth(),'px')===false && stripos($this->getWidth(),'px')===false) {
				$this->setWidth(($this->getWidth()).'px');
			}
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			if(stripos('px',$this->getHeight())===false && stripos('px',$this->getHeight())===false) {
				$this->setHeight(($this->getHeight()).'px');
			}
			$content.=' style="height: '.$this->getHeight().';"';
		}
		if($this->getMaxchar()>0)
		{
			$content.=' maxchar="'.$this->getMaxchar().'"';
		}
		$content.=' class="inputtextarea';
		if($this->getMustBe()) {
			$content.=' mustbe';
		}
		$content.='" />'.$value.'</textarea>';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>