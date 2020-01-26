$(document).ready(function() {
    // Set up the picker to update target timezone and country select lists.
    $('#timezone-image').timezonePicker({
        target: '#edit-date-default-timezone',
        countryTarget: '#edit-site-default-country'
    });
});