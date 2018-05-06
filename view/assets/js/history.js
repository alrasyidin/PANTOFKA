
function loadOrders() {
    var wrapper = document.getElementById("display-orders");

    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=order&action=getOrders");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4){
           if(this.status == 200){
              var history = JSON.parse(this.responseText);
              var heading = document.createElement('h1');
              heading.innerHTML = 'History';
               wrapper.appendChild(heading);
              wrapper.innerHTML = this.responseText;

            }

    }
    request.send();
}}
