const form = document.getElementById("donationForm");

form.addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent automatic form submission
    openModal(); // Open the modal for confirmation
});

// Function to check for missing required fields and open modal if valid
function openModal() {
    const requiredFields = form.querySelectorAll("[required]");
    let hasMissingFields = false;

    // Check each required field
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            hasMissingFields = true;
            field.style.borderColor = "red"; // Highlight missing fields
        } else {
            field.style.borderColor = ""; // Reset style if filled
        }
    });

    // Show modal only if all required fields are filled
    if (!hasMissingFields) {
        document.getElementById('confirmationModal').style.display = 'flex';
    }
}

// Close modal
function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}

// Confirm and submit form
function confirmSubmission() {
    closeModal();
    form.submit(); // Programmatically submit the form after confirmation
}
