<?php

class netTextUri extends netTextarea {
	function netTextUri($params) {
		$this->netTextarea($params);
		if ($params['rows'] > 1)
		{
      die("URL has to be one-line");
		}
	}

	function draw($type, $can, $linenum) {
		if($can=="write")
		{
			return $this->trueDraw($linenum);
		}
		else
		{
      $value = decodesafe(encode($this->getVal()));
			return "<a href=\"$value\">$value</a>";
		}
	}

	function destroy() {
		unset($this);
	}
}

?>