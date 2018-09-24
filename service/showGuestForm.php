<?php
include_once("../conn.php");

$tel = trim($_POST['tel']);

$query = mysql_query("SELECT * FROM `user_info` WHERE `user_tel` = '".$tel."';");
if($query) $result = mysql_fetch_array($query);
$id = $result['user_id'];
$query = mysql_query("SELECT * FROM `guest_info` WHERE `guest_user` = '".$id."'");
if(mysql_num_rows($query))
{
	$html = '<form role="form" action="addGuest.php" method="post" id="AddGuest" class="small">';
	$result = mysql_fetch_array($query);
	if($result['guest_arrival_time']=="") $arrival_time = "请补充"; else $arrival_time = $result['guest_arrival_time'];
	if($result['guest_arrival_trans']=="") $arrival_trans = "请补充"; else $arrival_trans = $result['guest_arrival_trans']; 
	if($result['guest_departure_time']=="") $departure_time = "请补充"; else $departure_time = $result['guest_departure_time'];
	if($result['guest_departure_trans']=="") $departure_trans = "请补充"; else $departure_trans = $result['guest_departure_trans'];
	if($result['guest_food']=="") $food = "请补充"; else $food = $result['guest_food'];
	if($result['guest_other']=="") $other = "请补充"; else $other = $result['guest_other'];
	if(intval($result['guest_type'] < 9))	$html.='
              <div class="form-group">
                <label for="ArrivalTime">抵达武汉时间</label>
                  <input type="text" class="form-control input-sm" id="ArrivalTime" name="ArrivalTime" value="'.$arrival_time.'">
			  </div>
			  <div class="form-group">
                <label for="ArrivalTrans">抵达武汉交通信息</label>
                  <input type="text" class="form-control input-sm" id="ArrivalTrans" name="ArrivalTrans" value="'.$arrival_trans.'">
              </div>
              <div class="form-group">
                <label for="DepartureTime">离开武汉时间</label>
                  <input type="text" class="form-control input-sm" id="DepartureTime" name="DepartureTime" value="'.$departure_time.'">
			  </div>
			  <div class="form-group">
                <label for="DepartureTrans">离开武汉交通信息</label>
                  <input type="text" class="form-control input-sm" id="DepartureTrans" name="DepartureTrans" value="'.$departure_trans.'">
              </div>
              <div class="form-group">
                <label for="Food">特殊饮食习惯</label>
                  <input type="text" class="form-control input-sm" id="Food" name="Food" value="'.$food.'">
			  </div>';
	$html.='<div class="form-group">
                <label for="Other">其他信息</label>
                  <textarea class="form-control input-sm" rows="3" id="Other" name="Other">'.$other.'</textarea>
              </div>
			  <div class="form-group text-center">
                <button type="button" class="btn btn-primary" id="guestFormAdd">提交补充</button>
			  </div>
			</form>';
	echo $html;
}
?>