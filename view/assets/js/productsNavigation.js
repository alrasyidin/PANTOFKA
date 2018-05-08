
function showProductsPage(category) {
    loadFilterStyles(category);
    loadFilterColors(category);
    loadFilterMaterials(category);

    var main = document.getElementById("main");

    var showProducts = document.getElementById("products-page");

    main.style.display = "none";

    showProducts.style.display = "block";

    filterProducts(1, 20, category, "all", "all", "all");

}


function showMain() {
    var showProducts = document.getElementById("products-page");

    var main = document.getElementById("main");
    if (main.style.display === "none") {
        showProducts.style.display = "none";
        main.style.display = "block";

    }
}

