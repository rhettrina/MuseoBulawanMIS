document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.button-container button, .logout-button');
    
    buttons.forEach(button => {
      button.addEventListener('click', function() {
        // Remove 'active' class from all buttons
        buttons.forEach(btn => btn.classList.remove('active'));
        
        // Add 'active' class to the clicked button
        button.classList.add('active');
      });
    });
  });
  