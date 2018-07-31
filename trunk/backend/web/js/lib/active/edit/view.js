$(function(){
	//活动产生购买则不能编辑价格区域
	if (flag==1) {
	  $("#link").parent().parent().remove();
	}
});