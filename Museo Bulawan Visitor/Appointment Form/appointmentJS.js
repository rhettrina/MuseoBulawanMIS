const appointmentForm = document.getElementById("appointmentForm");

// Add event listener for form submission
appointmentForm.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default submission
    openAppointmentModal(); // Trigger modal
});

// Function to open the modal after validation
function openAppointmentModal() {
    const requiredFields = appointmentForm.querySelectorAll("[required]");
    let hasMissingFields = false;

    // Validate required fields
    requiredFields.forEach((field) => {
        if (!field.value.trim()) {
            hasMissingFields = true;
            field.style.borderColor = "red"; // Highlight missing fields
        } else {
            field.style.borderColor = ""; // Reset if valid
        }
    });

    // Show modal only if no missing fields
    if (!hasMissingFields) {
        document.getElementById("appointmentModal").style.display = "flex";
    }
}

// Close the modal
function closeAppointmentModal() {
    document.getElementById("appointmentModal").style.display = "none";
}

// Confirm submission of the form
function confirmAppointmentSubmission() {
    closeAppointmentModal();
    appointmentForm.submit(); // Submit the form programmatically
}

// Add event listeners to modal buttons
document
    .getElementById("confirmAppointmentBtn")
    .addEventListener("click", confirmAppointmentSubmission);

document
    .getElementById("closeAppointmentModalBtn")
    .addEventListener("click", closeAppointmentModal);
