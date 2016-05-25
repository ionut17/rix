$( document ).ready(function() {
  //Tutorial text
  var tutorialArray = [
    "<p>Welcome to RIX (Resource Interactive Explorer). Our application gives you the opportunity to gather all your tech content in one place. We support services like: Github, Pocket, Slideshare and Vimeo.</p><p>Follow the tutorial and get started by adding content from your favourite service</p>",
    "<p>Step 2</p>",
    "<p>Step 3</p>",
    "<p>Step 4</p>",
    "<p>Step 5</p>",
    "<p>Step 6</p>",
    "<p>Step 7</p>"
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
