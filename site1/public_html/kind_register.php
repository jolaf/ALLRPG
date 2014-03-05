<?php
if($_SESSION["user_id"]!='') {
	if($redirectobj=='order') {
		if($redirectid>0) {
			$redirectlink=$server_absolute_path."order/act=add&subobj=".$redirectid;
			$redirectparams=redirectparamsdecode($redirectparams);
			if($redirectparams!='') {
				$redirectlink.='&'.$redirectparams;
			}
			redirect($redirectlink);
		}
		else {
			redirect($server_absolute_path."order/");
		}
	}
	elseif($redirectobj=='hosting') {
		redirect($server_absolute_path_site."hosting/act=add");
	}
	elseif($redirectobj=='hosting2') {
		redirect($server_absolute_path_site."hosting2/act=add");
	}
	elseif($redirectobj=='hosting3') {
		redirect($server_absolute_path_site."hosting3/act=add");
	}
	elseif($redirectobj=='myshops') {
		redirect($server_absolute_path_shop."myshops/act=add");
	}
	elseif($redirectobj=='pmswrite') {
		$redirectlink=$server_absolute_path."outbox/outbox/act=add";
		$redirectparams=redirectparamsdecode($redirectparams);
		if($redirectparams!='') {
			$redirectlink.='&'.$redirectparams;
		}
		redirect($redirectlink);
	}
	elseif($redirectobj=='pmsinbox') {
		redirect($server_absolute_path."inbox/");
	}
	elseif($redirectobj=='pmsoutbox') {
		redirect($server_absolute_path."outbox/");
	}
	elseif($redirectobj=='myreports') {
		redirect($server_absolute_path_info."myreports/act=add&game=".$redirectid);
	}
	elseif($redirectobj=='portfolio') {
		$redirectparams=redirectparamsdecode($redirectparams);

		$redirectlink=$server_absolute_path_calendar."portfolio/";
		if($redirectid==1) {
			$redirectlink.="subobj=future";
			if($redirectparams!='') {
				$redirectlink.='&'.$redirectparams;
			}
		}
		elseif($redirectid==2) {
			$redirectlink.="subobj=past";
			if($redirectparams!='') {
				$redirectlink.='&'.$redirectparams;
			}
		}
		elseif($redirectparams!='') {
			$redirectlink.=$redirectparams;
		}
		redirect($redirectlink);
	}
	else {
		redirect($server_absolute_path);
	}
}
else {
	// движок регистрации
	$act="add";

	// Создание объекта
	$obj=new netObj(
		'users',
		$prefix."users",
		"пользователя",
		Array("Регистрация успешно завершена. Спасибо!"),
		Array(),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	$obj_r=new netRight(
		true,
		true,
		false,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);

	// Создание полей объекта

	$obj_13=createElem(Array(
				'name'	=>	"login",
				'sname'	=>	"Логин",
				'type'	=>	"login",
				'help'	=>	"не менее 3 и не более 16 символов.",
				'minchar'	=>	3,
				'maxchar'	=>	16,
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_13);

	$obj_14=createElem(Array(
				'name'	=>	"pass",
				'sname'	=>	"Пароль",
				'type'	=>	"password",
				'help'	=>	"не менее 3 и не более 20 символов.",
				'minchar'	=>	3,
				'maxchar'	=>	20,
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_14);

	$obj_15=createElem(Array(
				'name'	=>	"pass2",
				'sname'	=>	"Повторите пароль",
				'type'	=>	"password2",
				'minchar'	=>	3,
				'maxchar'	=>	20,
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_15);

	$obj_5=createElem(Array(
				'name'	=>	"em",
				'sname'	=>	"Е-mail",
				'type'	=>	"email",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	true
			)
	);
	$obj->setElem($obj_5);

	if($redirectobj=="order") {
		$obj_8=createElem(Array(
					'name'	=>	"phone2",
					'sname'	=>	"Контактный телефон",
					'type'	=>	"text",
					'help'	=>	"требуется мастерам.",
					'read'	=>	10,
					'write'	=>	10,
					'mustbe'	=>	true,
				)
		);
		$obj->setElem($obj_8);

		$obj_2=createElem(Array(
					'name'	=>	"fio",
					'sname'	=>	"Ф.И.О.",
					'type'	=>	"text",
					'help'	=>	"требуется мастерам.",
					'read'	=>	10,
					'write'	=>	10,
					'mustbe'	=>	true
				)
		);
		$obj->setElem($obj_2);

		$obj_16=createElem(Array(
					'name'	=>	"birth",
					'sname'	=>	"Дата рождения",
					'type'	=>	"calendar",
					'default'	=>	date("Y-m-d"),
					'help'	=>	"требуется мастерам.",
					'read'	=>	10,
					'write'	=>	10,
					'mustbe'	=>	true
				)
		);
		$obj->setElem($obj_16);


		$obj_17=createElem(Array(
					'name'	=>	"city",
					'sname'	=>	"Город",
					'type'	=>	"sarissa",
					'parents'	=>	Array(Array('country','Страна'),Array('region','Регион')),
					'file'	=>	$helpers_path.'geo.php',
					'table'	=>	$prefix.'geography',
					'parent'	=>	'parent',
					'order'	=>	'name',
					'moreparams2'	=>	" and id!=2562 and parent!=2562",
					'help'	=>	"требуется мастерам.",
					'read'	=>	10,
					'write'	=>	10,
					'width'	=>	200,
					'mustbe'	=>	true
				)
		);
		$obj->setElem($obj_17);

		$obj_4=createElem(Array(
					'name'	=>	"gender",
					'sname'	=>	"Пол",
					'type'	=>	"select",
					'values'	=>	Array(Array('1','мужской'),Array('2','женский')),
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_4);

		$obj_3=createElem(Array(
					'name'	=>	"nick",
					'sname'	=>	"Никнейм",
					'type'	=>	"text",
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_3);

		$obj_12=createElem(Array(
					'name'	=>	"photo",
					'sname'	=>	"Фотография",
					'type'	=>	"file",
					'upload'	=>	4,
					'help'	=>	'не более 200*200 пикселей.',
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_12);

		$obj_18=createElem(Array(
					'name'	=>	"sickness",
					'sname'	=>	"Медицинские противопоказания",
					'type'	=>	"textarea",
					'help'	=>	"требуется мастерам. Не видно обычным пользователям.",
					'read'	=>	10,
					'write'	=>	10,
				)
		);
		$obj->setElem($obj_18);

		$obj_26=createElem(Array(
					'name'	=>	"hidesome",
					'sname'	=>	"Не публиковать",
					'type'	=>	"multiselect",
					'values'	=>	Array(Array('0','никнейм'),Array('10','ф.и.о.'),Array('1','фото'),Array('2','e-mail'),Array('5','контактный телефон')),
					'help'	=>	"проставив галочки в данном поле, Вы скроете соответствующую введенную информацию от обычных пользователей (мастера проектов, на которые Вы подадите заявки, ее увидят).",
					'read'	=>	10,
					'write'	=>	10,
					'default'	=>	'-2-',
				)
		);
		$obj->setElem($obj_26);
	}

	$obj_30=createElem(Array(
				'name'	=>	"date",
				'sname'	=>	"Последнее изменение",
				'type'	=>	"timestamp",
				'read'	=>	100,
				'write'	=>	100,
				'mustbe'	=>	true,
			)
	);
	$obj->setElem($obj_30);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction") {
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="users" && $actiontype=="add" && !$trouble)
		{
			if(encode($_POST["hash"])!='' && encode($_POST["regstamp"])!='')
			{
				$result=mysql_query("SELECT * FROM ".$prefix."regstamp WHERE hash='".encode_to_cp1251($_POST["hash"])."'");
				$a = mysql_fetch_array($result);
				if(strtoupper($a["code"])==strtoupper(encode_to_cp1251($_POST["regstamp"])))
				{
					$result=mysql_query("SELECT * FROM ".$prefix."users WHERE em='".$_POST["em"]."'");
//					$result=mysql_query("SELECT * FROM ".$prefix."users WHERE em='".iconv('UTF-8','cp1251',$_POST["em"])."'");
					$a = mysql_fetch_array($result);
					if($a["id"]=='') {
						function dynamic_add_success() {
							global
								$id,
								$_SESSION,
								$_SERVER,
								$_POST,
								$prefix,
								$server_absolute_path,
								$redirectobj,
								$redirectid,
								$redirectparams;

							if($id>0) {
								$result=mysql_query("SELECT * from ".$prefix."users order by sid desc limit 0,1");
								$a=mysql_fetch_array($result);
								$sid=$a["sid"]+1;

								mysql_query("UPDATE ".$prefix."users SET sid=".$sid." WHERE id=".$id);

								$_SESSION["user_id"]=$id;
								$_SESSION["user_sid"]=$sid;
								$_SESSION["loginsite"]=$_SERVER["SERVER_NAME"];
								setcookie("user_id", $id, time()+60*60*24*30, '/', SERVER_DOMAIN);
								setcookie("pass", md5(encode($_POST["pass"])), time()+60*60*24*30, '/', SERVER_DOMAIN);
								dynamic_err(array(),$server_absolute_path."register/redirectobj=".$redirectobj."&redirectid=".$redirectid."&redirectparams=".$redirectparams);
							}
						}
						dynamicaction($obj);
					}
					else {
						dynamic_err_one('error','На allrpg.info уже зарегистрирован пользователь с таким e-mail\'ом. Если это Вы, воспользуйтесь функцией восстановления пароля или обратитесь к администрации. Если Вы считаете, что это какая-то ошибка, обратитесь к администрации. Не следует создавать двойников. Спасибо.',array('em'));
					}
				}
				else
				{
					dynamic_err_one('error','Неверно введен регистрационный код! Попробуйте еще раз.',array('regstamp'));
				}
			}
			else
			{
				dynamic_err_one('error','Неверно введен регистрационный код! Попробуйте еще раз.',array('regstamp'));
			}
		}
	}

	$register=$obj->draw();

	$clear=time()-(60*60);
	mysql_query("DELETE FROM ".$prefix."regstamp where date<".$clear);

	$pass='';
	$salt = "abcdefghjkmnpqrstuvwxyz23456789";
	srand((double)microtime()*1000000);
	$i = 0;
	while ($i <= 5) {
		$num = rand() % 31;
		$tmp = substr($salt, $num, 1);
		$pass .= $tmp;
		$i++;
	}
	$code=$pass;
	$hash=md5($code);

	mysql_query("INSERT into ".$prefix."regstamp (code, hash, date) VALUES ('".$code."', '".$hash."', '".time()."')");

	$register2='<input type="hidden" name="hash" value="'.$hash.'" /><div class="fieldname" id="name_regstamp">Регистрационный код</div><div class="help" id="help_regstamp">введите регистрационный код, который вы видите на рисунке справа. Это сделано для предотвращения автоматической регистрации с других серверов. Регистр букв не имеет значения.</div><img src="'.$server_absolute_path.'image.php?hash='.$hash.'" style="width:200px; height:60px; float: right; margin-right: 1px;" />
<div class="fieldvalue" id="div_regstamp" style="margin-right: 220px;"><input type="text" name="regstamp" minlength="6" maxlength="6" class="inputtext mustbe" /></div><div class="clear"></div>';
	if($redirectobj!='') {
		$register2.='<input type="hidden" name="redirectobj" value="'.$redirectobj.'" />';
	}
	if($redirectid>0) {
		$register2.='<input type="hidden" name="redirectid" value="'.$redirectid.'" />';
	}
	if($redirectparams!='') {
		$register2.='<input type="hidden" name="redirectparams" value="'.$redirectparams.'" />';
	}
	$register=str_replace('<input type="hidden" name="date"',$register2.'<input type="hidden" name="date"',$register);

	$pagetitle=h1line('Регистрация');
	if($redirectobj=="order") {
		err_red("Прежде чем подать заявку, Вам необходимо зарегистрироваться или залогиниться. Большинство представленных полей требуются мастерам и будут в дальнейшем автоматически присоединяться к Вашим заявкам.");
		err_info("Вы можете скрыть большую часть своих данных от всех остальных пользователей, проставив галочки в пункте «Не публиковать».");
		err_info('После регистрации Вы можете расширить информацию о себе в «<a href="'.$server_absolute_path.'profile/">Профиле</a>», а также настроить своё <a href="'.$server_absolute_path_calendar.'portfolio/">портфолио</a> игрока/мастера.');
	}
	elseif($redirectobj!="") {
		err_red('Для данного действия необходимо зарегистрироваться. После регистрации Вы будете автоматически перенаправлены в нужный раздел.');
		err_info('После регистрации Вы можете расширить информацию о себе в «<a href="'.$server_absolute_path.'profile/">Профиле</a>», а также настроить своё <a href="'.$server_absolute_path_calendar.'portfolio/">портфолио</a> игрока/мастера. Эта информация автоматически присоединяется к Вашим заявкам.');
	}
	else {
		err_info('После регистрации Вы можете расширить информацию о себе в «<a href="'.$server_absolute_path.'profile/">Профиле</a>», а также настроить своё <a href="'.$server_absolute_path_calendar.'portfolio/">портфолио</a> игрока/мастера. Эта информация автоматически присоединяется к Вашим заявкам.');
	}

    $content2=$register;
	$content2=str_replace('Добавить пользователя','Зарегистрироваться',$content2);
	$content2=str_replace('<form','<form autocomplete="off"',$content2);
}
?>