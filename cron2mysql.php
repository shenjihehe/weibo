<?php 

include('lib.php');

$r = connredis();
$res = mysql_connect('127.0.0.1','root','root');
mysql_query('use weibo',$conn);
mysql_query('set names utf-8',$conn);

while($r->llen('global:store') >= 1000){


	$sql = 'insert into post(postid,userid,username,time,content) values';
	$i = 0;

	while ($i++<1000) {
		$postid = $r->rpop('global:store');
		$post = $r->hmget('post:postid:'.$postid,array('userid','username','time','content'));
		$sql .="($postid,".$post['userid'].",'".$post['username']."',".$post['time'].",'".$post['content']."'),";
	}	



	$sql = substr($sql,0,-1);
	mysql_query($sql,$coon);
}

//连接mysql，写入数据



echo "ok";

if($i == 0){
	echo "no job";
	exit;
}

?>