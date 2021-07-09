/**
 * Once the page is loaded, render the files within the root folder
 */
 $(document).ready( function() {
    /**
     * TOOLSIPS
     */
    $('[data-toggle="tooltip"]').tooltip();

    // Build the tree
    createJsTree();

    /** Modal Stuff */
    var opener;

    $('.modal').on('show.bs.modal', function(e) {
        opener = document.activeElement;
    });

    /**
     * Populate the input field with the selected file's effective URL
     */
    $('#folderContents').on('click', '.insert-file', function (event) {
        // @todo find a better way to get the target
        var target = $(opener).parent().parent().find('input').attr('id');

        // Update the target field
        $('#'+target).val($(this).attr('data-path'));
        $('#'+target).trigger('change');

        // Close the modal
        $('#mediaManagerModal').modal('hide');
    });

    /** Delete file */
    $('#folderContents').on('click', '.delete-file', function (event) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value === true) {
                $.post('/mediamanager/api/delete-file', { 
                    path : $(this).attr('data-path')
                }, function(data) {
                    $.pjax.reload('#folderContents');                

                });
            }
        });
    });

    $('#mm-upload-path').val(getCookie('mmpath'));
});

/**
 * Honestly, javascript? It's 2021. You literally run in the browser.
 * 
 * @param {string} name the name of the cookie 
 * @returns 
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function filemanagerTinyMCE(callback, value, meta)
{
    $('#mediaManagerModal').modal('show');
}