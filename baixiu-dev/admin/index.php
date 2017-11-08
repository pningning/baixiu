<?php 
//引入配置文件
require_once '../config.php';

// 校验数据当前访问用户的 箱子（session）有没有登录的登录标识

session_start();
if (empty($_SESSION['current_login_user'])) {
  // 没有当前登录用户信息，意味着没有登录
  header('Location: /admin/login.php');
  exit();
}

//建立链接
$conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);

if(!$conn) {
  die('<<h1>链接失败</h1>>');
}
//查询文章总数
$posts = mysqli_query($conn, 'select count(1) from posts;');
if(!$posts) {
  die('<h1>查询失败</h1>');
}
$result_posts = mysqli_fetch_row($posts)[0];
  //查询草稿总数
$drafted = mysqli_query($conn, "select count(1) from posts where status = 'drafted';");
if(!$drafted) {
  die('<h1>查询失败</h1>');
}
$result_drafted = mysqli_fetch_row($drafted)[0];
//查询分类
$categories = mysqli_query($conn, "select count(1) from categories;");
if(!$categories) {
  die('<h1>查询失败</h1>');
}
$result_categories = mysqli_fetch_row($categories)[0];
//查询评论
$comments = mysqli_query($conn, "select count(1) from comments;");
if(!$comments) {
  die('<h1>查询失败</h1>');
}
$result_comments = mysqli_fetch_row($comments)[0];
//待审核评论
$held = mysqli_query($conn, "select count(1) from comments where status = 'held';");
if(!$held) {
  die('<h1>查询失败</h1>');
}
$result_held = mysqli_fetch_row($held)[0];

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>One Belt, One Road</h1>
        <p>Thoughts, stories and ideas.</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.html" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $result_posts; ?></strong>篇文章（<strong><?php echo $result_drafted; ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $result_categories; ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $result_comments; ?></strong>条评论（<strong><?php echo $result_held; ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4"></div>
      </div>
    </div>
  </div>

  <?php $current_page = 'index'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
