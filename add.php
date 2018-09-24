<?php
include_once("conn.php");

$name= $_POST['AddName'];
$group= $_POST['AddGroup'];
$job= $_POST['AddJob'];
$tel= $_POST['AddTel'];
$upper = $_POST['AddUpper'];
$type = $_POST['type'];

if($type=="check" && $tel)
{
	$isAvailable = true;
	$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if(mysql_num_rows($query)) $isAvailable = false;
	echo json_encode(array('valid' => $isAvailable),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if($type=="upper" && $group)
{
	$group = substr($group,0,1);
	$isAvailable = true;
	if($upper!="0")
	{
		$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(user_group,1) = '".$group."' AND `user_id` = '".$upper."';");
		if(!mysql_num_rows($query)) $isAvailable = false;
	}
	echo json_encode(array('valid' => $isAvailable),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if($type=="eventRequireCheck" && $group)
{
	$upper = $_POST['EventRequirePerson'];
	$group = substr($group,0,1);
	$isAvailable = true;
	$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(user_group,1) = '".$group."' AND `user_id` = '".$upper."';");
	if(!mysql_num_rows($query)) $isAvailable = false;
	echo json_encode(array('valid' => $isAvailable),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if($type=="eventProcessCheck" && $group)
{
	$upper = $_POST['EventProcessPerson'];
	$group = substr($group,0,1);
	$isAvailable = true;
	$query = mysql_query("SELECT * FROM `user_info` WHERE LEFT(user_group,1) = '".$group."' AND `user_id` = '".$upper."';");
	if(!mysql_num_rows($query)) $isAvailable = false;
	echo json_encode(array('valid' => $isAvailable),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

if(!$type)
{
	if($name&&$group&&$tel)
	{
		preg_match("/^\d+/",$upper,$match);
		$upper = $match[0];
		if($upper=="1") $query = mysql_query("INSERT INTO `user_info` (`user_id`,`user_name`,`user_pwd`,`user_group`,`user_tel`,`user_type`,`user_parent`) VALUES (NULL,'".$name."','123456','".$group."','".$tel."','".$job."','1');");
		else if($upper=="0")
		{
			$query = mysql_query("INSERT INTO `user_info` (`user_id`,`user_name`,`user_pwd`,`user_group`,`user_tel`,`user_type`,`user_parent`) VALUES (NULL,'".$name."','123456','".$group."','".$tel."','".$job."','1');");
			if($query) $query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
			if($query) $result = mysql_fetch_array($query);
			$id = $result['user_id'];
			$parent = $result['user_parent'].",".$id;
			$query = mysql_query("UPDATE `user_info` SET `user_parent` = '".$parent."' WHERE `user_id` = '".$id."';");
		}
		else
		{
			$upper = "1,".$upper;
			$query = mysql_query("INSERT INTO `user_info` (`user_id`,`user_name`,`user_pwd`,`user_group`,`user_tel`,`user_type`,`user_parent`) VALUES (NULL,'".$name."','123456','".$group."','".$tel."','".$job."','".$upper."');");
		}
		if($query) echo "1";
		else echo "0";
	}
}
?>