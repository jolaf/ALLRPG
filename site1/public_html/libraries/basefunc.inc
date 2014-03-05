<?php

$onload=Array(
	Array("kind","string","start"),
	Array("object","string",''),
	Array("id","integer",0),
	Array("page","integer",0),
	Array("action","string",''),
	Array("act","string",''),
	Array("actiontype","string",''),
	Array("valuestype","integer",0),
	Array("sorting","integer",0),
	Array("redirectobj","string",''),
	Array("redirectid","integer",0),
	Array("redirectparams","string",''),
	Array("subobj","string",""),
	Array("filter","string",''),
	Array("filter2","string",''),
	Array("filter3","string",''),
	Array("filter4","string",''),
	Array("filter5","string",''),
	Array("filter6","string",'01.01.'.date("Y")),
	Array("filter7","string",'31.12.'.date("Y")),
	Array("filter8","string",''),
	Array("what","integer",1),
	Array("site","integer",0),
	Array("rolesite","integer",0),
	Array("step","integer",2),
	Array("qwerty","string",''),
	Array("testmode","integer",0),
	Array("seeall","integer",0),
	Array("dynrequest","integer",0),
	Array("adm_user","integer",0),
);
/* Правила обработки внешних GET и POST переменных, попадающих в код сайта, сайтом
	0 - название переменной;
	1 - тип переменной;
	2 - значение по умолчанию.
*/

for($i=0;$i<count($onload);$i++) {
	unset($data);
	if(isset($_REQUEST[$onload[$i][0]])) {
		$data=$_REQUEST[$onload[$i][0]];
		if(is_array($data)) {
			foreach($data as $key=>$piece) {
				$data[$key]=encode($piece);
			}
		}
		elseif($onload[$i][1]=="string") {
			$data=encode($data);
		}
		elseif($onload[$i][1]=="integer") {
			$data=encode($data);
			settype($data, "integer");
		}
	}

	$$onload[$i][0]=!isset($data)?$onload[$i][2]:$data;
}
if($object=='') {
	$object=$kind;
}

$error='';
$curdir=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],$kind.'/'));
if($curdir=='') {
	$curdir=substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],$formfile));
}

