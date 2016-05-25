$( document ).ready(function() {
  //Tutorial text
  var tutorialArray = [
    "<p class='bold'>Welcome to RIX (Resource Interactive Explorer)</p><p>Our application gives you the opportunity to gather all your tech content in one place. We support services like: Github, Pocket, Slideshare and Vimeo. Follow the tutorial and get started by adding content from your favourite services.</p>",
    "<p class='bold'>Start connecting with other services</p><p>You can add an account by clicking on the Add account button or by going to the Settings page and clicking on Connect account button.</p>",
    "<p class='bold'>Select your desired service</p><p>You can then select the service with which you want to connect, in order to transfer your content from the selected account to RIX (Resource Interactive Explorer).</p>",
    "<p class='bold'>Find your articles easily with our search tool</p><p>In order to find an article by title you can type into the search bar and all the articles that match will be shown. The list is updated as you type.</p>",
    "<p class='bold'>Filter your content and see what you want</p><p>By clicking on Filters you will be able to choose the services from which you want to see your content. Deselect/select services as you like.</p>",
    "<p class='bold'>We suggest you to refresh your content periodically</p><p> But you can also refresh by clicking on Refresh content button on the Settings page, and your content will be updated to the one on your added accounts.</p>",
    "<p class='bold'>See more content by going to recommendations</p><p> On the Recommended content page you can see articles that are similar with your added content. This might take a while as we try our best to give you interesting content.</p>"
  ];


  //Tutorial json
  var tutorialWrapper = $('.tutorial-wrapper');
  var tutorial = $('.tutorialr');
  var tutorialImage = $('.tutorial-image');
  var tutorialContent = $('.tutorial-content');
  var continueTutorial = $('#continue-tutorial');
  var finishTutorial = $('#finish-tutorial');
  var closeTutorial = $('#close-tutorial');
  //Actions
  finishTutorial.hide();
  continueTutorial.show();
  tutorialWrapper.show();
  closeTutorial.click(function() {
    tutorialWrapper.hide();
    $.ajax({
      url: '/hidetutorial',
      type: 'POST',
      success: function (data) {
        console.log(data);
      }
    });
  });

  $step = 1;
  tutorialImage.css('background-image', 'url(\'img/tutorial/'+($step)+'.jpg\')');
  tutorialContent.append(tutorialArray[$step-1]);
  continueTutorial.click(function() {
    $step = $step + 1;
    tutorialContent.empty();
    tutorialContent.append(tutorialArray[$step-1]);
    tutorialImage.css('background-image', 'url(\'img/tutorial/'+($step)+'.jpg\')');
    if ($step == tutorialArray.length){
      continueTutorial.hide();
      finishTutorial.show();
    }
  });
  finishTutorial.click(function() {
    tutorialWrapper.hide();
    $.ajax({
      url: '/hidetutorial',
      type: 'POST',
      success: function (data) {
        console.log(data);
      }
    });
  });

});
