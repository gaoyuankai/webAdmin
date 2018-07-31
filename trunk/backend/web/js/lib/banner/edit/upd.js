var csrfToken = $('meta[name="csrf-token"]').attr("content");
$(function(){
	$('#bannerform-type').find("option").each(function(index,element){
		if($(this).val()==type){
			$(this).attr("selected",true);
		}
	});
	if(type==1){
   	 $('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"none");
   	 $('.field-bannerform-kind').css('display',"none");
   	 $('.field-bannerform-url').css('display',"block");
   	 $('.field-bannerform-title').css('display',"block");
	 }
	if(type==2){
		$('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
  	    $('.field-bannerform-kind').css('display',"block");
		$('.field-bannerform-url').css('display',"none");
  	    $('.field-bannerform-title').css('display',"none");	
  	    $('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联id");
	 }
	if(type==3){
		$('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
		$('.field-bannerform-url').css('display',"none");
  	    $('.field-bannerform-title').css('display',"none");	
  	    $('.field-bannerform-kind').css('display',"none");
  	    $('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联活动id");
	 }
        if(type==4){
		$('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
		$('.field-bannerform-url').css('display',"none");
  	    $('.field-bannerform-title').css('display',"none");	 
    	$('.field-bannerform-kind').css('display',"none");
    	$('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联圈子主题id");
	 } 
	
});
