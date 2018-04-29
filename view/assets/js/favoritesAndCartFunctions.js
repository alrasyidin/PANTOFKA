function addToCart(id ) {

    var sizeSelect = document.getElementById( 'sizes-for-product' + id );
    var size = sizeSelect.options[sizeSelect.selectedIndex].value;
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

function addToFav(id , size) {
    var sizeSelect = document.getElementById( 'sizes-for-product' + id );

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