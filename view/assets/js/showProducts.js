function loadCategories() {
    var request = new XMLHttpRequest();
    request.open("get", "index.php?target=product&action=getCategories");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var allCategories =JSON.parse(this.responseText);
            var select = document.getElementById("categories");
            for(var i = 0; i < allCategories.length; i++){
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}

function filterProducts(pages, entries, category) {
    var request = new XMLHttpRequest();
    request.open("get", "index.php?target=product&action=getProducts&pages=" + pages + "&entries=" + entries + "&category=" + category);
    request.onreadystaychange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {

            if (this.responseText == "error") {
                location.href = "view/error.html";
            }
            var products = JSON.parse(this.responseText);
            var showProducts = document.getElementById("shown-products");
            if (products.length == 0) {
                showProducts.innerHTML = "No results!";
            }
            else {
                for (var i = 0; i < products.length; i++) {
                    var product = products[i];


                    var title = document.getElementById("title")
                    title.textContent = product.product_name;


//  TODO !!!!!!!!!!!


                }
            }
            loadPageLinks(entries, category);
        }
    };
    request.send();
}


function filter() {
    var categorySelect = document.getElementById("categories");
    var category = categorySelect.options[categorySelect.selectedIndex].value;

    filterProducts(1, 20, category);
}

// function showProducts() {
//     var productRequest = new XMLHttpRequest();
//     productRequest.open("get", "index.php?target=product&action=getProducts");
//     productRequest.onreadystatechange = function (ev) {
//         if(this.readyState == 4 && this.status == 200){
//             var products = (this.responseText);
//             for (var i = 0; i < products.length; i++) {
//                 var product = products[i];
//                 var name_span = document.getElementById("prod_name");
//                 var price_span = document.getElementById("prod_price");
//                 var img = document.getElementById("prod_img");
//                 price_span.innerHTML = product.price;
//             }
//         }
//     }
//     productRequest.send();
// }

function loadPageLinks(entries, category) {
    //get number of all products
    var request = new XMLHttpRequest();
    request.open("get", "index.php?target=product&action=numberOfProducts&category="+category);
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){
            var allProducts = JSON.parse(this.responseText);
            var pages = Math.ceil(allProducts/entries);
            var div = document.getElementById("page-links");
            div.innerHTML = "";
            for(var i = 0; i < pages; i++){
                var button = document.createElement("button");
                button.innerHTML = i+1;
                button.addEventListener("click", function () {

                    var categorySelect = document.getElementById("categories");
                    var category = categorySelect.options[categorySelect.selectedIndex].value;

                    filterProducts(this.innerHTML, entries, category);
                });
                div.appendChild(button);
            }
        }
    };
    request.send();

}