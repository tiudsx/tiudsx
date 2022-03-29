jQuery(function($){
// Form Label Overlapping
	var overlapMSLabel = $('#border .itx_wrp').find('>:text,>:password').prev('label');
	var overlapMSInput = overlapMSLabel.next();
	overlapMSInput
		.focus(function(){
			$(this).prev(overlapMSLabel).css('visibility','hidden');
		})
		.blur(function(){
			if($(this).val() == ''){
				$(this).prev(overlapMSLabel).css('visibility','visible');
			} else {
				$(this).prev(overlapMSLabel).css('visibility','hidden');
			}
		})
});
