jQuery(document).ready(function($) {
  $('.mos-pmf-wrapper .tab-nav > a').click(function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      //alert('#mos-pmf-' + id);
      $('#mos-pmf-'+id).addClass('active');
      $('#mos-pmf-'+id).siblings().removeClass('active');
      $(this).closest('.tab-nav').addClass('active');
      $(this).closest('.tab-nav').siblings().removeClass('active');
      //$(this).closest('.tab-nav').css("background-color", "red");
  });
});