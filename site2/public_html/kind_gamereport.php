<?php

if($_SESSION["user_id"]!='' && $workrights["site"]["gamereport"]) {
  
  $site_id = intval($_SESSION['siteid']);
	
	$result = mysql_query ("
    SELECT rl.id, rl.parent, rl.name, 0 AS vacancy_count, COUNT(r.id) AS total_count, SUM(IF(r.status=1, 1, 0)) AS podana, SUM(IF(r.status=2, 1, 0)) AS inprocess, SUM(IF(r.status=3, 1, 0)) AS accepted, SUM(IF (r.alltold = '1', 1, 0)) AS alltold, SUM(IF (r.moneydone = '1', 1,0)) AS moneydone, SUM(IF (r.moneydone = '1', REPLACE(REPLACE(REPLACE(r.money, 'р', ''), ' ', ''), '.', '')  + 0, 0)) AS money
    FROM  (
      SELECT id, parent, name, code FROM {$prefix}roleslocat WHERE site_id = $site_id
      UNION ALL 
      SELECT 0,0, 'Итого', 0
    ) rl
    LEFT JOIN {$prefix}roles r ON r.locat = rl.id AND r.site_id = $site_id AND r.todelete = 0 AND r.todelete2 = 0 AND r.status<>4 AND r.team = '0'
    GROUP BY rl.id, rl.parent, rl.name  ORDER BY rl.code ASC");
    
    echo mysql_error();
    $items  = array();
    
    while ($row = mysql_fetch_assoc($result))
		{
			$items  [] = $row;
		}
    
    $result = mysql_query("
    SELECT rl.id, SUM(rv.kolvo) AS vacancy_count
    FROM {$prefix}rolevacancy rv 
    LEFT JOIN {$prefix}roleslocat rl ON rv.locat = rl.id 
    WHERE rv.site_id = $site_id
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

    $obj_html = '
    <table class="menutable"><tr class="menu" style="font-size:90%"><th>Локация / Команда</th>
      <th>Вакансий</th>
      <th>Заявок</th>
      <th>Подано</th>
      <th>Обсуждается</th>
      <th>Принято</th>
      <th>Оплачено</th>
      <th>Сумма<abbr title="Система автоматически анализирует цифры. В случае если вы используете нестандартный взнос или нестандартную форму записи в поле взнос, система может ошибиться.">¹</abbr></th>
       <th>Прогружено</th>
       </tr>';
    foreach (get_rows ($childs, $items, $root, 0) as $row)
    {
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