
function userIsAdminReq() {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=user&action=userIsAdmin");

    request.onreadystatechange = function (ev) {

        if (this.readyState == 4){
            if (this.status == 200){
           var userIsAdmin = JSON.parse(this.responseText);
               if (userIsAdmin){
                   return true;
               }
               else {
                   return false;
               }
            }
        }


    };
    request.send();
}


