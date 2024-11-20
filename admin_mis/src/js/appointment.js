document.addEventListener('DOMContentLoaded', () => {
    const sortForm = document.getElementById('sortForm');
    const appointmentData = document.getElementById('appointmentData');

    const fetchAppointments = async (sort = 'date-newest') => {
        try {
            const response = await fetch(`backend/data_fetch.php?sort=${sort}`);
            const data = await response.json();

            appointmentData.innerHTML = data
                .map(row => `
                    <tr>
                        <td>${row.preferred_date}</td>
                        <td>${row.first_name} ${row.last_name}</td>
                        <td>${row.preferred_time}</td>
                        <td>Confirmed</td>
                        <td>${row.attendees}</td>
                        <td>August 25, 2024</td>
                    </tr>
                `)
                .join('');
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    };

    // Initial fetch
    fetchAppointments();

    // Handle sort change
    sortForm.addEventListener('change', (e) => {
        e.preventDefault();
        const sortValue = document.getElementById('sort').value;
        fetchAppointments(sortValue);
    });
});
