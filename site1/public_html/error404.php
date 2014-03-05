<?php
   header("HTTP/1.0 404 Not Found");
   header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
   header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
   header("Cache-Control: no-cache, must-revalidate");
   header("Pragma: no-cache");

   include_once("db.inc");

   print('<html>
<title>404 - не найдено</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="'.$server_absolute_path.'main_new.css" rev="contents" type="text/css">
<body><table width="100%" height="100%" border=0><tr><td style="text-align:center; color: white; vertical-align: middle;"><h1 style="color: white">404</h1>Запрашиваемая страница не найдена!<br>
<a href="http://www.allrpg.info" style="color: white;border-bottom: 1px dashed;">Перейти на основную страницу</a></td></tr></table></body></html>');
?>