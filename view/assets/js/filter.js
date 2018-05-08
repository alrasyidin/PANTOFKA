
function filter(category) {
    var styleSelect = document.getElementById("select_filter_style");
    var style = styleSelect.options[styleSelect.selectedIndex].value;

    var colorSelect = document.getElementById("select_filter_color");
    var color = colorSelect.options[colorSelect.selectedIndex].value;

    var materialSelect = document.getElementById("select_filter_material");
    var material = materialSelect.options[materialSelect.selectedIndex].value;



    filterProducts(1, 20, category, style, color, material);
    console.log("filter products");
}

function loadFilterStyles(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getStylesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var styles = JSON.parse(this.responseText);
            var select = document.getElementById("select_filter_style");
            while (select.options.length) {
                select.remove(0);
            }
            var optionAll = document.createElement("option");
            select.appendChild(optionAll);
            optionAll.value= "all";
            optionAll.innerHTML = "all";

            for (var i = 0; i < styles.length; i++) {
                var style = styles[i];
                select.options[select.options.length] = new Option(style, style);
            }
            select.addEventListener("change", filter.bind(window, parentCategory));
        }
    };
    request.send();

}

function loadFilterColors(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getColors");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allColors = JSON.parse(this.responseText);
            var select = document.getElementById("select_filter_color");
            while (select.options.length) {
                select.remove(0);
            }
            var optionAll = document.createElement("option");
            select.appendChild(optionAll);
            optionAll.value= "all";
            optionAll.innerHTML = "all";

            for (var i = 0; i < allColors.length; i++) {
                var color = allColors[i];
                select.options[select.options.length] = new Option(color, color);
            }
            select.addEventListener("change", filter.bind(window, parentCategory));

        }
    };
    request.send();
}

function loadFilterMaterials(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getMaterials");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var select = document.getElementById("select_filter_material");
            var allMaterials = JSON.parse(this.responseText);
            while (select.options.length) {
                select.remove(0);
            }
            var optionAll = document.createElement("option");
            select.appendChild(optionAll);
            optionAll.value= "all";
            optionAll.innerHTML = "all";
            for (var i = 0; i < allMaterials.length; i++) {
                var material = allMaterials[i];
                select.options[select.options.length] = new Option(material, material);
            }
            select.addEventListener("change", filter.bind(window, parentCategory));

        }
    };
    request.send();
}

