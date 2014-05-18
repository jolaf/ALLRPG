<?php
require_once ($server_inner_path."appcode/data/roles_linked.php");
function _internal_getlocatnotifications($locat) {
    $location_path = get_locations_to_root ($locat, $_SESSION["siteid"]);
    $list = "notifications IS NULL OR notifications='-' OR notifications='' OR notifications LIKE '%-0-%'";
    foreach ($location_path as $location)
    {
      $list.=" OR notifications LIKE '%-{$location['id']}-%'";
    }
		return $list;
	}

function get_notification_targets($site_id, $location_id, $signmode, $author_to_exclude = 0)
{
  global $prefix;
  $site_id = intval($site_id);
  $location_id = intval ($location_id);
  $author_to_exclude = intval ($author_to_exclude);
  
  $notification_rights_condition = _internal_getlocatnotifications($location_id);
  
  if (!in_array ($signmode, array('signtocomments', 'signtochange', 'signtonew')))
  {
    error_log ("get_notification_targets: unknown signmode $signmode");
    die();
  }
  
  $targets = array();
  
  $result2 = db_query("
				SELECT DISTINCT em 
				FROM {$prefix}allrights2 ar
				INNER JOIN {$prefix}users ON sid = ar.user_id
				WHERE 
          ar.site_id=$site_id 
          AND (ar.rights=1 OR ar.rights=2) 
          AND (locations='-' OR locations='' OR locations LIKE '%-0-%' OR locations LIKE '%-$location_id-%') 
          AND ($notification_rights_condition) 
          AND $signmode='1' 
          AND ar.user_id!=$author_to_exclude");
  while($b=mysql_fetch_array($result2)) {
    $targets [] = array ('em' => $b['em']);
  }
	return $targets;
}
?>