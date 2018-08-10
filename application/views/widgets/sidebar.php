<?php if (!empty($user_info)): ?>
<div id="sidebar" class="sidenav hidden-xs hidden-sm">
  <a href="javascript:void(0)" id="closeSidebar">&times;</a>
  <a href="#">About</a>
  <a href="#">Services</a>
  <a href="#">Clients</a>
  <a href="#">Contact</a>
</div>

<div id="sidebar-mobile" class="sidenav hidden-md hidden-lg">
  <a href="#" title="About">Ab</a>
  <a href="#" title="Services">Se</a>
  <a href="#" title="Clients">Cl</a>
  <a href="#" title="Contact">Co</a>
</div>

<!-- Use any element to open the sidenav -->
<button class="navbar-toggle hidden-xs hidden-sm" id="openSidebar">
  <span class="icon-bar"></span>
  <span class="icon-bar"></span>
  <span class="icon-bar"></span>
</button>
<?php endif; ?>
