jQuery(document).ready(function ($) {

  // Nav btn
  $(document).on('click','.menu-btn',function(e){
       $(".first-bar").toggleClass("open");
      $(".second-bar").toggleClass("open");
      $(".third-bar").toggleClass("open");
      $("body").toggleClass("nav-open");
  });
  $(document).on('click','.main-nav-link',function(e){
    $(".first-bar").removeClass("open");
    $(".second-bar").removeClass("open");
    $(".third-bar").removeClass("open");
    $("body").removeClass("nav-open");
  });
});