function authlogout() {
	global
		$_SESSION,
		$cookiedomain;

	unset($_SESSION);
	setcookie("user_id", '', time()-20, '/', $cookiedomain);
	setcookie("pass", '', time()-20, '/', $cookiedomain);
	setcookie("siteid", '', time()-20, '/', $cookiedomain);
	setcookie("sitename", '', time()-20, '/', $cookiedomain);
	setcookie("shopid", '', time()-20, '/', $cookiedomain);
	setcookie("shopname", '', time()-20, '/', $cookiedomain);
	setcookie("adm", '', time()-20, '/', $cookiedomain);

	if($cookiedomain==".allrpg.info") {
		setcookie("user_id", '', time()-20, '/', 'allrpg.info');
		setcookie("pass", '', time()-20, '/', 'allrpg.info');
		setcookie("siteid", '', time()-20, '/', 'allrpg.info');
		setcookie("sitename", '', time()-20, '/', 'allrpg.info');
		setcookie("shopid", '', time()-20, '/', 'allrpg.info');
		setcookie("shopname", '', time()-20, '/', 'allrpg.info');
		setcookie("adm", '', time()-20, '/', 'allrpg.info');
	}

	session_destroy();
}
#*************************************************************
function auth2($table, $allow) {
	global
		$prefix,
		$action,
		$server_absolute_path,
		$cookiedomain,
		$_SERVER,
		$_COOKIE,
		$_SESSION,
		$_POST,
		$_GET,
		$testmode,
		$seeall,
		$curdir,
		$adm_user;

	if(!isset($_SESSION["bazecount"]) || $_SESSION["bazecount"]<=0) {
		$_SESSION["bazecount"]=50;
	}
	$blocked=false;

	if(isset($_COOKIE["user_id"]) && isset($_COOKIE["pass"])) {
		$thepass=encode($_COOKIE["pass"]);
		$result=mysql_query("SELECT * from ".$prefix.$table." where id='".encode($_COOKIE["user_id"])."'");
		$a=mysql_fetch_array($result);
		if($thepass!=$a["pass"]) {
			$blocked=true;
		}
	}
	if($action=="login" || $blocked) {
		if(isset($_POST["pass"])) {
			$thepass=md5($_POST["pass"]);
			$thepass1251=md5(iconv('UTF-8','cp1251',$_POST["pass"]));
			$result=mysql_query("SELECT * from ".$prefix.$table." where login='".$_POST["login"]."' AND (pass = '".$thepass."' OR pass = '".$thepass1251."')");
			$a=mysql_fetch_array($result);
			if($a["id"]=='') {
				$blocked=true;
			}
			else {
				$blocked=false;
			}
		}
		else {
			$blocked=true;
		}
	}
	if($blocked && $action=="login") {
		dynamic_err_one('error','Неверный логин или пароль!');
	}
	elseif(!$blocked) {
		$_SESSION["user_id"]=$a["id"];
		$_SESSION["user_sid"]=$a["sid"];
		$allrights=$a["rights"];

		$_SESSION['admin']=false;
		$_SESSION["candoarticles"]=false;
		$_SESSION["candoevents"]=false;
		$_SESSION["testmode"]=false;
		$_SESSION["seeall"]=false;

		if(preg_match('#-1-#',$allrights)) {
			$_SESSION["admin"]=true;
		}

		if(preg_match('#-3-#',$allrights)) {
			$_SESSION["candoevents"]=true;
		}

		if(preg_match('#-5-#',$allrights)) {
			$_SESSION["candoarticles"]=true;
		}

		if($a["bazecount"]>0) {
			$_SESSION["bazecount"]=$a["bazecount"];
		}
		else {
			$_SESSION["bazecount"]=50;
		}
		if($a["calendarstyle"]=='1') {
			$_SESSION["calendarstyle"]=true;
		}
		else {
			unset($_SESSION["calendarstyle"]);
		}

		if($_SESSION["admin"]) {
			if($testmode==1) {
				$_SESSION["testmode"]=true;
			}
			elseif($testmode==2) {
				unset($_SESSION["testmode"]);
			}
			if($seeall==1) {
				$_SESSION["seeall"]=true;
			}
			elseif($seeall==2) {
				unset($_SESSION["seeall"]);
			}

			if($adm_user>0) {
				$result3=mysql_query("SELECT * from ".$prefix.$table." where sid=".$adm_user);
				$c=mysql_fetch_array($result3);
				if($c["id"]!='') {
					$_SESSION["user_id"]=$c["id"];
					$_SESSION["user_sid"]=$c["sid"];
				}
			}
		}

		setcookie("user_id", $a["id"], time()+60*60*24*30, '/', $cookiedomain);
		setcookie("pass", $thepass, time()+60*60*24*30, '/', $cookiedomain);

		if($action=="login") {
			dynamic_err(array(),$curdir);
		}
	}
}
#*************************************************************
function auth($table) {
	global
		$pause,
		$allrights,
		$formfile,
		$server_absolute_path;

	if(!isset($_SERVER['PHP_AUTH_USER'])) {
		header("WWW-Authenticate: Basic realm=\"restricted area\"");
		header("HTTP/1.0 401 Unauthorized");
		echo("<html><body><script>document.location='".$server_absolute_path."action=remind';</script></body></html>");
		exit;
	}
	else
	{
		$result=mysql_query("SELECT * from ".$table." where login='".encode($_SERVER['PHP_AUTH_USER'])."'");
		$a=mysql_fetch_array($result);

		if (encode($_SERVER['PHP_AUTH_USER']) != $a["login"] || md5(encode($_SERVER['PHP_AUTH_PW'])) != $a["pass"]) {
			sleep($pause);
			header("WWW-Authenticate: Basic realm=\"restricted area\"");
			header("HTTP/1.0 401 Unauthorized");
			echo("<html><body><script>document.location='".$server_absolute_path."action=remind';</script></body></html>");
			stop_mysql();
			exit;
		}
		$allrights["rights"]=$a["rights"];
		if(strpos($a["rights"],'-1-')!==false)
		{
			$allrights["admin"]=true;
		}
		elseif(strpos($a["rights"],'-2-')!==false)
		{
			$allrights["news"]=true;
		}
		elseif(strpos($a["rights"],'-3-')!==false)
		{
			$allrights["info"]=true;
		}
	}
}
#*************************************************************
function start_mysql() {
	global
		$link,
		$dbhost,
		$dbuser,
		$dbpass,
		$dbname;

	$link = mysql_connect($dbhost,$dbuser,$dbpass);
	mysql_query("USE $dbname");
	mysql_query("SET NAMES utf8");
}
#*************************************************************
function stop_mysql() {
	global
		$link;

	mysql_close($link);
}
#*************************************************************
function delback ($st) {
	$pos = strpos($st, "\\");
	while (!($pos===false)) {
		$st1 = substr($st,0,$pos);
		$st2 = substr($st,$pos+1,strlen($st));
		$st = $st1 . $st2;
		$pos = strpos($st, "\\");
		if ($pos === false) break;
	}
	return $st;
}
#*************************************************************
function encode($str) {
	global
		$_SESSION;

	$str = str_replace("[","&open;",$str);
	$str = str_replace("]","&close;",$str);
	$str = str_replace("'","&#39",$str);
	$str = str_replace("\"","&quot;",$str);
	$str = str_replace("<br>","&br;",$str);
	$str = str_replace("\r\n","<br>",$str);
	$str = str_replace(">","&gt;",$str);
	$str = str_replace("<","&lt;",$str);
	$str = delback($str);
	return $str;
}
#*************************************************************
function decode($str) {
	$str = str_replace("&open;","[",$str);
	$str = str_replace("&close;","]",$str);
	$str = str_replace("&#39","'",$str);
	$str = str_replace("&quot;","\"",$str);
	$str = str_replace("&gt;",">",$str);
	$str = str_replace("&lt;","<",$str);
	$str = str_replace("<br>","\r\n",$str);
	$str = str_replace("&br;","<br>",$str);
	return $str;
}
#*************************************************************
function decode2($str) {
	$str = str_replace("&open;","[",$str);
	$str = str_replace("&close;","]",$str);
	$str = str_replace("&#39","'",$str);
	$str = str_replace("&quot;","\"",$str);
	$str = str_replace("&gt;",">",$str);
	$str = str_replace("&lt;","<",$str);
	$str = str_replace("&br;","<br>",$str);
	return $str;
}
#*************************************************************
function decode3($str) {
	$str = str_replace("&open;","[",$str);
	$str = str_replace("&close;","]",$str);
	$str = str_replace("<br>","\r\n",$str);
	$str = str_replace("&br;","<br>",$str);
	return $str;
}
#*************************************************************
function decodesafe($str) {
	$str = str_replace("&lt;br&gt;","<br>",$str);
	return $str;
}
#*************************************************************
function encode_to_cp1251($str) {
//	$encode=mb_detect_encoding($str,('UTF-8, WINDOWS-1251'));
//	if($encode=='UTF-8') {
//		return iconv('UTF-8','cp1251',encode($str));
//	}
//	else {
//		return encode($str);
//	}
	return($str);
}
#*************************************************************
function killspaces($str) {
	$str = str_replace(" ","",$str);
	return $str;
}
#*************************************************************
function rehash($all) {
	$all=str_replace('&','&amp;',$all);
	$all=str_replace('<','&lt;',$all);
	$all=str_replace('>','&gt;',$all);
	return $all;
}
#*************************************************************
function redirect_construct($id=0) {
	global
		$redirectobj,
		$redirectid,
		$redirectparams,
		$page,
		$sorting,
		$curdir,
		$object,
		$redirect_path;

	$path=$curdir;
	if($object!='') {
		$path.=$object.'/';
	}
	if($id>0) {
		$path.=$id.'/';
	}
	$path2='';
    if($redirectobj!='') {
    	$path2!=''?$path2.='&':false;
    	$path2.='redirectobj='.$redirectobj;
    }
    if($redirectid!='') {
    	$path2!=''?$path2.='&':false;
    	$path2.='redirectid='.$redirectid;
    }
    if($redirectparams!='') {
    	$path2!=''?$path2.='&':false;
    	$path2.='redirectparams='.$redirectparams;
    }
    if($page!='') {
    	$path2!=''?$path2.='&':false;
    	$path2.='page='.$page;
    }
    if($sorting!='') {
    	$path2!=''?$path2.='&':false;
    	$path2.='sorting='.$sorting;
    }
    $path2!=''?$path.='?'.$path2:false;
	$redirect_path=$path;
}

