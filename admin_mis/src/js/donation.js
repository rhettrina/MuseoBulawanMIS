function init(){
    //call the display functions here
    fetchTotalDonations();
}
function fetchTotalDonations() {
    fetch('https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/php/fetchTotalDonations.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json(); // Parse JSON response
        })
        .then(data => {
            if (data.error) {
                // Log the error and display it in the console
                console.error('Error from PHP:', data.error);
                // Display an error message in the UI
                document.getElementById('total-donations').innerText = "Error fetching data";
                document.getElementById('total-accepted').innerText = "Error fetching data";
                document.getElementById('total-rejected').innerText = "Error fetching data";
                document.getElementById('total-donform').innerText = "Error fetching data";
                document.getElementById('total-lendform').innerText = "Error fetching data";
            } else {
                // Populate data if there are no errors
                document.getElementById('total-donations').innerText = data.total_donations || 0;
                document.getElementById('total-accepted').innerText = data.accepted_donations || 0;
                document.getElementById('total-rejected').innerText = data.rejected_donations || 0;
                document.getElementById('total-donform').innerText = data.total_donation_forms || 0;
                document.getElementById('total-lendform').innerText = data.total_lending_forms || 0;
            }
        })
        .catch(error => {
            // Catch network or other fetch errors
            console.error('Error fetching total donations:', error);
            document.getElementById('total-donations').innerText = "Error fetching data";
            document.getElementById('total-accepted').innerText = "Error fetching data";
            document.getElementById('total-rejected').innerText = "Error fetching data";
            document.getElementById('total-donform').innerText = "Error fetching data";
            document.getElementById('total-lendform').innerText = "Error fetching data";
        });
}


