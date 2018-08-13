<?php
  if (!empty($user_info)):
    $menu_html = '';

    // load user info
    // code here...

    // load menu items
    foreach ($user_info['menu_items'] as $menu) {
      $active_page = $this->session->userdata('active_page');
      $url = ($active_page == $menu['controller']) ? '#' : $menu['url'];
      $selected = ($active_page == $menu['controller']) ? ' menu-item-selected' : '';
      $collapsed = ($active_page == $menu['controller'] && !empty($menu['sub-menu'])) ? ' menu-item-collapsed' : '';
      $menu_html .= '<div class="menu-group'.$collapsed.'"><a href="'.base_url($url).'" title="'.($menu['caption']).'" class="menu-item'.$selected.'">'
                    .'<i class="fa '.$menu['icon'].'"></i> '
                    .'<span class="caption">'.$menu['caption'].'</span>'
                  .'</a>';
      // if menu has sub-menu items, load them
      if (!empty($menu['sub-menu'])) {
        $menu_html .= '<ul class="sub-menu-items">';
        foreach ($menu['sub-menu'] as $submenu) {
          $menu_html .= '<li>'
                          .'<a href="'.base_url($menu['url'].'/'.$submenu['url']).'">'
                          .'<i class="fa '.$submenu['icon'].'"></i> '
                          .'<span class="caption">'.$submenu['caption'].'</span></a>'
                        .'</li>';
        }
        $menu_html .= '</ul>';
      }
      $menu_html .= '</div>';
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
