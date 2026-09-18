document.addEventListener("DOMContentLoaded", function () {
    // 1. Año dinámico para el copyright
    const yearSpan = document.getElementById("year");
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // 2. Control del menú hamburguesa y desplegable lateral
    const iconMenu = document.getElementById("icon_menu");
    const navMenu = document.getElementById("nav_menu");
    const menuOverlay = document.getElementById("menu_overlay");

    function toggleMenu() {
        iconMenu.classList.toggle("open");
        navMenu.classList.toggle("show");
        if (menuOverlay) {
            menuOverlay.classList.toggle("show");
        }
    }

    if (iconMenu && navMenu) {
        iconMenu.addEventListener("click", toggleMenu);

        if (menuOverlay) {
            menuOverlay.addEventListener("click", toggleMenu);
        }

        // Cierra el menú al hacer clic en cualquier opción
        const navLinks = navMenu.querySelectorAll("a");
        navLinks.forEach(link => {
            link.addEventListener("click", function () {
                if (navMenu.classList.contains("show")) {
                    toggleMenu();
                }
            });
        });
    }
});