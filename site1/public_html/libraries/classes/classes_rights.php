<?php

class netRight {
	var $view; //право видеть данные
	var $add; //право добавлять данные
	var $change; //право менять данные
	var $delete; //право удалять данные
	var $rights; //общий уровень прав на объект (должен быть больше значения write переменной, чтобы в нее можно было писать)
	var $viewrestrict; //SQL-ограничение на просмотр данных
	var $changerestrict; //SQL-ограничение на изменение данных
	var $deleterestrict; //SQL-ограничение на удаление данных

	function setView($view) {
		$this->view=$view;
	}

	function setAdd($add) {
		$this->add=$add;
	}
	
	function setChange($change) {
		$this->change=$change;
	}

	function setDelete($delete) {
		$this->delete=$delete;
	}

	function setRights($rights) {
		$this->rights=$rights;
	}

	function setViewrestrict($viewrestrict) {
		$this->viewrestrict=$viewrestrict;
	}

	function setChangerestrict($changerestrict) {
		$this->changerestrict=$changerestrict;
	}

	function setDeleterestrict($deleterestrict) {
		$this->deleterestrict=$deleterestrict;
	}

	function getView() {
		return($this->view);
	}

	function getAdd() {
		return($this->add);
	}
	
	function getChange() {
		return($this->change);
	}

	function getDelete() {
		return($this->delete);
	}

	function getRights() {
		return($this->rights);
	}

	function getViewRestrict() {
		return($this->viewrestrict);
	}

	function getChangeRestrict() {
		return($this->changerestrict);
	}

	function getDeleteRestrict() {
		return($this->deleterestrict);
	}

	function netRight($view,$add,$change,$delete,$rights,$viewrestrict,$changerestrict,$deleterestrict) {
		if($add || $change || $delete)
		{
			$view=true;
		}
		$this->setView($view);
		$this->setAdd($add);
		$this->setChange($change);
		$this->setDelete($delete);
		$this->setRights($rights);
		$this->setViewrestrict($viewrestrict);
		$this->setChangerestrict($changerestrict);
		$this->setDeleterestrict($deleterestrict);
	}
}

?>