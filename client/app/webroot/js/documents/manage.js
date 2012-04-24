$(function() {
    // add new document button clicked
    $('#btn-add-document').on('click', function() {
        $(this).fadeOut('fast', function() {
            $('#form-add-document').fadeIn('medium');
        });
    });

    // verify button pressed
    $('.btn-verify').on('click', function() {
        // determine document to verify
        var id = $(this).parents('.document-row').attr('data-document-id');
        $(this).html('Verifying...').attr('disabled', true);

        // verify document with a random host
        var t = $(this);
        $.getJSON(webroot + 'documents/verify/' + id, function(response) {
            if (response.success) {
                // documents are the same
                if (response.same) {
                    // update button
                    t.html('<i class="icon-ok icon-white"></i> Verifed!').attr('disabled', false)
                        .addClass('btn-success');
                }

                // documents are different, so view diff
                else {
                    // documents can be compared
                    if (response.compare) {
                        // create url to diff file with host it was compared against
                        var url = webroot + 'documents/diff/' + response.document;
                        url += '?compare=' + response.compare;

                        // update button
                        t.html('<i class="icon-remove icon-white"></i> View differences').attr('disabled', false)
                            .attr('href', url).attr('target', '_blank').addClass('btn-danger');
                    }

                    // documents cannot be compared
                    else {
                        // update button
                        t.html('<i class="icon-remove icon-white"></i> Documents are different!').addClass('btn-danger');
                    }
                }
            }
        });
    });

    // create document list
    new List('document-list', {valueNames: ['document-name']});
});
