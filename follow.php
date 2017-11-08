<?php
include('header.php');
include('lib.php');

if (($user = isLogin())==false) {
	header('location:index.php');
	exit;
}

$uid = G('u');
$f = G('f');

/*
判断uid是否合法
判断uid是否是自己
*/


$r = connredis();

if ($f == 1) {
	$r->sadd('following:'.$user['userid'],$uid);
	$r->sadd('follower:'.$uid,$user['userid']);
} else {
	$r->srem('following:'.$user['userid'],$uid);
	$r->srem('follower:'.$uid,$user['userid']);
}


$uname = $r->get('user:userid:'.$uid.':username');

header('location:profile.php?u='.$uname);
exit;















include('footer.php');
?>