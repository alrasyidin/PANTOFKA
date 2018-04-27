var wrapper = document.getElementById('wrapper');

function show(pageName){
    if (pageName === undefined){
        return show("main");
    }
    return getAjax(
        'handle_requests.php?pageName='+pageName,

        function(pageContent){
            wrapper.innerHTML = '';
            wrapper.innerHTML = pageContent;
        },

        function(failure){
            console.log(failure);
        }
    );
}


function getAjax( url, success , failure) {
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open('GET', url);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) success (xhr.responseText);
        if (xhr.readyState == 4 && xhr.status != 200) failure (xhr.responseText);

    };
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
    return xhr;
}

