<?php

//目标：接收数据，并把图片从临时文件存到网络范围内，返回给客户端
if(empty($_FILES['file'])) {
	exit('请上传文件');
}

$file = $_FILES['file'];

if($file['error'] !== UPLOAD_ERR_OK) {
	exit('头像上传失败');
}

//判断图片大小
if($file['size'] > 1 * 1024 * 1024) {
	exit('图片太大了');
}

if(strpos($file['type'], 'image/') !== 0) {
	exit('不支持此图片格式');
}
//截取文本扩展名
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$target = '../../static/uploads/' . uniqid() . '.' . $ext;

if(!move_uploaded_file($file['tmp_name'], $target)) {
	exit('上传图片失败');
}

$img = substr($target, 5);

echo $img;
