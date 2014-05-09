<?php
// Common SQL functions. All new allrpg.info code should use db_XXXX() to make tracing/logging possible

// Can perform some work here (log query time, etc)
function db_query($query)
{
  $result = mysql_query ($query);
  if (!$result)
  {
    echo mysql_error();
    die();
  }
  return $result;
}

function db_get_row ($query)
{
  $result=db_query($query);
  return mysql_fetch_array($result);
}

?>