function addToCart(id ) {

    var sizeSelect = document.getElementById( 'sizes-for-product' + id );
    var sizeInputFromFav = document.getElementById( 'favorites-pick-size-for-product' + id );

    if (sizeSelect !== null){
        var size = sizeSelect.options[sizeSelect.selectedIndex].value;
    } else if(sizeInputFromFav !== null){
        var size = sizeInputFromFav.value;
        alert(size)
    }else{

        alert('Problem in add to cart' );
    }
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=cart&action=addToCart&id=" + id + '&size=' + size);
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                console.log(this.responseText);
                alert(this.responseText);
            }
        }
    };
    request.send();

}

function addToFav(id ) {

    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=favorites&action=addToFavorites&id=" + id );
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                console.log(this.responseText);
                alert(this.responseText);
            }
        }
    };
    request.send();


}

function loadFavorites() {
    var wrapper = document.getElementById("display-favorite-items");

    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=favorites&action=getFavorites");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                wrapper.innerHTML = this.responseText;
            }
        }
    };
    request.send();

}

function removeItemFromFavorites(id){
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=favorites&action=removeFromFavorites&id=" + id );
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                console.log(this.responseText);
                alert(this.responseText);
            }
        }
    };
    request.send();
}

function removeItemSizeFromCart(product_id , size_no){
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=cart&action=removeItemSize&productId=" + product_id + '&sizeNo=' +size_no );
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                console.log(this.responseText);
                alert(this.responseText);
            }
        }
    };
    request.send();
}

function removeItemFromCart(product_id ){
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=cart&action=removeItem&productId=" + product_id);
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
            if(this.status == 200){
                console.log(this.responseText);
                alert(this.responseText);
            }
        }
    };
    request.send();
}