<?php

class netFile extends netBaseElem {
	var $upload; //номер соответствующего подмассива из глобального массива uploads
	var $value2; //переменная для thumbnale'ов

	function setVal($a,$post,$linenum) {
		$value='';
		$thumb='';
		$upload=$this->getUpload();
		if($a[$upload["filesqlname"]]!='') {
			$value=$a[$upload["filesqlname"]];
		}
		if($a[$upload["thumbsqlname"]]!='') {
			$thumb=$a[$upload["thumbsqlname"]];
		}
		$this->setValue($value);
		$this->setValue2($thumb);
	}

	function setUpload($upload) {
		$this->upload=$GLOBALS["uploads"][$upload];
	}

	function getUpload() {
		return ($this->upload);
	}

	function setValue2($thumb) {
		$this->value2=$thumb;
	}

	function getValue2() {
		return ($this->value2);
	}

	function netFile($params) {
		$this->netBaseElem($params);
		$this->setUpload($params["upload"]);
		require_once("classes_elem_file_func.php");
	}

	function draw($type, $can, $linenum) {
		$value=$this->getVal();
		$value2=$this->getValue2();
		$upload=$this->getUpload();
		$name=$this->getName();

		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			if(($value!='' && !$upload["thumbmake"]) || ($value!='' && $value2!='' && $upload["thumbmake"]))
			{
				if($upload["isimage"] && $value2!='' && $upload["thumbmake"])
				{
					$content.='<img src="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value2.'" /><br />';
					$content.='<a target="_blank" href="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value.'"><b>ПОСМОТРЕТЬ В ПОЛНЫЙ РАЗМЕР?</b></a>';
				}
				elseif($upload["isimage"] && $value!='')
				{
					$content.='<img src="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value.'" />';
				}
				elseif(!$upload["isimage"] && $value!='')
				{
					$content.='<a target="_blank" href="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value.'"><b>СГРУЗИТЬ ФАЙЛ?</b></a>';
				}
			}
			else
			{
				$content.='<i><font color="#dd0000">Файл не загружен на сервер.</font></i>';
			}
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$value2=$this->getValue2();
		$upload=$this->getUpload();
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

		$content.='
<input type="file" name="'.$name.$linenum.'" class="inputtext" /><br />';

		if($value!='')
		{
			if($value2!='' && $upload["thumbmake"])
			{
				$content.='<br /><a target="_blank" href="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value.'"><img src="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value2.'" border="0" /></a>';
			}

			$content.='<a target="_blank" href="'.$GLOBALS["uploads"][0]["savewayto"].$upload["path"].$value.'"><b>ПОСМОТРЕТЬ</b></a> или <a href="'.$GLOBALS["curdir"].$GLOBALS["kind"].'/'.$GLOBALS["object"].'/action=dynamicaction&actiontype=delete&ill='.$name.'&act=view&id='.$GLOBALS["id"].'" class="careful">УДАЛИТЬ</a>';
		}
		else
		{
			$content.='<i><font color="#dd0000">Файл отсутствует.</font></i>';
		}

		return($content);
	}

	function destroy() {
		unset($this);
	}
}
?>