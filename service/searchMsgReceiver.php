<?php
include("../conn.php");
error_reporting(0);


$content = $_GET['query'];
if($content=="志愿者")
{
  $suggestions = array();
  $suggestions[0] = array("value" => "志愿者","data" => array("type"=>'发送全体','id'=>'C0'));
  $return = array('query'=>$content,'suggestions'=>$suggestions);
  echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
} 
else if($content=="来宾")
{
  $suggestions = array();
  $suggestions[0] = array("value" => "来宾","data" => array("type"=>'发送来宾','id'=>'F0'));
  $return = array('query'=>$content,'suggestions'=>$suggestions);
  echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}
else
{
  $query = mysql_query("SELECT * FROM `user_info` i LEFT JOIN `user_group` g USING(`user_group`) WHERE `user_tel` LIKE '%".$content."%' OR `user_name` LIKE '%".$content."%' ORDER BY LEFT(`user_group`,1), LENGTH(`user_group`),`user_type` DESC;");
  $suggestions = array();
  if(mysql_num_rows($query))
  {
	$k = 0;
	while($row = mysql_fetch_array($query))
	{
	  $group_name = "";
	  for($i=0;$i<strlen(str_replace(" ","",$row['user_group']));$i++)
	  {
		$j = $i;
		$query_group = mysql_query("SELECT * FROM `user_group` WHERE `user_group` = '".substr(str_replace(" ","",$row['user_group']),0,$j+1)."';");
		$result = mysql_fetch_array($query_group);
		$group_name .= $result['user_group_name'].' ';
	  }
	  $group_name = substr($group_name,0,-1);
	  $suggestions[$k] = array('value'=>$row['user_name']." - ".$group_name,'data'=>array('type'=>"发送个人",'id'=>'A'.$row['user_id']));
	  $k++;
	}
  }
  $query = mysql_query("SELECT * FROM `user_group` WHERE `user_group_name` LIKE '%".$content."%' ORDER BY LEFT(`user_group`,1), LENGTH(`user_group`);");
  if(mysql_num_rows($query))
  {
	$k = count($suggestions);
	while($row = mysql_fetch_array($query))
	{
	  $suggestions[$k] = array('value'=>$row['user_group_name'],'data'=>array('type'=>"发送整组",'id'=>'B'.$row['user_group']));
	  $k++;
	}
  }
  $query = mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_name` LIKE '%".$content."%' OR `guest_from` LIKE '%".$content."%' OR `guest_tel` LIKE '%".$content."%' ORDER BY `guest_type`,`guest_id`;");
  if(mysql_num_rows($query))
  {
	$k = count($suggestions);
	while($row = mysql_fetch_array($query))
	{
	  $suggestions[$k] = array('value'=>$row['guest_name']." - ".$row['guest_type_name'],'data'=>array('type'=>"发送来宾个人",'id'=>'D'.$row['guest_id']));
	  $k++;
	}
  }
  $query = mysql_query("SELECT * FROM `guest_type` WHERE `guest_type_name` LIKE '%".$content."%';");
  if(mysql_num_rows($query))
  {
	$k = count($suggestions);
	while($row = mysql_fetch_array($query))
	{
	  $suggestions[$k] = array('value'=>$row['guest_type_name'],'data'=>array('type'=>"发送来宾整类",'id'=>'E'.$row['guest_type']));
	  $k++;
	}
  }
  $return = array('query'=>$content,'suggestions'=>$suggestions);
  echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
}

?>