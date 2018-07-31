var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(".update").bind("click", function(){
	//当前主题id
    var id = $(this).attr('key');
    $.get('?r=lucky/lucky/update', {"id" : id ,'action' : 'update'}, function (data) {
        if(data.code == 1) {
            var dialog = Dialog.create({
                title:'编辑红包',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '更新'
                }],
                events : {
                    '#submit-audit click' : function(){
                    	var attrs = new Array("couponName","brief","expireFrom","expireTo","totalQty", 'couponCode'
                                ,"conditionPrice","expireDays", 'discountPrice');
                               var updateData = new Object();
                               for (var i=0;i<attrs.length;i++)
                               {
                                   updateData[attrs[i]] = $("#luckylistform-" + (attrs[i].toLowerCase())).val();
                               }
                               updateData.id        = id;
                               updateData.isExpire  = $('input[name="LuckyListForm[isExpire]"]:checked').val();
                               updateData.status    = $('input[name="LuckyListForm[status]"]:checked').val();
                               updateData.kind    = $('input[name="LuckyListForm[kind]"]:checked').val();
	                	$.post('?r=lucky/lucky/update', {"LuckyListForm" : updateData ,'action' : 'update'},function (data) {
	                		if(data.code == 1) {
	                			dialog.close();
	                			location.reload();
	                			alert(data.msg);
	                		} else {
	                			alert(data.msg);
	                		}
	                	});
                    }
                }
            });
        } else {
            Dialog.alert({'title':'编辑红包','msg':data.msg});
        }
    });

});

$(".addLucky").bind("click", function(){
    $.get('?r=lucky/lucky/update',{'action' : 'add'}, function (data) {
        if(data.code == 1) {
            var dialog = Dialog.create({
                title:'添加红包',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '添加'
                }],
                events : {
                    '#submit-audit click' : function(){
                    	var attrs = new Array("couponName","brief","expireFrom","expireTo","totalQty", 'couponCode'
                                ,"conditionPrice","expireDays", 'discountPrice');
                               var updateData = new Object();
                               for (var i=0;i<attrs.length;i++)
                               {
                                   updateData[attrs[i]] = $("#luckylistform-" + (attrs[i].toLowerCase())).val();
                               }
                               updateData.isExpire  = $('input[name="LuckyListForm[isExpire]"]:checked').val();
                               updateData.status    = $('input[name="LuckyListForm[status]"]:checked').val();
                               updateData.kind    = $('input[name="LuckyListForm[kind]"]:checked').val();
	                	$.post('?r=lucky/lucky/update', {"LuckyListForm" : updateData ,'action' : 'add'},function (data) {
	                		if(data.code == 1) {
	                			dialog.close();
	                			location.reload();
	                			alert(data.msg);
	                		} else {
	                			alert(data.msg);
	                		}
	                	});
                    }
                }
            });
        } else {
            Dialog.alert({'title':'添加红包','msg':data.msg});
        }
    });

});

$(".delete").bind("click", function(){
    var id = $(this).attr('key');
        var dialog = Dialog.confirm({
            title : '确认',
            msg : '是否删除该条红包信息，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                	dialog.close();
                }
            },
            yes:{
                text : '删除',
                handle : function(){
                	$.post('?r=lucky/lucky/del', {
                        id  : id,
                      _csrf : csrfToken,
                 }, function (data) {
                     if(data.code == 1) {
                        location.reload();
                     }
                     dialog.close();
                     alert(data.msg);
                     
                 });
                }
            }
        });
   // }
});
