//编辑活动----未完成
$(".update").bind("click", function(){
	var id = $(this).attr('key');
    location.href = window.location.pathname+'?r=active/edit/update&id='+id;
});

$(".delete").bind("click", function(){
    var id = $(this).attr('key');
    alert(id);
    return false;
    //获取该行圈子成员数量
    var number = $(this).parent().parent().find("td[data-col-seq=3]").text();
    if (number>0) {
    	alert("此圈子有成员，不能删除！");
    	return false;
    }
    
        var dialog = Dialog.confirm({
            title : '确认',
            msg : '是否删除该圈子信息，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                	dialog.close();
                 }
            },
            yes:{
                text : '删除',
                handle : function(){
                	$.post('?r=circle/circle/del', {
                           id  : id,
                         _csrf : csrfToken,
                    }, function (data) {
                        if(data.code == 1) {
                       	   dialog.close();
                           location.reload();
                        } 
                       //提交到控制器方法处理完后返回处理结果
                        alert(data.msg);
                    });
                	
                }
            }
        });
});