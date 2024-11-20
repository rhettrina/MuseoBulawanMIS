function init(){
    //call the display functions here

}

const dropdownButtons = document.querySelectorAll('.dropdown-menu');
        dropdownButtons.forEach(button => {
            button.previousElementSibling.addEventListener('click', () => {
                const menu = button;
                menu.classList.toggle('hidden');
            });
        });