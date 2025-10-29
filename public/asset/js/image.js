$(document).on('change', '.images', function() {
    const imageId = this.id;
    const file = this.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (event) {
            $("#review-" + imageId).attr("src", event.target.result);
            $("#old-" + imageId).val(0);
        };
        reader.readAsDataURL(file);
    }
});
