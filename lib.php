<?php

error_reporting(E_ALL & ~E_NOTICE);


// 基础函数库

function P($key){
    return $_POST[$key];
}

function G($key){
    return $_GET[$key];
}

// 报错函数
function error($msg){
	echo "<div>";
    echo $msg;
    echo "</div>";
    include('footer.php');
    exit;
}

// 连接redis
function connredis(){
	static $r = null;

	if ($r !==null) {
		return $r;
	}
	$r = new redis();
	$r->connect('localhost');

	return $r;
}

// 判断是否登录
function isLogin(){
	if (!$_COOKIE['userid'] || !$_COOKIE['username'] ) {
		return false;
	}
	if (!$_COOKIE['authsecret']) {
		return false;
	}
	$r = connredis();
	$authsecret = $r->get('user:userid:'.$_COOKIE['userid'].':authsecret');
	if ($authsecret!=$_COOKIE['authsecret']) {
		return false;
	}
	return array('userid'=>$_COOKIE['userid'],'username'=>$_COOKIE['username']);
}

function randsecret(){
	$str = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%%^&*()';
	return substr(str_shuffle($str),0,16);
}

function formattimes($time){
	$sec = time() - $time;
	if ($sec>=31536000){
		return floor($sec/31536000).'年';
	} else if ($sec>=2592000) {
		return floor($sec/2592000).'月';
	} else if ($sec>=86400) {
		return floor($sec/86400).'天';
	}else if ($sec>=3600) {
		return floor($sec/3600).'小时';
	} else if ($sec>=60) {
		return floor($sec/60).'分钟';
	} else {
		return $sec . '秒';
	}
		
	
}

?>