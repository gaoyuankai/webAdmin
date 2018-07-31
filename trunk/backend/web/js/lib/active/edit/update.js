var csrfToken = $('meta[name="csrf-token"]').attr("content");
var attrs = new Array('minNumber', "stock","activityDate","activityTime","regStart","regEnd");
//价格类型
var adultprice = $(".form-group.field-activeform-adultprice");
var kidPrice    = $(".form-group.field-activeform-kidprice");
var totalPrice  = $(".form-group.field-activeform-totalprice");
//复选框的选中
$(function(){

	if (type==1) {
		$("#activeform-agegroup").find("input[type=checkbox] :checked").removeAttr('checked');
		//选中年龄段
		if (ageGroup) {
			for(var key in ageGroup){
				$("#activeform-agegroup").find("input[type=checkbox]").each(function(){
					if($(this).val() == key){
						$(this).attr('checked',true);
					}
				});
			}
		}
		if (ability) {
			//选中活动能力
			for(var key in ability){
				$("#activeform-ability").find("input[type=checkbox]").each(function(){
					if($(this).val() == key){
						$(this).attr('checked',true);
					}
				});
			}
		}
	}
	if (type==3) {
		totalPrice.show();
		//价格类型的选中
		if (priceKind) {
			if (priceKind == 2) {
				adultprice.show();
		    	kidPrice.show();
		    	totalPrice.hide();
			} else {
		    	adultprice.hide();
		    	kidPrice.hide();
		    }
		}
	}


});


$('input[name="ActiveForm[priceKind]"]').click(function(){  
	var current = $('input[name="ActiveForm[priceKind]"]:checked');
	    $value  = current.val();

    if($value == 2) {
    	adultprice.show();
    	kidPrice.show();
    	totalPrice.hide();
    } else {
    	adultprice.hide();
    	kidPrice.hide();
    	totalPrice.show();
    }
}); 

//添加场次
$(".addSchedule").bind("click", function(){
	//弹窗
	  var schedule = {_csrf : csrfToken, schedule_data:schedule_data};
	$.post('?r=active/edit/schedule', schedule, function (data) {
		
        if(data.code == 1) {
            var dialog = Dialog.create({
                title:'活动场次添加',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '保存'
                }],
                //添加
                events : {
                	'#submit-audit click' : function(){
                        var updateData = new Object();
                        updateData.activityId = activityId;
                        for (var i=0;i<attrs.length;i++)
                        {
                            updateData[attrs[i]] =$("#scheduleform-" + (attrs[i].toLowerCase())).val();
                        }
	                	$.post('?r=active/edit/schedule_add', updateData, function (data) {
	                		if(data.code == 1) {
	                			dialog.close();
	                			alert(data.msg);
	                			location.reload();
	                		} else {
	                			alert(data.msg);
	                		}
	                	});
                	}
                }
            });
        }
	});
});

$('#grid').on('click', '.update', function () {
	 var key = $(this).attr('key');
		 var schedule = {_csrf : csrfToken, schedule_data:schedule_data,key:key, action:'update'}; 
		$.post('?r=active/edit/schedule', schedule, function (data) {
	        if(data.code == 1) {
	            var dialog = Dialog.create({
	                title:'活动场次编辑',
	                width : 1000,
	                bodyView : data.data,
	                buttons : [{
	                    id: 'submit-audit',
	                    className: 'btn-primary',
	                    value: '保存'
	                }],
	                events : {
		                	'#submit-audit click' : function(){
		                        var updateData = new Object();
			                    updateData.id         = schedule_data[key]['id'];
		                        updateData.activityId = activityId;
		                        //编辑时提交当前数据
		                        updateData.key    = key;
		                        for (var i=0;i<attrs.length;i++)
		                        {
		                            updateData[attrs[i]] =$("#scheduleform-" + (attrs[i].toLowerCase())).val();
		                        }
			                       //updateData.schedule_data = schedule_data;
				                	$.post('?r=active/edit/schedule_update', updateData, function (data) {
				                		if(data.code == 1) {
				                			dialog.close();
				                			alert(data.msg);
				                			location.reload();
				                		} else {
				                			alert(data.msg);
				                		}

				                	});
		                    }
	                	
	                }
	            });
	        } else {
	        	alert(data.msg);
	        }
		});
});


$('#grid').on('click', '.delete', function () {
	 var key = $(this).attr('key');
     schedule_id = schedule_data[key]['id'];
	 var dialog = Dialog.confirm({
         title : '确认',
         msg : '是否删除该场次，请谨慎操作！',
         no: {
             text : '取消',
             handle : function(){
                dialog.close();
               }
         },
         yes:{
             text : '删除',
             handle : function(){
            		var schedule = {_csrf : csrfToken, schedule_id:schedule_id,activityId:activityId, action:'delete'};
            		$.post('?r=active/edit/schedule_del', schedule, function (data) {
            			dialog.close();
            			if(data.code == 1) {
            			    alert(data.msg);
            			    location.reload();
            			} else {
            				alert(data.msg);
            			}
            		});
             }
         }
     });
});

 




