<?php

// Создание объекта
$obj=new netObj(
	'templates',
	$prefix."temps",
	"шаблон дизайна",
	Array("Шаблон дизайна успешно добавлен.","Шаблон дизайна успешно изменен.","Шаблон дизайна успешно удален."),
	Array(
		'0'=>Array(
			Array("name", "ASC", true, true),
			Array("date", "ASC", true, true)
		)
	),
	2,
	645,
	50
);

// Создание схемы прав объекта
if($allrights["admin"])
{
	$obj_r=new netRight(
		true,
		true,
		true,
		true,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}
else
{
	$obj_r=new netRight(
		false,
		false,
		false,
		false,
		100,
		'',
		'',
		''
	);
	$obj->setRight($obj_r);
}


// Создание полей объекта
$obj_1=createElem(Array(
			'name'	=>	"name",
			'sname'	=>	"Название шаблона",
			'type'	=>	"text",
			'help'	=>	"не более 255 символов",
			'default'	=>	"Шаблон №",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_1);

$obj_2=createElem(Array(
			'name'	=>	"descr",
			'sname'	=>	"Описание шаблона",
			'type'	=>	"wysiwyg",
			'height'	=>	400,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_2);

$obj_3=createElem(Array(
			'name'	=>	"htmlcode",
			'sname'	=>	"HTML-код шаблона",
			'type'	=>	"textarea",
			'rows'	=>	30,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_3);

$obj_4=createElem(Array(
			'name'	=>	"css",
			'sname'	=>	"Переменные шаблона и их значения по умолчанию",
			'type'	=>	"textarea",
			'help'	=>	"<u>При создании шаблона можно не вводить. Система автоматически выберет необходимые переменные из предложенного ей HTML-кода шаблона</u>.<br>Формат: <b>[тип][название переменной][значение по умолчанию][описание переменной]</b>.<br>Пример: <b>[2][bgpict_body][forfiles/logo.jpg][Изображение, устанавливаемое в качестве основного бэкграунда всей страницы.]</b><br>Тип переменной \"1\" присваивается переменным (например, для атрибута \"width\").<br>Тип \"2\" присваивается файлам (например, для тега \"img\").<br>Тип \"3\" присваивается переменным цвета (например, для атрибута \"color\" тега \"font\").",
			'rows'	=>	30,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_4);

$obj_5=createElem(Array(
			'name'	=>	"usercss",
			'sname'	=>	"Базовый CSS шаблона",
			'type'	=>	"textarea",
			'rows'	=>	10,
			'read'	=>	10,
			'write'	=>	100,
		)
);
$obj->setElem($obj_5);

$obj_6=createElem(Array(
			'name'	=>	"menualign",
			'sname'	=>	"Расположение меню",
			'type'	=>	"select",
			'help'	=>	"как выстраивать пункты меню: вертикально или горизонтально?",
			'values'	=>	Array(Array('1','вертикально'),Array('2','горизонтально')),
			'default'	=>	'1',
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_6);

$obj_7=createElem(Array(
			'name'	=>	"separkind",
			'sname'	=>	"Разделитель для пунктов меню",
			'type'	=>	"text",
			'help'	=>	"разделитель используется между текстовыми пунктами меню, если выбран горизонтальный вариант построения меню. По умолчанию используется следующий разделитель: | (с пробелами до и после разделителя).",
			'default'	=>	" | ",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem($obj_7);

$obj_8=createElem(Array(
			'name'	=>	"submenualign",
			'sname'	=>	"Расположение подменю",
			'type'	=>	"select",
			'help'	=>	"как выстраивать пункты подменю: вертикально или горизонтально?",
			'values'	=>	Array(Array('1','вертикально'),Array('2','горизонтально')),
			'default'	=>	'1',
			'read'	=>	10,
			'write'	=>	10,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_8);

$obj_9=createElem(Array(
			'name'	=>	"separsub",
			'sname'	=>	"Разделитель для пунктов подменю",
			'type'	=>	"text",
			'help'	=>	"разделитель используется между текстовыми пунктами подменю, если выбран горизонтальный вариант построения подменю. По умолчанию используется следующий разделитель: | (с пробелами до и после разделителя).",
			'default'	=>	" | ",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem($obj_9);

$obj_10=createElem(Array(
			'name'	=>	"newsformat1",
			'sname'	=>	"Конструктор внешнего вида новостной ленты",
			'type'	=>	"textarea",
			'help'	=>	"здесь можно сформировать представление каждой новости в общей новостной ленте. Кроме html-тегов, можно использовать следующие команды (автоматически заменяются системой сайта на те или иные данные):<ul><li class=\"sm2\"><b>&lt;!--linkstart--&gt;</b> – открывающий тег ссылки на подробный текст новости (ссылка сформируется, если у новости есть не только общий, но и подробный текст); <li class=\"sm2\"><b>&lt;!--linkfinish--&gt;</b> – закрывающий тег ссылки на подробный текст новости (ссылка сформируется, если у новости есть не только общий, но и подробный текст); <li class=\"sm2\"><b>&lt;!--date--&gt;</b> – дата новости; <li class=\"sm2\"><b>&lt;!--name--&gt;</b> – название новости; <li class=\"sm2\"><b>&lt;!--text--&gt;</b> – текст новости вкратце; <li class=\"sm2\"><b>&lt;!--moreinfo--&gt;</b> – автоматическая ссылка «Подробнее...» (формируется, если у новости есть не только общий, но и подробный текст); <li class=\"sm2\"><b>&lt;!--author--&gt;</b> – ник автора новости; <li class=\"sm2\"><b>&lt;!--source--&gt;</b> – источник новости.</ul>",
			'rows'	=>	10,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem($obj_10);

$obj_11=createElem(Array(
			'name'	=>	"newsformat2",
			'sname'	=>	"Конструктор внешнего вида новости",
			'type'	=>	"textarea",
			'help'	=>	"здесь можно сформировать представление раскрытой новости. Кроме html-тегов, можно использовать следующие команды (автоматически заменяются системой сайта на те или иные данные):<ul><li class=\"sm2\"><b>&lt;!--date--&gt;</b> – дата новости; <li class=\"sm2\"><b>&lt;!--name--&gt;</b> – название новости; <li class=\"sm2\"><b>&lt;!--text--&gt;</b> – текст новости полностью; <li class=\"sm2\"><b>&lt;!--author--&gt;</b> – ник автора новости; <li class=\"sm2\"><b>&lt;!--source--&gt;</b> – источник новости.</ul>",
			'rows'	=>	10,
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem($obj_11);

$obj_12=createElem(Array(
			'name'	=>	"separ",
			'sname'	=>	"Разделитель для «путеводителя»",
			'type'	=>	"text",
			'help'	=>	"путеводитель – набор последовательных ссылок, первая из которых ведет на родительский раздел, вторая – на родительский подраздел, третья – на конкретную страницу. По умолчанию используется следующий разделитель: « &#150;&#187; » (с пробелами до и после разделителя).",
			'default'	=>	" &#150;&#187; ",
			'read'	=>	10,
			'write'	=>	100,
			'mustbe'	=>	true,
		)
);
$obj->setElem($obj_12);

$obj_13=createElem(Array(
			'name'	=>	"date",
			'sname'	=>	"Последнее изменение",
			'type'	=>	"timestamp",
			'read'	=>	100,
			'write'	=>	100,
			'mustbe'	=>	true
		)
);
$obj->setElem($obj_13);

// Исполнение dynamicaction, если необходимо
if($action=="dynamicaction")
{
	require_once($server_inner_path.$direct."/dynamicaction.php");
	if($object=="templates")
	{
		dynamicaction($obj);
	}
}

// Исполнение дополнительных действий после dynamicaction, если необходимо
if(!$trouble && count($trouble2)==0)
{
	if($object=="templates")
	{
		if($actiontype=="add" || $actiontype=="change")
		{
			if($_POST['css']=='')
			{
				$j=$_POST['htmlcode'];
				$pos = strpos($j, "<!--");
				while (!($pos===false)) {
					$pos2 = strpos($j, "-->");
					if(substr($j,$pos+4,1)!=' ' && substr($j,$pos+4,1)!='/')
					{
						$d=substr($j,$pos+4,$pos2-$pos-4);
						if(eregi('pict',$d))
						{
							$type=2;
						}
						elseif(eregi('color',$d))
						{
							$type=3;
						}
						else
						{
							$type=1;
						}
						$css2[] = '['.$type.']['.$d.'][][Переменная '.$d.']&lt;br&gt;';
					}
					$j = substr($j,$pos2+3,strlen($j));
					$pos = strpos($j, "<!--");
					if ($pos === false) break;
				}
				sort($css2);
				for($i=0;$i<count($css2);$i++)
				{
					$css.=$css2[$i];
				}
				$css=eregi_replace('\[1\]\[style\]\[\]\[Переменная style\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[title\]\[\]\[Переменная title\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[description\]\[\]\[Переменная description\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[keywords\]\[\]\[Переменная keywords\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[css\]\[\]\[Переменная css\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[mainmenu\]\[\]\[Переменная mainmenu\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[maintext\]\[\]\[Переменная maintext\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[submenu\]\[\]\[Переменная submenu\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[banners\]\[\]\[Переменная banners\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[headtext\]\[\]\[Переменная headtext\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[id\]\[\]\[Переменная id\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[sub\]\[\]\[Переменная sub\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[kind\]\[\]\[Переменная kind\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[idname\]\[\]\[Переменная idname\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[subname\]\[\]\[Переменная subname\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[kindname\]\[\]\[Переменная kindname\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[innerpath\]\[\]\[Переменная innerpath\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[innerpathnolinks\]\[\]\[Переменная innerpathnolinks\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[allsubs\]\[\]\[Переменная allsubs\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[lastchangedate\]\[\]\[Переменная lastchangedate\]&lt;br&gt;','',$css);
				$css=eregi_replace('\[1\]\[comments\]\[\]\[Переменная comments\]&lt;br&gt;','',$css);
				$css=substr($css,0,strlen($css)-10);

				mysql_query("UPDATE ".$prefix."temps SET css='".encode($css)."' WHERE id='$id'");
			}
		}
	}
}

// Добавление параметра values к select'ам и multiselect'ам.


// Инициализация элементов поиска, если нужен.


// Отрисовка всего объекта html'ем в переменную
$obj_html.=$obj->draw();

// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
$content2.='<h1>ШАБЛОНЫ ДИЗАЙНА ПРОЕКТОВ</h1>'.$obj_html;
?>