function err($str)
{
	global
		$_SESSION;

	$_SESSION['errors'][]=array('success',$str);
}

function err_red($str)
{
	global
		$_SESSION;

	$_SESSION['errors'][]=array('error',$str);
}

function err_info($str)
{
	global
		$_SESSION;

	$_SESSION['errors'][]=array('information',$str);
}

function dynamic_err($errors,$redirect_path='',$fields=array())
{
	global
		$_SESSION;

    $response=array();
	if($redirect_path!='') {
		// выставляем сообщения в SESSION и отсылаем json'у путь перенаправления
		foreach($errors as $error) {
			if($error[0]=='success') {
				err($error[1]);
			}
			elseif($error[0]=='error') {
				err_red($error[1]);
			}
			elseif($error[0]=='information') {
				err_info($error[1]);
			}
		}
		$response['redirect']=$redirect_path;
	}
	else {
        // отсылаем json'у список ошибок и список ошибочных полей
		foreach($errors as $error) {
			$response['errors'][]=array($error[0],$error[1]); //массив массивов типов и текстов ошибок
		}
		foreach($fields as $field) {
			$response['fields'][]=$field; //массив имен полей
		}
	}
	print_r(json_encode($response));
	stop_mysql();
	exit;
}

function dynamic_err_one($type,$error,$fields=array())
{
	dynamic_err(array(array($type,$error)),'',$fields);
}

