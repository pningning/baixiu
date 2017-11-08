<?php

//根据客户端提交过来的id删除对应数据
//引入函数文件
require_once '../functions.php';

//校验
if(empty($_GET['id'])) {
	exit('缺少必要参数');
}

$id = $_GET['id'];
//存在sql注入问题

//查询数据
$rows = xiu_execute('delete from categories where id in(' . $id . ');');

//删除完毕，跳转到目录页
header('Location: /admin/categories.php');