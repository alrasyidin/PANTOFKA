
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