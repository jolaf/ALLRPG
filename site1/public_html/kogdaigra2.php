<?php
include_once("db.inc");
include_once("classes_objects_allrpg.php");
require_once "appcode/data/common.php";

start_mysql();
# Установление соединения с MySQL-сервером

$content='';

$from=intval($_GET["from"]);
$from = $from ? $from : 1500;

$to=intval($_GET["to"]);
$to = $to ? $to : ($from + 100);

function load_data_from_kogdaigra($id)
{
  $id = intval ($id);
	return json_decode(implode(file("http://kogda-igra.ru/api/game/$id")),true);
}

function get_region_by_name ($region)
{
  global $prefix;
  if ($region == 'Пермский край')
  {
    return 761;
  }
  if (!$region)
  {
    return 2563;
  }
  //TODO: FIX Комстромская область
  $a=db_get_row("SELECT id FROM ".$prefix."geography WHERE name LIKE '%$region%'");
	return intval($a['id']);	
}

function get_area_for_sync ($name, $kogda_igra_id)
{
  if($name=="Выбран" || $name=="Неизвестен") {
    return 110;
  }
  global $prefix;
  $name = mysql_real_escape_string ($name);
  $kogda_igra_id = intval ($kogda_igra_id);
  
  $result = db_get_row("SELECT id FROM {$prefix}areas WHERE name='$name' OR kogdaigra_id=$kogda_igra_id");
  return $result['id'];
}

function find_game_in_allrpg ($kogda_igra_id, $allrpg_id, $name, $begin)
{
  global $prefix;
  $a = db_get_row("SELECT * FROM {$prefix}allgames WHERE parent=0 and kogdaigra_id=$kogda_igra_id");
  if ($a['id'])
  {
    return $a;
  }
  if($allrpg_id>0 ) {
    return db_get_row("SELECT * FROM ".$prefix."allgames WHERE parent=0 and id=".$allrpg_id);
  }
  else {
    return db_get_row("SELECT * FROM ".$prefix."allgames WHERE parent=0 and LOWER(name)='".strtolower(encode($name))."' and datestart='".$info["begin"]."'");
  }
}

$types[1]=15;
$types[2]=2;
$types[3]=14;
$types[4]=14;
$types[5]=4;
$types[6]=33;
$types[7]=34;
$types[8]=2;
$types[9]=2;
$types[10]=34;
$types[12] = 3;
$inserts=0;
$updates=0;
$deletes=0;
$content_rows = array();
for($id=$from;$id<=$to;$id++) {
  $content = '';
  $result_row = array();
  
	$info= load_data_from_kogdaigra ($id);

	$name=$info["name"];
	$region=$info["sub_region_name"];
	$gametype=$info["game_type_name"];
	$polygon=$info["polygon_name"];
	$mg=$info["mg"];
	$allrpg_id=intval($info["allrpg_info_id"]);
	
	if(($name!='' || $allrpg_id>0) && $info["deleted_flag"]!='1') {
		$query='';
		$theid=0;
		$datearrival='';
		
		$a = find_game_in_allrpg ($id, $allrpg_id, $name, $info['begin']);
		
		$result_row['kogda_igra_id'] = $id;
		$result_row['kogda_igra_name'] = $name;
		$result_row['allrpg_id'] = $a['id'];
		$result_row['allrpg_name'] = $a['name'];
		$result_row['kogdaigra_subregion'] = $region;
		$theid=$a["id"];	
		if($theid) {
			$datearrival=$a["datearrival"];
		}
		
		$fields_list = array("kogdaigra_id", 
        "parent", 
        "name", 
        "region", 
        "area", 
        "gametype2", 
        /*"gametype3", 
        "mg", 
        "site", 
        "datestart", 
        "datefinish", 
        "datearrival", 
        "playernum", 
        "date", 
        "master", 
        "sid", 
        "wascancelled", 
        "moved" */);
    
    $sql_values = array('kogdaigra_id' => $id, 'parent' => 0, 'name' => mysql_real_escape_string($name));
		
		$region_id = get_region_by_name($region);		
		$result_row['region_id'] = $region_id;
		
		$sql_values['region'] = $region_id;

			if($theid && !$region_id) {
			// TODO: Do not update region 
			}
		
		$area_id = get_area_for_sync ($polygon, $info["polygon_name"]);
		$result_row['area_id'] = $area_id;
		$result_row['kogda_igra_polygon'] = $info["polygon_name"];
		
		$sql_values['area'] = $area_id ? $area_id : 110;
		
		$allrpg_gametypes = intval($types[$info["type"]]);
		$result_row['allrpg_gametypes'] = $allrpg_gametypes;
		$result_row['kogda_igra_gametype'] = $info["type"];
		$sql_values['gametype2'] = "-$allrpg_gametypes-";
				
		if($theid==0) {
			$query.="67,";
		}

		$mg=str_replace('«','',$mg);
		$mg=str_replace('»','',$mg);
		$content.=$mg;
		if($theid>0) {
			$query.="mg='".encode($mg)."',";
		}
		else {
			$query.="'".encode($mg)."',";
		}
		$content.='</td><td>';
		$content.=$info["uri"];
		if($info["uri"]!='') {
			if($theid>0) {
				$query.="site='".encode($info["uri"])."', ";
			}
			else {
				$query.="'".encode($info["uri"])."', ";
			}
		}
		elseif($theid==0) {
			$query.="'',";
		}
		$content.='</td><td>';
		$content.=$info["begin"];
		if($theid>0) {
			$query.="datestart='".$info["begin"]."', ";
		}
		else {
			$query.="'".$info["begin"]."',";
		}
		$content.='</td><td>';
		
		$datefinish = date("Y-m-d",strtotime($info["begin"])+60*60*24*($info["time"]-1));
		$content.=$datefinish;
		if($theid>0) {
			$query.="datefinish='".$datefinish."',";
			if(strtotime($datearrival)>strtotime($info["begin"]) || strtotime($datearrival)<strtotime($info["begin"])-(60*60*24*7)) {
				$query.="datearrival='".$info["begin"]."',";
			}
		}
		else {
			$query.="'".$datefinish."','".$info["begin"]."', ";
		}
		$content.='</td><td>';
		$content.=$info["players_count"];
		if($theid>0) {
			if($info["players_count"]!='') {
				$query.="playernum='".$info["players_count"]."', ";
			}
			$query.="date=".time();
		}
		else {
			$query.="'".$info["players_count"]."',".time().",'{menu}',0,";
		}
		
		$moved = $info["status"] == 3 ? 1 : 0;
		$cancelled = $info["status"] == 5 ? 1 : 0;
		
		$result_row['status_text'] = $moved ? 'отложена' : ($cancelled ? 'отменена' : '');
		
		
		if($theid) {
			$set = array();
			foreach ($fields_list as $field)
			{
        $set[] = "$field = '{$sql_values[$field]}'";
			}
			if($cancelled) {
				$query.=",wascancelled='1'";
      }
      elseif($moved) {
        $query.=",moved='1'";
      }
			$query = "UPDATE allgames SET " . implode (', ', $set) . ", $query WHERE id = $theid";
		}
		else {
      $fields_string = implode(', ', $fields_list);
      $set = array();
			foreach ($fields_list as $field)
			{
        $set[] = "'{$sql_values[$field]}'";
			}
			$values_string = implode (', ', $set);
			$query="INSERT into allgames ($fields_string,gametype2,gametype3,mg,site,datestart,datefinish,datearrival,playernum,date,master,sid,wascancelled,moved) VALUES ($values_string, $query,
			'$cancelled', '$moved')";
		}
		
		mysql_query($query);
		
		if (!$theid)
		{
      $result_row['allrpg_id'] = mysql_insert_id();
		}

		$result_row['sql_query'] = $query;
		$result_row['sql_result'] = mysql_affected_rows($link);
       
		
	}
	elseif($info["deleted_flag"]=='1') {
		if($allrpg_id>0) {
        	mysql_query("DELETE FROM ".$prefix."allgames WHERE parent=0 and id=".$allrpg_id);
		}
		else {
			mysql_query("DELETE FROM ".$prefix."allgames WHERE parent=0 and kogdaigra_id=".$id);
		}
		if(mysql_affected_rows($link)>0) {
			$deletes++;
		}
	}
	$result_row['content'] = $content;
	$content_rows [] = $result_row;
}

