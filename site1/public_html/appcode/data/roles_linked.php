<?php
// This file includes some code to load some roles-related data (users, links, comments, etc). 
// We SHOULD cache some data here and use batch loading when needed

require_once ($server_inner_path."appcode/data/common.php");

// Signal to cache that we probably need all data for this site. Cache can use this to eager-load all comments, for example
function role_linked_hint_sitewide()
{
}

// Signal to cache that we probably need only one role. 
function role_linked_hint_single()
{
}

function get_location_path ($location_id, $site_id)
{
  //TODO: implement caching
  global $prefix;
	
	$location_id = intval ($location_id);
	$site_id = intval ($site_id);
	
	if (!$location_id)
	{
    return array();
	}

		$result = db_get_row ("SELECT id, parent, name FROM {$prefix}roleslocat WHERE id=$location_id and site_id=$site_id");
		
		if($result["id"]) {
      $return =  $result["parent"] ? get_location_path ($result["parent"], $site_id) : array();
			$return [] = decode($result["name"]);
		}
		else {
			return array();
		}
		return($return);
}

function load_comments ($role_id, $site_id)
{
//TODO: implement caching
  global $prefix;
  $role_id = intval ($role_id);
  
  $obj_html = '';
  $result3=db_query("
  SELECT 
    rc.type, u.*, rc.date, rc.content
  FROM {$prefix}rolescomments rc 
  LEFT JOIN {$prefix}users u ON u.id = rc.user_id
  WHERE rc.role_id=$role_id 
  ORDER by rc.date desc");
  
  while($c = mysql_fetch_array($result3)) {
    
    $obj_html .= ($c["type"]==3) ? 'Игрок' : 'Мастер';

    $obj_html.=' '.usname($c,true).' в '.date("G:i d.m.Y",$c["date"]).' написал';
    if($c["gender"]==2) {
      $obj_html.='а';
    }
    if($c["type"]==2) {
      $obj_html.=' другим мастерам';
    }
    elseif($c["type"]==1) {
      $obj_html.=' игроку';
    }
    $obj_html.=':
'.decode($c["content"]).'{drn}';
  }
  return $obj_html;
}

 function load_links ($role_id, $vacancy_id, $site_id)
{
  global $prefix;
  $role_id = intval ($role_id);
  $vacancy_id = intval ($vacancy_id);
  $site_id = intval ($site_id);
  
  $alllinks = '';
  $result3=db_query("SELECT * from {$prefix}roleslinks where (roles LIKE '%-all{$vacancy_id}-%' OR roles LIKE '%-{$role_id}-%' OR roles2 LIKE '%-all{$vacancy_id}-%' OR roles2 LIKE '%-{$role_id}-%') and site_id={$site_id} and content!='' and parent IN (SELECT id from {$prefix}roleslinks WHERE vacancies LIKE '%-{$vacancy_id}-%') order by date desc");
  while($c=mysql_fetch_array($result3)) {
    $alllinks.='Загруз для ';

    unset($roles);
    unset($roles2);
    $roles=substr($c["roles"],1,strlen($roles)-1);
    $roles2=substr($c["roles2"],1,strlen($roles2)-1);
    $roles=explode('-',$roles);
    $roles2=explode('-',$roles2);
    $dosee='его видят: мастера';
    foreach($roles as $r) {
      $query="";
      if(strpos($r,'all')!==false) {
        $result2=db_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id={$site_id} and id=".str_replace('all','',$r));
        $b=mysql_fetch_array($result2);
        if($b["name"]!='') {
          $alllinks.=$b["name"].', ';
          $query="SELECT * from {$prefix}roles where vacancy=".$b["id"]." and site_id=".$_SESSION["siteid"];
        }
        elseif($r==0) {
          $alllinks.='глобального сюжета, ';
        }
        else {
          $alllinks.='удаленной роли, ';
        }
      }
      else {
        $query="SELECT * from {$prefix}roles where id=".$r." and site_id=".$_SESSION["siteid"];
        $result2=db_query($query);
        $b=mysql_fetch_array($result2);
        if($b["sorter"]!='') {
          $alllinks.=decode($b["sorter"]);
        }
        else {
          $alllinks.='удаленной заявки';
        }
        $alllinks.=', ';
      }
      if($query!='') {
        $result5=db_query($query);
        while($e=mysql_fetch_array($result5)) {
          if(strpos($c["roles"],'-'.$e["id"].'-')!==false) {
            $dosee.=', '.decode($e["sorter"]);
            if($b["hideother"]=='1') {
              $dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
            }
          }
          elseif(strpos($c["roles"],'-'.$r.'-')!==false) {
            $dosee.=', '.decode($e["sorter"]);
            if($e["status"]<3) {
              $dosee.=' (увидит, как только заявка будет принята)';
            }
            if($b["hideother"]=='1') {
              $dosee.=' (игрок не знает, на кого конкретно у него данный загруз)';
            }
          }
        }
      }
    }
    $alllinks=substr($alllinks,0,strlen($alllinks)-2).' про ';
    foreach($roles2 as $r) {
      if(strpos($r,'all')!==false) {
        $result2=db_query("SELECT * FROM {$prefix}rolevacancy WHERE site_id=".$_SESSION["siteid"]." and id=".str_replace('all','',$r));
        $b=mysql_fetch_array($result2);
        if($b["name"]!='') {
          $alllinks.=$b["name"].', ';
        }
        elseif($r==0) {
          $alllinks.='глобальный сюжет, ';
        }
        else {
          $alllinks.='удаленную роль, ';
        }
      }
      else {
        $result2=db_query("SELECT * FROM {$prefix}roles WHERE site_id=".$_SESSION["siteid"]." and id=".$r);
        $b=mysql_fetch_array($result2);
        if($b["sorter"]!='') {
          $alllinks.=decode($b["sorter"]);
        }
        else {
          $alllinks.='удаленную заявку';
        }
        $alllinks.=', ';
      }
    }
    $alllinks=substr($alllinks,0,strlen($alllinks)-2).' ('.$dosee.'){drn}';
    $result2=db_query("SELECT * FROM {$prefix}roleslinks WHERE id=".$c["parent"]);
    $b=mysql_fetch_array($result2);
    $alllinks.='сюжет «'.decode($b["name"]).'»{drn}';
    $alllinks.=decode($c["content"]);
    $alllinks.='{drn}{drn}';
  }
  $alllinks=substr($alllinks,0,strlen($alllinks)-8);

  return $alllinks;
}

function load_rolefields_virtual_structure ($site_id, $team = FALSE)
{
  global $prefix;
  $site_id = intval ($site_id);
  $team = $team ? 1 : 0;
  
  return virtual_structure("SELECT * from {$prefix}rolefields where site_id={$site_id} and team='$team' order by rolecode","allinfo","role");
}
?>