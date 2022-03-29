/*select js*/
$j(document).ready(function() {
    /*HTML 구조가 div.usa>a+ul.langList>li  */
        /* div.select 자식으로 존재하는 화살표를 클릭시  */
    $j('.usa').click(function(){
        /* div.lan 자식으로 존재하는 보여주고자 하는 콘텐츠를 보여준다(보여주는 명령어는 fadeIN)-fadeIn 안에는 보여주는 속도이며 normal, fast, slow, 숫자(1/1000) */
		$('.langList').fadeIn('normal');			  
	});
    /* mouseleave => 마우스가 콘텐츠에서 벗어났을때 this = .langList 는 감춰준다.(hide)  */
	$j('.language .langList').mouseleave(function(){     
		$(this).hide();			  
	});
    
	/*tab키 이동시 처리 방법입니다.*/
	  $j('.usa').bind('focus', function () {        
              $('.langList').show();	
       });
       $j('.langList li:last').find('a').bind('blur', function () {        
              $('.langList').hide();
       });  
});

