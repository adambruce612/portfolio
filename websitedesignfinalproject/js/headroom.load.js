// JavaScript Document

window.addEventListener('DOMContentLoaded', (event) => {
    // select navigation element
    const elNavMain = document.querySelector(".nav-main");
    // construct an instance of Headroom, passing the element
    const headroom = new Headroom(elNavMain);
    // initialise
    headroom.init();
});
