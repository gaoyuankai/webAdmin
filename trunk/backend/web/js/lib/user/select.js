var csrfToken = $('meta[name="csrf-token"]').attr("content");

function getSelectedId() {
    var ids = [];
    $('input[name="selection[]"]:checked').each(function(){ 
        ids.push(data[$(this).val()]); 
    });
    return ids;
}

$(".smessage").bind("click", function(){
	var ids = getSelectedId();
	if(ids.length == 0) {
		alert('请先选择用户');
		return false;
	}
    $.post('?r=user/user/message', {
        ids   : ids,
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
                         var updateData          = new Object();
                         updateData['usernames'] = $("#messageform-usernames").val();
                         updateData['title']     = $("#messageform-title").val();
                         updateData['message']   = $("#messageform-message").val();
                         updateData['style']     = [];
                         $('input[name="MessageForm[style][]"]:checked').each(function(){ 
                             updateData['style'].push($(this).val()); 
                         });
                         var flag = $.inArray("0", updateData['style']);
                         if ($.trim(updateData['usernames']) == "all" && flag >= 0) {
                        	 alert("选择发送对象为all时,只能发送站内信");
                        	 return false;
                         } 
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

$(".shongbao").bind("click", function(){
	var ids = getSelectedId();
	if(ids.length == 0) {
		alert('请先选择用户');
		return false;
	}
     $.post('?r=user/user/lucky', {
            ids   : ids,
            _csrf : csrfToken,
        }, function (data) {
            if(data.code == 1) {
                 var dialog = Dialog.create({
                     'title':'发布',
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
                             updateData['usernames'] = $("#luckyform-usernames").val();
                             updateData['title']     = $("#luckyform-title").val();
                             updateData['message']   = $("#luckyform-message").val();
                             updateData['lucky']     = $("#luckyform-lucky").val();
                             updateData['style']     = [];
                                $('input[name="LuckyForm[style][]"]:checked').each(function(){ 
                                    updateData['style'].push($(this).val()); 
                                });
                             updateData['data'] = user_data;
                             updateData['lucky_config'] = lucky_config;
                             $.post('?r=user/user/sendlucky', {
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


