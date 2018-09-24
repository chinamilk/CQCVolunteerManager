<?php
  include_once("conn.php");
  
  $isChangePwd = $_POST['ChangePwd'];
  $tel = $_POST['Tel'];
  $oldpwd = $_POST['Password'];
  $newpwd = $_POST['ConfirmPassword'];
  $state = 0;
  
  $action = $_GET['action'];
  if($action == 1)
  {
	  setcookie("Id","",time()-1);
	  setcookie("Tel","",time()-1);
	  setcookie("Password","",time()-1);
	  $state = 2;
  }
  else $state = 0; //Quit Login
  
  if($tel && $oldpwd && $newpwd && $isChangePwd)
  {
	$query = mysql_query("SELECT `user_pwd` FROM `user_info` WHERE `user_tel` = '".$tel."';");
	if($query) $result = mysql_fetch_array($query);
	
	if($oldpwd == $result['user_pwd'])
	{
		$state = 1;
		$query = mysql_query("UPDATE `user_info` SET `user_pwd` = '".$newpwd."' WHERE `user_tel` = '".$tel."';");
		setcookie("Id","",time()-1);
		setcookie("Tel","",time()-1);
		setcookie("Password","",time()-1);
	}
	else $state = 0;
  }
  else $state = 0; // Change Password
?>  
<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>登录 | 创青春·人员事务信息综合管理平台</title>
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
    <link href="http://cdn.bootcss.com/bootstrap-validator/0.5.0/css/bootstrapValidator.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/custom.css">
	<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap-validator/0.5.0/js/bootstrapValidator.min.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap-validator/0.5.0/js/language/zh_CN.js"></script>
    <script src="common/main.js"></script>
  </head>

  <body>
    <div class="container login">
      <img src="img/title.jpg" class="img-responsive center-block"/>
      <div class="well">
        <?php if($state == 1) echo '<div class="alert alert-success"><strong>恭喜！</strong> 密码修改成功，请重新登录。</div>'; ?>
        <?php if($state == 2) echo '<div class="alert alert-success"><strong>恭喜！</strong> 当前用户已退出，请重新登录。</div>'; ?>
        <form class="form-horizontal" role="form" action="index.php" method="post" id="Form">
          <div class="form-group">
            <label for="inputTel" class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="Tel" name="Tel" placeholder="请输入手机号">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="Password" name="Password" placeholder="请输入密码" autocomplete="off" onfocus="this.type='password'">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="Check" value="1"> 记住密码
                </label>
              </div>
            </div>
          </div>
          <div class="form-group">
              <button type="submit" class="btn btn-primary center-block">登录</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>