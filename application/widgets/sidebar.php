<?php
  class sidebar extends Widget {
    public function display($args = array()) {
      $args['user_info'] = $this->session->userdata('user_info') ?? [];
      $this->view('widgets/sidebar', $args);
    }
  }
?>