if ($_GET['automated'])
{
  header('Access-Control-Allow-Origin: *');
	header("Content-Type: text/html;charset=UTF-8");
  echo json_encode ($content_rows);
  die();
}

function write_or_error($row, $idcolumn, $namecolumn)
{
  $id = $row[$idcolumn];
  $name = $row[$namecolumn];
  $error = $id ? '' : 'color:red';
  echo "<td style='$error'>";
  echo $id ? $id : $name;
  echo '</td>';
}

echo '<table border=1 style="border: 1px solid black; border-collapse: collapse;"><tr style="font-weight: bold;"><td>№</td><td>kogda-igra</td><td>allrpg</td><td>регион</td><td>полигон</td><td>тип</td><td>МГ</td><td>сайт</td><td>дата начала</td><td>дата окончания</td><td>участников</td><td>отменена</td></tr>';

$i = 1;
foreach ($content_rows as $result_row)
{
  echo '<tr>';
  
  echo "<td>$i</td>";
  $i++;
  
  echo "<td><a href='http://kogda-igra.ru/game/{$result_row['kogda_igra_id']}'>{$result_row['kogda_igra_name']}</a></td>";
  
  echo "<td>";
  
  $allrpg_id = $result_row["allrpg_id"];
  $allrpg_name = encode ($result_row['allrpg_name']);
  if($allrpg_id) {
    $updates++;
    echo "<a href='http://inf.allrpg.info/events/$allrpg_id/'>$allrpg_name</a>";
  }
  else {
    $inserts++;
    echo '<font color="red">соответствия не найдено</font>';
  }
  
  echo '</td>';
  
  write_or_error ($result_row, 'region_id', 'kogdaigra_subregion');
  write_or_error ($result_row, 'area_id', 'kogda_igra_polygon');
  write_or_error ($result_row, 'allrpg_gametypes', 'kogda_igra_gametype');
  
  echo '<td>';
  
  echo $result_row['content'];
  
  echo '</td>';
  
  echo "<td>{$result_row['status_text']}</td>";
  
  echo '</tr>';
  
  echo '<tr><td colspan=12>'.$result_row['sql_query'];
  echo $result_row['sql_result'] ? ' <font color="green">ОК</font>' : ' <font color="red">NOT ОК</font>';
  echo '</td></tr>';
  
  $result_row['content'] ='';
  echo '<tr><td colspan=12>';
  var_dump ($result_row);
  echo '</td></tr>';
}

echo '</table><br>
Updates: '.$updates.'<br>
Inserts: '.$inserts.'<br>
Deletes: '.$deletes;

?>