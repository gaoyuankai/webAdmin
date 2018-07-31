var csrfToken = $('meta[name="csrf-token"]').attr("content");


$(".update").bind("click", function(){
    var id = $(this).attr('key');
    location.href = window.location.pathname+'?r=banner/update&id='+id;
});

$(".add").bind("click", function(){
    location.href = window.location.pathname+'?r=banner/add';
});
//删除操作
$(".delete").bind("click", function(){
    var id = $(this).attr('key');
        var dialog = Dialog.confirm({
            title : '确认',
            msg : '是否删除该条配置信息，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                	dialog.close();
                 }
            },
            yes:{
                text : '删除',
                handle : function(){
                	$.post('?r=banner/del', {
                           id  : id,
                         _csrf : csrfToken,
                    }, function (data) {
                        if(data.code == 1) {
                       	   dialog.close();
                           location.reload();
                        }
                        dialog.close();
                       //提交到控制器方法处理完后返回处理结果
                        alert(data.msg);
                    });
                }
            }
        });
});