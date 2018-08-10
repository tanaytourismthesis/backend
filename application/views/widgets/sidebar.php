<?php
  if (!empty($user_info)):
    $menu_html = '';

    foreach ($user_info['menu_items'] as $menu) {
      $menu_html .= '<a href="'.($menu['url']).'" title="'.($menu['caption']).'" class="menu-item">'
                  .'<i class="fa '.$menu['icon'].'"></i> '
                  .'<span class="caption">'.$menu['caption'].'</span>'
                  .'</a>';
    }
?>
    <div id="sidebar" class="sidenav">
      <a href="javascript:void(0)" class="closebtn hidden-xs hidden-sm" id="closeSidebar">&times;</a>
      <button class="navbar-toggle hidden-xs hidden-sm" id="openSidebar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <?php echo $menu_html; ?>
    </div>
    <!-- Use any element to open the sidenav -->
<?php endif; ?>
