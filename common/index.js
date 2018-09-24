var staffChart,eventChart;
var timecount;
var dif;
var isStart = 0;
var isCheck = 0;

$(document).ready(function(e) {
  var http = new XMLHttpRequest;
  http.open("HEAD", ".", false);
  http.send(null); 
  var remote = new Date(http.getResponseHeader("Date"));
  var now = new Date();
  dif = remote.getTime() - now.getTime();
  timecount = setInterval("TimeCountDown()",1000);
  var showevent = setInterval(function(){
	  showEventTable($("#pageNum").val());
	  var pageConfirmNum = $("#pageConfirmNum").val();
	  if(!pageConfirmNum) pageConfirmNum = 1;
 	  showConfirmTable(pageConfirmNum);
	  var pageNumMsgTable = $("#pageNumMsgTable").val();
	  if(!pageNumMsgTable) pageNumMsgTable = 1;
	  showMsgTable(pageNumMsgTable);
  	  var pageNumSmsTable = $("#pageNumSmsTable").val();
	  if(!pageNumSmsTable) pageNumSmsTable = 1;
	  showSmsTable(pageNumSmsTable);
	  getNewPrompt();
  },1000*120);
  var rebuildchart = setInterval(function(){
	  showChart("person",true);
      showChart("event",true);
  },1000*300); 
  
  setTimeout("CheckSignState()",1000*5);
  
  var options, a;
  $('#AutoSearchPerson').focus(function(e) {
	  options = { serviceUrl:'../service/searchPerson.php',
	              groupBy:'group',
				  onSelect:function (suggestion) {
					$("#AutoSearchPerson").val(suggestion.data.name);
					$("#showPersonWrapper").html('<table class="table table-striped" id="detailPersonTable"><thead><tr><th>#</th><th>姓名</th><th>签到信息</th><th>组别</th><th>职务</th><th>联系方式</th><th>来宾信息</th></tr></thead><tbody>'+suggestion.data.html);
					$("#AutoSearchPerson").blur();
					$("#searchPerson").bootstrapValidator('revalidateField','searchPerson');
				  },
				  autoSelectFirst:true
				};
	  a = $('#AutoSearchPerson').autocomplete(options); 
  });
  
  $("#msgSend .bootstrap-tagsinput input").focus(function(e) { 
	  options = { serviceUrl:'../service/searchMsgReceiver.php',
	              groupBy:'type',
				  width:300,
				  onSelect:function(suggestion) {
					  msg.tagsinput('add',{"value":suggestion.data.id, "text":suggestion.value, "type":suggestion.data.type});
					  this.value="";
				  },
				  autoSelectFirst:true
	            };
	  a = $("#msgSend .bootstrap-tagsinput input").autocomplete(options);
  }); 
  
  $("#smsSend .bootstrap-tagsinput input").focus(function(e) {
	  options = { serviceUrl:'../service/searchMsgReceiver.php',
	              groupBy:'type',
				  width:300,
				  onSelect:function(suggestion) {
					  sms.tagsinput('add',{"value":suggestion.data.id, "text":suggestion.value, "type":suggestion.data.type});
					  this.value="";
				  },
				  autoSelectFirst:true
	            };
	  a = $("#smsSend .bootstrap-tagsinput input").autocomplete(options);
  });
  
  $("#msgSend").find('#msgReceive')
     .change(function (e) {
       $('#msgSend').bootstrapValidator('revalidateField', 'msgReceive');
     })
     .end()
     .bootstrapValidator({
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                msgReceive: {
                    validators: {
                        notEmpty: {
                            message: '请至少选择一个消息接收者。'
                        }
                    }
                },
				msgContent: {
                    validators: {
                        notEmpty: {
                            message: '消息内容不能为空'
                        }
                    }
                }
			}
     })
   .on('success.form.bv', function(e) {
      e.preventDefault();
	  var $form = $(e.target);
	  var bv = $form.data('bootstrapValidator');
	  $.post('../message.php', $form.serialize()+'&tel='+$("#Tel").text(), function(result) {
		  if(result=="1") 
		  {
			  $("#btnSendMsg").removeAttr("disabled")
						   .removeClass("btn-primary")
						   .addClass("btn-success")
						   .text("发送成功");
              setTimeout( function(){
			  $("#btnSendMsg").text("确定发送")
							 .removeClass("btn-success")
							 .addClass("btn-primary");
			  $('#msgReceive').tagsinput('removeAll');
			  $("#msgSend").bootstrapValidator('resetForm', true);
			  showMsgTable();
			},1500);
		  }
	  });
    });
  $("#smsSend").find('#smsReceive')
     .change(function (e) {
       $('#smsSend').bootstrapValidator('revalidateField', 'smsReceive');
     })
     .end()
     .bootstrapValidator({
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                smsReceive: {
                    validators: {
                        notEmpty: {
                            message: '请至少选择一个短信接收者。'
                        }
                    }
                },
				smsContent: {
                    validators: {
                        notEmpty: {
                            message: '短信内容不能为空'
                        }
                    }
                }
			}
     })
   .on('success.form.bv', function(e) {
      e.preventDefault();
	  var $form = $(e.target);
	  var bv = $form.data('bootstrapValidator');
	  $.post('../sms.php', $form.serialize()+'&tel='+$("#Tel").text(), function(result) {
		  if(result.state=="1")
		  {
			  $("#smsReceive").siblings("small").after('<p class="text-success small">'+result.msg+'</p>');
			  $("#btnSendSms").removeAttr("disabled")
						   .removeClass("btn-primary")
						   .addClass("btn-success")
						   .text("发送成功");
              setTimeout( function(){
			  $("#btnSendSms").text("确定发送")
							 .removeClass("btn-success")
							 .addClass("btn-primary");
			  $("#smsReceive").siblings(".small").empty();
			  $('#smsReceive').tagsinput('removeAll');
			  $("#smsSend").bootstrapValidator('resetForm', true);
			  showSmsTable();
			},1500);
		  }
		  else
		  {
			  $("#smsReceive").siblings("small").after('<p class="text-danger small">'+result.msg+'</p>');
			  $("#btnSendSms").removeAttr("disabled")
						   .removeClass("btn-primary")
						   .addClass("btn-danger")
						   .text("发送失败");
              setTimeout( function(){
			  $("#btnSendSms").text("确定发送")
							 .removeClass("btn-danger")
							 .addClass("btn-primary");
			  $("#smsReceive").siblings(".small").empty();
			  $('#smsReceive').tagsinput('removeAll');
			  $("#smsSend").bootstrapValidator('resetForm', true);
			},1500);
		  }
	  },"json");
    });
   
  $('#AutoSearchEvent').focus(function(e) {
	  options = { serviceUrl:'../service/searchEvent.php',
	              groupBy:'require',
				  onSelect:function (suggestion){
					  $("#AutoSearchEvent").val(suggestion.data.id);
					  $("#showEventWrapper").html('<table class="table table-striped" id="detailEventTable"><thead><tr><th>#</th><th>需求方</th><th>时间</th><th>地点</th><th>进度</th><th></th></tr></thead><tbody>'+suggestion.data.html);
					  $("#AutoSearchPerson").blur();
					  $("#searchEvent").bootstrapValidator('revalidateField','searchEvent');
				  },
				  autoSelectFirst:true
	            };
	  a = $("#AutoSearchEvent").autocomplete(options);
  });
		
  $("#AddUpper").focus(function(e) {
	var group = $("#AddGroup").find("option:selected").val();
	if(group!="组别")
	{
		options = { serviceUrl:'../service/searchUpper.php',
					params:{'group':group},
					onSelect:function (suggestion){
						$("#AddForm").bootstrapValidator('revalidateField','AddUpper');
					},
					autoSelectFirst:true
				  };
		a = $('#AddUpper').autocomplete(options);
	}
  }); 
  $("#EventRequirePerson").focus(function(e) {
	var group = $("#EventRequire").find("option:selected").val();
	if(group!="需求方")
	{
		options = { serviceUrl:'../service/searchUpper.php',
					params:{'group':group},
					onSelect:function (suggestion){
						$("#AddEvent").bootstrapValidator('revalidateField','EventRequirePerson');
				    },
					autoSelectFirst:true
				  };
	    a = $("#EventRequirePerson").autocomplete(options);
	}  
  });
  $("#EventProcessPerson").focus(function(e) {
	var group = $("#EventProcess").find("option:selected").val();
	if(group!="处理方")
	{
		options = { serviceUrl:'../service/searchUpper.php',
					params:{'group':group},
					onSelect:function (suggestion){
						$("#AddEvent").bootstrapValidator('revalidateField','EventProcessPerson');
				    },
					autoSelectFirst:true
				  };
	    a = $("#EventProcessPerson").autocomplete(options);
	}  
  });
   
  $(".nav-sidebar").children("li").click(function(){
	  $(this).siblings().removeClass("active");
	  $(this).parent().siblings().children("li").removeClass("active");
	  $(this).addClass("active");
  });
 
  $("#AddForm")
    .bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
			live:'enable',
			container:'tooltip',
            fields: {
                AddName: {
                    validators: {
                        notEmpty: {
                            message: '姓名不能为空。'
                        },
	                    regexp: {
	                        regexp: /^[\u4e00-\u9fa5]+$/,
    	                    message: '姓名应为汉字。'
                    	}
                    }
                },
                AddGroup: {
                    validators: {
                        callback: {
                            message: '请选择组别。',
							callback:function(value,validator){
								return value=="组别"?false:true;
							}
                        }
                    }
                },
                AddJob: {
                    validators: {
                        callback: {
                            message: '请选择职务。',
							callback:function(value,validator){
								return value=="职务"?false:true;
							}
                        }
                    }
                },
				AddTel: {
                    validators: {
                        notEmpty: {
                            message: '手机号不能为空。'
                        },
                        regexp: {
	                        regexp: /^[0-9]{11}$/,
    	                    message: '手机号格式错误'
                    	},
						remote: {
							url:'../add.php',
							message:'此手机号已被注册。',
							type:'post',
							data:{ type:'check'}
						}
                    }
                },
				AddUpper: {
                    validators: {
						callback: {
							message:'请先选择组别。',
							callback:function(value, validator, $field){
								if($("#AddGroup").find("option:selected").val()=="组别") return false;
								else return true;
							}
						},
                        notEmpty: {
                            message: '直接上级不能为空。'
                        },
                        regexp: {
	                        regexp: /^[0-9]+( \- [\u4e00-\u9fa5]+)?$/,
    	                    message: '直接上级格式错误。'
                    	},
						remote: {
							url:'../add.php',
							message:'此编号不属于相应组别。',
							type:'post',
							data: function(validator) {
                              return {
                                AddGroup:$("#AddGroup").find("option:selected").val(),
								type:'upper'
							  }
							}
						}
					}
				}
            }
  })
    .on('change', '[name="AddGroup"]', function(e, data) {
		var $AddGroup = $(e.target);
		if(($AddGroup.val().length == 1 && parseInt($AddGroup.val()) > 1 && parseInt($AddGroup.val())<7) || ($AddGroup.val().length==2 && parseInt($AddGroup.val()) > 70 && parseInt($AddGroup.val()) < 77)) 
		{
			$AddGroup.parents('.col-sm-2').siblings().find('.job').html('<option selected>职务</option><option value="5">总负责人</option><option value="4">信息联络员</option><option value="3">信息记录员</option><option value="1">志愿者小组长</option><option value="0">志愿者</option>');
		}
		else if ($AddGroup.val().length == 1 && parseInt($AddGroup.val()) == 7 || parseInt($AddGroup.val()) == 1)
		{
			$AddGroup.parents('.col-sm-2').siblings().find('.job').html('<option selected>职务</option><option value="5">总负责人</option><option value="1">志愿者小组长</option><option value="0">志愿者</option>');
		}
		else if (parseInt($AddGroup.val()) == 111)
		{
			$AddGroup.parents('.col-sm-2').siblings().find('.job').html('<option selected>职务</option><option value="2">负责人</option><option value="1">志愿者小组长</option><option value="4">信息联络员</option><option value="3">信息记录员</option>');
		}
		else $AddGroup.parents('.col-sm-2').siblings().find('.job').html('<option selected>职务</option><option value="2">负责人</option><option value="1">志愿者小组长</option><option value="0">志愿者</option>');
		$("#AddForm").bootstrapValidator('revalidateField','AddUpper');
    })
    .on('success.form.bv', function(e) {
      e.preventDefault();
	  var $form = $(e.target);
	  var bv = $form.data('bootstrapValidator');
	  $.post('../add.php', $form.serialize(), function(result) {
		  if(result=="1") 
		  {
			$("#AddPerson").removeAttr("disabled")
						   .removeClass("btn-primary")
						   .addClass("btn-success")
						   .text("添加成功");
			setTimeout( function(){
			  $("#AddPerson").text("添加人员")
							 .removeClass("btn-success")
							 .addClass("btn-primary");
			  document.getElementById("AddForm").reset();
			  $("#AddForm").bootstrapValidator('resetForm', false);
			  showPersonTable();
			  showChart("person");
			},1500);
		  }
	  });
    });

  
  $("#AddEvent")
    .bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
			live:'enable',
			container:'tooltip',
            fields: {
			    EventRequire: {
                    validators: {
                        callback: {
                            message: '请选择需求方。',
							callback:function(value,validator){
								return value=="需求方"?false:true;
							}
                        }
                    }
                },
                EventRequirePerson: {
                    validators: {
						callback: {
							message:'请先选择需求方。',
							callback:function(value, validator, $field){
								if($("#EventRequire").find("option:selected").val()=="需求方") return false;
								else return true;
							}
						},
                        notEmpty: {
                            message: '需求方责任人不能为空。'
                        },
	                    regexp: {
	                        regexp: /^[0-9]+( \- [\u4e00-\u9fa5]+)?$/,
    	                    message: '需求方责任人格式错误。'
                    	},
						remote: {
							url:'../add.php',
							message:'此责任人编号不属于相应组别。',
							type:'post',
							data: function(validator) {
                              return {
                                AddGroup:$("#EventRequire").find("option:selected").val(),
								type:'eventRequireCheck'
							  }
							}
						}
                    }
                },
                EventTime: {
                    validators: {
                        notEmpty: {
                            message: '时间不能为空。'
                        }
                    }
                },
                EventPlace: {
                    validators: {
                        notEmpty: {
                            message: '地点不能为空。'
                        }
                    }
                },
                EventProcess: {
                    validators: {
                        callback: {
                            message: '请选择处理方。',
							callback:function(value,validator){
								return value=="处理方"?false:true;
							}
                        }
                    }
                },
                EventProcessPerson: {
                    validators: {
                        callback: {
							message:'请先选择处理方。',
							callback:function(value, validator, $field){
								if($("#EventProcess").find("option:selected").val()=="处理方") return false;
								else return true;
							}
						},
                        notEmpty: {
                            message: '处理方责任人不能为空。'
                        },
	                    regexp: {
	                        regexp: /^[0-9]+( \- [\u4e00-\u9fa5]+)?$/,
    	                    message: '处理方责任人格式错误。'
                    	},
						remote: {
							url:'../add.php',
							message:'此责任人编号不属于相应组别。',
							type:'post',
							data: function(validator) {
                              return {
                                AddGroup:$("#EventProcess").find("option:selected").val(),
								type:'eventProcessCheck'
							  }
							}
						}
                    }
                },
				EventContent: {
                    validators: {
                        notEmpty: {
                            message: '详细需求不能为空。'
                        }
                    }
                }
            }
  })
    .on('change', '[name="EventRequire"]', function(e, data) {
	  $("#AddEvent").bootstrapValidator('revalidateField','EventRequirePerson');
  })
    .on('change', '[name="EventProcess"]', function(e, data) {
	  $("#AddEvent").bootstrapValidator('revalidateField','EventProcessPerson');
  })
    .on('success.form.bv', function(e) {
	  e.preventDefault();
      var $form = $(e.target);
      var bv = $form.data('bootstrapValidator');
	  var postman = $("#Tel").text();
      $.post('../event.php', $form.serialize()+"&type=postnew&postman="+postman, function(result) {
		  if(result=="1") 
		  {
			  $("#PostEvent").removeAttr("disabled")
				             .removeClass("btn-primary")
							 .addClass("btn-success")
					         .text("发布成功");
			  setTimeout( function(){
				  $("#PostEvent").text("发布事件")
					             .removeClass("btn-success")
						         .addClass("btn-primary");
				  document.getElementById("AddEvent").reset();
			      showEventTable();
				  $("#AddEvent").bootstrapValidator('resetForm', false);
			  },1500);
		  }
      });
  });

  
  $("#searchEvent")
    .bootstrapValidator({
            feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
			},
			live:'enable',
			container:'tooltip',
            fields: {
			    searchEvent: {
                    validators: {
                        notEmpty: {
                            message: '搜索内容不能为空。'
					    },
						remote: {
							message: '查无此事件。',
							url: '../service/searchEvent.php'
						}
                    }
				},
            }
    })
	.on('error.field.bv',function(){
		if($("#detailEventTable").length) showEventTable();
	});
  
  $("#searchPerson")
    .bootstrapValidator({
            feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
			},
			container:'tooltip',
            fields: {
			    searchPerson: {
                    validators: {
                        notEmpty: {
                            message: '搜索内容不能为空。'
					    },
						remote: {
                            message: '查无此人。',
                            url: '../service/searchPerson.php'
						}
                    }
				},
            }
    })
	.on('error.field.bv',function(){
		if($("#detailPersonTable").length) showPersonTable();
	});
  
  $("#ChangePwdForm")
    .bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
		live:'enable',
        fields: {
            Password: {
                validators: {
                    notEmpty: {
                        message: '原密码不能为空。'
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
						message: '密码错误。',
						data: function(validator) {
                            return {
                                Tel: $("#Tel").text()
                            };
                        },
						url: '../check.php'
					}
                }
            },
            NewPassword: {
                validators: {
                    notEmpty: {
                        message: '新密码不能为空。'
                    },
					regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: '密码只能为字母、数字或下划线。'
                    },
                    stringLength: {
                        min: 4,
                        max: 16,
                        message: '密码应为4-16位。'
                    }
                }
            },
            ConfirmPassword: {
                validators: {
                    notEmpty: {
                        message: '确认新密码不能为空。'
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
					identical: {
                        field: 'NewPassword',
                        message: '两次输入的密码不一致。'
					}
                }
            }
        }
  })
    .on('error.field.bv',function(){
	  $("#showBtn").prop("disabled",true);
    })
	.on('success.field.bv',function(){
	  $("#showBtn").removeAttr("disabled");
	})
    .on('error.form.bv',function(){
	  $("#showBtn").prop("disabled",true);
    })
	.on('success.form.bv',function(){
	  $("#showBtn").removeAttr("disabled");
    });
	
  $("#AddSolutionForm")
    .bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
		live:'enable',
        fields: {
		    EventSolution:{
				validators:{
					notEmpty:{
						message:'解决方案不能为空。'
					}
				}
			}
		}
    })
	.on('error.field.bv',function(){
		$("#eventBtn").prop("disabled",true);
	})
	.on('success.form.bv',function(){
		$("#eventBtn").removeAttr("disabled");
		$("#eventBtn").click();			
	});
	
  $("#ChangePwd").on('shown.bs.modal', function() {
    $('#ChangePwdForm').bootstrapValidator('resetForm', true);
  });
  $("#AddSolution").on('shown.bs.modal',function(){
    $('#AddSolutionForm').bootstrapValidator('resetForm', true);
  });
  $("#showGuestModal").on('shown.bs.modal',function(){
      var vol_tel = $("#detailPersonTable").children("tbody").children("tr").children("td:eq(5)").text();
	  $.post('../service/getGuestTable.php','tel='+ vol_tel,function(result){
		$("#showGuestModal .modal-body").html(result);
		$("#showGuestModal tfoot td").empty();
		$("#showGuestModal thead th").empty();
	});
  });
  $("#showMsgModal").on('shown.bs.modal',function(){
	  getMsgTable();
  });
  
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	  if(e.target.getAttribute("href")=="#smsTab") showSmsTable();
	  if(e.target.getAttribute("href")=="#msgTab") showMsgTable();
  });
    
  $(".form-group").delegate("#signBtn","click",function(){
	  QuerySign();
  });
  
  $("body").delegate(".detail","click",function(){
	var event_id = $(this).parent("td").siblings(":eq(0)").text();
	$.post('../service/searchDetail.php','event_id='+event_id+'&tel='+$("#Tel").text(),function(result){
		$("#showEventPanel").html(result);
	},'html');
  })
  			.delegate("#finishEvent","click",function(){
	  var event_id = $(this).parents("tfoot").siblings("tbody").children("tr:eq(0)").children("td:eq(1)").text();
	  $.post('../event.php','type=finish&eventid='+event_id+'&tel='+$("#Tel").text(),function(result){
		  if(result=="1")
		  {
			  $.post('../service/searchDetail.php','event_id='+event_id+'&tel='+$("#Tel").text(),function(result){
				  $("#showEventPanel").html(result);
			  },'html');
			  showEventTable($("#pageNum").val());
			  showChart("event");
		  }
	  });
  })
  			.delegate("#pageEventBtn","click",function(){
  	  var value = $("#pageEventInput").val();
	  if(CheckPageEvent()) showEventTable(value);
  })
    	    .delegate("#pageEventInput","keyup",function(event){
	  if(event.keyCode == "13")
	  {
		  var value = $("#pageEventInput").val();
		  if(CheckPageEvent()) showEventTable(value);
	  }
	  else CheckPageEvent();
    })
   			.delegate("#pageEventInput","focusout",function(e,data){
				CheckPageEvent();
    })
  			.delegate("#pagePersonBtn","click",function(){
  	  var value = $("#pagePersonInput").val();
	  if(CheckPageEvent()) showPersonTable(value);
  })
    	    .delegate("#pagePersonInput","keyup",function(event){
	  if(event.keyCode == "13")
	  {
		  var value = $("#pagePersonInput").val();
		  if(CheckPagePerson()) showPersonTable(value);
	  }
	  else CheckPagePerson();
    })
   			.delegate("#pagePersonInput","focusout",function(e,data){
				CheckPagePerson();
    })
  			.delegate("#pageConfirmBtn","click",function(){
  	  var value = $("#pageConfirmInput").val();
	  if(CheckPageConfirm()) showConfirmTable(value);
  })
    	    .delegate("#pageConfirmInput","keyup",function(event){
	  if(event.keyCode == "13")
	  {
		  var value = $("#pageConfirmInput").val();
		  if(CheckPageConfirm()) showConfirmTable(value);
	  }
	  else CheckPageConfirm();
    })
   			.delegate("#pageConfirmInput","focusout",function(e,data){
				CheckPageConfirm();
    })
	        .delegate("#selectAll","click",function(){
				if($(this).prop("checked")) $(this).parents("thead").siblings("tbody").children().children().children("input:not(:disabled)").prop("checked",true);
				else $(this).parents("thead").siblings("tbody").children().children().children("input").removeAttr("checked");
			})
			.delegate(".confirm","click",function(){
				x = $("#showConfirmWrapper tbody").find("input:checked").toArray();
				for (i=0;i<x.length;i++)
				{
					var id = $(x[i]).parent("td").siblings(":eq(0)").text();
					var context = $(x[i]).parent("td").siblings(":eq(6)").children("input").val();
					$.post('../sign.php','type=confirm&id='+id,function(result){
						if(result=="1")
						{
							$(".confirm").removeClass("btn-primary")
							             .addClass("btn-success")
										 .text("确认成功");
							setTimeout(function(){
								showConfirmTable();
								getNewPrompt();
								showSignTable();
							},1500);
						}
					});
				}
			})
			.delegate("#detailPersonForm","change",function(){
				$.post('../service/getDetailChart.php','type=person&group='+$(this).val(),function(result){
					var ctx = $("#detailPersonChart").get(0).getContext("2d");
					var data = [{value:result.data.nosign,color:"#F7464A",highlight:"#FF5A5E",label:"未签到"},
								{value:result.data.sign,color:"#DAB85F",highlight:"#E6C04E",label:"待确认"},
								{value:result.data.checked,color:"#46BFBD",highlight:"#5AD3D1",label:"已确认"}];
					Chart.defaults.global.responsive = false; 
					if(result.data.nosign+result.data.sign+result.data.checked==0)
					{
						if(!$("#detailPersonChart").siblings("p").length) $("#detailPersonChart").before('<p class="text-danger"><strong>错误！</strong> 未查询到该组人员记录。</p>');
					}
					else $("#detailPersonChart").siblings("p").remove();
					var detailPersonChart = new Chart(ctx).Doughnut(data,{animationEasing:"easeInOutQuad",animationSteps:50});
				},'json');
			})
			.delegate("#detailEventForm","change",function(){
				$.post('../service/getDetailChart.php','type=event&group='+$(this).val(),function(result){
					var ctx = $("#detailEventChart").get(0).getContext("2d");
					var data = [{value:result.data.post,color:"#F4A81B",highlight:"#FAF43D",label:"已登记"},
					            {value:result.data.process,color:"#F7464A",highlight:"#FF5A5E",label:"处理中"},
								{value:result.data.finish,color:"#6AB82C",highlight:"#65D97D",label:"已完结"}];
					Chart.defaults.global.responsive = false;
					if(result.data.post+result.data.process+result.data.finish==0)
					{
						if(!$("#detailEventChart").siblings("p").length) $("#detailEventChart").before('<p class="text-danger"><strong>错误！</strong> 未查询到该组事务记录。</p>');
					}
					else $("#detailEventChart").siblings("p").remove();
					var detailEventChart = new Chart(ctx).Doughnut(data,{animationEasing:"easeInOutQuad",animationSteps:50});

				},'json');
			})
			.delegate("#eventBtn","click",function(){
				$.post('../event.php','type=addsolution&solution='+$("#EventSolution").val()+'&eventid='+$("#eventId").val()+'&tel='+$("#Tel").text(),function(result){
				  if(result=="1")
				  {
					  $("#eventBtn").text("添加成功")
								.removeClass("btn-primary")
								.addClass("btn-success");
					  setTimeout(function(){
						  $("#eventClose").click();
						  $("#eventBtn").text("确认")
										.removeClass("btn-success")
										.addClass("btn-primary");
					  },1500);
					  $.post('../service/searchDetail.php','event_id='+$("#eventId").val()+'&tel='+$("#Tel").text(),function(ret){
						  $("#showEventPanel").html(ret);
						  showEventTable($("#pageNum").val());
					  },'html');
					  showChart("event",true);
				  }
			  });
		  })
		    .delegate("#guestContent","click",function(){
				$.post('../service/showGuestForm.php','tel='+$("#Tel").text(),function(ret){
					$("#guestFormWrapper").addClass("well").addClass("well-sm").html(ret);
				});
			})
			.delegate("#guestFormAdd","click",function(){
				$.post('../guest.php',$("#AddGuest").serialize()+'&tel='+$("#Tel").text(),function(ret){
					if(ret=="1")
					{
						$("#guestFormAdd").removeClass("btn-primary").addClass("btn-success").text("提交成功");
						setTimeout(function(){
							$("#AddGuest").remove();
							$("#guestFormWrapper").removeClass("well").removeClass("well-sm");
							getGuestTable();
						},1500);
					}		
				});
			})
			.delegate("#btnAddConfirmComment","click",function(){
				$.post('../sign.php','type=comment&id='+$("#iptAddConfirmComment").parent('td').siblings(":eq(1)").text()+'&content='+$("#iptAddConfirmComment").val(),function(ret){
					if(ret=="1")
					{
						$("#btnAddConfirmComment").removeClass("btn-primary").addClass("btn-success").text("成功");
						setTimeout(function(){
							$("#btnAddConfirmComment").removeClass("btn-success").addClass("btn-primary").text("提交");
							$("#iptAddConfirmComment").val("");
						},1500);
					}
				})
			})
  
  $("msgSendWrapper").delegate(".popoverMsg","click",function(){
	  $(this).popover('show');
  });
   
  showEventTable();
  showPersonTable();
  showSignTable();
  showChart("person",false);
  showChart("event",false);
  showConfirmTable();
  getGuestTable();
  showMsgTable();
});

