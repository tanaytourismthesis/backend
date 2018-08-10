<?php
  if (!empty($user_info)):
    $menu_html = '';

    foreach ($user_info['menu_items'] as $menu) {
      $menu_html .= '<a href="'.($menu['url']).'" title="'.($menu['caption']).'">'.($menu['caption']).'</a>';
    }
?>
    <div id="sidebar" class="sidenav hidden-xs hidden-sm">
      <a href="javascript:void(0)" id="closeSidebar">&times;</a>
      <?php echo $menu_html; ?>
    </div>

    <div id="sidebar-mobile" class="sidenav hidden-md hidden-lg">
      <?php echo $menu_html; ?>
    </div>

    <!-- Use any element to open the sidenav -->
    <button class="navbar-toggle hidden-xs hidden-sm" id="openSidebar">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
<?php endif; ?>
