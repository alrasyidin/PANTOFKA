function showProductsPage(category) {
    var main = document.getElementById("main");

    var showProducts = document.getElementById("products-page");


    main.style.display = "none";

    showProducts.style.display = "block";
    filterProducts(1, 20, category);

}

function showMain() {
    var showProducts = document.getElementById("products-page");

    var main = document.getElementById("main");
    if (main.style.display === "none") {
        showProducts.style.display = "none";
        main.style.display = "block";

    }
}

window.onscroll = function () {
    myFunction()
};

var navbar = document.getElementById("navbar");
var sticky = navbar.offsetTop;

function myFunction() {
    if (window.pageYOffset >= sticky) {
        navbar.classList.add("sticky")
    } else {
        navbar.classList.remove("sticky");
    }
}