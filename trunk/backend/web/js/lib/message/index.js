var csrfToken = $('meta[name="csrf-token"]').attr("content");

function getSelectedId() {
    var ids = [];
    $('input[name="selection[]"]:checked').each(function(){ 
        ids.push(message_data[$(this).val()]['id']); 
    });
    return ids;
}

$(".deleteone").bind("click", function(){
	var messageId = $(this).attr('messageId');
	var ids = new Array(messageId);
	deleteAlert(ids);
});

$(".deletes").bind("click", function(){
	var ids = getSelectedId();
	if(ids.length == 0) {
		alert('请先选择用户');
		return false;
	}
	deleteAlert(ids);
});

function deleteAlert(ids){
	var dialog = Dialog.confirm({
        title : '确认',    
        msg : '是否删除此消息，请谨慎操作！',
        no: {
            text : '取消',
            handle : function(){
            	dialog.close();
            },
        },
        yes:{
            text : '删除',
            handle : function(){
            	deleteMsg(ids);
                
            }
        }
    });
	
}

function deleteMsg(ids){
	var post_data = {_csrf : csrfToken, ids: ids};
	$.post('?r=message/message/delete', post_data, function (data) {
		if(data.code == 1) {
			location.reload();
		}
		alert(data.msg);
	});
}

$(".smessage").bind("click", function(){
    $.post('?r=user/user/message', {
        ids   : 'all',
        _csrf : csrfToken,
    }, function (data) {
        if(data.code == 1) {
             var dialog = Dialog.create({
                 'title':'发送消息',
                 width : 1000,
                 'bodyView':data.data,
                 buttons : [{
                     id: 'submit-audit',
                 className: 'btn-primary',
                 value: '提交',
                 }],
                 events : {
                     '#submit-audit click' : function(){
                         var updateData = new Object();
                         updateData['usernames'] = $("#messageform-usernames").val();
                         updateData['title'] = $("#messageform-title").val();
                         updateData['message'] = $("#messageform-message").val();
                         updateData['style'] = [];
                         $('input[name="MessageForm[style][]"]:checked').each(function(){ 
                             updateData['style'].push($(this).val()); 
                         });
                         updateData['data'] = user_data;
                         $.post('?r=user/user/sendmessage', {
                              updateDatas  : updateData,
                              _csrf : csrfToken,
                          }, function (data) {
                              if(data.code == 1) {
                                  dialog.close();
                              }
                              alert(data.msg);
                          });
                     }
                 } 
             });
        } else {
            Dialog.alert({'title':'发布','msg':data.msg});
        }
    },'json');
});
