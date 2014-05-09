<?php
require_once ($server_inner_path."appcode/external/PHPExcel.php");
require_once ($server_inner_path."appcode/data/common.php");
require_once ($server_inner_path."appcode/data/roles_linked.php");
require_once ($server_inner_path."appcode/data/roles_main.php");

function get_excel_writer ($excel, $mode)
{
  switch ($mode)
  {
    case 'xlsx':
      return new PHPExcel_Writer_Excel2007($excel);
    case 'html':
      return new PHPExcel_Writer_HTML($excel);
    default:
      echo 'Unknown PHPExcel mode';
      die();
  }
}

function output_header($elems) {
  $header = array();
  foreach ($elems as $elem)
  {
    if ($elem -> isExcelSupported())
    {
      $header[] = $elem -> getSName();
    }
  }
  return $header;
}

function excel_clean_string ($line) {
                	$line = str_replace('<font color="red"><b>X</b></font>',"нет",$line);
					$line = str_replace('<font color="green"><b>&#8730</b></font>',"да",$line);
					$line = str_replace('&#39',"'",$line);
					$line = str_replace('&nbsp;'," ",$line);
					$line = str_replace("<br>",chr(10),$line);
					$line = strip_tags($line);
					$line = str_replace(chr(13),'',$line);
					$line = str_replace(chr(10).chr(10),chr(10),$line);
					$line = str_replace('{drn}',chr(10).chr(10),$line);
					$line = str_replace(chr(10).chr(10).chr(10),chr(10).chr(10),$line);
					$line = str_replace('"', '""', $line);
					return $line;
				}

function output_row ($elems, $a)
{
  $row = array();
  foreach ($elems as $elem)
  {
    if ($elem -> isExcelSupported())
    {
      $elem -> setVal ($a);
      $val = excel_clean_string( $elem->draw(1,"read") );
      if (strlen ($val) > 31000)
      {
        $val = substr ($val, 0, 31000) . ' (обрезано)';
      }
      $row[] = $val;
    }
  }
  return $row;
}

function output_excel_headers ($mode)
{
   switch ($mode)
  {
    case 'xlsx':
      header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header("Content-Disposition: attachment; filename=allroles.xlsx");
      break;
    case 'html':
      break;
    default:
      echo 'Unknown PHPExcel mode';
      die();
  }
}

function get_delete_string ($row)
{
  if ($row['todelete'] && $row['todelete2'])
  {
    return 'Окончательно';
  }
  if ($row['todelete'])
  {
    return 'Игроком';
  }
  if ($row['todelete2'])
  {
    return 'Мастером';
  }
  return "";
}

function export_roles_of_kind ($mode, $team, $fields_before, $fields_after, $sheet)
{
   $rolefields = load_rolefields_virtual_structure ($_SESSION["siteid"], $team);
	 
	 $excel_structure = array();
	 
	 foreach ($fields_before as $field)
	 {
    $excel_structure[] = createElem ($field);
	 }
	 
	 foreach ($rolefields as $field)
	 {
    $excel_structure[] = createElem ($field);
	 }
	 
	 foreach ($fields_after as $field)
	 {
    $excel_structure[] = createElem ($field);
	 }
	 
  $result = load_all_roles($_SESSION['siteid'], $team);
  
  $sheet -> fromArray (output_header ($excel_structure), NULL, 'A1');
  
  $row = 2;
  foreach ($result as $role)
  {
    $sheet -> fromArray ( output_row ($excel_structure, $role), NULL, "A$row");
    $row++;
  }
  
}

