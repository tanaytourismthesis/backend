<?php
  if (!empty($user_info)):
    $active_page_caption = $this->session->has_userdata('active_page_caption') ?
      $this->session->userdata('active_page_caption') : '';
?>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
    <?php if (!empty($active_page_caption)): ?>
      <div class="navbar-header">
        <a class="navbar-brand" href="#"><?php echo $active_page_caption; ?></a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-user"></span> <?php echo $user_info['username']; ?>
          </a>
          <ul class="dropdown-menu">
              <li><a href="<?php echo base_url('logout'); ?>"><i class="fa fa-fw fa-power-off"></i> Log Out</a></li>
          </ul>
        </li>
      </ul>
    <?php endif; ?>
    </div>
  </nav>
<?php endif; ?>
