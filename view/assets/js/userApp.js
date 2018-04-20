function login(email , password) {
    var request = new XMLHttpRequest();
    request.open("post", "");
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.onreadystatechange = function (ev) {
        if(this.readyState == 4 && this.status == 200){

            alert('logged');
    }
    request.send( "alala" , email , password);
    };
}