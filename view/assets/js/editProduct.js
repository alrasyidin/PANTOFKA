function editProduct(product_id) {
    var productRequest = new XMLHttpRequest();
    productRequest.open("get", "handle_requests.php?target=product&action=getProductById&id=" + product_id);
    productRequest.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var response = this.responseText;
            var product = JSON.parse(response);


            var productImgDiv= document.getElementById("edit_product_img");
            var productImg = document.getElementById("product_img");
            productImg.src = product.product_image_url;

            var productName = document.getElementById("change_name");
            productName.value = product.product_name;

            var promoPercentage = document.getElementById("change_promo");
            promoPercentage.value = product.promo_percentage;

            var productPrice = document.getElementById("change_price");
            productPrice.value = product.price;

            var productInfo = document.getElementById("change_info");
            productInfo.value = product.info;

            var productColor = document.getElementById("change_color");


            var material = document.getElementById("change_material");

            var div = document.getElementById("input-sizes");
            div.innerHTML = "";
            var sizes = product.sizes;
            for (var b = 0; b < sizes.length; b++) {
                var size = sizes[b];
                var sizeNumber = document.createElement('h6');
                div.appendChild(sizeNumber);
                sizeNumber.style.display = "inline-block";
                sizeNumber.innerHTML = "Number: " + size.size_number + " - Quantity: ";
                var inputQuantity = document.createElement("input");
                div.appendChild(inputQuantity);
                inputQuantity.type = "number";
                inputQuantity.name = size.size_number;
                inputQuantity.value = size.size_quantity;
            }


            var productId = document.getElementById("product_id");
            productId.value = product.product_id;

            var changeProductButton = document.getElementById("change_button");
            changeProductButton.setAttribute('onclick' , 'generateNewProduct()')


        }
    }
    productRequest.send();
}

function generateNewProduct() {
    var productImg = document.getElementById("product_img");
    var productImageUrl = productImg.src;

    var productName = document.getElementById("change_name");
   var productNewName= productName.value;

    var promoPercentage = document.getElementById("change_promo");
    var promoNewPercentage = promoPercentage.value;

    var productPrice = document.getElementById("change_price");
    var productNewPrice = productPrice.value;

    var productInfo = document.getElementById("change_info");
    var productNewInfo = productInfo.value;

    var productColor = document.getElementById("change_color");
    var newColor = productColor.options[productColor.selectedIndex].value;


    var material = document.getElementById("change_material");
var newMaterial = material.options[material.selectedIndex].value;

    var sizes = product.sizes;
    var newSizes = product.sizes;
    for (var b = 0; b < sizes.length; b++) {
        var newSize = sizes[b];
        newSize.size_number = size.size_number;
        var inputSize = document.getElementById(newSize.size_number);
        newSizes.size_quantity = inputQuantity.value;
    }


    var productId = product.product_id;




    var changedProduct = {
        product_id : productId,
        product_name : productNewName,
        price : productNewPrice,
        promo_percentage : promoNewPercentage,
        color : newColor,
        material : newMaterial,
        sizes : newSizes

    };

    var request = new XMLHttpRequest();
    request.open("post", "handle_requests.php?target=admin&action=editProduct");
    request.send(JSON.stringify(changedProduct));


}