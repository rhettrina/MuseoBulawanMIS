// script.js

// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {
    // Get the form element
    const loginForm = document.getElementById('login-form');

    // Add a submit event listener to the form
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Get the username and password values
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        // Create a data object to send to the server
        const data = {
            username: username,
            password: password
        };

        // Send the data to the server using fetch
        fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Redirect to the dashboard
                window.location.href = 'https://lightpink-dogfish-795437.hostingersite.com/admin_mis/index.html';
            } else {
                // Display an error message
                alert('Invalid username or password. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during login. Please try again later.');
        });
    });
});
