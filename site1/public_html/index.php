<?php
# Запускаем глобальный таймер
$globaltimer=microtime(true);

# Стартуем сессию
session_start();

# Подключаем основную авторизационную и функциональную библиотеку
include_once("db.inc");

# Устанавливаем соединение с MySQL-сервером
start_mysql();

# Логинимся / выходим
if($action=="logout") {
	authlogout();
}
else {
	auth2("users", true);
}

# Подключаем библиотеки объектов, полей (hidden, timestamp, h1) и прав
require_once($server_inner_path.$direct."/classes/classes_objects.php");
require_once($server_inner_path.$direct."/classes/classes_rights.php");
require_once($server_inner_path."classes_objects_allrpg.php");

# Подключаем глобальный файл структуры и прав доступа
if(!isset($usekinds)) {
	$usekinds=0;
}
require_once("kind_list.inc");

# Запускаем обработку соответствующего запросу раздела
if(file_exists("kind_".$kind.".php")) {
	require_once("kind_".$kind.".php");
}

# Создаем тайлы навигации в зависимости от раздела
require_once($server_inner_path."tile.inc");

# Если в результате обработки контента нет, ошибка 404
if($content2=='' || $content2=='<div class="narrow"></div>') {
	redirect($server_absolute_path."error404.php");
}

# Определяем общий шаблон
if(!($kind=="orders" && $object=="orders" && $act=="view" && $id!='' && $print==1 && $history!=1)) {
	require_once($server_inner_path."template.php");
}

# Рендерим окошко логина / пользователя
require_once($server_inner_path."login.inc");

# Создаем баннеры
require_once($server_inner_path."banners.inc");

# Вносим блоки информации в заданный шаблон визуализации
$content=preg_replace('#<!--pagetitle-->#',$pagetitle,$content);
$content=preg_replace('#<!--additional_commands-->#',$additional_commands,$content);
$content=preg_replace('#<!--maincontent-->#',$content2,$content);
$content=preg_replace('#<!--menu-->#',$menu,$content);
$content=preg_replace('#<!--login-->#',$login,$content);
$content=preg_replace('#<!--ushki-->#',$banners,$content);
$error_array='<script>
errors=[];';
foreach($_SESSION['errors'] as $error) {
	$error_array.='errors.push(Array("'.$error[0].'","'.str_replace('"','\"',$error[1]).'"));';
}
unset($_SESSION['errors']);
$error_array.='</script>';
$content=preg_replace('#<!--error-->#',$error_array,$content);

# Выводим основное содержание страницы
echo $content;

# Выводим суммарное время работы скрипта
$globaltimer=number_format(microtime(true)-$globaltimer,10);
echo ('
<!-- execution time: '.$globaltimer.'s-->');

# Разрываем соединение с MySQL-сервером
stop_mysql();
?>