document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menu-btn');
    const sideNav = document.getElementById('side-nav');
    const closeBtn = document.getElementById('close-btn');

    menuBtn.addEventListener('click', () => {
        sideNav.classList.add('show');
    });

    closeBtn.addEventListener('click', () => {
        sideNav.classList.remove('show');
    });
});
