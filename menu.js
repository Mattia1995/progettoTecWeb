function showSidebar(){
    const sidebar = document.querySelector('#menu ul');
    sidebar.classList.remove('menu-not-visible');
}

function hideSidebar(){
    const sidebar = document.querySelector('#menu ul');
    sidebar.classList.add('menu-not-visible');
}