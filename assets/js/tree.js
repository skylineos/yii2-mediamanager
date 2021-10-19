function createJsTree(data)
{
    $('#folderTree').jstree({
        'core': {
            'data': {
                'url': '/mediamanager/api/list-directories',
                'type': 'GET',
                'contentType': 'application/json',
                'data': function(node) {
                    return { 'id': node.id };
                }
            },
            'check_callback': true,
        },
        'plugins': [
            ['contextmenu']
        ],
        'contextmenu': {
            'items': function(node) {
                var items = $.jstree.defaults.contextmenu.items();
                items.ccp = false;
                items.rename = false;

                return items;
            }
        }
    }).bind("select_node.jstree", function (e, data) {
        document.cookie = `mmpath=${data.node.id}; path=/`;
        $('#mm-upload-path').val(data.node.id);
        $.pjax.reload({
            container: '#folderContents',
            async: false
        });
    }).bind('rename_node.jstree', function(e, data) {
        $.post('/mediamanager/api/create-directory', {
            name : data.text,
            parent : data.node.parent,
        }, function( res ) {
            $('#folderTree').jstree(true).refresh();
        });

    }).bind('delete_node.jstree', function(e, data) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            // @todo: only remove the node if ajax does not return an error rather than reloding the whole thing
            if (result.value === true) {

                $.post('/mediamanager/api/delete-directory', {
                    key : data.node.id,
                }, function(data) {
                    if ( data.code !== 200 ) {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                        });

                        $('#folderTree').jstree(true).refresh();
                    } else {
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        );
                    }
                });
            } else {
                $('#folderTree').jstree(true).refresh();
            }
          });
    });
}