<?php
include_once("conn.php");
$state = 0;//Is login

$tel = $_POST["Tel"];
$pwd = $_POST["Password"];
$check = $_POST["Check"];

if(!$tel) $tel = $_COOKIE['Tel'];
if(!$pwd) $pwd = base64_decode($_COOKIE['Password']);//Get POST or COOKIE data

if(!$tel||!$pwd) $state=0;//Login fail
else
{
  $query = mysql_query("SELECT * FROM user_info WHERE `user_tel` = '".$tel."';");
  if($query) $result = mysql_fetch_array($query);
  if($pwd==$result['user_pwd'])
  {
	$id = $result['user_id'];
	$name = $result['user_name'];
	$group_r = $result['user_group'];
	$type_r = $result['user_type'];
	$query = mysql_query("SELECT `user_group_name` FROM `user_group` WHERE `user_group` = ".$group_r.";");
	if($query) $result = mysql_fetch_array($query);
	$group = $result['user_group_name'];
	if(substr($group_r,0,1)=="7" || $type_r == "9") $is_guest = 1;
	else $is_guest = 0;
	if($type_r=="9") $is_super = 1;
	else $is_super = 0;
	$query = mysql_query("SELECT * FROM `user_authority` WHERE `user_type` = ".$type_r.";");
	if($query) $result = mysql_fetch_array($query);
	$is_confirm = $result['is_confirm'];
	$is_add = $result['is_add'];
	$is_info = $result['is_info'];
	$is_post = $result['is_post'];
	$is_show = $result['is_show'];
	//Get user authority
	
	$query = mysql_query("SELECT `user_type_name` FROM `user_type` WHERE `user_type` = ".$type_r.";");
	if($query) $result = mysql_fetch_array($query);
	$type = $result['user_type_name'];
	//Get user info from database
		  
	if($check=="1")
	{
	  setcookie("Tel",$tel,time()+3600*24*7);
	  setcookie("Password",base64_encode($pwd),time()+3600*24*7);
	  setcookie("Id",$id,time()+3600*24*7);
	}//Write COOKIE
	
	$state=1; //Login success
  }
}
if($state==0) echo "<script>window.location.href='login.php';</script>";//Skip to login page
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>创青春·人员事务信息综合管理平台</title>
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<link href="http://cdn.bootcss.com/bootstrap-validator/0.5.0/css/bootstrapValidator.min.css" rel="stylesheet">
<link rel="stylesheet" href="common/tagsinput.css">
<link rel="stylesheet" href="common/custom.css">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="http://cdn.bootcss.com/Chart.js/1.0.1-beta.2/Chart.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap-validator/0.5.0/js/bootstrapValidator.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap-validator/0.5.0/js/language/zh_CN.js"></script>
<script src="common/autocomplete.js"></script>
<script src="common/tagsinput.min.js"></script>
<script src="common/index.js"></script>
</head>

<body data-spy="scroll" data-target="#sidebar" data-offset="50">

<!--修改密码模态框开始-->
<div class="modal fade" id="ChangePwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
        <h4 class="modal-title" id="myModalLabel">修改密码</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="ChangePwdForm" role="form" method="post" action="login.php">
          <div class="form-group">
            <label class="col-sm-4 control-label">姓名</label>
            <div class="col-sm-8">
              <p class="form-control-static"><?php echo $name; ?></p>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">手机号</label>
            <div class="col-sm-8">
              <p class="form-control-static" id="Tel"><?php echo $tel; ?></p>
              <input type="hidden" name="Tel" value="<?php echo $tel; ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="Password" class="col-sm-4 control-label">原密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="Password" name="Password" placeholder="请输入原密码。">
            </div>
          </div>
          <div class="form-group">
            <label for="NewPassword" class="col-sm-4 control-label">新密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="NewPassword" name="NewPassword" placeholder="请输入新密码。">
            </div>
          </div>
          <div class="form-group">
            <label for="ConfirmPassword" class="col-sm-4 control-label">确认新密码</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="ConfirmPassword" name="ConfirmPassword" placeholder="请重新输入新密码。">
            </div>
          </div>
          <input type="hidden" name="ChangePwd" value="1">
          <button type="submit" id="hiddenBtn" class="hidden">确认</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button class="btn btn-primary" type="submit" id="showBtn" onClick="$('#hiddenBtn').click();">确认</button>
      </div>
    </div>
  </div>
