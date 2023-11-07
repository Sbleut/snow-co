document.getElementById("responsive-pics-button").addEventListener('click', function (event) {
    var block = $("#pics-n-video");

    // Check the current display property.
    var display = block.css("display");

    if (display === "none") { // If it's currently hidden, show the block.
        block.show();
    } else { // If it's currently visible, hide the block.
        block.hide();
    }
});