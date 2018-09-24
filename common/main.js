$(document).ready(function(e) {
	$("#Form").
	bootstrapValidator({
		live:'disabled',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            Tel: {
                message: '手机号输入错误。',
                validators: {
                    notEmpty: {
                        message: '手机号不能为空。'
                    },
                    regexp: {
                        regexp: /^[0-9]{11}$/,
                        message: '手机号格式错误。'
                    }
                }
            },
            Password: {
                message: '密码输入错误。',
                validators: {
                    notEmpty: {
                        message: '密码不能为空。'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: '密码只能为字母、数字或下划线。'
                    },
                    stringLength: {
                        min: 4,
                        max: 16,
                        message: '密码应为4-16位。'
                    },
					remote: {
						message: '用户名或密码错误。',
						data: function(validator) {
                            return {
                                Tel: validator.getFieldElements('Tel').val()
                            };
                        },
						url: '../check.php'
					}
                }
            }
        }
    })
	if(!navigator.cookieEnabled)
	{
		$(".checkbox").addClass("hidden");
		$("#alert").text("您的浏览器已禁用Cookie，为确保自动登录，请更改浏览器设置。");
	}
	
});