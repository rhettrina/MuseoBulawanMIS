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

        // Convert form data to a FormData object (for sending via POST)
        const formData = new FormData(form);

        // Send a fetch request to submit the form data
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

    // Optional: Event listener to close the modal if needed
    closeModalBtn.addEventListener("click", closeModal);
});
