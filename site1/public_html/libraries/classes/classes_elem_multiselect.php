<?php

class netMultiselect extends netBaseElem {
	var $values; //массив данных, из которых создаются id и названия checkbox'ов
	var $one; //одновыборность (radio) из всего массива (true|false)
	var $images; //массив данных, из которых создаются картинки для соответствующих checkbox'ов
	var $path; //путь до папки картинок $images
	var $locked; //заблокированные к изменению checkbox'ы

	function setVal($a,$post,$linenum) {
		$value='-';
		$name=$this->getName();
		if($post)
		{
			if($this->getOne())
			{
				if(isset($linenum)) {
					$value.=$_POST[$name][$linenum].'-';
				}
				else
				{
					$value.=$_POST[$name].'-';
				}
			}
			else
			{
				$values=$this->getValues();
				for($t=0;$t<count($values);$t++)
				{
					if(isset($linenum) && $_POST[$name][$values[$t][0]][$linenum]=="on") {
						$value.=$values[$t][0].'-';
					}
					elseif($_POST[$name][$values[$t][0]]=="on") {
						$value.=$values[$t][0].'-';
					}
				}
			}
		}
		elseif($a[$name]!='') {
			$value=decode($a[$name]);
		}
		$this->setValue($value);
	}

	function setValues($values) {
		$this->values=$values;
	}

	function setOne($one) {
		$this->one=$one;
	}

	function setImages($images) {
		$this->images=$images;
	}

	function setPath($path) {
		$this->path=$path;
	}

	function setLocked($locked) {
		$this->locked=$locked;
	}

	function getValues() {
		return ($this->values);
	}

	function getOne() {
		return ($this->one);
	}

	function getImages() {
		return ($this->images);
	}

	function getPath() {
		return ($this->path);
	}

	function getLocked() {
		return ($this->locked);
	}

	function netMultiselect($params) {
		$this->netBaseElem($params);
		$this->setValues($params["values"]);
		$this->setOne($params["one"]);
		$this->setImages($params["images"]);
		$this->setPath($params["path"]);
		$this->setLocked($params["locked"]);
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$values=$this->getValues();
			$val=$this->getVal();

			$images=$this->getImages();
   			$linkatend=$this->getLinkAtEnd();

			for($t=0;$t<count($values);$t++)
			{
				if(stripos($val,'-'.$values[$t][0].'-')!==false)
				{
					$linkatbegin=$this->getLinkAtBegin();
					if(stripos($linkatbegin,'{value}')!==false) {
                		$linkatbegin=str_ireplace('{value}',$values[$t][0],$linkatbegin);
   					}
					$content.=$linkatbegin;
					if($images!='') {
						$content.='<img src="'.$this->getPath().$images[$t][1].'" border="0" />'.$linkatend.' '.$linkatbegin;
					}
					$content.=$values[$t][1].$linkatend;
					if($this->getOne()==false)
					{
						$content.='<br />
';
					}
				}
			}
		}
		return($content);
	}

	function trueDraw($linenum) {
		$value=$this->getVal();
		$name=$this->getName();

		$massiv=$this->getValues();
		$images=$this->getImages();
		$locked=$this->getLocked();
		$width=$this->getWidth();
		if($width!='' && stripos($width,'%')===false && stripos($width,'px')===false) {
			$width.='px';
		}
		$height=$this->getHeight();
		if($height!='' && stripos($height,'%')===false && stripos($height,'px')===false) {
			$height.='px';
		}

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='[line'.$linenum.']';
		}
		else
		{
			$linenum='';
		}

		$content.='<div class="dropfield';
		if($this->getMustBe()) {
			$content.=' mustbe';
		}
		$content.='" ';
		if($width!='') {
			$content.='style="width: '.$width.'" ';
		}
		$content.='id="selected_'.$name.$linenum.'">';
		if($value!='' && $value!='-' && $value!='--')
		{
			for($i=0;$i<count($massiv);$i++)
			{
				if(strpos($value,"-".$massiv[$i][0]."-")!==false)
				{
					$content.='<div>'.$massiv[$i][1].'</div>';
					//$content.=str_replace('"','\'',$massiv[$i][1]).'<br />';
				}
			}
		}
		else
		{
			$content.='<div>– Выбрать –</div>';
		}
		$content.='</div>';
		$content.='<div class="dropfield2"';
        /*if($height!='') {
        	$content.=' style="height: '.$height.'"';
        }*/
		$content.=' id="choice_'.$name.$linenum.'">';

		if($this->getOne() && !$this->getMustBe()) {
   			$content.='<div><input type="radio" name="'.$name.$linenum.'" id="'.$name.'[0]'.$linenum.'" value="" class="inputradio"';
   			if(stripos($value,'-0-')!==false) {
				$content.=' checked';
			}
			$content.='><label for="'.$name.'[0]'.$linenum.'"> не выбирать</label></div>';
		}

		for($i=0;$i<count($massiv);$i++)
		{
			if($this->getOne())
			{
				$content.='<div><input type="radio" name="'.$name.$linenum.'" id="'.$name.'['.$massiv[$i][0].']'.$linenum.'" value="'.$massiv[$i][0].'" class="inputradio"';
			}
			else
			{
				$content.='<div><input type="checkbox" name="'.$name.'['.$massiv[$i][0].']'.$linenum.'" id="'.$name.'['.$massiv[$i][0].']'.$linenum.'" class="inputcheckbox"';
			}

			if(stripos($value,'-'.$massiv[$i][0].'-')!==false)
			{
				$content.=' checked';
			}

			if(stripos($locked,'-'.$massiv[$i][0].'-')!==false)
			{
				$content.=' OnClick="return false;"';
			}
			$content.='><label for="'.$name.'['.$massiv[$i][0].']'.$linenum.'"> ';
			if($images!='')
			{
				$content.='<img src="'.$this->getPath().$images[$i][1].'" /> ';
			}
			$content.=$massiv[$i][1].'</label></div>';
		}
		$content.='</div>';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>