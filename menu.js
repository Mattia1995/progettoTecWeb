function showSidebar(){
    const sidebar = document.querySelector('#menu ul');
    sidebar.classList.remove('menu-not-visible');

    const openMenuButton = document.getElementById ("open-menu-mobile");
    openMenuButton.classList.add('menu-not-visible');
    
}

function hideSidebar(){
    const sidebar = document.querySelector('#menu ul');
    sidebar.classList.add('menu-not-visible');

    const openMenuButton = document.getElementById ("open-menu-mobile");
    openMenuButton.classList.remove('menu-not-visible');
}