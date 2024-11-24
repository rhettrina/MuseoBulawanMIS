document.addEventListener("DOMContentLoaded", function () {
    const stickyNav = document.getElementById("stickyNav");
    const loginLink = stickyNav.querySelector('a[href="../admin_login/login.html"]');

    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            // Add class to make background opaque and show links
            stickyNav.classList.add("scrolled");
        } else {
            // Remove class to make background transparent and hide links
            stickyNav.classList.remove("scrolled");
        }
    });

    // Ensure the login link is always visible
    window.addEventListener("scroll", function () {
        if (stickyNav.classList.contains("scrolled")) {
            loginLink.style.opacity = "1";
        }
    });
});