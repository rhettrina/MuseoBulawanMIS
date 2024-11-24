function init() {
    // Initialize the doughnut chart
    const visitorCtx = document.getElementById('visitorChart').getContext('2d');
    new Chart(visitorCtx, {
      type: 'doughnut',
      data: {
        datasets: [
          {
            data: [80, 20], // 80% progress, 20% remaining
            backgroundColor: ['#4b3621', '#d2b48c'], // Brown and light brown
            borderWidth: 0,
          },
        ],
      },
      options: {
        responsive: true,
        cutout: '75%', // Makes the chart circular
        plugins: {
          tooltip: { enabled: false }, // Disable tooltips for simplicity
        },
      },
    });
  
    // Initialize the bar chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const data = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [
        {
          label: 'Monthly Data',
          data: [10, 2, 3, 20, 18, 1, 8, 7, 6, 15, 16, 20], // Replace these values with your dataset
          backgroundColor: 'rgba(34, 139, 34, 0.8)', // Forest Green
          borderColor: 'rgba(34, 139, 34, 1)',
          borderWidth: 1,
          borderRadius: 2, // Makes the bars rounded
        },
      ],
    };
  
    const config = {
      type: 'bar',
      data: data,
      options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
          legend: {
            display: false, // Hide legend for simplicity
          },
        },
        scales: {
          x: {
            grid: {
              display: false, // Hide grid lines for the x-axis
            },
          },
          y: {
            beginAtZero: true,
            grid: {
              borderColor: 'rgba(0, 0, 0, 0.1)', // Light grid lines
            },
          },
        },
      },
    };
  
    new Chart(monthlyCtx, config);
  }
  
  // Call the init function
  init();
  