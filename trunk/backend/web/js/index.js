(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/**
 * @author H.Yvonne
 * @create 2015.11.27
 * find password
 */

// add form method
(function () {
    $.validator.addMethod('phone',function(value, element, param){
        return this.optional(element) || value.match(/^1[34578][0-9]{9}$/);
    },'Data is invalid');
    $.validator.addMethod('piccode',function(value, element, param){
        return this.optional(element) || value.match(/^[A-Za-z]{4}$/);
    },'Data is invalid');
    $.validator.addMethod('msgcode',function(value, element, param){
        return this.optional(element) || value.match(/^[0-9]{4}$/);
    },'Data is invalid');
    $.validator.addMethod('password',function(value, element, param){
        return this.optional(element) || value.match(/^[A-Za-z0-9]{6,20}$/);
    },'Data is invalid');
})();
/*error info*/
function errorInfo (obj,txt) {
    obj.find('div.form-false-tip').css({
        'visibility': 'visible'
    });
    obj.find('u[nt="error-wrap"]').html(txt);
};  
/*phone mail validate*/
(function () {
    function validFun (obj) {
        obj.validate({
            focusInvalid: false,
            showErrors: function (errorMap, errorList) {
                if(!errorList.length) {
                    obj.find('div.form-false-tip').css({
                        'visibility': 'hidden'
                    });
                    $('input:focus').parents('div.form-item-wrap').removeClass('form-item-false');
                    return;
                };
                errorInfo(obj,errorList[0].message);
                for(var i = 0; i<errorList.length;i++){
                    $(errorList[i].element).parents('div.form-item-wrap').addClass('form-item-false');
                    $(errorList[i].element).parents('a.dropdown-toggle').addClass('form-item-false');
                }
            },
            rules: {
                'Findpassword[username]' : {
                    required : true
                },
                'Findpassword[verifyCode]' : {
                    required : false
                },
                'Findpassword[iphoneCode]' : {
                    required : false
                },
                'Findpassword[mailCode]' : {
                    required : false
                },
                'Findpassword[password]' : {
                    required : true,
                    rangelength : [6,20],
                    password : true
                },
                'Findpassword[repassword]' : {
                    required : true,
                    rangelength : [6,20],
                    password : true,
                    equalTo : 'input[name="Findpassword[password]"]'
                }
            },
            messages: {
                'Findpassword[username]' : {
                    required : '',
                    phone : '手机号码格式不正确！',
                    email : '邮箱地址不正确！'
                },
                'Findpassword[mailCode]' : {
                    required : '验证码不能为空！',
                    msgcode : '验证码格式不正确！'
                },
                'Findpassword[verifyCode]' : {
                    required : '图形验证码不能空！',
                    piccode : '图形验证码格式不正确！',
                    remote : '图形验证码不正确！' 
                },
                'Findpassword[iphoneCode]' : {
                    required : '验证码不能为空！',
                    msgcode : '验证码格式不正确！'
                },
                'Findpassword[password]' : {
                    required : '密码不能空！',
                    rangelength : '密码为6-20位字母或数字！',
                    password : '密码为6-20位字母或数字！'
                },
                'Findpassword[repassword]' : {
                    required : '重复密码不能空！',
                    rangelength : '重复密码为6-20位字母或数字！',
                    password : '重复密码为6-20位字母或数字！',
                    equalTo : '重复密码与密码不一致！'
                }
            },
            submitHandler : function(form){
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: obj.attr('action'),
                    data: obj.serialize()
                }).done(function (data) {
                    if(data.code == 1){
                        location.href = data.data;
                    } else {
                        errorInfo(obj,data.msg);
                    }
                });
            }
        });
    }
    validFun($('#phone-form'));
    validFun($('#mail-form'));
})();

