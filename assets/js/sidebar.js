$(function(){
  /* Set the width of the side navigation to 250px */
  $('#openSidebar').on('click', function(){
    $('#sidebar, #sidebar-mobile').addClass('sidenav-open')
    $('.main.container').addClass('adjust-left');
  });

  /* Set the width of the side navigation to 0 */
  $('#closeSidebar').on('click', function(){
    $('#sidebar, #sidebar-mobile').removeClass('sidenav-open')
    $('.main.container').removeClass('adjust-left');
  });
  
  $('#openSidebar').click();
});
