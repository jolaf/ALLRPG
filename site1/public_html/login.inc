<?php
if($kind=="start" && $itsthemainpage) {
	$login='<div class="login" style="visibility: hidden"></div>';
}
else {
	if($_SESSION["user_id"]!='')
	{
		$result=mysql_query("SELECT * from ".$prefix."users where id=".$_SESSION["user_id"]);
		$a=mysql_fetch_array($result);
		if($a["photo"]=='' || !file_exists($server_inner_path.$uploads[4]['path'].$a["photo"])) {
			$photo=$server_absolute_path.'identicon.php?hash='.md5(md5($a["em"]).'cetb').'&size=100';
		}
		else {
			$photo=$server_absolute_path.$uploads[4]['path'].$a["photo"];
		}
		$login='<div class="login" id="login"><div class="text">'.usname2($a,true).'</div>';
		$result=mysql_query("SELECT COUNT(id) from ".$prefix."blog_pms where user_id=".$_SESSION["user_id"]." and pmread='0'");
		$a=mysql_fetch_array($result);
        if($a[0]>0) {
			$login.='<div class="messages_count">новые сообщения: <b>'.$a[0].'</b></div>';
        }
		$login.='
<div class="login_options">
<a href="'.$server_absolute_path_info.'users/'.$_SESSION["user_sid"].'/"><img src="'.$photo.'"/></a>
<a href="'.$server_absolute_path_info.'users/'.$_SESSION["user_sid"].'/">профиль</a><br>
<a href="'.$server_absolute_path.'inbox/">сообщения: <b>'.$a[0].'</b></a><br>
<a href="'.$curdir.$kind.'/action=logout">разлогиниться</a>
</div>
</div>
';
	}
	else
	{
		$login='<div class="login" id="login"><div class="text">Войти/регистрация</div>
<div class="login_options big">
<form action="'.$curdir.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="object" value="'.$object.'" />
<input type="hidden" name="id" value="'.$id.'" />
<input type="hidden" name="subobj" value="'.$subobj.'" />
<input type="hidden" name="action" value="login" />';
	    if($redirectobj!='') {
			$login.='<input type="hidden" name="redirectobj" value="'.$redirectobj.'" />';
		}
		if($redirectid>0) {
			$login.='<input type="hidden" name="redirectid" value="'.$redirectid.'" />';
		}
		if($redirectparams!='') {
			$login.='<input type="hidden" name="redirectparams" value="'.$redirectparams.'" />';
		}
		$login.='
<input type="text" name="login" id="login_global" placehold="Логин" /><br>
<input type="password" name="pass" id="pass_global" placehold="Пароль" /><br>
<button class="main" id="btn_login_global">войти</button><button class="nonimportant" id="btn_remind_global">забыл</button><button class="nonimportant" id="btn_register_global"><a href="'.$server_absolute_path.'register/">регистрация</a></button>
</form>
</div>
<div class="login_options big remind">
<form action="'.$server_absolute_path.'" method="post" enctype="multipart/form-data" id="remind_form_global">
<input type="hidden" name="action" value="remind" />
<input type="text" id="em_global" name="em" placehold="Ваш е-mail" /><br>
<button class="main" id="btn_make_remind_global">восстановить</button>
</form>
</div>
</div>
';
	}
}
?>