function get_fields_before ()
{
  return array (
    array(
					'name'	=>	"sid",
					'sname'	=>	"ИНП",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
    array(
					'name'	=>	"todelete",
					'sname'	=>	"Удалена",
					'type'	=>	"text",
					'valueExtractor' => function ($obj, $row) { return get_delete_string($row); },
					'read'	=>	1,
					'write'	=>	100000,
			),
    array(
				'name'	=>	"money",
				'sname'	=>	"Взнос",
				'type'	=>	"text",
				'read'	=>	10,
				'write'	=>	100,
		),
		array(
				'name'	=>	"moneydone",
				'sname'	=>	"Взнос сдан",
				'type'	=>	"checkbox",
				'read'	=>	10,
				'write'	=>	100,
		),
		array(
				'name'	=>	"alltold",
				'sname'	=>	"Игрок прогружен",
				'type'	=>	"checkbox",
				'read'	=>	100,
				'write'	=>	100,
		),
		array(
				'name'	=>	"locat",
				'sname'	=>	"Локация / команда",
				'type'	=>	"text", // TODO: this normally should be select, but select don't play nicely with valueExtractor
				'valueExtractor' => function ($obj, $row) { return  implode ('→', get_location_path ($row [$obj -> getName()], $_SESSION ['siteid'])); },
				'read'	=>	10,
				'write'	=>	100,
		),
		Array(
					'name'	=>	"status",
					'sname'	=>	"Статус",
					'type'	=>	"select",
					'values'	=>	Array(Array('1','подана'),Array('2','обсуждается'),Array('3','принята'),Array('4','отклонена')),
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"name",
					'sname'	=>	"Имя роли",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"fio",
					'sname'	=>	"ФИО",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"nick",
					'sname'	=>	"Ник",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"gender",
					'sname'	=>	"пол",
					'type'	=>	"select",
					'values'	=>	Array(Array('1','мужской'),Array('2','женский')),
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"em",
					'sname'	=>	"email1",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"em2",
					'sname'	=>	"email2",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"icq",
					'sname'	=>	"icq",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"phone2",
					'sname'	=>	"phone2",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			
			Array(
					'name'	=>	"skype",
					'sname'	=>	"skype",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			
			Array(
					'name'	=>	"jabber",
					'sname'	=>	"jabber",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"vkontakte",
					'sname'	=>	"vkontakte",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"tweeter",
					'sname'	=>	"tweeter",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"livejournal",
					'sname'	=>	"livejournal",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"googleplus",
					'sname'	=>	"googleplus",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"facebook",
					'sname'	=>	"facebook",
					'type'	=>	"text",
					'read'	=>	1,
					'write'	=>	100000,
			),
	 );
}

function get_fields_after()
{
  return array(
  Array(
					'name'	=>	"links",
					'sname'	=>	"Связи и загрузы",
					'type'	=>	"text",
					'valueExtractor' => function ($obj, $row) { return load_comments (intval ($row ['id']), $_SESSION["siteid"]); },
					'read'	=>	1,
					'write'	=>	100000,
			),
			Array(
					'name'	=>	"comments",
					'sname'	=>	"Комментарии",
					'type'	=>	"text",
					'valueExtractor' => function ($obj, $row) { return $row ['vacancy'] ? load_links ($row ['id'], $row ['vacancy'], $_SESSION["siteid"]) : NULL; },
					'read'	=>	1,
					'write'	=>	100000,
			),
	 );
}

function export_roles ($mode, $team, $short = FALSE)
{
  $result=db_query("SELECT * from {$prefix}allrights2 where user_id=".$_SESSION['user_sid']." and site_id=".$_SESSION["siteid"]." and (rights=1 || rights=2) LIMIT 1");
  $a=mysql_fetch_array($result);
  if(!$a["id"] && !$_SESSION["admin"])
  {
    exit;
  }
			
  role_linked_hint_sitewide();
 
  if (!$short) // If we include comments, we should use disk instead of memory
  {
    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
    PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
  }

  $excel = new PHPExcel();
  $excel->getProperties()
    ->setCreator("Allrpg.info")
    ->setTitle("Roles export");
    
  $sheet = $excel -> getActiveSheet();
  
  export_roles_of_kind ($mode, $team, get_fields_before(), $short ? array() : get_fields_after(), $sheet);
  
  output_excel_headers ($mode);
  header("Pragma: no-cache");
  header("Expires: 0");
  
  $objWriter = get_excel_writer($excel, $mode);
 
  ob_end_clean();
  $objWriter->save('php://output');
  die ();
}
?> 