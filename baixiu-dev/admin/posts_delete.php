<?php
require_once '../functions.php';
//接受数据
if(empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];

//查询数据库
$rows = xiu_execute('delete from posts where id in (' . $id . ');');


//跳回文章页
//http 中的 referer 用来标识当前请求的来源
//获取方式 $_SERVER['HTTP_REFERER'];
header('Location:' . $_SERVER['HTTP_REFERER']);