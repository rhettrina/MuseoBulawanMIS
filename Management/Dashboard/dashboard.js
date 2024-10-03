document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.button-container button');
    
    buttons.forEach(button => {
      button.addEventListener('click', function() {
        // Remove 'active' class from all buttons
        buttons.forEach(btn => btn.classList.remove('active'));
        
        // Add 'active' class to the clicked button
        button.classList.add('active');
      });
    });
  });
  if(window.onload){
    hideNotification();
  }

  const dialog = document.getElementById("notification")

  const wrapper =document.querySelector(".notif-wrapper")


  function showNotification(){
    dialog.showModal();

  }

  function hideNotification(){
    dialog.close();

  }

dialog.addEventListener("click", (e) => {
  if(!wrapper.contains(e.target)){
    hideNotification();
  }

})