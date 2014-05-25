<?php
// This file includes some quick formatting functions

function phone_formatter($obj, $row)
{
  return phone_formatter_raw($row [$obj -> getName()]);
}

function phone_formatter_raw ($phone)
{
  if (strlen ($phone) > 20) 
  {
    return $phone;
  }
  $phone = trim (preg_replace("/[^0-9,.]/", "", $phone));
  if (strlen($phone) == 11 && $phone[0] == '8')
  {
    $phone = substr_replace ($phone, '+7', 0, 1);
  }
  elseif (strlen($phone) == 11 && $phone[0] == '7')
  {
    $phone = '+' . $phone;
  }
  return  $phone; 
}


function name_as_master_formatter_row ($row, $options) //Master should always ignore 'hidesome'
{
  $sid = strpos($options, 'skipsid') !== FALSE ? '' : $row['sid'];
  $fio = $row['fio'];
  $nick = $row['nick'] ? "({$row['nick']})" : '';
  $parts = array_filter( //Remove empty elements
    array($fio, $nick, $sid));
  return implode (' ',  $parts);
}

?>