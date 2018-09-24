<?php
  include_once("conn.php");
  
  $tel = $_POST['tel'];
  $time = $_POST['time'];
  $type = $_POST['type'];

  if($type=="sign")
  {
	  $query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	  if($query) $result = mysql_fetch_array($query);
	  $id = $result['user_id'];
	  $query = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$id."';");
	  if(!mysql_num_rows($query)) mysql_query("INSERT INTO `user_sign` (`user_id` , `".$time."`) VALUES ('".$id."' , '1');");
	  else mysql_query("UPDATE `user_sign` SET `".$time."` = '1' WHERE `user_id` = '".$id."';");
	  echo "1";
  }
  
  else if($type=="ask")
  {
	  $query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	  if($query) $result = mysql_fetch_array($query);
	  $id = $result['user_id'];

  	  $query = mysql_query("SELECT * FROM `sign_list` WHERE `sign_title` = '".$time."';");
  	  if(!mysql_num_rows($query)) 
	  {
		  mysql_query("INSERT INTO `sign_list` (`sign_id` , `sign_title`) VALUES ( NULL , '".$time."');");
		  mysql_query("ALTER TABLE `user_sign` ADD `".$time."` INT(1) NULL DEFAULT '0';");
	  }

	  $query = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$id."';");
	  if(!mysql_num_rows($query))
	  {
		  $query = mysql_query("INSERT INTO `user_sign` (`user_id`) VALUES ('".$id."');");
		  if($query) $query = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$id."';");
	  }
	  $result = mysql_fetch_array($query);
	  if($result[$time]=="0") echo "0";
	  else if($result[$time]=="1") echo "1";
	  else if($result[$time]=="2") echo "2";
  }
  
  else if($type=="confirm")
  {
	  $id = $_POST['id'];
	  $query = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
	  $row = mysql_fetch_array($query);
	  $query = mysql_query("UPDATE `user_sign` SET `".$row['sign_title']."` = '2' WHERE `user_id` = '".$id."';");
	  if($query) echo "1";
	  else echo "0";
  }
  
  else if ($type=="comment")
  {
	  $id = $_POST['id'];
	  $comment = trim($_POST['content']);
	  $query = mysql_query("UPDATE `user_info` SET `user_context` = CONCAT(IFNULL(`user_context`,''),'".$comment."\n评论时间：".date("Y/m/d H:i:s")."\n\n"."') WHERE `user_id` = '".$id."';");
	  if($query) echo "1";
	  else echo "0";	  
  }
?>