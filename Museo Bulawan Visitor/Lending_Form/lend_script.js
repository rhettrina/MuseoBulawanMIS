const form = document.getElementById("lendingForm");

form.addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent automatic form submission
    openModal(); // Open the modal for confirmation
});

function openModal() {
    const requiredFields = form.querySelectorAll("[required]");
    let hasMissingFields = false;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            hasMissingFields = true;
            field.style.borderColor = "red"; // Highlight missing fields
        } else {
            field.style.borderColor = ""; // Reset style if filled
        }
    });

    if (!hasMissingFields) {
        document.getElementById("confirmationModal").style.display = "flex";
    } else {
        alert("Please fill all required fields.");
    }
}

function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}

function confirmSubmission() {
    closeModal();
    form.submit(); // Submit the form programmatically
}
