var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(".update").bind("click", function(){
    var id = $(this).attr('key');
    if(place_data[id] == undefined) {
        alert("没有该场馆记录");
    } else {
        post('update',place_data[id]);
    }
});

function post(action) {
     var dailog_data=arguments[1]?arguments[1]:false;  
     var post_data = {_csrf : csrfToken};
     if(dailog_data) {
         post_data.placeData = dailog_data;
     }
     post_data.action = action;
     $.post('?r=place/place/dailog', post_data, function (data) {
         if(data.code == 1) {
             var dialog = Dialog.create({
                 title:'场馆',
                 width : 1000,
                 bodyView : data.data,
                 buttons : [{
                     id: 'submit-audit',
                     className: 'btn-primary',
                     value: '提交'
                 }],
                 events : {
                     '#submit-audit click' : function(){
                         var attrs = new Array('id', "venueName","venueAddr","longitude","latitude","Region_districtId");
                         var updateData = new Object();
                         for (var i=0;i<attrs.length;i++)
                         {
                             updateData[attrs[i]] =$("#placeform-" + (attrs[i].toLowerCase())).val();
                         }
                         updateData.venueName = $('input[nt="test"]').val();
                         $.post('?r=place/place/updatesubmit', {
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
             Dialog.alert({'title':'发布','msg':data.msg});
         }
     },'json');
};

$(".add").bind("click", function(){
    post('add');
});

$(".delete").bind("click", function(){
        var id = $(this).attr('key');
        var dialog = Dialog.confirm({
            title : '确认',    
            msg : '是否删除该场馆信息，请谨慎操作！',
            no: {
                text : '取消',
                handle : function(){
                				dialog.close();
                            }
            },
            yes:{
                text : '删除',
                handle : function(){
                	var post_data = {id:id, _csrf:csrfToken};
                	$.post('?r=place/place/delete', post_data, function (data) {
                		dialog.close();
                		if(data.code == 1) {
                            location.reload();
                        } else {
                        	alert(data.msg);
                        }
                	});
                }
            }
        });
});
