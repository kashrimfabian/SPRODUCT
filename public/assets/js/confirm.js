
document.addEventListener('DOMContentLoaded', function() {
    // Select all forms on the page
    
    const forms = document.querySelectorAll('form');
    

    // Loop through each form and attach the submit event listener
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Prevent the form from submitting immediately
            event.preventDefault();

            // Show the confirmation dialog
            const userConfirmed = confirm("Are you sure you want to to perform this action?");

            // If the user confirms, submit the form
            if (userConfirmed) {
                form.submit();
            }
        });
    });
});
