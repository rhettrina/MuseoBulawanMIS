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
// appointmentJS.js

// Function to open the confirmation modal
function openModal(event) {
    event.preventDefault(); // Prevent the form from submitting immediately
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'block'; // Show the modal
}

// Function to close the confirmation modal
function closeModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none'; // Hide the modal
}

// Event listeners for modal buttons
document.getElementById('confirmBtn').addEventListener('click', function() {
    // Submit the form after confirmation
    document.getElementById('appointmentForm').submit();
    closeModal();
});

document.getElementById('closeModalBtn').addEventListener('click', function() {
    closeModal();
});

// Attach the openModal function to the form's submit event
document.getElementById('appointmentForm').addEventListener('submit', openModal);
