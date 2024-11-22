
    // Add validation check for the preferred date
    const preferredDateInput = document.getElementById('preferred_date');

    // Set a minimum date to prevent selecting past dates
    const today = new Date().toISOString().split('T')[0];
    preferredDateInput.setAttribute('min', today);

    // Add a custom invalid message
    preferredDateInput.addEventListener('invalid', function () {
        this.setCustomValidity("Please select a valid date that is not in the past.");
    });

    // Clear the custom message once the input is valid
    preferredDateInput.addEventListener('input', function () {
        this.setCustomValidity('');
    });

    const form = document.querySelector("form[action='process_form.php']");

    // Add event listener for form submission
    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent automatic submission
        validateForm(); // Validate the form
    });
    
    // Function to validate the form and open the modal
    function validateForm() {
        const requiredFields = form.querySelectorAll("[required]");
        let hasMissingFields = false;
    
        // Check each required field
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasMissingFields = true;
                field.style.borderColor = "red"; // Highlight missing fields
            } else {
                field.style.borderColor = ""; // Reset style for valid fields
            }
        });
    
        // Show confirmation modal if all required fields are valid
        if (!hasMissingFields) {
            openModal();
        }
    }
    
    // Function to open the confirmation modal
    function openModal() {
        document.getElementById("confirmationModal").style.display = "flex";
    }
    
    // Function to close the confirmation modal
    function closeModal() {
        document.getElementById("confirmationModal").style.display = "none";
    }
    
    // Function to confirm and submit the form
    function confirmSubmission() {
        closeModal(); // Close the modal
        form.submit(); // Submit the form programmatically
    }
    
    // Add event listeners for modal buttons
    document.getElementById("confirmBtn").addEventListener("click", confirmSubmission);
    document.getElementById("closeModalBtn").addEventListener("click", closeModal);
    