document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form"); // Select the form element
    const confirmBtn = document.querySelector("#confirmBtn"); // Assuming the button that confirms submission has this ID
    const closeModalBtn = document.querySelector("#closeModalBtn"); // Close modal button (if necessary)

    // Function to close modal (if applicable)
    function closeModal() {
        const modal = document.querySelector("#confirmationModal");
        modal.classList.remove("show"); // Assuming you're using Bootstrap for modal
        modal.setAttribute("aria-hidden", "true");
    }

    // Confirm and submit form
    function confirmSubmission() {
        closeModal(); // Close the modal
        form.submit(); // Programmatically submit the form after confirmation
    }

    // Event listener for the confirmation button
    confirmBtn.addEventListener("click", confirmSubmission);

    // Optional: Event listener to close the modal if needed
    closeModalBtn.addEventListener("click", closeModal);
});
