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

?>