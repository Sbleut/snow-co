function moveCardTrick(toHeight) {
    var trickDetailCard = document.querySelector("#trick-detail-card");

    // Add the CSS class for the transition
    trickDetailCard.classList.add("smooth-transition");

    // Set the top margin of the trick-detail-card to the specified height.
    trickDetailCard.style.marginTop = toHeight + "px";
}

// Listen for click events on the navbar-toggler button.
document.getElementById("navbar-toggler").addEventListener('click', function (event) { // Toggle between the two predefined heights.
    var trickDetailCard = document.querySelector(".navbar");
    var currentHeight = trickDetailCard.offsetHeight;
    var newHeight = currentHeight === 57 ? 235 : 57;
    moveCardTrick(newHeight);
});

document.addEventListener("DOMContentLoaded", function () { // Initialize the margin to the default height (60px).
    moveCardTrick(73);
});