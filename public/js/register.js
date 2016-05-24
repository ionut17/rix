$( document ).ready(function() {

    //AJAX Search
    $(":input").on('input', function() {
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
        }
      });
    });
    
});
