<?php
/*
key -- value设计

set user:userid:1:username zhangsan
set user:userid:1:password 11111111

set user:usename:zhangsan:userid 1

连接redis
查询用户名
是否存在
不存在则写入，存在则返回

*/

include('header.php');
include('lib.php');

if (isLogin()!=false) {
	header('location:home.php');
	exit;
}

$username = P('username');
$password = P('password');
$password2 = P('password2');


if(!$username|| !$password || !$password2){
    error('请输入完整的注册信息！');
}

if($password !== $password2){
    error('密码不一致！');
}

$r = connredis();

$r->get('user:username:'.$username.':userid');

if ($r->get('user:username:'.$username.':userid')) {
	error('用户名已被注册！请重新注册！');
}

$userid = $r->incr('golbal:userid');

$r->set('user:userid:'.$userid.':username',$username);
$r->set('user:userid:'.$userid.':password',$password);

$r->set('user:username:'.$username.':userid',$userid);

//创建50长度链表，用来展示新注册的用户
$r->lpush('newuserlink',$userid);
$r->ltrim('newuserlink',0,49);


echo "<div>";
echo "注册成功，请登录！";
echo "</br>";
echo "5s后自动跳转！";
echo "<div>";

header("Refresh:5;url=index.php");




include('footer.php');
?>