var csrfToken = $('meta[name="csrf-token"]').attr("content");

//控制内容显示宽度，以防样式变形
$(function(){
    $("tr td[data-col-seq=1]").css("width","8%");
    $("tr td[data-col-seq=2]").css("width","8%");
    $("tr td[data-col-seq=9]").css("width","25%");
    $("tr td[data-col-seq=9]").css({"margin":"auto","padding":"auto"});
});
$(".update").bind("click", function(){
	//当前主题id
    var id = $(this).attr('key');
    $.get('?r=circle/theme/update', {"id" : id}, function (data) {
        if(data.code == 1) {
            var dialog = Dialog.create({
                title:'编辑主题',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '更新'
                }],
                events : {
                    '#submit-audit click' : function(){
                    	var top = $("input[type=radio]:checked").val();
	                	$.post('?r=circle/theme/update', {"top":top , "id":id},function (data) {
	                		if(data.code == 1) {
	                			dialog.close();
	                			location.reload();
	                			alert(data.msg);
	                		} else {
	                			alert(data.msg);
	                		}
	                	});
                    dialog.close();
                    }
                }
            });
        } else {
            Dialog.alert({'title':'编辑主题','msg':data.msg});
        }
    
    });

});

$(".add").bind("click", function(){
    location.href = window.location.pathname+'?r=circle/theme/add&circle_id='+circle_id+'&circle_name='+circle_name;
});

$(".comment").bind("click", function(){
	var id = $(this).attr('key');
	console.log(id);
    location.href = window.location.pathname+'?r=circle/comment/list&id='+id;
});

//删除操作
$(".delete").bind("click", function(){
    var id = $(this).attr('key');
    var user_id = $(this).val();
    console.log(id);
        var dialog = Dialog.confirm({
            title : '确认',
            msg : '是否删除该条主题信息，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                	dialog.close();
                 }
            },
            yes:{
                text : '删除',
                handle : function(){
                	$.post('?r=circle/theme/del', {
                           id      : id,
                           user_id : user_id,
                           _csrf   : csrfToken,
                    }, function (data) {
                        if(data.code == 1) {
                       	   dialog.close();
                           location.reload();
                        } 
                       //提交到控制器方法处理完后返回处理结果
                        alert(data.msg);
                    });
                	 dialog.close();
                }
            }
        });
});