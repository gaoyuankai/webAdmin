var csrfToken = $('meta[name="csrf-token"]').attr("content");

function getSelectedId() {
    var ids = [];
    $('input[name="selection[]"]:checked').each(function(){ 
        ids.push(comment_data[$(this).val()]); 
    });
    return ids;
}

$(".deleteone").bind("click", function(){
	var key = $(this).attr('key');
	if(comment_data[key] == undefined) {
        alert("没有该条评论记录");
    } else {
    	deleteAlert(comment_data[key]);
    }
	//var ids = new Array(comment_data[key]);
});

$(".deletes").bind("click", function(){
	var ids = getSelectedId();
	if(ids.length == 0) {
		alert('请先选择用户');
		return false;
	}
	deleteAlert(ids);
});

function deleteAlert(comment){
	if(comment['display'] == 1) {
		var msg = '是否屏蔽此评论？';
		var notext = '屏蔽';
	} else {
		var msg = '是否取消屏蔽此评论？';
		var notext = '取消屏蔽';
	}
	var dialog = Dialog.confirm({
        title : '确认',
        msg : msg,
        yes: {
            text : notext,
            handle : function(){
            	deleteMsg(comment,notext);
            },
        },
        no:{
            text : '取消',
            handle : function(){
                dialog.close();
            }
        }
    });
	
}

function deleteMsg(comment,notext){
	var post_data = {_csrf : csrfToken, comment: comment};
	$.post('?r=active/comment/delete', post_data, function (data) {
		if(data.code == 1) {
			location.reload();
			alert(notext+data.msg);
			return false;
		}
		alert(data.msg);
	});
}

$(".edit").bind("click", function(){
	var key = $(this).attr('key');
	if(comment_data[key] == undefined) {
        alert("没有该条评论记录");
        return;
    }
	var post_data = {_csrf : csrfToken, comment: comment_data[key]};
	$.post('?r=active/comment/detail', post_data, function (data) {
		 if(data.code == 1) {
             var dialog = Dialog.create({
                 title:'查看圈子主题',
                 width : 1000,
                 bodyView : data.data,
                 buttons : [{
                     id: 'submit-audit',
                     className: 'btn-primary',
                     value: '修改'
                 }],
                 events : {
                     '#submit-audit click' : function(){
                         var editData = {};
                         editData['lastData'] = lastData;
                         editData['top'] =  $('input[name="CommentForm[top]"]:checked').val();
                         editData['id'] =  $("#commentform-id").val();
                         $.post('?r=active/comment/edit', {'editData' : editData}, function (data) {
                             if(data.code == 1) {
                                 location.reload();
                             } else {
                                 if(data.msg) {
                                	 alert(data.msg);
                                	 return;
                                 }
                             }
                             dialog.close();
                         });
                     }
                 }
             });
         } else {
             Dialog.alert({'title':'查看圈子主题','msg':data.msg});
         }
	});
});
