<?php
/*
流程
接收post
判断合法性和完整性
查询用户名是否存在
查询密码是否匹配
登陆成功
设置cookies


*/
include('header.php');
include('lib.php');

if (isLogin()!=false) {
	header('location:home.php');
	exit;
}

$username = P('username');
$password = P('password');

if(!$username || !$password){
    error('请输入完整！');
}

$r = connredis();
$userid = $r->get('user:username:'.$username.':userid');

if(!$userid){
	error('用户不存在！');

}

$realpass = $r->get('user:userid:'.$userid.':password');

if ($password != $realpass) {
	error('密码不对！');
}

$authsecret = randsecret();
$r->set('user:userid:'.$userid.':authsecret',$authsecret);
// 设置cookie
setcookie('username',$username);
setcookie('userid',$userid);
setcookie('authsecret',$authsecret);

include('footer.php');
header('location:home.php');
exit;
?>