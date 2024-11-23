document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById("appointmentForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent automatic form submission
        openModal(); // Open the modal for confirmation
    });
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
            field.nextElementSibling?.classList.add('text-danger'); // Show error (if applicable)
        } else {
            field.style.borderColor = ""; // Reset style if filled
        }
    });

    // Custom validation for organization and attendees (if needed)
    const organization = form.querySelector("#organization");
    const attendees = form.querySelector("#attendees_count");

    if (organization && !organization.value.trim()) {
        hasMissingFields = true;
        organization.style.borderColor = "red";
    }

    if (attendees && (!attendees.value.trim() || isNaN(attendees.value) || attendees.value <= 0)) {
        hasMissingFields = true;
        attendees.style.borderColor = "red";
    }

    // Show modal only if all required fields are filled
    if (!hasMissingFields) {
        document.getElementById("confirmationModal").style.display = "flex";
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

// Add event listener for confirm button
document.getElementById("confirmBtn").addEventListener("click", confirmSubmission);

// Add event listener for close button
document.getElementById("closeModalBtn").addEventListener("click", closeModal);
