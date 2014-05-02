<?php

class netText extends netBaseElem {
	var $minchar; //минимальное количество символов
	var $maxchar; //максимальное количество символов

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

	function netText($params) {
		$this->netBaseElem($params);
		$this->setMinchar($params["minchar"]);
		$this->setMaxchar($params["maxchar"]);
		if (!array_key_exists ('valueExtractor', $params))
		{ //override default extractor, if not supplied
      $this -> valueFunctor = function ($obj, $row)
      {
        return decode3 ($row [$obj -> name]);
      };
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

		$content='<input type="text" name="'.$name.$linenum.'" value="'.$value.'"';
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
		$content.='" ';
		if($this->getType()=="login") {
			$content.='autocomplete="off" ';
		}
		$content.='/>';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

class netEmail extends netText {
	function netEmail($params) {
		$this->netBaseElem($params);
	}

	function check_email($s) {
		# проверяем на синтакс
		$wr='Неверный формат данных в поле «'.$this->getSname().'».';
		$r='OK';
		if($s=='') return $r;

		$isValid = true;
		$atIndex = strrpos($s, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
		   $isValid = false;
		}
		else
		{
		   $domain = substr($s, $atIndex+1);
		   $local = substr($s, 0, $atIndex);
		   $localLen = strlen($local);
		   $domainLen = strlen($domain);
		   if ($localLen < 1 || $localLen > 64)
		   {
		      // local part length exceeded
		      $isValid = false;
		   }
		   else if ($domainLen < 1 || $domainLen > 255)
		   {
		      // domain part length exceeded
		      $isValid = false;
		   }
		   else if ($local[0] == '.' || $local[$localLen-1] == '.')
		   {
		      // local part starts or ends with '.'
		      $isValid = false;
		   }
		   else if (preg_match('/\\.\\./', $local))
		   {
		      // local part has two consecutive dots
		      $isValid = false;
		   }
		   else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		   {
		      // character not valid in domain part
		      $isValid = false;
		   }
		   else if (!preg_match('/^[A-Za-z0-9\\-\\.]{2,}[\\.][A-Za-z0-9]{2,}$/', $domain))
		   {
		      // character not valid in domain part; doesnt't have .com
		      $isValid = false;
		   }
		   else if (preg_match('/\\.\\./', $domain))
		   {
		      // domain part has two consecutive dots
		      $isValid = false;
		   }
		   else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local)))
		   {
		      // character not valid in local part unless
		      // local part is quoted
		      if (!preg_match('/^"(\\\\"|[^"])+"$/',
		          str_replace("\\\\","",$local)))
		      {
		         $isValid = false;
		      }
		   }
		   /*if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		   {
		      // domain not found in DNS
		      $isValid = false;
		   }*/
		}
		if($isValid) {
			return $r;
		}
		else {
			return $wr;
		}
	}
}

class netLogin extends netText {
	function netLogin($params) {
		$this->netBaseElem($params);
		$this->setMinchar($params["minchar"]);
		$this->setMaxchar($params["maxchar"]);
	}
}

?>