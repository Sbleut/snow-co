function showConfirmation() { // Display a confirmation dialog with translated message
    var result = window.confirm("{{ 'Trick.ConfirmDelete'|trans }}");

    // Check the user's choice and perform the action or navigate to the link
    if (result) {
        window.location.href = "{{ path('homepage') }}"; // Replace with the actual URL
    } else { // Optional code if needed
    }
}