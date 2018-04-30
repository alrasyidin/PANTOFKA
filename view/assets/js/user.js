
function userIsAdminReq(tempObject) {
    var request = new XMLHttpRequest();
    request.open("get", "handle_requests.php?target=user&action=userIsAdmin");

    request.onreadystatechange = function (ev) {

        if (this.readyState == 4){
            if (this.status == 200){
                alert(1);
                tempObject = {'userIsAdmin' : 'yes'};
                return tempObject.isAdmin;

            }else {
                tempObject.isAdmin = false;
            }
        };


    }
    request.send();
}


