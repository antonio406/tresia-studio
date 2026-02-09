document.querySelectorAll('.dropdown > a').forEach(item => {
    item.addEventListener('click', function(event) {
        event.preventDefault(); // Evita que el enlace funcione
        const subMenu = this.nextElementSibling;

        // Cierra otros submenús abiertos
        document.querySelectorAll('.sub-menu').forEach(menu => {
            if (menu !== subMenu) {
                menu.style.display = 'none';
            }
        });

        // Alterna la visibilidad del submenú actual
        subMenu.style.display = (subMenu.style.display === 'block') ? 'none' : 'block';
    });
});
