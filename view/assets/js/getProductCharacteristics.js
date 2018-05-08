function loadCategories() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getCategories");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allCategories = JSON.parse(this.responseText);
            var select = document.getElementById("categories");
            for (var i = 0; i < allCategories.length; i++) {
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}


function loadCategoriesForEdit() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getCategories");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allCategories = JSON.parse(this.responseText);
            var select = document.getElementById("change-category");
            for (var i = 0; i < allCategories.length; i++) {
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}


function loadParentCategories() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getCategories");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allCategories = JSON.parse(this.responseText);
            var select = document.getElementById("parent-categories");
            for (var i = 0; i < allCategories.length; i++) {
                var category = allCategories[i];
                select.options[select.options.length] = new Option(category, category);
            }
        }
    };
    request.send();
}

function loadColors() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getColors");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);

            var allColors = JSON.parse(this.responseText);
            var select = document.getElementById("select-color");
            for (var i = 0; i < allColors.length; i++) {
                var color = allColors[i];
                select.options[select.options.length] = new Option(color, color);
            }
        }
    };
    request.send();
}

function loadMaterials() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getMaterials");
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var allMaterials = JSON.parse(this.responseText);
            console.log(this.responseText);
            var select = document.getElementById("select-material");
            for (var i = 0; i < allMaterials.length; i++) {
                var material = allMaterials[i];
                select.options[select.options.length] = new Option(material, material);
            }
        }
    };
    request.send();
}


function getStyles(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getStylesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);

            var styles = JSON.parse(this.responseText);
            var select = document.getElementById("select-style");
            while (select.options.length) {
                select.remove(0);
            }
            for (var i = 0; i < styles.length; i++) {
                var style = styles[i];
                select.options[select.options.length] = new Option(style, style);
            }
        }
    };
    request.send();

}

function getStylesForEdit(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=category&action=getStylesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var styles = JSON.parse(this.responseText);
            var select = document.getElementById("change-style");
            while (select.options.length) {
                select.remove(0);
            }
            for (var i = 0; i < styles.length; i++) {
                var style = styles[i];
                select.options[select.options.length] = new Option(style, style);
            }
        }
    };
    request.send();

}



function loadInputSizes(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getSizesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var sizes = JSON.parse(this.responseText);
            var divSizesInput = document.getElementById("input-sizes");
            divSizesInput.innerHTML = "";

            for (var i = 0; i < sizes.length; i++) {
                var size = sizes[i];
                var sizeNumber = document.createElement('h6');
                divSizesInput.appendChild(sizeNumber);
                sizeNumber.style.display = "inline-block";
                sizeNumber.innerHTML = "Size: " + size + "  -  Insert quantity: ";

                var inputQuantity = document.createElement("input");
                inputQuantity.placeholder = "Quantity for size: " + size;
                divSizesInput.appendChild(inputQuantity);
                inputQuantity.type = "number";
                inputQuantity.name = size;
            }
        }
    };
    request.send();
}

function loadInputSizesForEdit(parentCategory) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=product&action=getSizesByParentCategory&pc=" + parentCategory);
    request.onreadystatechange = function (ev) {
        if (this.readyState == 4 && this.status == 200) {
            var sizes = JSON.parse(this.responseText);
            var div = document.getElementById("change-sizes");
            div.innerHTML = "";

            for (var i = 0; i < sizes.length; i++) {
                var size = sizes[i];
                var sizeNumber = document.createElement('h6');
                div.appendChild(sizeNumber);
                sizeNumber.style.display = "inline-block";
                sizeNumber.innerHTML = size + ": ";
                var inputQuantity = document.createElement("input");
                div.appendChild(inputQuantity);
                inputQuantity.type = "number";
                inputQuantity.name = size;
            }
        }
    };
    request.send();
}