var uploader = new Dropzone('#mm-file-upload-form', { 
    init: function() {
        this.on("success", function(file) {
            // Clear the dropzone and add the new file
            this.removeAllFiles();
            $.pjax.reload('#folderContents');
        });
    }
});