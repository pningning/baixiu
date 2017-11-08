<?php
//引入公共函数文件
require_once '../functions.php';
//获取当前登录用户信息
xiu_get_current_user();

//====接收筛选参数=============================

//查询分类
$categories = xiu_fetch_all('select * from categories');

//设置初始的筛选条件
$where = '1 = 1';
//设置初始的初始的跳转参数
$search = '';

if(isset($_GET['category']) && $_GET['category'] !== 'all') {
  $where .= ' and posts.category_id=' . $_GET['category'];
  $search .= '&category=' . $_GET['category'];
}

if(isset($_GET['status']) && $_GET['status'] !== 'all') {
  $where .= " and posts.status = '{$_GET['status']}'";
  $search .= '&status=' . $_GET['status'];
}

//=========接受处理提交的分页参数====================

$size = 20;//每页显示多少条
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];//因为提交的数据都是字符串类型。要转成数据类型
//如果页数小于时，跳转到第一页
if($page < 1) {
  header('Location: /admin/posts.php?page=1' . $search);
}
//计算出总共的页数
$total_count = (int)xiu_fetch_one("select count(1) as count from posts
  inner join categories on posts.category_id = categories.id
  inner join users on posts.user_id = users.id
  where {$where}")['count'];
  $total_pages = (int)ceil($total_count / $size);
 
  //如果页数大于最大页数时，跳转到最大的一页
  if($page > $total_pages) {
    header('Location: /admin/posts.php?page=' . $total_pages . $search);
  }
//分页处理计算越过多少条
$offset = ($page - 1) * $size;
//关联查询数据
$posts = xiu_fetch_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where}
order by posts.created desc
limit {$offset}, {$size};");
//=====处理分页查询=======================================

//显示多少页的变量
$visiables = 5;

//计算出最大值和最小显示的页码
$begin = $page - ($visiables-1)/2;
$end = $begin + $visiables-1;

//解决合理性问题
//如果开始的位置小于1的时候
$begin = $begin < 1 ? 1 : $begin;//小于的时候等于1
$end = $begin + $visiables - 1;//固定结束的位置
//如果结束的位置大于总页数的时候
$end = $end > $total_pages ? $total_pages : $end;//大于总页数的时候，等于总页数
$begin = $end - $visiables + 1;//固定开始和结尾的位置
//如果页数很少的时候，在限制一下开始的位置
$begin = $begin < 1 ? 1 : $begin;


//====数据格式转换==========================================
//发布时间函数
function data($created) {
  //设置时区
  date_default_timezone_set('PRC');
  //把获取到的时候转换为时间戳
  $timestamp = strtotime($created);
  return date('Y年m月d日<b\r>H:i:s', $timestamp);
}

//状态函数
function status($status) {
  //创建一个数组，把所有的状态放在里面
  $dict = array(
    'published' => '已发布',
    'drafted' => '草稿',
    'trashed' => '回收站'
    );
  //判断状态是否存在，并把当前的状态返回
  return isset($dict[$status]) ? $dict[$status] : '未知';
}

//======================================================
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="/admin/posts_delete.php" style="display: none" id="btn-delete">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($categories as $value): ?>
              <option value="<?php echo $value['id']?>"<?php echo isset($_GET['category']) && $_GET['category'] === $value['id'] ? ' selected' : ''; ?>>
                <?php echo $value['name']; ?>
              </option>
            <?php endforeach; ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] === "drafted" ? ' selected' : ''; ?>>
            草稿</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] === "published" ? ' selected' : ''; ?>>
            已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] === "trashed" ? ' selected' : ''; ?>>
            回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <?php for($i = $begin; $i <= $end; $i++): ?>
          <li<?php echo $i === $page ? ' class="active"' : ''; ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
          <?php endfor; ?>
          <li><a href="#">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center"f>发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($posts as $value): ?>
          <tr>
            <td class="text-center"><input type="checkbox" data-id="<?php echo $value['id']; ?>"></td>
            <td><?php echo $value['title']; ?></td>
            <td><?php echo $value['user_name']; ?></td>
            <td><?php echo $value['category_name']; ?></td>
            <td class="text-center"><?php echo data($value['created'])?></td>
            <td class="text-center"><?php echo status($value['status'])?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts_delete.php?id=<?php echo $value['id']?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function($) {
      var $bodyInput = $('tbody input');
      var $btn = $('#btn-delete');

        //创建数组存储改变元素的id
        var allCheckeds = [];
      $bodyInput.on('change', function() {
        //获取到当前改变元素的id
        var id = $(this).data('id');
        //判断当前的是否是改变了
        if($(this).prop('checked')) {
          allCheckeds.includes(id) || allCheckeds.push(id);
        } else {
          //如果如果没有改变，删除这一项
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }

        //根据数组剩余的id数量，，来判断是否显示批量删除
        allCheckeds.length ? $btn.fadeIn() : $btn.fadeOut()
        //从a标签的属性中有search，属性，包含?id= 属性值
        //字符串跟数组相加，数组，会转换成字符串
        $btn.prop('search', '?id=' + allCheckeds)

      });
      //全选按钮

      $('thead input').on('change', function() {
        var checked =  $(this).prop('checked');
        $bodyInput.prop('checked', checked).trigger('change');
      })


    })
  </script> 
  <script>NProgress.done()</script>
</body>>
</html>
