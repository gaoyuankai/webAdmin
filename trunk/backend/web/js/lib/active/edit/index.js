var csrfToken = $('meta[name="csrf-token"]').attr("content");
var attrs = new Array('minNumber', "stock","activityDate","activityTime","regStart","regEnd");
$(".addSchedule").bind("click", function(){
	var schedule = {_csrf : csrfToken, schedule_data:schedule_data};
	$.post('?r=active/edit/schedule', schedule, function (data) {
        if(data.code == 1) {
            var dialog = Dialog.create({
                title:'活动场次编辑',
                width : 1000,
                bodyView : data.data,
                buttons : [{
                    id: 'submit-audit',
                    className: 'btn-primary',
                    value: '提交'
                }],
                events : {
                	'#submit-audit click' : function(){
                        var updateData = new Object();
                        for (var i=0;i<attrs.length;i++)
                        {
                            updateData[attrs[i]] =$("#scheduleform-" + (attrs[i].toLowerCase())).val();
                        }
                        updateData.schedule_data = schedule_data;
	                	$.post('?r=active/edit/schedule_validate', updateData, function (data) {
	                		if(data.code == 1) {
	                			dialog.close();
	                			var schedule_data = data.data;
	                		    $("#grid").html(schedule_data);
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
	                    value: '提交'
	                }],
	                events : {
	                	'#submit-audit click' : function(){
	                        var updateData = new Object();
	                        for (var i=0;i<attrs.length;i++)
	                        {
	                            updateData[attrs[i]] =$("#scheduleform-" + (attrs[i].toLowerCase())).val();
	                        }
	                        updateData.schedule_data = schedule_data;
	                        updateData.key     = key;
	                        //console.log(updateData);
		                	$.post('?r=active/edit/schedule_validate', updateData, function (data) {
		                		if(data.code == 1) {
		                			dialog.close();
		                			var schedule_data = data.data;
		                		    //$.pjax.reload({container:'#grid'});  //Reload GridView
		                		    $("#grid").html(schedule_data);
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
//场次删除
$('#grid').on('click', '.delete', function () {
	var key = $(this).attr('key');
	var schedule = {_csrf : csrfToken, schedule_data:schedule_data,key:key, action:'delete'};
	$.post('?r=active/edit/schedule_validate', schedule, function (data) {
		if(data.code == 1) {
			var schedule_data = data.data;
		    $("#grid").html(schedule_data);
		} else {
			alert(data.msg);
		}
	});
});
var adultprice = $(".form-group.field-activeform-adultprice");
var kidPrice    = $(".form-group.field-activeform-kidprice");
var totalPrice  = $(".form-group.field-activeform-totalprice");
//价格类型的选中
$(function(){
	var kind = $('input[name="ActiveForm[priceKind]"]:checked').val();
	if(kind == 2) {
		adultprice.show();
    	kidPrice.show();
    	totalPrice.hide();
	} else {
    	adultprice.hide();
    	kidPrice.hide();
    	totalPrice.show();
    }
});

$('input[name="ActiveForm[priceKind]"]').click(function(){  
	$value = $('input[name="ActiveForm[priceKind]"]:checked').val();
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

