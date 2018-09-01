<?php
  if (!empty($user_info)):
    $menu_html = '';

    // load menu items
    foreach ($user_info['menu_items'] as $menu) {
      $active_page = $this->session->userdata('active_page');
      $active_page_method = $this->session->userdata('active_page_method');
      $url = ($active_page == $menu['controller'] && empty($menu['sub-menu'])) ? '#' : base_url($menu['url']);
      $menu_item_selected = ($active_page == $menu['controller']) ? ' menu-item-selected' : '';
      $collapsed = ($active_page == $menu['controller'] && !empty($menu['sub-menu'])) ? ' menu-item-collapsed' : '';
      $menu_html .= '<div class="menu-group'.$collapsed.$menu_item_selected.'">'
                  .'<a href="'.$url.'" title="'.$menu['caption'].'" class="menu-item">'
                    .'<i class="'.$menu['icon'].'"></i> '
                    .'<span class="caption">'.$menu['caption'].'</span>'
                  .'</a>';
      // if menu has sub-menu items, load them
      if (!empty($menu['sub-menu'])) {
        $menu_html .= '<ul class="sub-menu-items">';
        foreach ($menu['sub-menu'] as $submenu) {
          $submenu_item_selected = ($active_page_method == $submenu['url']) ? ' sub-menu-item-selected' : '';
          $menu_html .= '<li class="sub-menu-item'.$submenu_item_selected.'">'
                          .'<a href="'.base_url($menu['url'].'/'.$submenu['url']).'" title="'.($menu['caption'].' &rsaquo; '.$submenu['caption']).'">'
                          .'<i class="'.$submenu['icon'].'"></i> '
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
