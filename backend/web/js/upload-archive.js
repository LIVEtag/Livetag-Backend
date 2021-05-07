//directUrlInputId and videoFileInputId should be defined in view file

$(function () {
  $('#type-link').on('click', function () {
    checkDirectUrl();
  });

  $('#type-upload').on('click', function () {
    checkFileInput();
  });

  function checkDirectUrl() {
    if ($('#type-link').is(':checked')) {
      $(directUrlInputId).prop('disabled', false);
      $(videoFileInputId).fileinput('lock').fileinput('refresh');
    }
  }

  function checkFileInput() {
    if ($('#type-upload').is(':checked')) {
      var directurlInput = $(directUrlInputId);
      directurlInput.val('');
      directurlInput.prop('disabled', true);
      $(videoFileInputId).fileinput('unlock').fileinput('refresh');
    }
  }

  checkDirectUrl();
  checkFileInput();
});