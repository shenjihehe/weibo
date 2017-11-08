<?php 
include('header.php');
include('lib.php');

if (($user = isLogin())==false) {
	header('location:index.php');
	exit;
}

$r = connredis();
/*
// 取出自己发的和关注的人发的微博
$r->ltrim('recivepost'.$user['userid'],0,49);
*/

//获取关注的人
$star = $r->smembers('following:'.$user['userid']);
//把自己加入其中
$star[] = $user['userid'];

$lastpull = $r->get('lastpull:userid:'.$user['userid']);
if(!$lastpull) {
	$lastpull = 0;
}

$latest = array();

//拉取数据
foreach ($star as $s) {
	$latest = array_merge($latest,$r->zrangebyscore('starpost:userid:'.$s,$lastpull+1,1<<32-1));
}

sort($latest,SORT_NUMERIC);

//更新lastpull
$r->set('lastpull:userid:'.$user['userid'],end($latest));
//循环把latest放到自己主页应该收取的微博链表里
foreach ($latest as $l) {
	$r->lpush('recivepost:'.$user['userid'],$l);
}

$r->ltrim('recivepost:'.$user['userid'],0,999);
//之多收取100微博


$newpost = $r->sort('recivepost:'.$user['userid'],array('sort'=>'desc'));


// 计算几个粉丝，几个关注（集合个数）
$myfans = $r->sCard('follower:'.$user['userid']);
$mystars = $r->sCard('folloer:'.$user['userid']);


?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $user['username']; ?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="status"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="提交"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans; ?> 粉丝<br>
<?php echo $mystars; ?>关注<br>
</div>
</div>
<?php 
foreach ($newpost as $postid) { 
	$p = $r->hmget('post:postid:'.$postid,array('userid','time','content'));
?>
<div class="post">
<a class="username" href="profile.php?u=<?php echo $p['userid']; ?>"><?php echo $p['userid']; ?></a><?php echo $p['content']; ?><br>
<i><?php echo formattimes($p['time']); ?>前 通过 web发布</i>
</div>
<?php } ?>
<?php include('footer.php');?> 