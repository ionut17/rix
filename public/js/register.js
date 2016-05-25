$( document ).ready(function() {
    $('.button').prop('disabled',true);
    $('.error').hide();
    //AJAX Search
    $(":input").on('blur', function() {
      $.ajax({
        url: '/validator',
        type: 'GET',
        data: {
          username: $('#username').val(),
          email: $('#email').val(),
          password: $('#password').val(),
          rpassword: $('#rpassword').val()
        },
        success: function (data) {
          console.log(data);
          if (data=='ok'){
            $('.error').hide();
            $('.button').prop('disabled',false);
          } else{
            $('.error').show();
            $('.error').html(data);
          }
        }
      });
    });

});
