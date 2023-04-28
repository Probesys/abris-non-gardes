jQuery(document).ready(function ($) {


  // SEARCH-PANEL
  $(document).on('click','.close-search-panel-btn',function(e){
       $(".search-panel-parent").fadeOut( 300 );
  });

  $(document).on('click','.open-search-panel-btn',function(e){
       $(".search-panel-parent").fadeIn( 300 );
  });

});