function CheckPageEvent()
{
	var total = $("#pageEventBtn").parents('p').siblings(':eq(2)').text();
	total = total.match(/\/\d+/).toString();
	total = total.replace(/\//,"").toString();
	var reg = /^\d+$/;
	if(!data) var data = $("#pageEventInput").val();
	if(!data.match(reg))
	{
		$("#pageEventBtn").addClass("disabled").addClass("btn-danger");
		return false;
	}
	else 
	{
	    if(parseInt(data)>parseInt(total))
		{
		    $("#pageEventBtn").addClass("disabled").addClass("btn-danger");
			return false;
		}
		else
		{
		    $("#pageEventBtn").removeClass("disabled").removeClass("btn-danger");
			return true;
		}
	}
}

function CheckPagePerson()
{
	var total = $("#pagePersonBtn").parents('p').siblings(':eq(2)').text();
	total = total.match(/\/\d+/).toString();
	total = total.replace(/\//,"").toString();
	var reg = /^\d+$/;
	if(!data) var data = $("#pagePersonInput").val();
	if(!data.match(reg))
	{
		$("#pagePersonBtn").addClass("disabled").addClass("btn-danger");
		return false;
	}
	else 
	{
	    if(parseInt(data)>parseInt(total))
		{
		    $("#pagePersonBtn").addClass("disabled").addClass("btn-danger");
			return false;
		}
		else
		{
		    $("#pagePersonBtn").removeClass("disabled").removeClass("btn-danger");
			return true;
		}
	}
}

function CheckPageConfirm()
{
	var total = $("#pageConfirmBtn").parents('p').siblings(':eq(2)').text();
	total = total.match(/\/\d+/).toString();
	total = total.replace(/\//,"").toString();
	var reg = /^\d+$/;
	if(!data) var data = $("#pageConfirmInput").val();
	if(!data.match(reg))
	{
		$("#pageConfirmBtn").addClass("disabled").addClass("btn-danger");
		return false;
	}
	else 
	{
	    if(parseInt(data)>parseInt(total))
		{
		    $("#pageConfirmBtn").addClass("disabled").addClass("btn-danger");
			return false;
		}
		else
		{
		    $("#pageConfirmBtn").removeClass("disabled").removeClass("btn-danger");
			return true;
		}
	}
}

function CheckSignState()
{
	var user_tel = $("#Tel").text();
	var time_str = $("#hiddenTimeStr").val();
	if(time_str)
	{
		$.post('../sign.php',{"tel":user_tel,"time":time_str,"type":"ask"},function(data)
		{
			if(data=="0") $("#signBtn")
			                   .removeAttr("disabled")
							   .removeClass("btn-default")
							   .addClass("btn-primary")
							   .html('<span class="glyphicon glyphicon-plus-sign"></span> 请签到');
			else if(data=="1") $("#signBtn")
			                   .removeClass("btn-default")
							   .addClass("btn-warning")
							   .prop("disabled",true)
							   .html('<span class="glyphicon glyphicon-question-sign"></span> 待确认');
			else if(data=="2") $("#signBtn")
			                   .removeClass("btn-warning")
							   .addClass("btn-success")
							   .prop("disabled",true)
							   .html('<span class="glyphicon glyphicon-ok-sign"></span> 已签到');
			showSignTable();
		});
	}
	
}

function QuitLogin()
{
	window.location.href = "../login.php?action=1";
}

function TimeCountDown()
{
	  var now = new Date();
	  now.setTime(now.getTime()+dif);
	  var year = now.getFullYear();
	  var month = now.getMonth()+1;
	  var date = now.getDate();
	  var hour = now.getHours();
	  var minute = now.getMinutes();
	  var second = now.getSeconds();
	  var str = "";
	  if(hour == 8 &&( minute <= 59 && minute >= 30 ))
	  {
		  str = "AM";
		  $("#hiddenTimeStr").val(month+"."+date+" "+str);
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 08:59:59");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown")
		    .removeClass("alert-warning")
			.addClass("alert-success")
		    .html('<strong>正在签到！</strong> 距离签到结束还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
			isStart = 1;
	  }
	  else if(hour == 14 &&( minute <= 29 && minute >= 0 ))
	  {
		  str = "PM";
		  $("#hiddenTimeStr").val(month+"."+date+" "+str);
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 14:29:59");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown")
		    .removeClass("alert-warning")
			.addClass("alert-success")
		    .html('<strong>正在签到！</strong> 距离签到结束还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
			isStart = 1;
	  }
	  else if(hour == 19 &&( minute <= 29 && minute >= 0 ))
	  {
		  str = "NG";
		  $("#hiddenTimeStr").val(month+"."+date+" "+str);
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 19:29:59");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown")
		    .removeClass("alert-warning")
			.addClass("alert-success")
		    .html('<strong>正在签到！</strong> 距离签到结束还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
			isStart = 1;
	  }
	  else if((hour>=0&&hour<=7)||(hour==8&&(minute<=29&&minute>=0)))
	  {
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 08:30:00");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown").html('<strong>请注意！</strong> 现在不是签到时间段，距离下次签到开始还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
	  }
	  else if((hour>=20&&hour<=23)||(hour==19&&(minute>=30&&minute<=59)))
	  {
		  var endTime = Date.parse(year+"/"+month+"/"+(date+1)+" 08:30:00");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown").html('<strong>请注意！</strong> 现在不是签到时间段，距离下次签到开始还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
	  }
	  else if(hour>=9&&hour<=13)
	  {
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 14:00:00");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown").html('<strong>请注意！</strong> 现在不是签到时间段，距离下次签到开始还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
	  }
	  else if((hour>=15&&hour<=18)||(hour==14&&(minute>=30&&minute<=59)))
	  {
		  var endTime = Date.parse(year+"/"+month+"/"+date+" 19:00:00");
	      var startTime = now.getTime();
		  var hourTo = parseInt((endTime-startTime)/(3600*1000));
		  var minuteTo = parseInt((endTime-startTime)/(60*1000)) - 60*hourTo;
		  var secondTo = parseInt((endTime-startTime)/(1000)) - 3600*hourTo - 60*minuteTo;
		  $("#TimeCountDown").html('<strong>请注意！</strong> 现在不是签到时间段，距离下次签到开始还有'+hourTo+'小时'+minuteTo+'分钟'+secondTo+'秒钟。');
	  }
}

function QuerySign()
{
	var user_tel = $("#Tel").text();
	var time_str = $("#hiddenTimeStr").val();
	$.post("../sign.php",{"tel":user_tel, "time":time_str, "type":"sign"},function(data){
		if(data=="1")
		{
			clearInterval(timecount);
			$("#TimeCountDown")
			  .removeClass("alert-warning")
			  .addClass("alert-success")
			  .html("<strong>签到成功！</strong> 请等待上级确认。");
			$("#signBtn")
			  .removeClass("btn-primary")
			  .addClass("btn-warning")
			  .prop("disabled",true)
              .html('<span class="glyphicon glyphicon-question-sign"></span>待确认');
			showSignTable();
            CheckSignState();
			showChart("person");
		}
	});
}

function showEventModal(id)
{
	$("#eventId").val(id);
	$("#AddSolution").modal('show');
}

function showEventTable()
{
	var len= arguments.length; 
	if(len==1) var page = arguments[0];
	else var page = 1;
	$.post('../service/showEventTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		  $("#showEventWrapper").html(result);
	});
	getNewPrompt();
}

function showPersonTable()
{
	var len= arguments.length; 
	if(len==1) var page = arguments[0];
	else var page = 1;
	$.post('../service/showPersonTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		  $("#showPersonWrapper").html(result);
	  });
}

function showSignTable()
{
	$.post('../service/showSignTable.php','tel='+$("#Tel").text(),function(result){
		$("#showSignWrapper").html(result);
	});
}

function getNewPrompt()
{
	$.post('../service/getNewPrompt.php','tel='+$('#Tel').text(),function(json){
		if(json.eventNum) $("#eventPrompt").text(json.eventNum);
		else $("#eventPrompt").empty();
		if(json.confirmNum) $("#confirmPrompt").text(json.confirmNum);
		else $("#confirmPrompt").empty();
		if(json.msgNum) $(".msgNum").text(json.msgNum);
		else $(".msgNum").empty();
	},'json');
}

function showConfirmTable()
{
	var len = arguments.length;
	if(len==1) var page = arguments[0];
	else var page = 1
	$.post('../service/showConfirmTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		$("#showConfirmWrapper").html(result);
	});
    CheckSignState();
	showChart("person",true);
}

