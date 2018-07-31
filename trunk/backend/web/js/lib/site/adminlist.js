var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(".deleteone").bind("click", function(){
	var adminId = $(this).attr('adminId');
    deleteAlert(adminId);
	//var ids = new Array(comment_data[key]);
});

function deleteAlert(adminId){
	var dialog = Dialog.confirm({
        title : '确认',    
        msg : '是否删除管理员',
        no: {
            text : '取消',
            handle : function(){
            	dialog.close();
            },
        },
        yes:{
            text : '删除',
            handle : function(){
            	deleteAdmin(adminId);
            }
        }
    });
}

function deleteAdmin(adminId){
	alert(adminId);
}

$(".update").bind("click", function(){
    var admin_data = $(this).attr('admin_data');
    post('update',admin_data);
});

$(".add").bind("click", function(){
    post('add');
});

function post(action) {
	var admin_data=arguments[1]?arguments[1]:false;  
    var post_data = {_csrf : csrfToken};
    if(admin_data) {
        post_data.adminData = admin_data;
    }
    post_data.action = action;
    $.post('?r=site/dialog', post_data, function (data) {
    	if(data.code == 1) {
            var dialog = Dialog.create({
                title:'管理员',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '提交'
                }],
                events : {
                    '#submit-audit click' : function(){
                        var attrs = new Array('id', "username","password","repassword","status","role");
                        var updateData = new Object();
                        updateData.id = $("#adminform-id").val();
                        updateData.password = $("#adminform-password").val();
                        updateData.repassword = $("#adminform-repassword").val();
                        updateData.username = $('input[nt="username"]').val();
                        updateData.status = $('select[nt="status"]').val();
                        updateData.role = $('select[nt="role"]').val();
                        $.post('?r=site/updateadmin', {
                            actions : action,
                            updateDatas  : updateData,
                             _csrf : csrfToken,
                        }, function (data) {
                            if(data.code == 1) {
                           	 dialog.close();
                                location.reload();
                            } 
                            alert(data.msg);
                        });
                    }
                }
            });
        } else {
            Dialog.alert({'title':'消息','msg':data.msg});
        }
    },'json');
}