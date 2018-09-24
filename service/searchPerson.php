<?php
  include_once("../conn.php");
  error_reporting(0);

  $content = $_GET['query'];
  if($content)
  {
	  $query = mysql_query("SELECT * FROM (((`user_info` i LEFT JOIN `user_group` g USING(`user_group`)) LEFT JOIN `user_type` t USING(`user_type`)) LEFT JOIN `guest_info` gi ON i.`user_id` = gi.`guest_user`) WHERE `user_tel` LIKE '%".$content."%' OR `user_name` LIKE '%".$content."%' OR `user_group_name` LIKE '%".$content."%' OR `guest_name` LIKE '%".$content."%' OR `guest_from` LIKE '%".$content."%' ORDER BY LEFT(`user_group`,1), LENGTH(`user_group`),`user_type` DESC;");
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

			$query_sign = mysql_query("SELECT * FROM `sign_list` ORDER BY `sign_id` DESC;");
			$sign_result = mysql_fetch_array($query_sign);
			$query_sign = mysql_query("SELECT * FROM `user_sign` WHERE `user_id` = '".$row['user_id']."';");
			if(!mysql_num_rows($query_sign)) $issign = '<span class="text-danger"><strong>未签到</strong></span>';
			else
			{
				$result = mysql_fetch_array($query_sign);
				switch($result[$sign_result['sign_title']])
				{
					case '0': $issign = '<span class="text-danger"><strong>未签到</strong></span>';break;
					case '1': $issign = '<span class="text-warning"><strong>待确认</strong></span>';break;
					case '2': $issign = '<span class="text-warning"><strong>已确认</strong></span>';break;
					default:break;
				}
			}
			$query_guest= mysql_query("SELECT * FROM `guest_info` LEFT JOIN `guest_type` USING(`guest_type`) WHERE `guest_user` = '".$row['user_id']."';");
			if(mysql_num_rows($query_guest)) $guest_html = '<button class="btn btn-xs btn-primary" type="button" data-toggle="modal" data-target="#showGuestModal">查看</button></td>';
			else $guest_html = '';
			$html = '<tr><td>'.$row['user_id'].'</td><td>'.$row['user_name'].'</td><td>'.$issign.'</td><td>'.$group_name.'</td><td>'.$row['user_type_name'].'</td><td>'.$row['user_tel'].'</td><td>'.$guest_html.'</td></tr></tbody><tfoot><tr><td colspan="7"></td></tr></tfoot></table>';
			$suggestions[$k] = array('value'=>$row['user_name']." - ".$row['user_tel'],'data'=>array('group'=>$row['user_group_name'],'name'=>$row['user_name'],'html'=>$html));
			$k++;
		}
		$return = array('query'=>$content,'suggestions'=>$suggestions);
		echo json_encode($return,JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
	  }
	  else json_encode(array('query'=>$content,'suggestions'=>$suggestions),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
  }
  else
  {
	  $content = $_POST['searchPerson'];
	  $query = mysql_query("SELECT * FROM `user_info` WHERE `user_name` = '".$content."';");
	  if(!mysql_num_rows($query))
	  {
		  echo json_encode(array('valid' => false),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
	  }
	  else echo json_encode(array('valid' => true),JSON_HEX_QUOT+JSON_HEX_TAG+JSON_HEX_APOS);
  }  
?>