<?php

require_once '../functions.php';

xiu_get_current_user();
//添加的函数
function add_category() {
  if(empty($_POST['name'])) {
    $GLOBALS['message'] = '请输入名称';
    $GLOBALS['success'] = false;
    return;
  }
  if(empty($_POST['slug'])) {
    $GLOBALS['message'] = '请输入别名';
    $GLOBALS['success'] = false;
    return;
  }
  //接受并保持数据
  $name = $_POST['name'];
  $slug = $_POST['slug'];

  $rows = xiu_execute("insert into categories values (null, '{$slug}', '{$name}');");
  //判断是否添加成功
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0;
}

//编辑的函数
function edit_category() {
  global $current_edit_category;
  //接收并保存
  $id = $current_edit_category['id'];
  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;

  //查询数据库

  $rows = xiu_execute("update categories set slug = '{$slug}', name = '{$name}' where id = {$id}");

  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
}

//判断提交的时候是否待id参数
if(empty($_GET['id'])) {
  //如果不存在id为添加
  //判断是不是post提交
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //执行添加函数
    add_category();
  }
} else {
    //如果提交带有id
    //拿到提交的id对应的数据，渲染到页面上

    $current_edit_category = xiu_fetch_one('select * from categories where id = ' . $_GET['id']);
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_category();

    }
}

// function add_category () {
//   if (empty($_POST['name']) || empty($_POST['slug'])) {
//     $GLOBALS['message'] = '请完整填写表单！';
//     $GLOBALS['success'] = false;
//     return;
//   }

//   // 接收并保存
//   $name = $_POST['name'];
//   $slug = $_POST['slug'];

//   // insert into categories values (null, 'slug', 'name');
//   $rows = xiu_execute("insert into categories values (null, '{$slug}', '{$name}');");

//   $GLOBALS['success'] = $rows > 0;
//   $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
// }

// // 如果修改操作与查询操作在一起，一定是先做修改，再查询
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   // 一旦表单提交请求，就意味着是要添加数据
//   add_category();
// }

// 查询全部的分类数据
$categories = xiu_fetch_all('select * from categories;');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <?php if ($success): ?>
      <div class="alert alert-success">
        <strong>成功！</strong> <?php echo $message; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
        <?php if(isset($current_edit_category)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
            <h2>编辑《<?php echo $current_edit_category['name']; ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">更新</button>
            </div>
          </form>
        <?php else: ?>
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif; ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">

                  <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
  $(function($) {

    var $tbodyCheckbox = $('tbody input')
    var $btnDelete = $('#btn_delete')
    //定义一个数组，记录被选中的
    var allCheckeds = []
    //添加改变事件
    $tbodyCheckbox.on('change', function() {
      //通过data函数获取到被选中的对象的id，并存到变量中
      var id = $(this).data('id')
      
      //判断当前这个对象是否被选中，决定是否添加这个对象的id
      if($(this).prop('checked')) {
        //把选中的id存入到数组中
        allCheckeds.push(id)
      }else {
        //如果没有被选中，从数组中把读的id删除
        allCheckeds.splice(allCheckeds.indexOf(id),1)
      }

      //根据剩下被选中的CheckBox(通过数组的长度) 决定是否显示批量删除
      
      allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
      // allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()

      $btnDelete.prop('search', '?id=' + allCheckeds)

    })

    //全选按钮
    $('thead input').on('change', function() {
      //获取当前全选框的状态
      var checked = $(this).prop('checked')
      //给下面所有的选框
      $tbodyCheckbox.prop('checked', checked).trigger('change')
    })



  })




    //#方法一=========================
    // $(function($){ 
    //   var $tbodyCheckbox = $('tbody input')
    //   var $btnDelete = $('#btn_delete')
    //   $tbodyCheckbox.on('change', function(){
    //     //有任意一个checkbox选中就显示，反之隐藏
    //     //进行遍历的得到的元素数组
    //     var flag = false;
    //     $tbodyCheckbox.each(function(i, item) {
    //       if($(item).prop('checked')) {
    //         flag = true;
    //       }
    //     })

    //     flag ? $btnDelete.fadeIn() : $btnDelete.fadeOut()
    //   })
    // })
  </script>
  <script>NProgress.done()</script>
</body>
</html>