</div>
<!--修改密码模态框结束--> 

<!--查看消息模态框开始-->
<div class="modal fade" id="showMsgModal" tabindex="-1" role="dialog" aria-labelledby="showMsgLabel" aria-hidden="true">
  <div class="modal-dialog center-block">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
        <h4 class="modal-title" id="showMsgLabel">查看消息</h4>
      </div>
      <div class="modal-body" id="showMsgWrapper">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">确认</button>
      </div>
    </div>
  </div>
</div>
<!--查看消息模态框结束-->
<?php
if($is_show) echo '
<!--添加解决方案模态框开始-->
<div class="modal fade" id="AddSolution" tabindex="-1" role="dialog" aria-labelledby="SolutionLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
		<h4 class="modal-title" id="SolutionLabel">添加解决方案</h4>
	  </div>
	  <div class="modal-body">
		<form class="form-horizontal" role="form" id="AddSolutionForm">
		  <input type="hidden" name="eventId" id="eventId">
		  <div class="form-group">
			<label class="col-sm-4 control-label" for="EventSolution">解决方案</label>
			<div class="col-sm-8">
			  <textarea name="EventSolution" id="EventSolution" rows="3" placeholder="请输入解决方案" class="form-control"></textarea>
			</div>
		  </div>
		</form>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal" id="eventClose">取消</button>
		<button type="button" class="btn btn-primary" id="eventBtn">确认</button>
	  </div>
	</div>
  </div>
</div>
<!--添加解决方案模态框结束-->
';

