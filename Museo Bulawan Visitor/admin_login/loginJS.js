// script.js

// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {
    // Get the form element
    const loginForm = document.getElementById('login-form');
    
});



document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login-form");

    // Predefined username and password (for demonstration purposes)
    const validUsername = "admin"; // Replace with your desired username
    const validPassword = "admin"; // Replace with your desired password

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent form submission

        const usernameInput = document.getElementById("username").value;
        const passwordInput = document.getElementById("password").value;

        // Validate credentials
        if (usernameInput === validUsername && passwordInput === validPassword) {
            // Successful login
            alert("Login successful!");
            // Redirect to another page (optional)
            window.location.href = "https://museobulawan.online/development/admin_mis/index.html";
        } else {
            // Invalid login
            alert("Invalid username or password. Please try again.");
        }
    });
});



    // // Add a submit event listener to the form
    // loginForm.addEventListener('submit', function(event) {
    //     event.preventDefault(); // Prevent the default form submission

    //     // Get the username and password values
    //     const username = document.getElementById('username').value;
    //     const password = document.getElementById('password').value;

    //     // Create a data object to send to the server
    //     const data = {
    //         username: username,
    //         password: password
    //     };

    //     // Send the data to the server using fetch
    //     fetch('http://museobulawan.online/development/Museo%20Bulawan%20Visitor/admin_login/loginphp.php', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json'
    //         },
    //         body: JSON.stringify(data)
    //     })
    //     .then(response => response.json())
    //     .then(result => {
    //         if (result.success) {
    //             // Redirect to the dashboard
    //             window.location.href = 'https://museobulawan.online/development/admin_mis/index.html?fbclid=IwY2xjawGtNZJleHRuA2FlbQIxMAABHe7Y0KsRdT_19rbwM2EpvcmaFJAH69yQ_KD8BS9OFmLJjJlFIyp0KMLEvw_aem_L4VLV0-shPW1l51WI2nYQg';
    //         } else {
    //             // Display an error message
    //             alert('Invalid username or password. Please try again.');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //         alert('An error occurred during login. Please try again later.');
    //     });
    //});