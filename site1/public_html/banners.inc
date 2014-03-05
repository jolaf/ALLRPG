<?php
$i=0;
$result=mysql_query("SELECT * FROM ".$prefix."ushki WHERE active='1' order by RAND() limit 0,10");
while ($a = mysql_fetch_array($result)) {
	$i++;
	if(substr($a["img"],strlen($a["img"])-4,strlen($a["img"]))=='.swf') {
		$swfname=substr($a["img"],0,strlen($a["img"])-4);
		$banners.="
	<script language=\"JavaScript\" type=\"text/javascript\">
	AC_FL_RunContent(
		'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0',
		'width', '130',
		'height', '66',
		'src', '".$server_absolute_path.$uploads[2]['path'].$swfname."',
		'quality', 'high',
		'pluginspage', 'http://www.adobe.com/go/getflashplayer',
		'align', 'middle',
		'play', 'true',
		'loop', 'true',
		'scale', 'showall',
		'wmode', 'window',
		'devicefont', 'false',
		'id', 'banner',
		'bgcolor', '',
		'name', 'banner',
		'menu', 'true',
		'allowFullScreen', 'false',
		'allowScriptAccess','sameDomain',
		'movie', '".$server_absolute_path.$uploads[2]['path'].$swfname."',
		'salign', ''
		); //end AC code
	</script>";
	}
	else {
		$banners.='<a href="'.$a["link"].'"><img src="'.$server_absolute_path.$uploads[2]['path'].$a["img"].'" title="'.decode($a["name"]).'"></a>';
	}
	if($i==5) {
		$banners.='</div>';
	}
}
?>