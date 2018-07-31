var csrfToken = $('meta[name="csrf-token"]').attr("content");
$(function(){
	//隐藏第一列
	$('table tr').find('td:eq(1)').hide();
	$('table tr').find('th:eq(1)').hide();
	$("th[data-col-seq=13]").css("width",'13%');
	$("td[data-col-seq=13]").css("width",'13%');
});
$(".refund").bind("click", function(){
    var key = $(this).attr('key');
    if(trade_data[key] == undefined) {
        alert("没有该订单记录");
    } else {
        $.post('?r=trade/trade/refund', {'data' : trade_data[key]}, function (data) {
            if(data.code == 1) {
                var dialog = Dialog.create({
                    title:'详细',
                    width : 1000,
                    bodyView : data.data,
                    buttons : [{
                        id: 'submit-audit',
                        className: 'btn-primary',
                        value: '确定'
                    }],
                    events : {
                        '#submit-audit click' : function(){
                            $("#submit-audit").attr("disabled", true);  
                         if (data.status == 7) {
                        	 dialog.close();
                        	 return false;
                         }
                            var refundData = {};
                            refundData['adminNote'] = $("#tradeform-adminnote").val();
                            refundData['User_id'] = refund_model['User_id'];
                            refundData['orderNumber'] = refund_model['orderNumber'];
                            refundData['status'] = refund_model['status'];
                            $.post('?r=trade/trade/refundhandle', {'refundData' : refundData}, function (data) {

                                if(data.code == 1) {
                                    dialog.close();
                                    location.reload();
                                } else {
                                    $("#submit-audit").attr("disabled", false);
                                }
                            });
                        }
                    }
                });
            } else {
                Dialog.alert({'title':'详细','msg':data.msg});
            }
        });
    }
});

$(".detail").bind("click", function(){
    var key = $(this).attr('key');
    if(trade_data[key] == undefined) {
        alert("没有该订单记录");
    } else {
    	//console.log( trade_data[key]);
        $.post('?r=trade/trade/detail', {'data' : trade_data[key]}, function (data) {
            if(data.code == 1) {
                var dialog = Dialog.create({
                    title:'详细',
                    width : 1000,
                    bodyView : data.data,
                    buttons : [{
                        id: 'submit-audit',
                        className: 'btn-primary',
                        value: '确定'
                    }],
                    events : {
                        '#submit-audit click' : function(){
                         dialog.close();
                         }
                    }
                });
            } else {
                Dialog.alert({'title':'详细','msg':data.msg});
            }
        
        });
    }
});



$(".delete").bind("click", function(){
    var id = $(this).attr('key');
        var dialog = Dialog.confirm({
            title : '确认',    
            msg : '是否删除该条订单信息，请谨慎操作！',
            no: {
                text : '删除',
                handle : function(){
                                alert(id);
                            }
            },
            yes:{
                text : '取消',
                handle : function(){
                    dialog.close();
                }
            }
        });
   // }
});

function getSelectedId() {
    var ids = [];
    $('input[name="selection[]"]:checked').each(function(){ 
        ids.push(trade_data[$(this).val()]); 
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
                         var updateData = new Object();
                         updateData['usernames'] = $("#messageform-usernames").val();
                         updateData['title'] = $("#messageform-title").val();
                         updateData['message'] = $("#messageform-message").val();
                         updateData['style'] = [];
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

//
$(".insurance").bind("click", function(){
	var id = $(this).attr("key");
    location.href = window.location.pathname+'?r=user/user/additioninfo&orderNumber='+id;
});

