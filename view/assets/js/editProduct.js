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


        }
    }
    productRequest.send();
}