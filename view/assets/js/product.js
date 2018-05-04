function deleteProduct(productId) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=admin&action=unsetProduct&id=" + productId);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);

        }
    };

    request.send();
}


function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}