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

// Add event listener for confirm button
document.getElementById('confirmBtn').addEventListener('click', confirmSubmission);

// Add event listener for close button
document.getElementById('closeModalBtn').addEventListener('click', closeModal);

document.addEventListener("DOMContentLoaded", function () {
    const stickyNav = document.getElementById("stickyNav");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            // Add class to make background opaque and show links
            stickyNav.classList.add("scrolled");
        } else {
            // Remove class to make background transparent and hide links
            stickyNav.classList.remove("scrolled");
        }
    });
});