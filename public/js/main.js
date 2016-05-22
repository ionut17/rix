$( document ).ready(function() {
    //Loader gear
    $('.loader').fadeOut(500, function() {
      console.log("fading in");
      $('.main-wrapper').fadeIn(500);
    });

    //API Dropdown listener
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

    //Notification removal
    setTimeout(function(){
      console.log("Removed notifications");
      $('.notification').slideUp('medium');
    }, 4000);
});
