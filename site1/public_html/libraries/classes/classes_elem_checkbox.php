<?php

class netCheckbox extends netBaseElem {
	function setVal($a,$post,$linenum) {
		$value='';
		$name=$this->getName();
		if($post)
		{
			if(isset($linenum)) {
				$value=$_POST[$name][$linenum];
			}
			else
			{
				$value=$_POST[$name];
			}
		}
		elseif($a[$name]!='') {
			$value=decode($a[$name]);
		}
		$this->setValue($value);
	}

	function netCheckbox($params) {
		$this->netBaseElem($params);
	}

	function drawSymbol($value)
	{
    return $value ? '<font color="green"><b>&#8730</b></font>' : '<font color="red"><b>X</b></font>';
	}
	
	
	function drawForGrid($value) {
    return $this -> drawSymbol($value);
  }

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			return $this->trueDraw($linenum);
		}
		else
		{
      return $this -> drawSymbol($this->value);
		}
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

		$content='<input type="checkbox" name="'.$name.$linenum.'" id="'.$name.$linenum.'" class="inputcheckbox';
		if($this->getMustBe()) {
			$content.=' mustbe';
		}
		$content.='"';
		if($this->getWidth()!='')
		{
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			$content.=' style="height: '.$this->getHeight().';"';
		}
		if($value=='on' || $value==1)
		{
			$content.=' checked';
		}
		$content.=' />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>