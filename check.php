<?php
include_once("conn.php");

$tel = $_POST['Tel'];
$pwd = $_POST['Password'];

if($tel&&$pwd)
{
	$query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	if($pwd == $result['user_pwd']) $isAvailable = true;
	else $isAvailable = false;
}
else $isAvailable = false;

echo json_encode(array('valid' => $isAvailable),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
?>