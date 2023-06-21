
function gpe_getCookie1(name) { 
    var Found = false 
    var start, end 
    var i = 0 
    while(i <= document.cookie.length) { 
        start = i 
        end = start + name.length 
        if(document.cookie.substring(start, end) == name) { 
            Found = true 
            break 
        } 
        i++ 
    } 
    
    if(Found == true) { 
        start = end + 1 
        end = document.cookie.indexOf(";", start) 

        if(end < start) 
            end = document.cookie.length 

        return document.cookie.substring(start, end) 
    } 
    return "" 
} 

function gpe_setCookie1( name, value, expiredays ) { 
    var todayDate = new Date(); 
    todayDate.setDate( todayDate.getDate() + expiredays ); 
    document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
}

function gpe_closeWin1(num) { 
    if ( document.gpe_form1.event1.checked ){ 
        gpe_setCookie1( "act_pop" + num, "no" + num , 1 ); 
    }
    document.getElementById("gpe_divpop" +num).style.display='none';
}

function gpe_closeCoupon() { 
    document.getElementById("gpe_divpop2").style.display='none';
}