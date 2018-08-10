<?php
  if (!empty($user_info)):
    $menu_html = '';
    foreach ($user_info['menu_items'] as $menu) {
      $active_page = $this->session->userdata('active_page');
      $url = ($active_page == $menu['controller']) ? '#' : $menu['url'];
      $selected = ($active_page == $menu['controller']) ? ' menu-item-selected' : '';
      $menu_html .= '<a href="'.$url.'" title="'.($menu['caption']).'" class="menu-item'.$selected.'">'
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
