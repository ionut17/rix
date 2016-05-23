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

    //Loader
    $( "#connect-api" ).click(function() {
      $('#normal-text').hide();
      $('#importing-text').show();
      $('.main-wrapper').fadeOut(100);
      $('.modal-backdrop').hide();
      $('.loader').fadeIn(100);
      console.log( "Importing api..." );
    });

    //Search
    var searchBox = $('#search');
    // searchBox.focus(function() {
    //   $(this).css({
    //     backgroundColor: 'white',
    //     color: '#114f2b'
    //   });
    // });
    // searchBox.blur(function(){
    //   console.log('fired');
    //   $(this).css({
    //     backgroundColor: '#114f2b',
    //     color: white
    //   });
    // });
});
