$(function() {
    // download button pressed
    $('.btn-download').on('click', function() {
        // determine document to verify
        var id = $(this).parents('.document-row').attr('data-document-id');
        $(this).html('Downloading...').attr('disabled', true);

        // download document onto host
        var t = $(this);
        $.getJSON(webroot + 'documents/download/' + id, function(response) {
            if (response.success) {
                // update button
                t.html('<i class="icon-ok icon-white"></i> Downloaded!').addClass('btn-success');
            }
        });
    });

    // create document list
    new List('document-list', {valueNames: ['document-name']});
});
