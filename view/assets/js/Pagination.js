function loadPageLinks(entries, category, style, color, material) {
    //get number of all products
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=numberOfProducts&category=" + category
        + "&style=" + style + "&color=" + color + "&material=" + material);
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


                    var styleSelect = document.getElementById("select_filter_style");
                    var style = styleSelect.options[styleSelect.selectedIndex].value;
                    var colorSelect = document.getElementById("select_filter_color");
                    var color = colorSelect.options[colorSelect.selectedIndex].value;
                    var materialSelect = document.getElementById("select_filter_material");
                    var material = materialSelect.options[materialSelect.selectedIndex].value;

                    filterProducts(this.innerHTML, entries, category, style, color, material);
                });
                div.appendChild(button);
            }
        }
    };
    request.send();

}