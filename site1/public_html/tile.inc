<?php
if(!$itsthemainpage) {
	unset($matches);
	$tiles=array();
	if($additional_commands!='') {
		preg_match_all('#<a ([^>]*)>(.+?)<\/a>#',$additional_commands,$matches);
	}


	foreach($matches[0] as $key=>$match) {
		preg_match('#href="([^"]+)"#',$matches[1][$key],$match_href);
		preg_match('#onClick="([^"]+)"#i',$matches[1][$key],$match_onclick);
		preg_match('#class="([^"]+)"#',$matches[1][$key],$match_class);
		if($match_class[1]=='') {
			preg_match('#\/([^\/]+)\/$#',$match_href[1],$match_class);
		}
		$tiles[]=array('black',$matches[2][$key],$match_href[1],$match_class[1],$match_onclick[1]);
	}

	$additional_commands='';
	foreach($tiles as $tile) {
		$additional_commands='<div class="tile '.$tile[0].'"><a'.($tile[2]!=''?' href="'.$tile[2].'"':'').($tile[3]!=''?' class="'.$tile[3].'"':'').($tile[4]!=''?' onClick="'.$tile[4].'"':'').'>'.$tile[1].'</a></div>'.$additional_commands;
	}

	$global_menu='';
	if(isset($usekinds)) {
		$global_menu='<div class="tile blue"><a href="'.$server_absolute_path.'" class="home">Главная</a></div>';
		$opened_block='';
		$i=0;
		foreach($subkinds as $key=>$subkind) {
			if($subkind[0]==$usekinds) {
				if($subkind[1]=='block') {
					if($opened_block!='') {
						$global_menu='<div class="tile black"><a block="'.$i.'" class="block_open">'.$opened_block.'</a></div><div id="block_'.$i.'" class="menu_block"><div class="tile black close"><a block="'.$i.'" class="block_close"></a></div>'.$global_menu;
						$i++;
					}
					$opened_block=$subkind[2];
					$global_menu='</div>'.$global_menu;

				}
				else {
					preg_match('#\/([^\/]+)\/$#',$subkind[1],$match_class);
					$global_menu='<div class="tile black"><a href="'.$subkind[1].'" class="'.$match_class[1].'">'.$subkind[2].'</a></div>'.$global_menu;
				}
			}
		}
		if($opened_block!='') {
			$global_menu='<div class="tile black"><a block="'.$i.'" class="block_open">'.$opened_block.'</a></div><div id="block_'.$i.'" class="menu_block"><div class="tile black close"><a block="'.$i.'" class="block_close"></a></div>'.$global_menu;
		}
	}
	$i++;
	if($additional_commands!='') {
		$additional_commands='<div class="tile black"><a block="'.$i.'" class="block_open">Функции</a></div><div id="block_'.$i.'" class="menu_block"><div class="tile black close"><a block="'.$i.'" class="block_close"></a></div>'.$additional_commands.'</div>'.$global_menu;
	}
	else {
		$additional_commands=$global_menu;
	}
}
?>