/*tab switch*/
(function () {
    $('a[nt="tab-btn"]').on('click', function () {
        var id = $(this).attr('data-attr');
        animate($(this));
    });

    function animate (obj) {
        var old = obj.parents('section.account-main'),
            news = old.siblings('section.account-main');
        old.stop(true,false).animate({
            'left' : -600,
            'opacity' : 0
        },200);
        news.css({
            'left' : 600
        }).stop(true,false).animate({
            'left' : 0,
            'opacity' : 1
        },300);
    };
})();
/*phone*/
(function () {
    var form1 = $('#phone-form');
    form1.find('input[name="Findpassword[username]"]').rules('add', {
        phone : true,
        messages : {
            required : '请输入手机号码！'
        }
    });
    form1.find('input[name="Findpassword[verifyCode]"]').rules('add', {
        required : true,
        piccode : true,
        remote : {
            type:"POST",
            url:form1.attr('action'),
            async:false,
            complete:function (xhr,ts) {
                if(xhr.responseJSON === true) {
                    remoteSucc();
                }
            }
        }
    });
    form1.find('input[name="Findpassword[iphoneCode]"]').rules('add', {
        required : true,
        msgcode : true
    });
    form1.find('input[name="Findpassword[repassword]"]').rules('add', {
        equalTo : 'input[name="Findpassword[password]"]:eq(0)'
    });
    function remoteSucc () {
        $('li[nt="pic-code"]').hide();
        $('li[nt="phone-code"]').show().find('div.form-item-wrap').removeClass('form-item-false');
        form1.find('input[name="Findpassword[verifyCode]"]').rules('remove');
    };
})();

/*mail*/
(function () {
    var form2 = $('#mail-form');
    form2.find('input[name="Findpassword[username]"]').rules('add', {
        email : true,
        messages : {
            required : '请输入邮箱地址！'
        }
    });
    form2.find('input[name="Findpassword[mailCode]"]').rules('add', {
        required : true,
        msgcode : true
    });
    form2.find('input[name="Findpassword[repassword]"]').rules('add', {
        equalTo : 'input[name="Findpassword[password]"]:eq(1)'
    });
})();

/*找回密码提交*/
(function () {
    var form1 = $('#phone-form'), form2 = $('#mail-form');
    $('a[nt="next-btn"]').on('click', function () {
        var id = $(this).attr('data-attr');
        if((id|0) === 0) {
            if(phoneValid() === false) return;
            var info = {
                url : '?r=site/findpassword',
                data : {
                    username:form1.find('input[name="Findpassword[username]"]').val(),
                    iphoneCode:form1.find('input[name="Findpassword[iphoneCode]"]').val()
                },
                type : 0
            }
        } else {
            if(mailValid() === false) return;
            var info = {
                url : '?r=site/findpassword',
                data : {
                    username:form2.find('input[name="Findpassword[username]"]').val(),
                    mailCode:form2.find('input[name="Findpassword[mailCode]"]').val()
                },
                type : 1
            }
        }
        post(info);
    });

    function phoneValid () {
        var code = form1.find('input[name="Findpassword[iphoneCode]"]').valid(),
            pic = form1.find('input[name="Findpassword[verifyCode]"]').valid(),
            name = form1.find('input[name="Findpassword[username]"]').valid();
        var flag = name && pic && code;
        return flag;
    };
    function mailValid () {
        var code = form2.find('input[name="Findpassword[mailCode]"]').valid(),
            name = form2.find('input[name="Findpassword[username]"]').valid();
        var flag = name && code;
        return flag;
    };
    function post (info) {
        $.post(info.url, info.data, function (data) {
            var obj;
            info.type == 0?obj = form1:obj = form2;
            if(data.code == 1) {
                obj.find('div.form-false-tip').css({
                    'visibility': 'hidden'
                });
                next(info.type);
            } else {
                errorInfo(obj,data.msg);
            }
        },'json');
    };
    function next (type) {
        var obj;
        type == 0?obj = form1:obj = form2;
        var step = obj.parents('section.account-main').find('div.find-step-item');
        step.removeClass('find-step-active');
        step.eq(1).addClass('find-step-active');
        showForm(obj);
    };
    function showForm (obj) {
        obj.find('li[dt="step-one"]').hide();
        obj.find('li[dt="step-two"]').show();
        $('li[nt="pic-code"]').remove();
        obj.find('a[nt="next-btn"]').hide();
        obj.find('button[nt="submit-btn"]').show();
    };
})();

