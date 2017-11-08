<?php 
include('header.php');
include('lib.php');

/*
发微博

*/

$content = P('status');
if(!$content){
	error('请填写内容');
}

if (($user = isLogin())==false) {
	header('location:index.php');
	exit;
}

$r = connredis();
$postid=$r->incr('global:postid');
/*
$r->set('post:postid:'.$postid.':userid',$user['userid']);
$r->set('post:postid:'.$postid.':time',time());
$r->set('post:postid:'.$postid.':content',$content);
*/

$r->hmset('post:postid:'.$postid,array('userid'=>$user['userid'],'time'=>time(),'content'=>$content));

//把自己发的微博维护在一个有序集合中,只存20个供粉丝拉取
$r->zadd('starpost:userid:'.$user['userid'],$postid,$postid);
if ($r->zcard('starpost:userid:'.$user['userid']) >20){
	$r->zremrangebyrank('starpost:userid'.$user['userid'],0,0);
}

//把自己的微博id放到链表里，1000个供自己查看
//超过1000个的作为冷数据写入mysql
$r->lpush('mypost:userid:'.$user['userid'],$postid);
if ($r->llen('mypost:userid:'.$user['userid']) >10000) {
	$r->rpoplpush('mypost:userid:'.$user['userid'],'global:store');
}

/*
$fans = $r->smembers('follow:'.$user['userid'],array('userid'=>$user['userid'],'time'=>time(),'content'=>$content));

$fans[] = $user['userid'];

foreach ($fans as $fansid) {
	$r->lpush('recivepost:'.$fansid,$postid);
}
*/

header('location:home.php');
exit;


include('footer.php');
?>