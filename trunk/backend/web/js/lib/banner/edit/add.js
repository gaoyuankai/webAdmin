var csrfToken = $('meta[name="csrf-token"]').attr("content");
//首次进入该页面显示情况
$(function(){
         show();
})
//add显示页面下拉框变化事件
$("#bannerform-type").bind("change", function(){
        show();
});
//根据type显示相应字段
function show() {
    if($("#bannerform-type").val()==1){
        	 $('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"none");
        	 $('.field-bannerform-kind').css('display',"none");
        	 $('.field-bannerform-url').css('display',"block");
        	 $('.field-bannerform-title').css('display',"block");
	}
	if($("#bannerform-type").val()==2){
			$('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
       	    $('.field-bannerform-kind').css('display',"block");
			$('.field-bannerform-url').css('display',"none");
       	    $('.field-bannerform-title').css('display',"none");	
       	    $('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联id");

	}
	if($("#bannerform-type").val()==3){
	    $('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
            $('.field-bannerform-url').css('display',"none");
       	    $('.field-bannerform-title').css('display',"none");	
       	    $('.field-bannerform-kind').css('display',"none");

       	    $('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联活动id");
	}
        if($("#bannerform-type").val()==4){
            $('.field-bannerform-asociateactivityidorassociatecircleid').css('display',"block");
            $('.field-bannerform-url').css('display',"none");
            $('.field-bannerform-title').css('display',"none");	 
            $('.field-bannerform-kind').css('display',"none");
            $('.field-bannerform-asociateactivityidorassociatecircleid :first').text("关联圈子主题id");
       } 
}