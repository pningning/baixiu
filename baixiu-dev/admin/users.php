<?php
//引入配置文件
require_once '../functions.php';

//获取到当前登录用户信息，如果没有获取到跳转到登录页面
xiu_get_current_user();

//添加用户
function add_user() {
  //TODO:验证表单
  //TODO: 保存表单数据
  //TODO: 添加到数据库
}

//编辑用户
function edit_user() {
  //TODO:校验表单信息
  //TODO:保存提交的表单数据
  //TODO:添加到数据库
}

//判断是不是带有id的提交
if(empty($_GET['id'])) {
  //判断是不是post提交
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
  }
} else {
  //TODO：根据提交过来的id查询数据，渲染到页面
  if($SERVER['REQUEST_METHOD'] === 'POST') {
    edit_user();
  }


}
  

//查询信息渲染页面
$users = xiu_fetch_all('select * from users');

?>



<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($users as $value): ?>
              <tr>
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $value['avatar']?>"></td>
                <td><?php echo $value['email']; ?></td>
                <td><?php echo $value['slug']; ?></td>
                <td><?php echo $value['nickname']; ?></td>
                <td><?php echo $value['status'] === 'activated' ? '激活' : '待激活'; ?></td>
                <td class="text-center">
                  <a href="/admin/users.php?id=<?php echo $value['id'];?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach; ?>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    //TODO：给checkbox添加改变事件，通过事件得到被选中的checkbox的id，根据被选中的checkbox的数量，判断批量删除的显示
    //TODO:设置批量删除的herf,提交表单，掉转到服务端进行接收查询
  </script>
  <script>NProgress.done()</script>
</body>
</html>
