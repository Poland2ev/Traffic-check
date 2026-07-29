<?php $is_admin = $_settings->userdata('type') == 1; ?>
</style>
<aside class="main-sidebar sidebar-dark-primary text-white bg-lightblue disabled elevation-4 sidebar-no-expand">
  <a href="<?php echo base_url ?>admin" class="brand-link bg-lightblue text-sm">
    <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8;width: 1.7rem;height: 1.7rem;max-height: unset">
    <span class="brand-text font-weight-light"><?php echo $_settings->info('short_name') ?></span>
  </a>
  <div class="sidebar">
    <div class="clearfix"></div>
    <nav class="mt-4">
      <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
        <?php if($is_admin): ?>
        <li class="nav-item dropdown">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Tổng quan</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=offenses" class="nav-link nav-offenses">
            <i class="nav-icon fas fa-file-alt"></i>
            <p><?php echo $is_admin ? 'Biên bản vi phạm' : 'Biên bản của tôi' ?></p>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=drivers" class="nav-link nav-drivers">
            <i class="nav-icon fas fa-id-card"></i>
            <p><?php echo $is_admin ? 'Danh sách xe/người vi phạm' : 'Xe của tôi' ?></p>
          </a>
        </li>

        <?php if($is_admin): ?>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=reports" class="nav-link nav-reports">
            <i class="nav-icon fas fa-file"></i>
            <p>Báo cáo</p>
          </a>
        </li>
        <li class="nav-header">Quản trị dữ liệu</li>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=maintenance/offenses" class="nav-link nav-maintenance_offenses">
            <i class="nav-icon fas fa-traffic-light"></i>
            <p>Danh mục lỗi vi phạm</p>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
            <i class="nav-icon fas fa-users"></i>
            <p>Người dùng</p>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
            <i class="nav-icon fas fa-cogs"></i>
            <p>Cài đặt hệ thống</p>
          </a>
        </li>
        <?php else: ?>
        <li class="nav-item dropdown">
          <a href="<?php echo base_url ?>admin/?page=support" class="nav-link nav-support">
            <i class="nav-icon fas fa-headset"></i>
            <p>Liên hệ hỗ trợ</p>
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>
<script>
  $(document).ready(function(){
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : ($is_admin ? 'home' : 'offenses') ?>';
    page = page.split('/').join('_');
    if($('.nav-link.nav-'+page).length > 0){
      $('.nav-link.nav-'+page).addClass('active')
    }
  })
</script>
