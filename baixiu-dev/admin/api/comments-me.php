<?php
//引入封装的公共函数
require_once '../../functions.php';

//链接数据库，拿到数据
$sql = sprintf('select
	comments.*,
	posts.title as post_title
	from comments
	inner join posts on comments.post_id = posts.id
	order by comments.created desc')
xiu_fetch_all($sql);