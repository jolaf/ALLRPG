<?php

if($_SESSION["user_id"]!='' /*&& $workrights["site"]["report"]*/) {
  
  $site_id = intval($_SESSION['siteid']);
	
	$result = mysql_query ("
    SELECT rl.id, rl.parent, rl.name, COUNT(r.id) AS total_count, SUM(IF(r.status=1, 1, 0)) AS podana, SUM(IF(r.status=2, 1, 0)) AS inprocess, SUM(IF(r.status=3, 1, 0)) AS accepted, SUM(IF (r.alltold = 1, 0, 1)) AS alltold, SUM(IF (r.moneydone+0 = 1, 0,1)) AS moneydone
    FROM  (
      SELECT id, parent, name, code FROM {$prefix}roleslocat WHERE site_id = $site_id
      UNION ALL 
      SELECT 0,0, 'Итого', 0
    ) rl
    LEFT JOIN {$prefix}rolevacancy rv ON rv.locat = rl.id 
    LEFT JOIN {$prefix}roles r ON r.vacancy = rv.id
    WHERE (r.id IS NOT NULL OR rl.Id is NOT NULL) AND (r.id IS NULL OR (r.site_id = $site_id AND r.todelete = 0 AND r.todelete2 = 0 AND r.status<>4))
    GROUP BY rl.id, rl.parent, rl.name  ORDER BY rl.code ASC");
    
   
   echo mysql_error();
    $items  = array();
    
    while ($row = mysql_fetch_assoc($result))
		{
			$items  [] = $row;
		}

		$childs = array();

    foreach($items as $item)
    {
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
      }
      
              $allrows[0]['name'] = str_repeat('•', $deep - 1) . $allrows[0]['name'];
      
      return $allrows;
    }

    echo '<table><tr><th>id</td><th>parent</td><th>name</td><th>count</td><th>podana</td>
      <th>inprocess</td>
      <th>accepted</td>
      <th>alltold</td>
      <th>moneydone</td></tr>';
    foreach (get_rows ($childs, $items, $root, 0) as $row)
    {
      echo "<tr><td>{$row['id']}</td><td>{$row['parent']}</td><td>{$row['name']}</td>
      <td>{$row['total_count']}</td>
      <td>{$row['podana']}</td>
      <td>{$row['inprocess']}</td>
      <td>{$row['accepted']}</td>
      <td>{$row['alltold']}</td>
      <td>{$row['moneydone']}</td>
      </tr>";
    }
    echo "</table>";
    
    die();
    

	// Передача целиком проработанного maincontent'а данного kind'а основному скрипту
	$pagetitle=h1line('Дерево локаций / команд',$curdir.$kind.'/');
	$content2.='<div class="narrow">'.$obj_html.'</div>';
}
?>