<?php

class netWysiwyg extends netBaseElem {
	var $css; //пользовательский набор классов в формате CSS
	var $minchar; //минимальное количество символов
	var $maxchar; //минимальное количество символов

	function setCss($css) {
		$this->css=$css;
	}

	function setMinchar($minchar) {
		$this->minchar=$minchar;
	}

	function setMaxchar($maxchar) {
		$this->maxchar=$maxchar;
	}

	function getCss() {
		return($this->css);
	}

	function getMinchar() {
		return($this->minchar);
	}

	function getMaxchar() {
		return($this->maxchar);
	}

	function netWysiwyg($params) {
		$this->netBaseElem($params);
		$this->setCss($params["css"]);
	}

	function draw($type,$can,$linenum,$sBasePath) {
		if($can=="write")
		{
			$content.=$this->trueDraw($linenum);
		}
		else
		{
			$content.=$this->getVal();
		}
		return($content);
	}

	function trueDraw($linenum,$sBasePath) {
		$value=$this->getVal();

		if(isset($linenum))
		{
			$linenum+=0;
			$linenum='['.$linenum.']';
		}
		else
		{
			$linenum='';
		}

  		//$content='<textarea cols="80" id="'.$this->getName().$linenum.'" name="'.$this->getName().$linenum.'" rows="10">'.$value.'</textarea>
//';
		$content='<div id="'.$this->getName().$linenum.'-toolbar">
      <header>
        <ul class="commands">
          <li data-wysihtml5-command="bold" title="Выделить жирным (CTRL + B)" class="command"></li>
          <li data-wysihtml5-command="italic" title="Выделить наклонным (CTRL + I)" class="command"></li>
          <li data-wysihtml5-command="insertUnorderedList" title="Маркированный список" class="command"></li>
          <li data-wysihtml5-command="insertOrderedList" title="Нумерованный список" class="command"></li>
          <li data-wysihtml5-command="createLink" title="Вставить ссылку" class="command"></li>
          <li data-wysihtml5-command="insertImage" title="Вставить изображение" class="command"></li>
          <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Заголовок 1" class="command"></li>
          <li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Заголовок 2" class="command"></li>
          <li data-wysihtml5-command-group="foreColor" class="fore-color" title="Цвет текста" class="command">
            <ul>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy"></li>
              <li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue"></li>
            </ul>
          </li>
          <li data-wysihtml5-action="change_view" title="В виде HTML" class="action"></li>
        </ul>
      </header>
      <div data-wysihtml5-dialog="createLink" style="display: none;">
        <label>
          Ссылка:
          <input data-wysihtml5-dialog-field="href" value="http://">
        </label>
        <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Отмена</a>
      </div>

      <div data-wysihtml5-dialog="insertImage" style="display: none;">
        <label>
          Изображение:
          <input data-wysihtml5-dialog-field="src" value="http://">
        </label>
        <a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Отмена</a>
      </div>
    </div>

    <section'.($this->getMustBe()?' class="mustbe"':'').'>
      <textarea class="wysihtml5-editor'.($this->getMustBe()?' mustbe':'').'" name="'.$this->getName().$linenum.'" id="'.$this->getName().$linenum.'" spellcheck="false" style="height:'.(preg_match('#px#',$this->getHeight())?$this->getHeight():$this->getHeight().'px').'">'.$value.'</textarea>
    </section>
';

		return($content);
	}

	function destroy() {
		unset($this);
	}
}

?>