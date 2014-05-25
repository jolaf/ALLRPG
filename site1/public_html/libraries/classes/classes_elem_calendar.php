<?php

class netCalendar extends netBaseElem {
  var $formatString; 
  
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
			return(date($this -> formatString,strtotime($this->getValue())));
		}
		else
		{
			return(date($this -> formatString,strtotime($this->getDefault())));
		}
	}

	function netCalendar($params) {
		$this->netBaseElem($params);
		$this -> formatString = (array_key_exists('formatString', $params)) ? $params['formatString'] : "d.m.Y";
		if($params["default"]=='')
		{
			$this->setDefault(date($this -> formatString));
		}
		
	}

	function drawForGrid($value) {
    $format_string = $this -> formatString;
    return date($this -> formatString, strtotime($value));
  }

	function draw($type, $can, $linenum) {
		if($can=="write") {
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$linkatbegin=$this->getLinkAtBegin();
			$linkatend=$this->getLinkAtEnd();
			$content.=$linkatbegin.date($this -> formatString,strtotime($this->getVal())).$linkatend;
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

		$value2=date($this -> formatString, strtotime($value));
		$content.='<input type="text" name="'.$name.$linenum.'" class="dpkr" value="'.$value2.'" />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>