/*get code*/
(function () {
    var speed = 1000;
    $('a[nt="get-msg"]').on('click', function () {
        var inp = $('#phone-form').find('input[name="Findpassword[username]"]');
        var postInfo = {
            url : '?r=site/scbp',
            data : {
                phone : inp.val(),
                kind : 3
            },
            type : 0
        };
        codeFun(postInfo);
    });
    $('a[nt="get-yy"]').on('click', function (data) {
        var inp = $('#phone-form').find('input[name="Findpassword[username]"]');
        var postInfo = {
            url : '?r=site/scby',
            data : {
                phone : inp.val()
            },
            type : 0
        };
        codeFun(postInfo);
    });
    /*获取语音和短信*/
    function codeFun (postInfo) {
        var inp = $('#phone-form').find('input[name="Findpassword[username]"]'),msgCount;
        if(inp.valid() === false) return;
        getCode(postInfo);
        var info = {
            wrap : 'msgCount',
            btn : $('a[nt="get-msg"]'),
            countwrap : $('span[nt="msg-countwrap"]'),
            count : $('span[nt="msg-count"]'),
            seconds : 60,
            type : 0
        }
        countDown(info);
    };
    /*获取邮件验证码*/
    $('a[nt="get-mail"]').on('click', function () {
        var inp = $('#mail-form').find('input[name="Findpassword[username]"]'),mailCount;
        if(inp.valid() === false) return;
        var postInfo = {
            url : '?r=site/scbm',
            data : {
               address : inp.val()
            },
            type : 1
        }
        getCode(postInfo);
        var info = {
            wrap : 'mailCount',
            btn : $('a[nt="get-mail"]'),
            countwrap : $('span[nt="mail-countwrap"]'),
            count : $('span[nt="mail-count"]'),
            seconds : 120,
            type : 1
        }
        countDown(info); 
    });

    function getCode (postInfo) {
        $.post(postInfo.url, postInfo.data, function (data) {
            if(data.code == 1) {
                if(postInfo.type === 1) {
                    var param = postInfo.data.address.split('@');
                    window.open('http://mail.'+param[1]);
                }
            } else {
                var obj;
                postInfo.type === 0?obj = $('#phone-form'): obj = $('#mail-form');
                errorInfo(obj,data.msg);
            }
        },'json');
    };

    function countDown (info) {
        info.btn.hide();
        info.countwrap.show();
        var yyBtn = $('a[nt="get-yy"]');
        if(info.type === 0) {
            yyBtn.hide();
            yyBtn.siblings('span').show();
        }
        var warp = info.count;
        warp.html(info.seconds);
        info.wrap = setInterval(function() {
            info.seconds = info.seconds - 1;
            var html = info.seconds;
            warp.html(html);
            if (info.seconds == 0) {
                clearInterval(info.wrap);
                if(info.type === 0) {
                    yyBtn.show();
                    yyBtn.siblings('span').hide();
                }
                info.btn.show();
                info.countwrap.hide();
            }
        }, speed);
    }
})();

/*placeholder*/
(function () {
    $('input[nt="inp"]').on('focus', function () {
        $(this).parents('div.form-item-wrap').removeClass('form-item-false').addClass('form-item-focus');
        $(this).siblings('p[nt="placeh"]').hide();
    }).on('blur', function () {
        $(this).parents('div.form-item-wrap').removeClass('form-item-focus');
    });
    $('p[nt="placeh"]').on('click', function () {
        $(this).parents('div.form-item-wrap').removeClass('form-item-false').addClass('form-item-focus');
        $(this).hide();
        $(this).siblings('input[nt="inp"]').focus();
    });
})();
},{}]},{},[1]);

//# sourceMappingURL=index.js.map