if($is_info)
{
  echo '
<!--分组签到信息模态框开始-->
<div class="modal fade" id="detailPersonModal" tabindex="-1" role="dialog" aria-labelledby="detailPersonLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="detailPersonLabel">签到分组详情</h4>
	  </div>
	  <div class="modal-body row">
		<div class="col-sm-6">
		  <div class="form-group">
			<select multiple class="form-control" id="detailPersonForm" size="8">';
  if(substr($group_r,0,1)=="1")
  {
	  echo '  <option selected>请选择</option>
			  <option value="0">全部</option> ';
	  $query = mysql_query("SELECT * FROM `user_group`;");
	  while($row = mysql_fetch_array($query))
	  {
		  $str = '';
		  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
  }//全部组别清单
  else
  {
	 echo ' 
			  <option selected>请选择</option>
			  <option value="0">全部</option> ';
	 $query = mysql_query("SELECT * FROM `user_group` WHERE LEFT(`user_group`,1) = '".substr($group_r,0,1)."';");
	 while($row = mysql_fetch_array($query))
	 {
		$str = '';
		for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	 }
  }
  echo '
			</select>
		  </div>
		</div>
		<div class="col-sm-6">
		  <canvas id="detailPersonChart" width="150" height="150" class="center-block"></canvas>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
	  </div>
	</div>
  </div>
</div>
<!--分组签到信息模态框结束-->
  ';
}

if($is_info)
{
  echo '
<!--分组事务信息模态框开始-->
<div class="modal fade" id="detailEventModal" tabindex="-1" role="dialog" aria-labelledby="detailEventLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<h4 class="modal-title" id="detailEventLabel">事务分组详情</h4>
	  </div>
	  <div class="modal-body row">
		<div class="col-sm-6">
		  <div class="form-group">
			<select multiple class="form-control" id="detailEventForm" size="8">';
  if(substr($group_r,0,1)=="1")
  {
	  echo '  <option selected>请选择</option>
			  <option value="0">全部</option> ';
	  $query = mysql_query("SELECT * FROM `user_group`;");
	  while($row = mysql_fetch_array($query))
	  {
		  $str = '';
		  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
  }//全部组别清单
  else
  {
	echo ' 
			  <option selected>请选择</option>
			  <option value="0">全部</option> ';
	$query = mysql_query("SELECT * FROM `user_group` WHERE LEFT(`user_group`,1) = '".substr($group_r,0,1)."';");
	while($row = mysql_fetch_array($query))
	{
	  $str = '';
	  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
	  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	}
  }
  echo '
			</select>
          </div>
        </div>
		<div class="col-sm-6">
		  <canvas id="detailEventChart" width="150" height="150" class="center-block"></canvas>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">返回</button>
	  </div>
	</div>
  </div>
</div>
<!--分组事务信息模态框结束-->
  ';
}

if($is_guest)
{
  echo '
<!--来宾信息模态框开始-->
<div class="modal fade" id="showGuestModal" tabindex="-1" role="dialog" aria-labelledby="showGuestLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
		<h4 class="modal-title" id="showGuestLabel">来宾信息</h4>
	  </div>
	  <div class="modal-body">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">确认</button>
	  </div>
	</div>
  </div>
</div>
<!--来宾信息模态框结束-->
  ';
}
?>

<!--顶部导航开始-->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <img src="img/logo.png" class="logo">
      <a href="" class="navbar-brand">创青春·人员事务信息综合管理平台</a>
      <ul class="list-inline mobile text-right">
        <li class="text-right" onClick="mobileShowMsg();">查看消息<span class="badge small msgNum"></span></li>
        <li class="text-right" onClick="QuitLogin();">退出登录</li>
      </ul>
    </div>
    <div id="navbar">
      <ul class="nav navbar-nav navbar-right screen">
        <li>
          <p>欢迎您，<?php echo "来自".$group."的".$name.$type; ?></p>
        </li>
        <li>
          <p data-toggle="modal" data-target="#showMsgModal">查看消息<span class="badge small msgNum"></span></p>
        </li>
        <li>
          <p data-toggle="modal" data-target="#ChangePwd">修改密码</p>
        </li>
        <li>
          <p onClick="QuitLogin();">退出登录</p>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!--顶部导航结束-->

  <div class="container-fluid"> 
  
    <!--左侧导航开始-->
    <div class="row">
      <div class="col-sm-3 col-md-2 sidebar" id="sidebar">
<?php
if($is_info || $is_add || $is_guest) echo '
	  <ul class="nav nav-sidebar" role="tablist">';
if($is_info) echo '
		<li class="active">
		  <a href="#info">信息概览</a>
		</li>';
if($is_guest) echo '
		<li>
		  <a href="#guest">来宾信息</a>
		</li>';
if($is_add) echo '
		<li>
		  <a href="#add">人员增加</a>
		</li>';
if($is_info || $is_add || $is_guest) echo '
	  </ul>';

if($is_super) echo '
	  <ul class="nav nav-sidebar" role="tablist">
		<li>
		  <a href="#message">发布中心</a>
		</li>
	  </ul>';
echo '
	  <ul class="nav nav-sidebar" role="tablist">
		<li>
		  <a href="#sign">签到</a>
		</li>';
if($is_confirm) echo '
		<li>
		  <a href="#confirm">
			确认签到
			<span class="badge" id="confirmPrompt"></span>
		  </a>
		</li>';
echo '
	  </ul>';
if($is_post || $is_show) echo '
	  <ul class="nav nav-sidebar" role="tablist">';
if($is_post) echo '
		<li>
		  <a href="#post">发布事件</a>
		</li>';
if($is_show) echo '
		<li>
		  <a href="#show">
			事件查看
			<span class="badge" id="eventPrompt"></span>
		  </a>
		</li>';
if($is_post || $is_show) echo '
	  </ul>';
?> 
      </div>
    </div>
    <!--左侧导航结束-->
    
    <!--正文开始-->
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    
<?php 
if($is_info) echo '
	  <!--信息概览开始-->
	  <h1 class="page-header">
	  <a id="info">信息概览</a>
	  </h1>
	  <div class="row placeholders" id="info">
	  
	  <!--签到信息开始-->
	  <div class="col-xs-12 col-sm-6">
		<h3 class="sub-header text-left">签到信息</h3>
		<div class="row">
		  <div class="col-xs-12 col-sm-6">
			<canvas id="staffChart" width="200" height="200" class="center-block"></canvas>
		  </div>
		  <div class="col-xs-12 col-sm-6" id="staffChartLegend">
		  </div>
		</div>
	  </div>
	  <!--签到信息结束-->
	  
	  <!--事务信息开始-->
	  <div class="col-xs-12 col-sm-6">
		<h3 class="sub-header text-left">事务信息</h3>
		<div class="row">
		  <div class="col-xs-12 col-sm-6">
			<canvas id="eventChart" width="200" height="200" class="center-block"></canvas>
		  </div>
		  <div class="col-xs-12 col-sm-6" id="eventChartLegend">
		  </div>
		</div>
	  </div>           
	  <!--事务信息结束-->
	  
	  <!--人员列表开始-->
	  <div class="col-xs-7 col-sm-9 screen">
		<h3 class="sub-header text-left" style="margin-top:15px;">人员列表</h3>
	  </div>
	  <div class="col-xs-5 col-sm-3 screen">
		<form id="searchPerson">
		  <div class="form-group">
			<div class="input-group input-group-sm">
			  <span class="input-group-addon">
				<span class="glyphicon glyphicon-search"></span>
			  </span>
			  <input type="text" class="form-control" name="searchPerson" id="AutoSearchPerson" placeholder="姓名、组别或手机号，回车搜索">
			</div>
		  </div>
		</form>
	  </div>
	  <div class="table-responsive screen" id="showPersonWrapper">
	  </div>
	  <!--人员列表结束-->
	  
	  </div>
	  <!--信息概览结束--> ';
  
if($is_guest) echo '
	  <!--来宾信息开始-->
	  <h1 class="page-header"><a id="guest">来宾信息</a></h1>
	  <div class="row placeholders">
		<div class="col-xs-12 col-sm-8" id="guestTableWrapper">
		</div>
		<div class="col-xs-12 col-sm-4 text-left" id="guestFormWrapper">
		</div>
	  </div>
	  <!--来宾信息结束--> ';

if($is_add)
{
  echo '
	  <!--人员增加开始-->
	  <h1 class="page-header screen"><a id="add">人员增加</a></h1>
	  <div class="row placeholders screen">
		<form role="form" id="AddForm" action="add.php" method="post">
		  <div class="form-inline">
			<div class="col-sm-2 col-sm-offset-1">
			  <div class="form-group">
				<label class="sr-only" for="AddName">姓名</label>
				<input type="text" class="form-control" name="AddName" placeholder="姓名">
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="AddGroup">组别</label>
				<select class="form-control group" name="AddGroup" id="AddGroup">';
	  if($type_r=="9"||($type_r=="5"&&$group_r=="1"))
	  {
		  echo '  <option selected>组别</option>
		       ';
		  $query = mysql_query("SELECT * FROM `user_group`;");
		  while($row = mysql_fetch_array($query))
		  {
			  $str = '';
			  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
			  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
		  }
	  }//全部组别清单
	  else
	  {
	  echo '<option selected>需求方</option>';
	  $query = mysql_query("SELECT * FROM `user_group` WHERE LEFT(`user_group`,".strlen($group_r).") = '".$group_r."';");
	  while($row = mysql_fetch_array($query))
	  {
		$str = '';
		for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
	  }
	  echo '
				</select>
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="AddJob">职务</label>
				<select class="form-control job" name="AddJob">
				  <option selected>职务</option>
				  <option value="2">负责人</option>
				  <option value="1">志愿者小组长</option>
				  <option value="0">志愿者</option>
				</select>
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="AddTel">联系电话</label>
				<input type="tel" class="form-control" name="AddTel" placeholder="手机号">
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="AddUpper">直接上级编号</label>
				<input type="text" class="form-control" name="AddUpper" id="AddUpper" placeholder="直接上级编号">
			  </div>
			</div>
		  </div>
          <div class="col-sm-12">
		  <div class="form-group">
			<button type="submit" class="btn btn-lg btn-primary center-block" id="AddPerson">添加人员</button>
		  </div>
          </div>
		</form>
	  </div>
	  <!--人员增加结束--> ';
}

if($is_super)
{
  echo '
	  <!--发布中心开始-->
	  <h1 class="page-header screen"><a id="message">发布中心</a></h1>
      <ul class="nav nav-tabs screen" role="tablist">
		<li class="active">
		  <a href="#msgTab" role="tab" data-toggle="tab">站内发布</a>
		</li>
		<li>
		  <a href="#smsTab" role="tab" data-toggle="tab">短信发布</a>
		</li>
      </ul>
	  <div class="tab-content screen">
		<div class="tab-pane active" id="msgTab">
		  <div class="row placeholders" id="msgSendForm">
		    <div class="col-sm-8" id="msgSendWrapper"></div>
			<div class="col-sm-4 well text-left">
			  <form role="form" id="msgSend" method="post" action="message.php">
				<div class="form-group">
				  <label for="msgContent">消息内容</label>
				  <textarea class="form-control" rows="3" name="msgContent" id="msgContent" placeholder="请输入消息内容"></textarea>
				</div>
				<div class="form-group">
				  <label for="msgReceive">接收对象</label>
				  <input class="form-control" type="text" name="msgReceive" id="msgReceive" placeholder="请输入接收对象" data-role="tagsinput">
				</div>
				<div class="form-group">
				  <button type="submit" class="btn btn-primary center-block" id="btnSendMsg">确定发送</button>
				</div>
			  </form>
			</div>
		  </div>
		</div>
		<div class="tab-pane" id="smsTab">
		  <div class="row placeholders" id="smsSendForm">
		    <div class="col-sm-8" id="smsSendWrapper"></div>
			<div class="col-sm-4 well text-left">
			  <form role="form" id="smsSend" method="post" action="sms.php">
				<div class="form-group">
				  <label for="smsContent">短信内容</label>
				  <textarea class="form-control" rows="3" name="smsContent" id="smsContent" placeholder="请输入短信内容"></textarea>
				</div>
				<div class="form-group">
				  <label for="smsReceive">接收对象</label>
				  <input class="form-control" type="text" name="smsReceive" id="smsReceive" placeholder="请输入接收对象" data-role="tagsinput">
				</div>
				<div class="form-group">
				  <button type="submit" class="btn btn-primary center-block" id="btnSendSms">确定发送</button>
				</div>
			  </form>
			</div>		
		  </div>
	    </div>
	  </div>
	  <!--发布中心结束-->';
}

echo '
      <!--签到开始-->
      <h1 class="page-header"><a id="sign">签到</a></h1>
      <div class="row placeholders">
        <div class="alert alert-warning text-left" id="TimeCountDown" role="alert">
        </div>
        <div class="col-xs-12 col-sm-10" id="showSignWrapper">
        </div>
        <div class="col-xs-12 col-sm-2">
          <div class="form-group">
            <input type="hidden" id="hiddenTimeStr">
            <button class="btn btn-lg btn-default" id="signBtn" disabled> <span class="glyphicon glyphicon-remove-sign"></span> 未开始 </button>
          </div>
        </div>
      </div>
      <!--签到结束--> ';
	  
if($is_confirm) echo '
  
	  <!--确认签到开始-->
	  <h1 class="page-header"><a id="confirm">确认签到</a></h1>
	  <div class="row placeholders" id="showConfirmWrapper">
	  </div>
	  <!--确认签到结束-->';
	
if($is_post)
{
  echo '
	  <!--发布事件开始-->
	  <h1 class="page-header">
		<a id="post">发布事件</a>
	  </h1>
	  <div class="row placeholders">
		<form role="form" id="AddEvent">
		  <div class="form-inline">
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventRequire">需求方</label>
				<select class="form-control" name="EventRequire" id="EventRequire">';
	if($type_r=="9" || ($type_r=="3" && $group_r=="111")) 
	{
	  echo '  <option selected>需求方</option>
	       ';
	  $query = mysql_query("SELECT * FROM `user_group`;");
	  while($row = mysql_fetch_array($query))
	  {
		  $str = '';
		  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
	}//全部组别清单
	else
	{
	  echo '<option selected>需求方</option>';
	  $query = mysql_query("SELECT * FROM `user_group` WHERE LEFT(`user_group`,1) = '".substr($group_r,0,1)."';");
	  while($row = mysql_fetch_array($query))
	  {
		$str = '';
		for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
	}
	echo '
				</select>
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventRequirePerson">需求方责任人</label>
				<input class="form-control" type="text" name="EventRequirePerson" id="EventRequirePerson" placeholder="需求方责任人">
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventTime">时间</label>
				<input class="form-control" type="text" name="EventTime" placeholder="时间">
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventPlace">地点</label>
				<input class="form-control" type="text" name="EventPlace" placeholder="地点">
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventProcess">处理方</label>
				<select class="form-control" name="EventProcess" id="EventProcess">';
	  echo '  <option selected>处理方</option>
	       ';
	  $query = mysql_query("SELECT * FROM `user_group`;");
	  while($row = mysql_fetch_array($query))
	  {
		  $str = '';
		  for($i=1;$i<strlen($row['user_group']);$i++) $str.='　';
		  echo '<option value="'.$row['user_group'].'">'.$str.$row['user_group_name'].'</option>';
	  }
      echo '</select>
			  </div>
			</div>
			<div class="col-sm-2">
			  <div class="form-group">
				<label class="sr-only" for="EventProcessPerson">处理方责任人</label>
				<input class="form-control" type="text" name="EventProcessPerson" id="EventProcessPerson" placeholder="处理方责任人">
			  </div>
			</div>
		  </div>
		  <div class="col-sm-12">
			<div class="form-group">
			  <label class="sr-only" for="EventContent">详细需求</label>
			  <textarea class="form-control" name="EventContent" placeholder="详细需求" rows="3"></textarea>
			</div>
		  </div>
		  <div class="form-group">
			<button type="submit" class="btn btn-lg btn-primary center-block" id="PostEvent">发布事件</button>
		  </div>
		</form>
	  </div>
	  <!--发布事件结束--> ';
}

if($is_show) echo '
	  <!--事件查看开始-->
	  <h1 class="page-header screen"><a id="show">事件查看</a></h1>
	  <div class="row placeholders screen">
		<div class="col-xs-7 col-sm-9">
		  <h3 class="sub-header text-left">事件清单</h3>
		</div>
		<div class="col-xs-5 col-sm-3 searchEvent">
		  <form id="searchEvent">
			<div class="form-group">
			  <div class="input-group input-group-sm">
				<span class="input-group-addon">
				  <span class="glyphicon glyphicon-search"></span>
				</span>
				<input type="text" class="form-control" name="searchEvent" id="AutoSearchEvent" placeholder="流水号或需求方，回车搜索">
			  </div>
			</div>
		  </form>
		</div>
		<div class="col-xs-12 col-sm-8" id="showEventWrapper">
		</div>
		<div class="screen col-sm-4">
		  <div class="table-responsive" id="showEventPanel">
		  </div>
		</div>
	  </div>
	  <!--事件查看结束--> ';
?>
    </div>
    <!--正文结束--> 
  </div>
  <script>
  var msg = $('#msgReceive');
  msg.tagsinput({
	tagClass: function(item) {
	  switch (item.type) {
		case '发送整组'   : return 'label label-primary';break;
		case '发送个人'  : return 'label label-success';break;
		case '发送全体'  : return 'label label-danger';break;
		default:return 'label label-default';break;
	  }
	},
	itemValue: 'value',
	itemText: 'text'
  });
  var sms = $('#smsReceive');
  sms.tagsinput({
	tagClass: function(item) {
	  switch (item.type) {
		case '发送整组'   : return 'label label-primary';break;
		case '发送来宾整类': return 'label label-primary';break;
		case '发送来宾个人': return 'label label-success';break;		
		case '发送个人'  : return 'label label-success';break;
		case '发送全体'  : return 'label label-danger';break;
		case '发送来宾' : return 'label label-danger';break;

		default:return 'label label-default';
	  }
	},
	itemValue: 'value',
	itemText: 'text'
  });
  </script>
</body>
</html>