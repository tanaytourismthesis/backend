$(function(){
  /* Set the width of the side navigation to 250px */
  $('#openSidebar').on('click', function(){
    $('#sidebar').css({
      'width' : '250px'
    });

    $('.main.container').addClass('adjust-left');
  });

  /* Set the width of the side navigation to 0 */
  $('#closeSidebar').on('click', function(){
    $('#sidebar').css({
      'width' : '0'
    });

    $('.main.container').removeClass('adjust-left');
  });
  $('#openSidebar').click();
});
