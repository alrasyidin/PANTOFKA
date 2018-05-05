
function addToFav(id ) {

    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=favorites&action=addToFavorites&product_id=" + id );
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

function addToCart(id ) {

    var sizeSelect = document.getElementById( 'sizes-for-product' + id );
    var sizeSelectFav = document.getElementById( 'favorites-pick-size-for-product' + id );

    if (sizeSelect !== null){
        var size = sizeSelect.options[sizeSelect.selectedIndex].value;
    } else if(sizeSelectFav !== null){
        var size = sizeSelectFav.options[sizeSelectFav.selectedIndex].value;
    }else{
        alert('Problem in add to cart' );
    }
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=cart&action=addToCart&product_id=" + id + '&size_no=' + size);
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
