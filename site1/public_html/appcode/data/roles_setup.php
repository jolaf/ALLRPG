<?php
// This file includes some code to operate roles and linked entities

require_once ($server_inner_path."appcode/data/common.php");

function get_enabled_roles_types ($site_id)
{
  global $prefix;
  $site_id = intval ($site_id);
  
  
  $query = db_query("
   SELECT DISTINCT team
    FROM rolefields
    WHERE site_id = $site_id
    "); 
  
  $result = array();
  
  while($a = mysql_fetch_array($query)) {
    if ($a['team'])
    {
      $result [] = 'team';
    }
    else
    {
      $result [] = 'individual';
    }
  }
  
  return $result;
}

function get_vacancy ($id)
{
  global $prefix;
  $id = intval ($id);
  
  return db_get_row ("SELECT * FROM {$prefix}rolevacancy WHERE id = $id");
}


?>