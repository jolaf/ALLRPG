<?php

class netSelect extends netBaseElem {
	var $values; //массив данных, из которых создаются id и названия option'ов

	function setValues($values) {
		$this->values=$values;
	}

	function getValues() {
		return ($this->values);
	}

	function netSelect($params) {
		$this->netBaseElem($params);
		$this->setValues($params["values"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$massiv=$this->getValues();

			for($i=0;$i<count($massiv);$i++)
			{
				if($massiv[$i][0]==$this->getVal())
				{
					$linkatbegin=$this->getLinkAtBegin();
					if(stripos($linkatbegin,'{value}')) {
                		$linkatbegin=str_replace('{value}',$massiv[$i][0],$linkatbegin);
   					}
					$linkatend=$this->getLinkAtEnd();
					$content.=$linkatbegin.$massiv[$i][1].$linkatend;
					break;
				}
			}
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$name=$this->getName();

		$massiv=$this->getValues();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		$content='<select name="'.$name.$linenum.'"';
		if($this->getWidth()!='')
		{
			$content.=' style="width: '.$this->getWidth().';"';
		}
		if($this->getHeight()!='')
		{
			$content.=' style="height: '.$this->getHeight().';"';
		}
		$content.=' class="inputselect';
		if($this->getMustBe()) {
			$content.=' mustbe';
		}
		$content.='" />
';
		$content.='<option value="" style="font-weight: bold">- Выбрать -</option>
';

		for($i=0;$i<count($massiv);$i++)
		{
			$content.='<option value="'.$massiv[$i][0].'"';
			if($massiv[$i][0]==$value) {$content.=' selected';}
			$content.='>';
			if($massiv[$i][2]>0)
			{
				for($j=0;$j<$massiv[$i][2];$j++)
				{
					$content.="&nbsp;&nbsp;";
				}
			}
			$content.=$massiv[$i][1].'</option>
';
		}
		$content.='</select>';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>