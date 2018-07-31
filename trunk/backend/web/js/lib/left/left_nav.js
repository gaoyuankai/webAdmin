/*左侧导航效果*/
$(function () {
	if(route){
		var url="/index.php?r="+route;
		$("div[class=well]").find("a").each(function(index,element){
			if ($(this).attr("href") == url){
				$(this).css("background-color","#CCCCCC");
				$(this).parent().parent().parent().prev().attr({'class':'list-group-item collapsed','aria-expanded':'true'});
				$(this).parent().parent().parent().attr("class","collapse in");
			}
		})
		
	}
});