<?php
// This file includes some code to load roles lists.

require_once ($server_inner_path."appcode/data/common.php");

function load_all_roles ($site_id, $team = FALSE)
{
  global $prefix;
  $site_id = intval ($site_id);
  $team = $team ? 1 : 0;
  
  $query = db_query("
    SELECT 
      r.id, r.site_id, r.player_id, r.new_player_sid, r.new_player_deny, r.team, r.vacancy, r.money, r.moneydone,
      r.sorter, r.locat, r.allinfo, r.status, r.changed, r.todelete, r.todelete2, r.alltold, r.roleteamkolvo, r.signtochange, r.signtocomments, r.datesent, r.date,
      rv.name, rv.code, rv.kolvo, rv.autonewrole, rv.teamkolvo, rv.maybetaken, rv.taken, rv.content,
      u.sid, u.fio, u.nick, u.gender, u.em, u.em2, u.phone2, u.icq, u.skype, u.jabber, u.vkontakte, u.tweeter, u.livejournal, u.googleplus, u.facebook, u.photo,
      u.login, 
      u.birth, u.city, u.sickness
    from {$prefix}roles r
    LEFT JOIN {$prefix}users u ON r.player_id = u.id
    LEFT JOIN {$prefix}rolevacancy rv ON rv.id = r.vacancy
    where r.site_id={$site_id} and r.team='$team'
    "); 
  
  $result = array();
  
  while($a = mysql_fetch_array($query)) {
    $a=array_merge($a, unmakevirtual($a["allinfo"]));
    $result[] = $a;
  }
  
  return $result;
}

?>