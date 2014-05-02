<?php

class netTextUri extends netText {
	function netTextUri($params) {
		$this->netText($params);
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