function getGuestTable()
{
	$.post('../service/getGuestTable.php','tel='+$("#Tel").text(),function(result){
		$("#guestTableWrapper").html(result);
	});
}

function showChart(type,update)
{
	if(type=="person")
	{
		$.post('../service/getChartData.php','type=person&tel='+$("#Tel").text(),function(result){
			if($("#staffChart").length)
			{
				if(!update) 
				{
					var ctx = $("#staffChart").get(0).getContext("2d");
					var data = [{value:result.data.nosign,color:"#F7464A",highlight:"#FF5A5E",label:"未签到"},
								{value:result.data.sign,color:"#DAB85F",highlight:"#E6C04E",label:"待确认"},
								{value:result.data.checked,color:"#46BFBD",highlight:"#5AD3D1",label:"已确认"}];
					Chart.defaults.global.responsive = false;
					staffChart = new Chart(ctx).Doughnut(data,{animationEasing:"easeInOutQuad",animationSteps:50});
				}
				else
				{
					staffChart.segments[0].value = result.data.nosign;
					staffChart.segments[1].value = result.data.sign;
					staffChart.segments[2].value = result.data.checked;
					staffChart.update();
				}

			}
			$("#staffChartLegend").html(result.html);
		},'json');
	}
	else if(type=="event")
	{
		$.post('../service/getChartData.php','type=event&tel='+$("#Tel").text(),function(result){
			if($("#eventChart").length)
			{
				if(!update)
				{
					var ctx = $("#eventChart").get(0).getContext("2d");
					var data = [{value:result.data.post,color:"#F4A81B",highlight:"#FAF43D",label:"已登记"},{value:result.data.process,color:"#F7464A",highlight:"#FF5A5E",label:"处理中"},{value:result.data.finish,color:"#6AB82C",highlight:"#65D97D",label:"已完结"}];
					Chart.defaults.global.responsive = false;
					eventChart = new Chart(ctx).Doughnut(data,{animationEasing:"easeInOutQuad",animationSteps:50});
				}
				else
				{
					eventChart.segments[0].value = result.data.post;
					eventChart.segments[1].value = result.data.process;
					eventChart.segments[2].value = result.data.finish;
					eventChart.update();
				}
			}
			$("#eventChartLegend").html(result.html);
		},'json');
	}
}

function showMsgTable()
{
	var len= arguments.length; 
	if(len==1) var page = arguments[0];
	else var page = 1;
	$.post('../service/showMsgTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		  $("#msgSendWrapper").html(result);
		  $(".popoverMsg").popover();
	});
	getNewPrompt();
}

function showSmsTable()
{
	var len= arguments.length; 
	if(len==1) var page = arguments[0];
	else var page = 1;
	$.post('../service/showSmsTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		  $("#smsSendWrapper").html(result);
		  $(".popoverSms").popover();
	});
	getNewPrompt();
}

function getMsgTable()
{
	var len= arguments.length; 
	if(len==1) var page = arguments[0];
	else var page = 1;	
	$.post('../service/getMsgTable.php','tel='+$("#Tel").text()+'&page='+page,function(result){
		  $("#showMsgModal .modal-body").html(result);
	});
}

function alreadyRead(uid,mid)
{
	$.post('../service/checkMsgRead.php','uid='+uid+'&mid='+mid,function(result){
		if(result=="1")
		{
			getMsgTable($("#msgPageNum").val());
			showMsgTable();			
		}
	});
}

function mobileShowMsg()
{
	$("#showMsgModal").modal('show');
}