function modal_link($kind,$text,$class='') {
	global
		$server_absolute_path;

	return '<a data-url="'.$server_absolute_path.'template_modal.php?kind='.$kind.'" class="modal-window'.($class!=''?' '.$class:'').'">'.$text.'</a>';
}
#*************************************************************
function find5field($table,$where,$what,$want)
{
	$result=mysql_query("SELECT ".$want." FROM ".$table." WHERE ".$where."=".$what);
	$a = mysql_fetch_array($result);
	return $a[$want];
}

function make5field($query,$which1,$which2)
{
	if(!is_Array($which2))
	{
		$result=mysql_query("SELECT * FROM ".$query);
		while ($a = mysql_fetch_array($result)) {
			$ok[]=Array($a[$which1],$a[$which2]);
		}
	}
	else
	{
		$result=mysql_query("SELECT * FROM ".$query);
		while ($a = mysql_fetch_array($result))
		{
			$whicher='';
			for($i=0;$i<count($which2);$i++)
			{
				if(isset($a[$which2[$i]])) {
					$whicher.=$a[$which2[$i]];
					if($i+1<count($which2)) {
						if(isset($a[$which2[$i+1]]))
						{
							$whicher.=' ';
						}
					}
				}
				else
				{
					$whicher.=$which2[$i];
				}
			}
			$ok[]=Array($a[$which1],$whicher);
		}
	}
	return $ok;
}

function make5fieldtree($empty,$table,$where,$whereequal,$and,$order,$level,$which1,$which2,$maxlevel)
{
	global
		$prefix;

	$ok = Array();

	if($empty)
	{
		$ok[]=Array('0','Верхний уровень',0);
	}
	if($level<=$maxlevel)
	{
		$query="SELECT * FROM ".$table." WHERE ".$where."=".$whereequal.$and." ORDER by ".$order;

		$result=mysql_query($query);
		while ($a = mysql_fetch_array($result))
		{
			if($a[$which1]=='')
			{
				$a[$which1]='Не определено';
			}
			if($a[$which2]=='')
			{
				$a[$which2]='Не определено';
			}
			$ok[]=Array($a[$which1],$a[$which2],$level);

			if($level+1<$maxlevel)
			{
				$ok2=make5fieldtree(false,$table,$where,$a["id"],$and,$order,($level+1),$which1,$which2,$maxlevel);

				$ok = array_merge($ok,$ok2);
			}
		}
	}

	return $ok;
}

#*************************************************************
function createElem($params) {
	global
		$server_inner_path,
		$direct;

	if($params["type"]=="multiselect")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_multiselect.php");
		return new netMultiselect($params);
	}
	elseif($params["type"]=="checkbox")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_checkbox.php");
		return new netCheckbox($params);
	}
	elseif($params["type"]=="login" || $params["type"]=="text" || $params["type"]=="email")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_text_login_email.php");
		if($params["type"]=="login")
		{
			return new netLogin($params);
		}
		elseif($params["type"]=="text")
		{
			return new netText($params);
		}
		elseif($params["type"]=="email")
		{
			return new netEmail($params);
		}
	}
	elseif($params["type"]=="password" || $params["type"]=="password2")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_password.php");
		if($params["type"]=="password")
		{
			return new netPassword($params);
		}
		elseif($params["type"]=="password2")
		{
			return new netPassword2($params);
		}
	}
	elseif($params["type"]=="number")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_number.php");
		return new netNumber($params);
	}
	elseif($params["type"]=="select")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_select.php");
		return new netSelect($params);
	}
	elseif($params["type"]=="file")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_file.php");
		return new netFile($params);
	}
	elseif($params["type"]=="sarissa")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_sarissa.php");
		return new netSarissa($params);
	}
	elseif($params["type"]=="calendar")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_calendar.php");
		return new netCalendar($params);
	}
	elseif($params["type"]=="textarea")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_textarea.php");
		return new netTextarea($params);
	}
	elseif($params["type"]=="colorpicker")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_colorpicker.php");
		return new netColorpicker($params);
	}
	elseif($params["type"]=="wysiwyg")
	{
		require_once($server_inner_path.$direct."/classes/classes_elem_wysiwyg.php");
		return new netWysiwyg($params);
	}
	elseif($params["type"]=="timestamp")
	{
		require_once($server_inner_path.$direct."/classes/classes_objects.php");
		return new netTimestamp($params);
	}
	elseif($params["type"]=="h1")
	{
		require_once($server_inner_path.$direct."/classes/classes_objects.php");
		return new netH1($params);
	}
	elseif($params["type"]=="hidden")
	{
		require_once($server_inner_path.$direct."/classes/classes_objects.php");
		return new netHidden($params);
	}
}

function redirectparamsdecode($params) {
	$params=str_replace(':','=',$params);
	$params=str_replace('*','&',$params);
	return $params;
}

function redirect($link) {
	header('Location: '.$link);
	stop_mysql();
	exit;
}

function get_real_ip() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

?>