<?php

class netNumber extends netBaseElem {
	var $round; //принудительное округление чисел в данном поле (true|false)

	function setRound($round)
	{
		$this->round=$round;
	}

	function getRound()
	{
		return ($this->round);
	}

	function netNumber($params) {
		$this->netBaseElem($params);
		$this->setRound($params["round"]);
		if($params["name"]=="id")
		{
			$this->setWrite('99999999999');
		}
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$linkatbegin=$this->getLinkAtBegin();
			$linkatend=$this->getLinkAtEnd();
			$content.=$linkatbegin.$this->getVal().$linkatend;
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

		$content='<input type="text" name="'.$name.$linenum.'" value="';
		$content.=$value;
		$content.='"';
		if($this->getWidth()!='')
		{
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			$content.=' style="height: '.$this->getHeight().';"';
		}
		$content.=' class="inputnum';
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

?>