<?php

class netCalendar extends netBaseElem {
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

	function getVal() {
		if($this->getValue()!='' && $this->getValue()!='0000-00-00' && $this->getValue()!='01.01.1970')
		{
			return(date("d.m.Y",strtotime($this->getValue())));
		}
		else
		{
			return(date("d.m.Y",strtotime($this->getDefault())));
		}
	}

	function netCalendar($params) {
		$this->netBaseElem($params);
		if($params["default"]=='')
		{
			$this->setDefault(date("d.m.Y"));
		}
	}

	function draw($type, $can, $linenum) {
		if($can=="write") {
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$linkatbegin=$this->getLinkAtBegin();
			$linkatend=$this->getLinkAtEnd();
			$content.=$linkatbegin.date("d.m.Y",strtotime($this->getVal())).$linkatend;
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

		$value2=date("d.m.Y", strtotime($value));
		$content.='<input type="text" name="'.$name.$linenum.'" class="dpkr" value="'.$value2.'" />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>