<?php
  include_once("../conn.php");
  error_reporting(0);

  $content = $_GET['query'];
  $group = $_GET['group'];
  $group = substr($group,0,1);
  if($content&&$group)
  {
	  $query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(user_group,1) = '".$group."' AND `user_name` LIKE '%".$content."%';");
	  if(mysql_num_rows($query))
	  {
		$i = 0;
		while($row = mysql_fetch_array($query))
		{
			$suggestions[$i] = array('value'=>$row['user_id']." - ".$row['user_name'],'data'=>$row['user_id']);
			$i++;
		}
		$return = array('query'=>$content,'suggestions'=>$suggestions);
		echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
	  }
  }
?>