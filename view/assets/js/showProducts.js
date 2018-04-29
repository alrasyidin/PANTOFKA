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


function loadParentCategories() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getCategories");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allCategories = JSON.parse(this.responseText);
            var select = document.getElementById("parent-categories");
            for (var i = 0; i < allCategories.length; i++) {
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}

function loadColors() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getColors");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allColors = JSON.parse(this.responseText);
            var select = document.getElementById("select-color");
            for (var i = 0; i < allColors.length; i++) {
                var color = allColors[i];
                select.options[select.options.length] = new Option(color, color);
            }
        }
    };
    request.send();
}

function loadMaterials() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getMaterials");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allMaterials = JSON.parse(this.responseText);
            var select = document.getElementById("select-material");
            for (var i = 0; i < allMaterials.length; i++) {
                var material = allMaterials[i];
                select.options[select.options.length] = new Option(material, material);
            }
        }
    };
    request.send();
}



function filterProducts(pages, entries, category) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getProducts&pages=" + pages + "&entries=" + entries + "&category=" + category);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var products = JSON.parse(response);
            document.getElementById('visualisation').innerHTML = "";
            visualiseProducts(products);

    }
    loadPageLinks(entries, category);
}

request.send();
}

function visualiseProducts(products) {
    for (var i = 0; i < products.length; i++) {

        var product = products[i];
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
        showProduct.appendChild(productSizes);
        showProduct.appendChild(divButtons);


        productName.innerHTML = product.product_name;
        img.src = product.product_image_url;
        var sizeText = document.createElement('h6');
        var selectSize = document.createElement("select");
        selectSize.className = "sizes";
        selectSize.style = "display: inline-block";
        var sizes = product.sizes;
        if (sizes.length === 0){
            sizeText.innerHTML = "Out of stock!"
        }
        else {
            sizeText.innerHTML = "Sizes: ";

        }
        productSizes.appendChild(sizeText);
        productSizes.appendChild(selectSize);


        for (var j = 0; j < sizes.length; j++) {
            var size = sizes[j];
            if (size.size_quantity > 0) {
                selectSize.options[selectSize.options.length] = new Option(size.size_number, size.size_number);
            }
        }


        if (product.promo_percentage > 0) {

            var newPrice = product.price_on_promotion;
            spanPriceOnSale.innerHTML = product.price;
            spanPrice.innerHTML = " Now: " + newPrice + " BGN Was: ";

        }
        else {
            spanPrice.innerHTML = " Price: " + product.price + " BGN ";
        }

        var addToCartButton = document.createElement("button");
        divButtons.appendChild(addToCartButton);
        addToCartButton.innerHTML = "Add to cart";

        var addToFavButton = document.createElement("button");
        divButtons.appendChild(addToFavButton);
        addToFavButton.innerHTML = "Add to favorites";

    }

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
                button.className = "page-button";
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
            var select = document.getElementById("select-style");
            while (select.options.length) {
                select.remove(0);
            }
            for (var i = 0; i < styles.length; i++) {
                var style = styles[i];
                select.options[select.options.length] = new Option(style, style);
            }
        }
    };
    request.send();

}



function loadInputSizes(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getSizesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var sizes = JSON.parse(this.responseText);
            var div = document.getElementById("input-sizes");
            div.innerHTML = "";

            for (var i = 0; i < sizes.length; i++) {
                var size = sizes[i];
                var sizeNumber = document.createElement('h6');
                div.appendChild(sizeNumber);
                sizeNumber.style.display = "inline-block";
                sizeNumber.innerHTML = size + ": ";
                var inputQuantity = document.createElement("input");
                div.appendChild(inputQuantity);
                inputQuantity.type = "number";
                inputQuantity.name = size;
            }
        }
    };
    request.send();
}

function filterCategories() {
        var categorySelect = document.getElementById("parent-categories");
        var category = categorySelect.options[categorySelect.selectedIndex].value;
        getStyles(category);
        loadInputSizes(category);

}




function productInfo(product_id) {
    var productRequest = new XMLHttpRequest();
    productRequest.open("get", "handle_requests.php?target=product&action=getProductById&id=" + product_id);
    productRequest.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var product = JSON.parse(response);

            var div = document.getElementById("products-page");
            div.visibility = "hidden";
            var h6 =  document.createElement("h6");
            h6.innerHTML = product.product_name;


        }

    }
    productRequest.send();
}


