<?php

if($_SESSION["user_id"]!='' && $workrights["site"]["gamereport"]) {

  require_once ($server_inner_path."appcode/data/common.php");
  
  $site_id = intval($_SESSION['siteid']);
  $team = (array_key_exists('action', $_GET) && $_GET['action'] == 'team') ? 1 :0;
	
	$result = db_query ("
    SELECT rl.id, rl.parent, rl.name, 0 AS vacancy_count, COUNT(r.id) AS total_count, SUM(IF(r.status=1, 1, 0)) AS podana, SUM(IF(r.status=2, 1, 0)) AS inprocess, SUM(IF(r.status=3, 1, 0)) AS accepted, SUM(IF (r.alltold = '1', 1, 0)) AS alltold, SUM(IF (r.moneydone = '1', 1,0)) AS moneydone, SUM(IF (r.moneydone = '1', REPLACE(REPLACE(REPLACE(r.money, 'р', ''), ' ', ''), '.', '')  + 0, 0)) AS money
    FROM  (
      SELECT id, parent, name, code FROM {$prefix}roleslocat WHERE site_id = $site_id
      UNION ALL 
      SELECT 0,0, 'Итого', 0
    ) rl
    LEFT JOIN {$prefix}roles r ON r.locat = rl.id AND r.site_id = $site_id AND r.todelete = 0 AND r.todelete2 = 0 AND r.status<>4 AND r.team = '$team'
    GROUP BY rl.id, rl.parent, rl.name  ORDER BY rl.code ASC");
    
    $items  = array();
    
    while ($row = mysql_fetch_assoc($result))
		{
			$items  [] = $row;
		}
    
    $result = db_query("
    SELECT rl.id, SUM(rv.kolvo) AS vacancy_count
    FROM {$prefix}rolevacancy rv 
    LEFT JOIN {$prefix}roleslocat rl ON rv.locat = rl.id 
    WHERE rv.site_id = $site_id AND rv.team = '$team'
    GROUP BY rl.id
    ");
		
		$vacancy_locat  = array();
    
    while ($row = mysql_fetch_assoc($result))
		{
			$vacancy_locat  [$row['id']] = $row ['vacancy_count'];
		}

		$childs = array();

    foreach($items as $item)
    {
        $item['vacancy_count'] = intval ($vacancy_locat[$item['id']]);
        $childs[$item['parent']][] = $item;
        if ($item['id'] == 0)
        {
          $root = $item;
        }
    }

    function get_rows($childs, $items, $our_row, $deep)
    {
      $our_child = $childs[intval($our_row['id'])]; 
      
      $allrows = array ($our_row);
      foreach ($our_child as $child)
      {
        if ($child['id'] == $our_row['id'])
        {
          continue;
        }
        
        $rows = get_rows ($childs, $items, $child, $deep + 1);
        
        foreach($rows as $row)
        {
          $allrows[] = $row;
        }
        
        $child_total = $rows[0];
        

        $allrows[0]['total_count'] += $child_total ['total_count'];
        $allrows[0]['podana'] += $child_total ['podana'];
        $allrows[0]['inprocess'] += $child_total ['inprocess'];
        $allrows[0]['accepted'] += $child_total ['accepted'];
        $allrows[0]['alltold'] += $child_total ['alltold'];
        $allrows[0]['moneydone'] += $child_total ['moneydone'];
        $allrows[0]['money'] += $child_total ['money'];
        $allrows[0]['vacancy_count'] += $child_total ['vacancy_count'];
      }
      
      $allrows[0]['name'] = str_repeat('→', $deep - 1) . $allrows[0]['name'];
      
      return $allrows;
    }
    
    function show_menu_link($team, $action, $label)
    {
      global $server_absolute_path_site, $kind;
      $obj_html = '';
      if ($team)
      {
        $obj_html .= "<a href=\"{$server_absolute_path_site}{$kind}/{$action}\">";
      }
      $obj_html .= $label;
      if ($team)
      {
        $obj_html .= "</a>";
      }
      return $obj_html;
    }
    
    $obj_html = "<div style=\"text-align:center\"><b>Отчет по:</b> ";
    $obj_html .= show_menu_link ($team, '', 'индивидуальным');
    $obj_html .= " / ";
    $obj_html .= show_menu_link (!$team, 'action=team', 'командным');
    $obj_html .= " заявкам</div>";

    $result= db_get_row("SELECT SUM(IF (moneydone = '1', REPLACE(REPLACE(REPLACE (money, 'р', ''), ' ', ''), '.', '')  + 0, 0)) AS money FROM {$prefix}roles WHERE moneydone='1' 
    and todelete2=1 and site_id=$site_id");
    $deleted_money = $result['money'];
		if($deleted_money) {
			$obj_html .="<div style=\"text-align:center\"><b>Взносов в удаленных заявках</b>: $deleted_money </div>";
		}

    $obj_html .= '
    <table class="menutable"><tr class="menu" style="font-size:90%"><th>Локация / Команда</th>
      <th  style="text-align:right">Вакансий</th>
      <th style="text-align:right">Заявок</th>
      <th style="text-align:right">Подано</th>
      <th style="text-align:right">Обсуждается</th>
      <th style="text-align:right">Принято</th>
      <th style="text-align:right">Оплачено</th>
      <th style="text-align:right" title="Система автоматически анализирует цифры. В случае если вы используете нестандартный взнос или нестандартную форму записи в поле взнос, система может ошибиться.">Сумма¹</th>
       <th  style="text-align:right">Прогружено</th>
       </tr>';
    foreach (get_rows ($childs, $items, $root, 0) as $row)
    {
      $hasSomething = array_sum ($row);
      if ($row['id'] && ($row['vacancy_count'] + $row['total_count'] + $row['podana'] + $row['inprocess'] + $row['accepted'] + $row['moneydone'] + $row['money'] + $row['alltold']) == 0)
      {
        continue;
      }
      $obj_html .= "<tr><td><a href=\"/locations/locations/{$row['id']}/act=view\">{$row['name']}</a></td>
      <td style=\"text-align:right\">{$row['vacancy_count']}</td>
      <td style=\"text-align:right\">{$row['total_count']}</td>
      <td style=\"text-align:right\">{$row['podana']}</td>
      <td style=\"text-align:right\">{$row['inprocess']}</td>
      <td style=\"text-align:right\">{$row['accepted']}</td>

      <td style=\"text-align:right\">{$row['moneydone']}</td>
      <td style=\"text-align:right\">{$row['money']}</td>
            <td  style=\"text-align:right\">{$row['alltold']}</td>
      </tr>";
    }
    $obj_html.= "</table>";

    

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Отчет об игроках',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>