<?php

class netPassword extends netBaseElem {
	var $minchar; //минимальное количество символов
	var $maxchar; //максимальное количество символов

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

	function setMinchar($minchar)
	{
		$this->minchar=$minchar;
	}

	function setMaxchar($maxchar)
	{
		$this->maxchar=$maxchar;
	}

	function getMinchar()
	{
		return ($this->minchar);
	}

	function getMaxchar()
	{
		return ($this->maxchar);
	}

	function netPassword($params) {
		$this->netBaseElem($params);
		$this->setMinchar($params["minchar"]);
		$this->setMaxchar($params["maxchar"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$content.="******";
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

		$content='<input type="password" name="'.$name.$linenum.'"';
		if($this->getWidth()!='')
		{
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			$content.=' style="height: '.$this->getHeight().';"';
		}
		if($this->getMinchar()!='')
		{
			$content.=' minlength="'.$this->getMinchar().'"';
		}
		if($this->getMaxchar()!='')
		{
			$content.=' maxlength="'.$this->getMaxchar().'"';
		}
		$content.=' class="inputtext';
		if($this->getMustBe()) {
			$content.=' mustbe';
		}
		$content.='" />';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

class netPassword2 extends netPassword {
	function netPassword2($params) {
		$this->netBaseElem($params);
		$this->setMinchar($params["minchar"]);
		$this->setMaxchar($params["maxchar"]);
	}
}

?>