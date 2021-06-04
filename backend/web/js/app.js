$('body').on('beforeSubmit', 'form', function() {
    $(this).find('[type=submit]').attr('disabled', true).addClass('disabled');
});