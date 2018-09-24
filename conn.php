<?php
define("UID","401");
define("UNAME","hust");
define("PSW","123456");

class ConnectDatabase
{
	public static function DBConnect() //发起数据库连接。
	{
		@mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
		@mysql_select_db("app_cqchust") or die("数据库不存在或不可用");			
	}
}
ConnectDatabase::DBConnect();
?>