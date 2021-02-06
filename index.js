/*window.isCycling = false;*/

function closeMessage(id){
    var element = document.getElementById(id);
    element.parentNode.removeChild(element);
}

function removeFadeOut( id, speed, delay ) {

    var el = document.getElementById(id);
    var seconds = speed/1000;

    setTimeout(function() {
        el.style.transition = "opacity "+seconds+"s ease";
        el.style.opacity = 0;
        setTimeout(function() {
            el.parentNode.removeChild(el);
        }, speed);
    }, delay);
}

function selectAll(){
    var items=document.getElementsByClassName('checkAll');
    for(var i=0; i<items.length; i++){
        if(items[i].type=='checkbox')
            items[i].checked=true;
    }
}

function deSellectAll(){
    var items=document.getElementsByClassName('checkAll');
    for(var i=0; i<items.length; i++){
        if(items[i].type=='checkbox')
            items[i].checked=false;
    }
}

function handleChange(checkbox) {
    if(checkbox.checked == true){
        selectAll();
    }else{
        deSellectAll();
   }
}

function comparePassword(password) {
    if (password.length === 0) {
        document.getElementById("msg2").innerHTML = "";
        return;
    }
    var firstPwd = document.getElementById("pwd").value;
    var color = "";
    var message = "";
    if (password === firstPwd) {
        message = "Shodují se";
        color = "green";
    } else {
        message = "Neshodují se";
        color = "red";
    }
    document.getElementById("msg2").innerHTML = message;
    document.getElementById("msg2").style.color = color;
}

function validatePassword(password) {
    // Do not show anything when the length of password is zero.
    if (password.length === 0) {
        document.getElementById("msg").innerHTML = "";
        
        //document.getElementById("submit").disabled = true;
        return;
    }
    // Create an array and push all possible values that you want in password
    var matchedCase = new Array();
    matchedCase.push("[$@$!%*#?&]"); // Special Charector
    matchedCase.push("[A-Z]");      // Uppercase Alpabates
    matchedCase.push("[0-9]");      // Numbers
    matchedCase.push("[a-z]");     // Lowercase Alphabates

    // Check the conditions
    var ctr = 0;
    for (var i = 0; i < matchedCase.length; i++) {
        if (new RegExp(matchedCase[i]).test(password)) {
            ctr++;
        }
    }

    if (password.length > 5) {
        // Display it
        var color = "";
        var strength = "";
        switch (ctr) {
            case 0:
            case 1:
            case 2:
                strength = "Příliš jednoduché";
                color = "red";
                toogleButton('submit', 'unactive-page', 'disable');
                break;
            case 3:
                strength = "Středně silné";
                color = "orange";
                toogleButton('submit', 'unactive-page', 'enable');
                break;
            case 4:
                strength = "Silné";
                color = "green";
                toogleButton('submit', 'unactive-page', 'enable');
                break;
        }
    } else {
        toogleButton('submit', 'unactive-page', 'disable');
        strength = "Příliš jednoduché";
        color = "red";
    }
    
    document.getElementById("msg").innerHTML = strength;
    document.getElementById("msg").style.color = color;
}

function toogleMenu() {
    var menu = document.getElementById('nav');
    var ham = document.getElementById('menu-toggle');
    if (menu.classList.contains('nav-open')){
        menu.classList.remove('nav-open');
        ham.classList.remove('open');
    } else {
        menu.classList.add('nav-open');
        ham.classList.add('open');
    }
}

function toogleFilter(id, what) {
    var menu = document.getElementById(id);
    if (menu.classList.contains(what)){
        menu.classList.remove(what);
    } else {
        menu.classList.add(what);
    }
}

function toogleButton(id, what, how) {
    var menu = document.getElementById(id);
    if (menu.classList.contains(what)) {
        if (how === "enable"){
            menu.classList.remove(what);
        }
    } else {
        if (how === "disable"){
            menu.classList.add(what);
        }
    }
}

function increase(id) {
    var input = document.getElementById(id);
    if (!input.value) {
        input.value = 0;
    }
    input.value = parseInt(input.value) + 1;
}

function decrease(id) {
    var input = document.getElementById(id);
    if ((!input.value) || (input.value == 0) ) {
        input.value = 1;
    }
    input.value = parseInt(input.value) - 1;
}

function showDeleteModal(id) {
    toogleFilter('modal-delete', 'modal-show');
    document.getElementById('delete_id').value = id;
}

function cookiesAccept() {
    // hide cookie info DIV
    toogleFilter('cookie-info', 'hidden');

    // set cookie with expire date 60 days from now
    var expire = new Date();
    expire.setTime(expire.getTime() + (60*24*60*60*1000)); // expires in 60 days
    var expires = "expires="+ expire.toUTCString();
    document.cookie = 'cookiesAccept' + "=" + 'true' + ";" + expires + ";path=/; SameSite=Lax";
}