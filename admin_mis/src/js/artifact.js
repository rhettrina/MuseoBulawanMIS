function init(){
    //call the display functions here

}

function selectTab(selectedButton) {
    const buttons = document.querySelectorAll('#tabs button');
    buttons.forEach((button) => {
      // Remove active styles from all buttons
      button.classList.remove('text-black', 'font-bold', 'active-tab');
      button.classList.add('text-gray-500', 'font-medium');
    });

    // Apply active styles to the clicked button
    selectedButton.classList.remove('text-gray-500', 'font-medium');
    selectedButton.classList.add('text-black', 'font-bold', 'active-tab');
  }

  // Set "Artifacts" as default on page load
  document.addEventListener('DOMContentLoaded', () => {
    const defaultButton = document.querySelector('#tabs button');
    selectTab(defaultButton);
  });