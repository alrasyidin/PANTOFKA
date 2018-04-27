function loadCategories() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getCategories");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allCategories = JSON.parse(this.responseText);
            var select = document.getElementById("categories");
            for (var i = 0; i < allCategories.length; i++) {
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}

function visualiseProducts(products) {
    console.log(products);
    for (var i = 0; i < products.length; i++) {

        var product = products[i];

        var visualisation = document.createElement("div");
        visualisation.className = "visualisation";
        var showProduct = document.createElement('div');
        showProduct.className = "shown_products";
        visualisation.appendChild(showProduct);
        var productName = document.createElement("div");
        productName.className = "product_name";
        var productImg = document.createElement("div");
        productImg.className = "product_img";
        var showProductInfoLink = document.createElement("a");
        showProductInfoLink.id = "show_product";
        var img = document.createElement("img");
        showProductInfoLink.appendChild(img);
        productImg.appendChild(showProductInfoLink);
        var productPrice = document.createElement("div");
        productPrice.className = "price";
        var productSizes = document.createElement("div");
        productSizes.classList = "product-type";
        var spanPrice = document.createElement("span");
        spanPrice.className = "price";
        var spanPriceOnSale = document.createElement("span");
        spanPriceOnSale.className = "line-trough";
        productPrice.appendChild(spanPrice);
        productPrice.appendChild(spanPriceOnSale);
        var divButtons = document.createElement("div");
        divButtons.className = "div-buttons";

        showProduct.appendChild(productName);
        showProduct.appendChild(productImg);
        showProduct.appendChild(productPrice);
        showProduct.appendChild(divButtons);
        showProduct.appendChild(productSizes);


        productName.innerHTML = product.product_name;
        img.src = product.product_image_url;
        showProductInfoLink.href = "index.php?page=product_info&id=" + product.product_id;

        var selectSize = document.createElement("select");
        selectSize.className = "sizes";
        selectSize.style = "display: inline-block";
        productSizes.appendChild(selectSize);
        var sizes = product.sizes;
        for (var j = 0; j < sizes.length; j++ ){
            var size = sizes[j];
            selectSize.options[selectSize.options.length] = new Option(size.size, size.size);

        }

        var price = product.price;
        if(product.promo_percentage > 0){

            var newPrice = product.price  - (product.price * product.promo_percentage)/100;
            spanPrice.innerHTML =  " Now: " + newPrice + " BGN Was: ";
            spanPriceOnSale.innerHTML =  price;

        }
        else{
            spanPrice.innerHTML = " Price: " + price + " BGN ";
        }

        var addToCartButton = document.createElement("button");
        divButtons.appendChild(addToCartButton);

        addToCartButton.innerHTML = "Add to cart";
        addToCartButton.onclick = addToCart(product.product_id);

        var addToFavButton = document.createElement("button");
        divButtons.appendChild(addToFavButton);
        addToFavButton.innerHTML = "Add to favorites";
        addToFavButton.onclick = addToFav(product.product_id);

    }

}

function filterProducts(pages, entries, category) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getProducts&pages=" + pages + "&entries=" + entries + "&category=" + category);
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
                visualiseProducts(products);
            }
        }
        loadPageLinks(entries, category);
    }

    request.send();
}



function filter() {
    var categorySelect = document.getElementById("categories");
    var category = categorySelect.options[categorySelect.selectedIndex].value;

    filterProducts(1, 20, category);
}

function loadPageLinks(entries, category) {
    //get number of all products
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=numberOfProducts&category=" + category);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allProducts = JSON.parse(this.responseText);
            var pages = Math.ceil(allProducts / entries);
            var div = document.getElementById("page-links");
            div.innerHTML = "";
            for (var i = 0; i < pages; i++) {
                var button = document.createElement("button");
                button.innerHTML = i + 1;
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

function getStyles(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getStylesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var styles = JSON.parse(this.responseText);
            var select = document.getElementById("product-styles");
            for (var i = 0; i < styles.length; i++) {
                var style = styles[i];
                select.options[select.options.length] = new Option(style, style);
            }
        }
    };
    request.send();

}

function filterCategories() {
    function filter() {
        var categorySelect = document.getElementById("categories");
        var category = categorySelect.options[categorySelect.selectedIndex].value;

        getStyles(category);
    }
}


function visualisationOfProducts() {

    var productRequest = new XMLHttpRequest();
    productRequest.open("get", "handle_requests.php?target=product&action=getAllProducts");
    productRequest.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var products = JSON.parse(response);
            console.log(products);


        }
    }
    productRequest.send();


}

function productInfo(product_id) {
    var productRequest = new XMLHttpRequest();
    productRequest.open("get", "handle_requests.php?target=product&action=getProductById&id=" + product_id);
    productRequest.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var product = JSON.parse(response);

            var visualisation = document.getElementById("visualisation");
            var showProduct = document.createElement('div');
            showProduct.className = "shown_products";
            visualisation.appendChild(showProduct);
            var productName = document.createElement("div");
            productName.className = "product_name";
            var productImg = document.createElement("div");
            productImg.className = "product_img";
            var showProductInfoLink = document.createElement("a");
            showProductInfoLink.id = "show_product";
            var img = document.createElement("img");
            showProductInfoLink.appendChild(img);
            productImg.appendChild(showProductInfoLink);
            var productPrice = document.createElement("div");
            productPrice.className = "price";
            var spanPrice = document.createElement("span");
            spanPrice.className = "price";
            productPrice.appendChild(spanPrice);

            showProduct.appendChild(productName);
            showProduct.appendChild(productImg);
            showProduct.appendChild(productPrice);

            productName.innerHTML = product.product_name;
            img.src = product.product_image_url;
            productPrice.innerHTML = product.price;
            showProductInfoLink.href = "index.php?page=main";


        }

    }
    productRequest.send();
}
function loadProducts(category) {


    var productRequest = new XMLHttpRequest();
    productRequest.open("get", "handle_requests.php?target=product&action=getAllProductsByCategory&category" + category);
    productRequest.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var products = JSON.parse(response);
            console.log(products);
            visualiseProducts(products);

        }
        productRequest.send();

    }
}
