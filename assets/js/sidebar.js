$(function(){
  /* Set the width of the side navigation to 250px */
  $('#openSidebar').on('click', function(){
    $('#sidebar').addClass('sidenav-open');
    $('.main.container').addClass('adjust-left');
    setTimeout(function(){
      if ($(window).width() > 992){
        $('#sidebar').find('.caption').fadeIn('fast');
      }
    }, 500);
  });

  /* Set the width of the side navigation to 0 */
  $('#closeSidebar').on('click', function(){
    $('#sidebar').find('.caption').fadeOut('fast', function(){
      $('#sidebar').removeClass('sidenav-open');
    });
    $('.main.container').removeClass('adjust-left');
  });

  $('#openSidebar').click();

  if (active_page === 'login') {
    $('.main.container').addClass('login');
  }
});
