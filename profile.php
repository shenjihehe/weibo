<?php 
include('header.php');
include('lib.php');

if (($user = isLogin())==false) {
	header('location:index.php');
	exit;
}

/*
每人有自己的粉丝记录 follower
每人有自己的关注记录 following

following aid(bid)
follower bid(aid)

获取用户名
查询id
查询此id，是否在following集合里

*/
$r = connredis();

$u = G('u');
$prouid = $r->get('user:username:'.$u.':userid');
/*
判断是否是用户自己进入自己的详情页
是的话则隐藏关注按钮
*/
if ($u == $user['userid']) {
	$hidden= 'hidden';
} else {
	$hidden ="";
}



/*
判断uid是否合法
判断uid是否是自己
是自己则隐藏关注键
*/

if (!$prouid) {
	error('非法用户！');
	exit;
}

$isf = $r->sismember('following:'.$user['userid'],$prouid);
$isfstatus = $isf?'0':'1';
$isfword = $isf?'取消关注':'关注ta';


?>

<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<h2 class="username"><?php echo $prouid;  ?></h2>
<a  <?php echo $hidden; ?> href="follow.php?u=<?php echo $prouid; ?>&f=<?php echo $isfstatus; ?>" class="button"><?php echo $isfword; ?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>

<?php include('footer.php');?>