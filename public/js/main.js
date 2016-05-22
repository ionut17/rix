$( document ).ready(function() {
    $('.loader').fadeOut(500, function() {
      console.log("fading in");
      $('.main-wrapper').fadeIn(500);
    });

    $( "#api" ).select(function() {
      console.log("called");
    });

    var apiSelect = $('#api_select');
    var slideshareWrapper = $('#slideshare_account_wrapper');
    apiSelect.bind('change', function (e) {
      if(apiSelect.val() == 'slideshare') {
        slideshareWrapper.slideDown('medium');
      }
      else {
        slideshareWrapper.slideUp('medium');
      }
    }).trigger('change');
});
