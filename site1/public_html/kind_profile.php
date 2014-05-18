<?php

require_once ($server_inner_path."appcode/possible_values.php");

if($_SESSION["user_id"]=="") {
	if($action=="setbazecount") {
		$_SESSION["bazecount"]=encode($_POST["bazecount"]);
		err("Установки успешно сохранены.");
	}

	if($_SESSION["bazecount"]!='') {
		$bazecount=$_SESSION["bazecount"];
	}
	else {
		$bazecount=50;
	}

	$pagetitle=h1line('Мой профиль');
	err('<a href="'.$server_absolute_path.'register/">Зарегистрируйтесь</a> или залогиньтесь для того, чтобы полноценно управлять вашим профилем.');
	$content2.='<form action="'.$server_absolute_path.$kind.'/" method="post" enctype="multipart/form-data">
<input type="hidden" name="kind" value="profile">
<input type="hidden" name="object" value="profile">
<input type="hidden" name="action" value="setbazecount">
<center><div class="cb_editor"><!-- start users object -->
<div class="fieldname" id="name_bazecount">Количество записей в инфотеке</div><div class="help" id="help_bazecount">сколько записей показывать на одной странице в <a href="'.$server_absolute_path_info.'">инфотеке</a>?</div>
<div class="fieldvalue" id="div_bazecount"><input type="text" name="bazecount" value="'.$bazecount.'" class="inputnum"></div>
<div class="clear"></div>
</div>
<button class="main">сохранить установки</button>
</form>
';
}
else
{
	//мой профиль

	$act='view';
	$id=$_SESSION["user_id"];

	// Создание объекта
	$obj=new netObj(
		'profile',
		$prefix."users",
		"профиль",
		Array("","Профиль успешно изменен.","Профиль успешно удален."),
		Array(
			'0'=>Array(
				Array("sid", "DESC", true, true),
			)
		),
		2,
		'100%',
		50
	);

	// Создание схемы прав объекта
	if($_SESSION["user_id"]!='')
	{
		$obj_r=new netRight(
			true,
			false,
			true,
			true,
			100,
			'id='.$_SESSION["user_id"],
			'id='.$_SESSION["user_id"],
			'id='.$_SESSION["user_id"]
		);
		$obj->setRight($obj_r);
	}

	// Создание полей объекта
	$obj_1=createElem(Array(
				'name'	=>	"sid",
				'sname'	=>	"Ваш ИНП",
				'help'	=>	'идентификационный номер профиля пользователя',
				'type'	=>	"number",
				'read'	=>	10,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_1);

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

    if($redirectobj=='order') {
    	$profilemust=true;
    }
    else {
    	$profilemust=false;
    }

	$obj_8=createElem(Array(
				'name'	=>	"phone2",
				'sname'	=>	"Контактный телефон",
				'type'	=>	"text",
				'help'	=>	"требуется мастерам. Автоматически присоединяется к заявкам.",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	$profilemust,
			)
	);
	$obj->setElem($obj_8);

	$obj_2=createElem(Array(
				'name'	=>	"fio",
				'sname'	=>	"Ф.И.О.",
				'type'	=>	"text",
				'help'	=>	"требуется мастерам. Автоматически присоединяется к заявкам.",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	$profilemust,
			)
	);
	$obj->setElem($obj_2);

	$obj_16=createElem(Array(
				'name'	=>	"birth",
				'sname'	=>	"Дата рождения",
				'type'	=>	"calendar",
				'default'	=>	date("Y-m-d"),
				'help'	=>	"требуется мастерам. Автоматически присоединяется к заявкам.",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	$profilemust,
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
				'help'	=>	"требуется мастерам. Автоматически присоединяется к заявкам.",
				'read'	=>	10,
				'write'	=>	10,
				'mustbe'	=>	$profilemust,
			)
	);
	$obj->setElem($obj_17);

    $obj_3=createElem(Array(
				'name'	=>	"nick",
				'sname'	=>	"Никнейм",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_3);

	$obj_4=createElem(Array(
				'name'	=>	"gender",
				'sname'	=>	"Пол",
				'type'	=>	"select",
				'values'	=>	get_possible_values ('gender'),
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_4);
	
	$obj->setElem(createElem(Array(
				'name'	=>	"medic",
				'sname'	=>	"Медицинская квалификация",
				'type'	=>	"select",
				'values'	=>	get_possible_values ('medic'),
				'help'	=>	'Указание медицинской квалификации в профиле позволит мастерам обратиться за помощью в экстренных случаях. Это актуально как на маленьких играх (где может не быть выделенного медика), так и на больших (где медик не всегда может быть в прямом доступе).',
				'read'	=>	10,
				'write'	=>	10,
			)
	));

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
				'read'	=>	10,
				'write'	=>	10,
				'help'	=>	"требуется мастерам. Автоматически присоединяется к заявкам. Не видно обычным пользователям.",
			)
	);
	$obj->setElem($obj_18);

	$obj_6=createElem(Array(
				'name'	=>	"em2",
				'sname'	=>	"Дополнительный е-mail",
				'type'	=>	"email",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_6);

	$obj_9=createElem(Array(
				'name'	=>	"icq",
				'sname'	=>	"ICQ",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_9);

	$obj_10=createElem(Array(
				'name'	=>	"skype",
				'sname'	=>	"Skype",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_10);

	$obj_11=createElem(Array(
				'name'	=>	"jabber",
				'sname'	=>	"Jabber",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_11);

	$obj_36=createElem(Array(
				'name'	=>	"vkontakte",
				'sname'	=>	"ВКонтакте",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_36);

	$obj_37=createElem(Array(
				'name'	=>	"livejournal",
				'sname'	=>	"Живой Журнал",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_37);

	$obj_33=createElem(Array(
				'name'	=>	"facebook",
				'sname'	=>	"Facebook",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_33);

	$obj_34=createElem(Array(
				'name'	=>	"googleplus",
				'sname'	=>	"Google+",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_34);

	$obj_35=createElem(Array(
				'name'	=>	"tweeter",
				'sname'	=>	"Twitter",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_35);

	$obj_19=createElem(Array(
				'name'	=>	"ingroup",
				'sname'	=>	"Состою в мастерской группе",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_19);

	$obj_20=createElem(Array(
				'name'	=>	"prefer",
				'sname'	=>	"Предпочитаемые жанры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_20);

	$obj_21=createElem(Array(
				'name'	=>	"prefer2",
				'sname'	=>	"Предпочитаемые типы игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=2 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_21);

	$obj_22=createElem(Array(
				'name'	=>	"prefer3",
				'sname'	=>	"Предпочитаемые миры игр",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gameworlds order by name","id","name"),
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_22);

	$obj_23=createElem(Array(
				'name'	=>	"prefer4",
				'sname'	=>	"Дополнительные предпочтения",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","name"),
				'images'	=>	make5field($prefix."gametypes where tipe=3 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[6]['path'],
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_23);

	$obj_24=createElem(Array(
				'name'	=>	"specializ",
				'sname'	=>	"Основная специализация на играх",
				'type'	=>	"multiselect",
				'values'	=>	make5field($prefix."specializ where gr=1 order by name","id","name"),
				'images'	=>	make5field($prefix."specializ where gr=1 order by name","id","im"),
				'path'	=>	$server_absolute_path.$uploads[7]['path'],
				'read'	=>	10,
				'write'	=>	100,
			)
	);
	$obj->setElem($obj_24);

	$obj_25=createElem(Array(
				'name'	=>	"additional",
				'sname'	=>	"Дополнительная информация",
				'type'	=>	"textarea",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_25);

	$obj_26=createElem(Array(
				'name'	=>	"hidesome",
				'sname'	=>	"Скрыть в моем профиле следующие данные",
				'type'	=>	"multiselect",
				'values'	=>	Array(Array('0','никнейм'),Array('10','ф.и.о.'),Array('1','фото'),Array('2','основной e-mail'),Array('3','дополнительный e-mail'),Array('5','контактный телефон'),Array('6','ICQ'),Array('7','Skype'),Array('8','Jabber'),Array('9','медицинские противопоказания')),
				'read'	=>	10,
				'write'	=>	10,
				'default'	=>	'-2-',
			)
	);
	$obj->setElem($obj_26);

	$obj_31=createElem(Array(
				'name'	=>	"bazecount",
				'sname'	=>	"Количество записей в инфотеке",
				'help'	=>	'сколько записей показывать на одной странице в <a href="'.$server_absolute_path_info.'">инфотеке</a>?',
				'type'	=>	"number",
				'default'	=>	50,
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_31);

	/*$obj_32=createElem(Array(
				'name'	=>	"blogforum",
				'sname'	=>	"Показывать блоги в виде форумов",
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	10,
			)
	);
	$obj->setElem($obj_32);*/

	$obj_29=createElem(Array(
				'name'	=>	"rights",
				'sname'	=>	"Права",
				'type'	=>	"multiselect",
				'values'	=>	Array(Array('1','Вы администратор allrpg.info'),Array('2','Вы автор новостей allrpg.info'),Array('3','Вы можете управлять информацией allrpg.info'),Array('5','Вы можете добавлять статьи в инфотеку')),
				'read'	=>	100,
				'write'	=>	100000,
			)
	);
	$obj->setElem($obj_29);

	$obj_30=createElem(Array(
				'name'	=>	"date",
				'sname'	=>	"Последнее изменение",
				'type'	=>	"timestamp",
				'read'	=>	100,
				'write'	=>	100,
				'mustbe'	=>	true,
				'show'	=>	false,
			)
	);
	$obj->setElem($obj_30);

	// Исполнение dynamicaction, если необходимо
	if($action=="dynamicaction")
	{
		require_once($server_inner_path.$direct."/dynamicaction.php");
		if($object=="profile")
		{
			if($actiontype=="change" || $actiontype=="add") {
				$_POST["tweeter"]=social($_POST["tweeter"]);
				$_POST["googleplus"]=social($_POST["googleplus"]);
				$_POST["livejournal"]=social($_POST["livejournal"]);
				$_POST["facebook"]=social($_POST["facebook"]);
				$_POST["vkontakte"]=social($_POST["vkontakte"]);
			}
			if($actiontype=="change")
			{
				function dynamic_save_success() {
					global
						$redirectobj,
						$redirectid,
						$redirectparams,
						$server_absolute_path,
						$id,
						$prefix,
						$cookiedomain,
						$_SESSION;

					$_SESSION["bazecount"]=encode($_POST["bazecount"]);

                    if(isset($_POST["pass"]) && $_POST["pass"]!='') {
						$thepass=md5(encode($_POST["pass"]));
						setcookie("pass", $thepass, time()+60*60*24*30, '/', $cookiedomain);
					}

					$result=mysql_query("SELECT * from ".$prefix."users where id=".$id);
					$a=mysql_fetch_array($result);
					if($redirectobj=='order') {
						if($a["phone2"]!='' && $a["fio"]!='' && $a["city"]>0 && $a["birth"]!='0000-00-00') {
							if($redirectobj=='order') {
								if($redirectid>0) {
									$redirectlink=$server_absolute_path."order/act=add&subobj=".$redirectid;
									$redirectparams=redirectparamsdecode($redirectparams);
									if($redirectparams!='') {
										$redirectlink.='&'.$redirectparams;
									}
									dynamic_err(array(),$redirectlink);
								}
								else {
									dynamic_err(array(),$server_absolute_path."order/");
								}
							}
						}
					}
				}

				$result2=mysql_query("SELECT * FROM ".$prefix."users WHERE id=".$id);
				$b = mysql_fetch_array($result2);
				if(encode_to_cp1251($_POST["em"])!=$b["em"]) {
					$result=mysql_query("SELECT * FROM ".$prefix."users WHERE em='".encode($_POST["em"])."' AND id!=".$id);
					$a = mysql_fetch_array($result);
					if($a["id"]=='') {
						dynamicaction($obj);
					}
					else {
						$trouble=true;
						err_red('На allrpg.info уже зарегистрирован пользователь с таким e-mail\'ом. Если вы считаете, что это какая-то ошибка, обратитесь к администрации. Не следует создавать двойников. Спасибо.');
					}
				}
				else {
					dynamicaction($obj);
				}
			}
			elseif($actiontype=="delete" && (encode($_POST["ill"])!='' || encode($_GET["ill"])!='')) {
				dynamicaction($obj);
			}
			elseif($actiontype=="delete" && encode($_POST["ill"])=='' && encode($_GET["ill"])=='')
			{
				function dynamic_delete_success() {
					redirect($server_absolute_path."action=logout");
				}

				$sid=$_SESSION["user_sid"];

				$result=mysql_query("SELECT * from ".$prefix."roles where player_id=".$id);
				while($a=mysql_fetch_array($result))
				{
					mysql_query("DELETE from ".$prefix."roleshistory where role_id=".$a["id"]);
					mysql_query("DELETE from ".$prefix."roles where id=".$a["id"]);
				}

				mysql_query("DELETE from ".$prefix."virtrights where user_id=".$sid);

				mysql_query("DELETE from ".$prefix."allrights2 where user_id=".$sid);

				mysql_query("DELETE from ".$prefix."reports where user_id=".$id);

				mysql_query("UPDATE ".$prefix."areas set user_id='' where user_id=".$sid);

				mysql_query("DELETE from ".$prefix."allgames where user_id=".$sid);

				mysql_query("UPDATE ".$prefix."allgames set sid='' where sid=".$sid);

				mysql_query("DELETE from ".$prefix."played where user_id=".$id);

				mysql_query("DELETE from ".$prefix."comments where user_id=".$id);

				mysql_query("DELETE from ".$prefix."comments where whom=".$id);

				dynamicaction($obj);
			}
		}
	}

	$stayhere=true;

	// Добавление параметра values к select'ам и multiselect'ам.

	// Инициализация элементов поиска, если нужен.

	// Отрисовка всего объекта html'ем в переменную
	$obj_html.=$obj->draw();

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Мой профиль');
	if($redirectobj!='') {
		$redirect.='<input type="hidden" name="redirectobj" value="'.$redirectobj.'">';
		if($redirectobj=='order') {
			err_red("Прежде чем подать заявку, Вам необходимо заполнить поля, требующиеся мастерам: «Контактный телефон», «Город», «Дату рождения» и «Ф.И.О.». Эти поля будут в дальнейшем автоматически присоединяться к Вашим заявкам.");
			err_info("Вы можете скрыть большую часть своих данных от всех остальных пользователей, проставив галочки в пункте «Не публиковать».");
		}
	}
	if($redirectid>0) {
		$redirect.='<input type="hidden" name="redirectid" value="'.$redirectid.'">';
	}
	if($redirectparams!='') {
		$redirect.='<input type="hidden" name="redirectparams" value="'.$redirectparams.'" />';
	}
	$obj_html=str_replace('<input type="hidden" name="kind" value="profile" />',$redirect.'<input type="hidden" name="kind" value="profile" />',$obj_html);

	err('Ваш профиль в инфотеке выглядит <a href="'.$server_absolute_path_info.'users/'.$_SESSION["user_sid"].'/">так</a>.');
	$result=mysql_query("SELECT COUNT(id) from ".$prefix."played where user_id=".$_SESSION["user_id"]);
	$a=mysql_fetch_array($result);
	if($a[0]==0) {
    	err('Не забудьте <a href="'.$server_absolute_path_calendar.'portfolio/subobj=past">заполнить Ваше ролевое портфолио</a> и <a href="'.$server_absolute_path_calendar.'portfolio/subobj=future">настроить Ваш ролевой календарь</a>.');
	}
	$content2.='<div class="narrow">'.$obj_html.'</div>';
	$content2=str_replace('<form','<form autocomplete="off"',$content2);
}
?>