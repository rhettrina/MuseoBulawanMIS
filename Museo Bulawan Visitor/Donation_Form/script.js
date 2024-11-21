document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#donationForm"); // Select the form element
    const confirmBtn = document.querySelector("#confirmBtn"); // Confirm button inside the modal
    const closeModalBtn = document.querySelector("#closeModalBtn"); // Close button inside the modal

    // Function to open the modal
    function openModal() {
        const modal = document.querySelector("#confirmationModal");
        modal.classList.add("show"); // Add Bootstrap's 'show' class to display the modal
        modal.setAttribute("aria-hidden", "false");
    }

    // Function to close the modal
    function closeModal() {
        const modal = document.querySelector("#confirmationModal");
        modal.classList.remove("show"); // Remove 'show' class to hide the modal
        modal.setAttribute("aria-hidden", "true");
    }

    // Confirm and submit the form data
    function confirmSubmission() {
        closeModal(); // Close the modal

        // Convert form data to a FormData object (for sending via POST)
        const formData = new FormData(form);

        // Send the form data via fetch to the server
        fetch('https://lightpink-dogfish-795437.hostingersite.com/Museo%20Bulawan%20Visitor/Donation_Form/process_donation.php', {
            method: 'POST',
            body: formData // Send the FormData directly without stringifying it
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to submit form');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                console.log('Form submitted successfully');
            } else {
                console.error('Failed to submit form:', data.error);
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
        });
    }

    // Event listener for the confirmation button
    confirmBtn.addEventListener("click", confirmSubmission);

    // Event listener to close the modal if the close button is clicked
    closeModalBtn.addEventListener("click", closeModal);
});
