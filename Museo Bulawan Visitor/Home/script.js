document.addEventListener("DOMContentLoaded", function () {
    const stickyNav = document.getElementById("stickyNav");

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            // Add class to make background opaque and show links
            stickyNav.classList.add("scrolled");
        } else {
            // Remove class to make background transparent and hide links
            stickyNav.classList.remove("scrolled");
        }
    });
});


