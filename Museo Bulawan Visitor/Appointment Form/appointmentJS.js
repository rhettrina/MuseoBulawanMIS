document.addEventListener("DOMContentLoaded", function() {
    const appointmentForm = document.getElementById("appointmentForm");
    const confirmBtn = document.getElementById("confirmAppointmentBtn");
    const closeBtn = document.getElementById("closeAppointmentModalBtn");

    if (appointmentForm) {
        appointmentForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission
            openAppointmentModal(); // Open the modal
        });
    }

    if (confirmBtn) {
        confirmBtn.addEventListener("click", confirmAppointmentSubmission); // Confirm the form submission
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", closeAppointmentModal); // Close the modal
    }

    function openAppointmentModal() {
        const requiredFields = appointmentForm.querySelectorAll("[required]");
        let hasMissingFields = false;

        requiredFields.forEach((field) => {
            if (!field.value.trim()) {
                hasMissingFields = true;
                field.style.borderColor = "red"; // Highlight missing fields
            } else {
                field.style.borderColor = ""; // Reset if filled
            }
        });

        if (!hasMissingFields) {
            document.getElementById("appointmentModal").style.display = "flex"; // Show modal
        }
    }

    function closeAppointmentModal() {
        document.getElementById("appointmentModal").style.display = "none"; // Close modal
    }

    function confirmAppointmentSubmission() {
        closeAppointmentModal();
        appointmentForm.submit(); // Submit the form after confirmation
    }
});
