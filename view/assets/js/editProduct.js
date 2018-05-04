function editProduct(productId) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=admin&action=editProduct&id=" + productId);

    request.send();
}