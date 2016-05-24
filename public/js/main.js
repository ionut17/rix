$( document ).ready(function() {
    //Variables
    var searchBox = $('#search');
    var searchResults = $('#search-results');

    searchResults.hide();

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
      $('#text').html('Importing articles');
      $('.main-wrapper').fadeOut(100);
      $('.modal-backdrop').hide();
      $('.loader').fadeIn(100);
      console.log( "Importing api..." );
    });

    //refresh
    $('#refresh-content').click(function() {
      $('#text').html('Refreshing content');
      $('.main-wrapper').fadeOut(100);
      $('.modal-backdrop').hide();
      $('.loader').fadeIn(100);
      console.log( "Refreshing content..." );
    });

    //generating recommendations
    $('#recommended-generate').click(function() {
      $('#text').html('Generating recommendations');
      $('.main-wrapper').fadeOut(100);
      $('.modal-backdrop').hide();
      $('.loader').fadeIn(100);
      console.log( "Refreshing content..." );
    });


    //Search
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

    //AJAX Search
    $('#search-clear').hide();
    setTimeout(function(){
      searchBox.on('input', function() {
        var searchInput = $(this).val();
        searchResults.hide();
        searchResults.empty();
        if (searchInput.length>0){
          $('#search-normal').hide();
          $('#search-clear').show();
          if (searchInput.length>=3){
            $.ajax({
              url: '/search',
              type: 'GET',
              data: {search_string: searchInput},
              success: function (data) {
                searchResults.empty();
                searchResults.show();
                //Building li's
                console.log(data.length);
                if (data.length>0){
                  $.each(data, function(index, value){
                    var currentItem = "<a href='"+value.url+"'><li><h3>"+value.title+"</h3><label>"+value.type+"</label></li></a>";
                    var wrapper = "<div style='display:none' id='wrapper"+value.id+"'>"+currentItem+"</div>";
                    searchResults.append(wrapper);
                    $('#wrapper'+value.id).slideDown('fast');
                  });
                }
                else{
                  var currentItem = "<a href=''><li><h3>No results found</h3><label></label></li></a>";
                  var wrapper = "<div style='display:none' id='wrapper0'>"+currentItem+"</div>";
                  searchResults.append(wrapper);
                  $('#wrapper0').slideDown('fast');
                }
              }
            });
          }
        } else{
          $('#search-normal').show();
          $('#search-clear').hide();
        }
      });
    }, 500);

    //Clear on x-click
    $('#search-clear').click(function() {
      searchBox.val("");
      $('#search-normal').show();
      $('#search-clear').hide();
    });

    //Filters
    var filterOption = $( "#filter-option" );
    var filterContent = $("#filter-content");
    var filterSave = $('#filter-save');
    $.ajax({
      url: '/filters',
      type: 'GET',
      success: function (data) {
        if (data.github==1){
          $('#github-opt').prop('checked',true);
        }
        else $('#github-opt').prop('checked',false);
        if (data.pocket==1){
          $('#pocket-opt').prop('checked',true);
        }
        else $('#pocket-opt').prop('checked',false);
        if (data.slideshare==1){
          $('#slideshare-opt').prop('checked',true);
        }
        else $('#slideshare-opt').prop('checked',false);
        if (data.vimeo==1){
          $('#vimeo-opt').prop('checked',true);
        }
        else $('#vimeo-opt').prop('checked',false);
      }
    });
    filterOption.click(function() {
      filterContent.slideToggle('fast');
    });

    filterSave.click(function() {
      var githubSwitch, pocketSwitch, slideshareSwitch, vimeoSwitch;
      if ($('#github-opt').is(':checked')){
        githubSwitch = 1;
      }
      else {
        githubSwitch = 0;
      }
      if ($('#pocket-opt').is(':checked')){
        pocketSwitch = 1;
      }
      else {
        pocketSwitch = 0;
      }
      if ($('#slideshare-opt').is(':checked')){
        slideshareSwitch = 1;
      }
      else {
        slideshareSwitch = 0;
      }
      if ($('#vimeo-opt').is(':checked')){
        vimeoSwitch = 1;
      }
      else {
        vimeoSwitch = 0;
      }
      console.log(githubSwitch,pocketSwitch,slideshareSwitch,vimeoSwitch);
      $.ajax({
        url: '/filters',
        type: 'POST',
        data: {
          github: githubSwitch,
          pocket: pocketSwitch,
          slideshare: slideshareSwitch,
          vimeo: vimeoSwitch
        },
        success: function (data) {
          console.log(data);
          window.location.href = '/mycontent';
          // location.reload();
        }
      });
    });

});
