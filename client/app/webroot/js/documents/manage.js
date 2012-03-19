$(function() {
    // add new document button clicked
    $('#btn-add-document').on('click', function() {
        $(this).fadeOut('fast', function() {
            $('#form-add-document').fadeIn('medium');
        });
    });

    // create document list
    new List('document-list', {valueNames: [ 'document-name', 'document-url' ]});
});
