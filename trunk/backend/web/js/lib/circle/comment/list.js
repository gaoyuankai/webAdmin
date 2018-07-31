var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(function(){
    $("th[data-col-seq=1]").css("width","50%");
});
$(".delete").bind("click", function(){
	var id = $(this).attr('key'); 
	var user_id = $(this).val();
	console.log(user_id);
        var dialog = Dialog.confirm({
            title : '确认',
            msg : '是否删除该评论，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                	dialog.close();
                 }
            },
            yes:{
                text : '删除',
                handle : function(){
                	$.post('?r=circle/comment/del', {
                           id  : id,
                           user_id:user_id,
                         _csrf : csrfToken,
                    }, function (data) {
                        if(data.code == 1) {
                       	   dialog.close();
                           location.reload();
                        } 
                       //提交到控制器方法处理完后返回处理结果
                        alert(data.msg);
                        dialog.close();
                    });
                	
                }
            
            }
        });
});

