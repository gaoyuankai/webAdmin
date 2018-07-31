<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use backend\assets\AppAsset;
use yii\helpers\Url;


$this->title = 'select';
$this->params['breadcrumbs'][] = $this->title;
?>

<script type="text/javascript" src="js/lib/jquery.js"></script>

<div class="site-signup">
        </div>
         	 <form id="Form1"  method="post" >
         	 <div class="form-item-inp fl">
					用户名：<input name="username" type="text" /><br />
				</div>
				<div class="form-item-inp fl">
					订单开始时间：
					<input name="starttimestamp" type="text" onfocus="setday(this)" readonly="readonly" />
					<input name="endtimestamp" type="text" onclick="setday(this)" readonly="readonly" />
				<div class="form-item-inp fl">
				活动名称：<input name="activeName" type="text" /><br />
				</div>
				<div class="form-item-inp fl">
				活动时间：<input name="activeTime" type="text" onclick="setday(this)" readonly="readonly" />
				</div>
				<div class="form-item-inp fl">
				订单状态：<input name="tradeStatus" type="text" /><br />
				</div>
				<input type="submit" value="提 交" />
			</form>
    		</div>
</div>

<script type="text/javascript">
    //将form转为AJAX提交
	function ajaxSubmit(frm, fn) {
		var dataPara = getFormJson(frm);
		$.ajax({
			url:'?r=trade/trade/list',
			type: frm.method,
			data: dataPara,
			success: fn
		});
	}

	//将form中的值转换为键值对。
	function getFormJson(frm) {
		var o = {};
		var a = $(frm).serializeArray();
		$.each(a, function () {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});

		return o;
	}

	//调用
    $(document).ready(function(){
		$('#Form1').bind('submit', function(){
			ajaxSubmit(this, function(data){
				//alert(data);
			});
			return false;
		});
    });
    </script>

<?=AppAsset::addScript($this,'@web/js/DatePicker.js')?>