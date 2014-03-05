<?php
session_start();

include_once("db.inc");

if($kind!='') {
	start_mysql();
	# Установление соединения с MySQL-сервером

	auth2("users", true);
	# Авторизация

	// Подключение библиотеки объектов, полей (hidden, timestamp, h1) и прав
	require_once($server_inner_path.$direct."/classes/classes_objects.php");
	require_once($server_inner_path.$direct."/classes/classes_rights.php");
	require_once($server_inner_path."classes_objects_allrpg.php");

	$curdir=$server_absolute_path;
	require_once($server_inner_path."kind_".$kind.".php");

	$content2='<div class="modal-title">'.$pagetitle.'</div><div class="modal-content">'.$content2.'</div>';

	$init='<script>
errors=[];
';
	foreach($_SESSION['errors'] as $error) {
		$init.='errors.push(Array("'.$error[0].'","'.str_replace('"','\"',$error[1]).'"));
';
	}
	unset($_SESSION['errors']);
	$init.='
show_errors();
$("button").attr("type","button");
$("button").button();
</script>';

	$content2.=$init;

	print($content2);
	stop_mysql();
}
?>