window.isCycling = false;

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
        document.getElementById("submit").disabled = true;
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
    // Display it
    var color = "";
    var strength = "";
    switch (ctr) {
        case 0:
        case 1:
        case 2:
            strength = "Velmi jednoduché";
            document.getElementById("submit").disabled = true;
            color = "red";
            break;
        case 3:
            strength = "Středně silné";
            color = "orange";
            document.getElementById("submit").disabled = false;
            break;
        case 4:
            strength = "Silné";
            color = "green";
            document.getElementById("submit").disabled = false;
            break;
    }
    document.getElementById("msg").innerHTML = strength;
    document.getElementById("msg").style.color = color;
}

/*unction toogleDetails(id) {
    var x = document.getElementById(id);
    var i = document.getElementById("i_"+id);

    if (x.className.indexOf("show") == -1) {
      x.className += " show";
      i.className = i.className.replace("fa-angle-double-down", "fa-angle-double-up");
    } else { 
      x.className = x.className.replace(" show", "");
      i.className = i.className.replace("fa-angle-double-up", "fa-angle-double-down");
    }

}*/

/*-------------------------------------
//--------------carousel---------------
//-------------------------------------
function cycleCarousel(smer) { 
    var divs = document.querySelectorAll('*[id^="container"');
    //console.log(divs);
    //console.log(smer);
    //console.log(isCycling);
    var length = divs.length;
    var x;
    if (!isCycling){
        disableCycle();
        if (smer == 'right') {
            for (i = 0; i < length; i++) {
                var name = divs[i].getAttribute('name');
                if (name == "show") {
                    if (i < (length - 1)) {
                        x = i+1;
                    } else {
                        x = 0;
                    }
                    //console.log("active was " + i);
                    divs[i].className += " slide-toRight";
                    hide(divs[i], 'right');
                    divs[i].setAttribute("name","hide");
        
                    divs[x].className = divs[x].className.replace(" hidden", " slide-fromLeft");
                    setTimeout(function() { divs[x].className = divs[x].className.replace( " slide-fromLeft", "");}, 1000);
                    divs[x].setAttribute("name","show");
                    //console.log("active is now " + x);
                    i++;
                }
            }
        } else if (smer == 'left') {
            for (i = (length - 1); i >= 0; i--) {
                var name = divs[i].getAttribute('name');
                if (name == "show") {
                    if (i > 0) {
                        x = i-1;
                    } else {
                        x = (length - 1);
                    }
                    //console.log("active was " + i);
                    //divs[i].className += " hidden";
                    divs[i].className += " slide-toLeft";
                    hide(divs[i], 'left');
                    divs[i].setAttribute("name","hide");
        
                    divs[x].className = divs[x].className.replace(" hidden", " slide-fromRight");
                    setTimeout(function() { divs[x].className = divs[x].className.replace( " slide-fromRight", "");}, 1000);
                    divs[x].setAttribute("name","show");
                    //console.log("active is now " + x);
                    i--;
                }
            }
        }
    }
}

function hide(element, where) {
    if (where = 'left'){
        setTimeout(function() { element.className = element.className.replace( " slide-toLeft", " hidden");}, 1000);
    } 
    if (where = 'right') {
        setTimeout(function() { element.className = element.className.replace( " slide-toRight", " hidden");}, 1000);
    }
    
}

function disableCycle() {
    isCycling = true;
    setTimeout(function() {isCycling = false;}, 1500);
}

function cycle(){
    setTimeout(function() {cycleCarousel('right'); cycle();}, 8000);
}*/

/*function getHeight() {
    var h = window.innerHeight;
      // Create our stylesheet
      var style = document.createElement('style');
      style.innerHTML =
          'article {' +
              'min-height: ' + (h - 230) + 'px;'
          '}';
  
      // Get the first script tag
      var ref = document.querySelector('script');
  
      // Insert our new styles before the first script tag
      ref.parentNode.insertBefore(style, ref);
  }*/

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

function toogleFilter(id) {
    var menu = document.getElementById(id);
    if (menu.classList.contains('filter-open')){
        menu.classList.remove('filter-open');
    } else {
        menu.classList.add('filter-open');
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