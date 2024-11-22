const form = document.querySelector("form[action='process_form.php']");

form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent default form submission

    // Collect form data
    const formData = new FormData(form);

    // Send AJAX request
    fetch("process_form.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.json()) // Parse JSON response
        .then(data => {
            if (data.status === "success") {
                showModal("Success", data.message); // Show success modal
            } else {
                showModal("Error", data.message); // Show error modal
            }
        })
        .catch(error => {
            showModal("Error", "An unexpected error occurred. Please try again.");
            console.error("Error:", error);
        });
});

// Function to show modal with custom title and message
function showModal(title, message) {
    const modal = document.getElementById("confirmationModal");
    modal.querySelector("h4").innerText = title;
    modal.querySelector("p").innerText = message;
    modal.style.display = "flex";
}

// Function to close modal
function closeModal() {
    document.getElementById("confirmationModal").style.display = "none";
}

// Add event listener to close modal button
document.getElementById("closeModalBtn").addEventListener("click", closeModal);
