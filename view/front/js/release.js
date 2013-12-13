$(document).ready(function(){
	var bIsFocusCheckCode = false;
	var upToken = document.getElementsByName('uptoken')[0].value;
	var settings_object = {
		upload_url : "http://up.qiniu.com",
		flash_url : "http://www.fusui.cc/view/front/js/swfupload.swf",
		file_post_name : "file",
		use_query_string : false,
		requeue_on_error : false,
		http_success : [201, 202],
		assume_success_timeout : 0,
		file_types : "*.jpg;*.bmp",
		file_types_description: "图片类型",
		file_size_limit : "2048",
		file_queue_limit : 5,
		custom_settings :{
			stopUpload : false,
			fileLimit : 0
		},
		debug : false,
		prevent_swf_caching : false,
		preserve_relative_urls : false,
		button_placeholder_id : "upbtn",
		button_width : 95,
		button_height : 30,
		button_image_url : "http://www.fusui.cc/view/front/image/upload_pic.png",
		button_text_left_padding : 3,
		button_text_top_padding : 2,
		button_action : SWFUpload.BUTTON_ACTION.SELECT_FILES,
		button_disabled : false,
		button_cursor : SWFUpload.CURSOR.HAND,
		button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
		file_dialog_complete_handler : dialogComplete,
		file_queue_error_handler : queueErr,
		upload_success_handler : uploadSuccess,
		upload_start_handler : uploadStart,
		upload_progress_handler : upProgress,
		upload_complete_handler : uploadComplete,
		upload_error_handler : uploadErr
	};
	var swfup = new SWFUpload(settings_object);

	function uploadStart(oFile){
		//检查上传了多少个文件，如果大于5就停止上传
		var sCurrPic = 'pic' + (new Date().valueOf()) + Math.round(Math.random()*10000) + '.' + getExt(oFile.name);
		this.customSettings.currPic = sCurrPic;
		this.setPostParams({
			'token' : upToken,
			'key' : sCurrPic
		});
		if(this.customSettings.fileLimit >= 5){
			this.cancelQueue();
			return false;
		}
		var oUl = document.getElementById('pic_list');
		var oNewLi = document.createElement('li');
		oNewLi.innerHTML = '<div id="' + oFile.id + 'div"><img src="http://www.fusui.cc/view/front/image/picloading.gif" /></div>';
		oNewLi.innerHTML += '<span id="' + oFile.id + 'span" class="filedetail"></span>';
		oUl.appendChild(oNewLi);
	}

	function uploadErr(file, code, msg){

	}

	//一个文件完成后继续上传下一个文件
	function uploadComplete(oFile){
		//检查上传了多少个文件，如果大于5就不做任何动作
		//如果这里不检测的话就会死循环
		if(this.customSettings.fileLimit >= 5){
			return;
		}
		var xhr = createXHR();
		var res = null;
		xhr.open('POST','/getqiniufile.php?'+Math.random(),true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				res = eval('('+xhr.responseText+')');
				document.getElementById(oFile.id+'div').getElementsByTagName('img')[0].src = res.keyurl + '&imageView/1/w/100/h/80';
			}
		};
		xhr.send('fileName='+this.customSettings.currPic);
		//计数器，每成功上传一个文件就+1
		this.customSettings.fileLimit += 1;
		this.startUpload();
	}

	function upProgress(oFile, nUploaded, nTotal){
		var oArea = document.getElementById(oFile.id+'span');
		oArea.innerHTML = Math.round(nUploaded/nTotal * 100) + '%';
	}

	function uploadSuccess(oFile, sMsg){
		var oArea = document.getElementById(oFile.id+'span');
		var oRet = eval('('+sMsg+')');
		if(oRet.statu == 1){
			oArea.innerHTML = '上传成功<span id="'+oFile.id+'del" class="filedel">删除</span>';
			//创建一个input放置图片的地址
			var oNewInput = createInput('filedata');
			oNewInput.setAttribute('picaddress',this.customSettings.currPic);
			oNewInput.setAttribute('type','hidden');
			oArea.parentNode.appendChild(oNewInput);

			$(oArea).parent().append(oNewInput);
			//每上传一个图片就把上传过的图片地址储存起来，便于表单提交过后后台删除不用的
			sUploadedPic += this.customSettings.currPic + '|';
			//$('#uploadedpic').val(this.value + '|' + swfup.customSettings.currPic);
		}
	}

	function queueErr(oFile,nCode,msg){
		if(nCode == -100){
			alert('你选择了太多的文件');
		}else if(nCode == -110){
			alert(oFile.name + '过大，单个文件不能大于2M');
		}else if(nCode == -120){
			alert(oFile.name + '大小不能为0');
		}else if(nCode == -130){
			alert(oFile.name + '只能上传jpg，bmp格式的文件');
		}
	}

	function dialogComplete(nSelNum,nQueueNum){
		if(nQueueNum >= 1){
			this.startUpload();
		}
	}

	$('#pic_list').live('mouseover click',function(ev){
		if(ev.type == 'mouseover' && $(ev.target).attr('class') == 'filedel'){
			$(ev.target).css('textDecoration', 'underline');
			$(ev.target).mouseout(function(){
				$(this).css('textDecoration', 'none');
			});
		}

		if(ev.type == 'click' && $(ev.target).attr('class') == 'filedel'){
			$($(ev.target).parent().parent()).remove();
			swfup.customSettings.fileLimit -= 1;
		}
	});

	//输入框获得焦点时改变样式
	$('#title_input, #content_input, #contact_input, #delkey_input, #chkcode_input').focus(function(){
		this.style.boxShadow = '0px 0px 1px #C64038';
		this.style.background = '#fff';
		this.style.border = '1px solid #C98381';
		$(this).next().css('display', 'inline');
	});

	//焦点离开输入框时取消样式
	$('#title_input, #content_input, #contact_input, #delkey_input, #chkcode_input').blur(function(){
		this.style.boxShadow = '';
		this.style.background = '';
	});

	//栏目选择如果值不正确的话
	$('#category_input').change(function(){
		if(this.value==0){
			$('#category_msg').css('display','inline');
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff', border: '2px solid red'});
			bCategoryPass = false;
		}else{
			$('#category_msg').css('display','none');
			$(this).css({boxShadow : '', background: '', border: ''});
			bCategoryPass = true;
		}
	});

	//标题输入框失去焦点时验证
	$('#title_input').blur(function(){
		if(this.value.length < 2 || this.value.length > 9){
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff', border: '2px solid red'});
			bTitlePass = false;
		}else{
			$(this).css({boxShadow: '', background: '', border: '1px solid #C98381'});
			$(this).next().css('display', 'none');
			bTitlePass = true;
		}
	});

	//信息内容输入框失去焦点时验证
	$('#content_input').blur(function(){
		if(this.value.length < 10 || this.value.length > 800 || qqnumber_pattern.test(this.value) || phonenumber_pattern.test(this.value)){
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff', border: '2px solid red'});
			bContentPass = false;
		}else{
			$('#content_msg').css('display', 'none');
			$(this).css({boxShadow: '', background: '', border: '1px solid #C98381'});
			bContentPass = true;
		}
	});

	//信息删除时间选择框改变时验证
	$('#deltime_input').change(function(){
		if(!isNaN(this.value) || this.value >= 604800){
			$('#deltime_msg').css('display', 'none');
			$(this).css({boxShadow: '', background: ''});
			bDeltimePass = true;
		}else{
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff'});
			bDeltimePass = false;
		}
	});

	if(!isNaN($('#deltime_input').val()) || $('#deltime_input').val() >= 604800){
		bDeltimePass = true;
	}

	//联系电话输入框失去焦点时验证
	$('#contact_input').blur(function(){
		if(!phonenumber_pattern.test(this.value)){
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff', border: '2px solid red'});
			bContactPass = false;
		}else{
			$(this).css({boxShadow: '', background: '', border:'1px solid #C98381'});
			$('#contact_msg').css('display', 'none');
			bContactPass = true;
		}
	});

	//删除密码输入框失去焦点时验证
	$('#delkey_input').blur(function(){
		if(this.value.length < 4){
			$(this).css({boxShadow: '0px 0px 5px red', background: '#fff', border: '2px solid red'});
			bDelkeyPass = false;
		}else{
			$(this).css({boxShadow: '', background: '', border:'1px solid #C98381'});
			$('#delkey_msg').css('display', 'none');
			bDelkeyPass = true;
		}
	});

	//验证码获得焦点时改变验证码图片的链接
	$('#chkcode_input').focus(function(){
		if(bIsFocusCheckCode == false){
			$(this).prevAll('img').attr('src', '/checkcode.php?change='+Math.random());
			bIsFocusCheckCode = true;
		}
		if(this.value == '请输入验证码'){
			this.value = '';
			$(this).css('color', 'black');
		}
	});

	//点击验证码图片时图片发生改变
	$('#chkcode_input').prevAll('img').click(function(){
		if(bIsFocusCheckCode == false){
			return;
		}
		$(this).attr('src', '/checkcode.php?change='+Math.random());
	});

	//鼠标移动到提交按钮的时候改变样式
	$('#release_submit').hover(function(){
		$(this).css('background', '#c98381');
	},function(){
		$(this).css('background', '');
	});

	//AJAX提交
	$('#release_submit').click(function(){
		if(!bCategoryPass){
			window.scrollTo(0,$('#category_input').offset().top - 50);
			$('#category_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red'});
			$('#category_input').next().css('display', 'inline');
			return;
		}

		if(!bTitlePass){
			window.scrollTo(0,$('#title_input').offset().top - 50);
			$('#title_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red', background: '#fff'});
			$('#title_input').next().css('display', 'inline');
			return;
		}

		if(!bContentPass){
			window.scrollTo(0,$('#content_input').offset().top - 50);
			$('#content_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red', background: '#fff'});
			$('#content_input').next().css('display', 'inline');
			return;
		}

		if(!bContactPass){
			$('#contact_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red', background: '#fff'});
			$('#contact_input').next().css('display', 'inline');
			return;
		}

		if(!bDeltimePass){
			$('deltime_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red', background: '#fff'});
			$('#deltime_input').next().css('display', 'inline');
			return;
		}

		if(!bDelkeyPass){
			$('#delkey_input').css({boxShadow: '0px 0px 5px red', border: '2px solid red', background: '#fff'});
			$('#delkey_input').next().css('display', 'inline');
			return;
		}

		var xhr = createXHR();
		xhr.open('POST','/release.php?math='+Math.random(),true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				bIsSubmit = true;
				var result = eval('('+xhr.responseText+')');
				var reMsg = $('#response_msg');
				if(result.statu == 1){
					reMsg.find('p').html('信息发布成功，3秒后转回首页');
					reMsg.find('p').css('color', 'green');
					setTimeout(function(){
						window.location.href = '/';
					},3000);
				}else if(result.statu == 0){

					reMsg.find('p').html(result.text);
					reMsg.find('p').css('color', 'red');
				}
				$('#srceen').css({display: 'block',height: $(document).height(),width: $(document).width(), opacity: '0'});
				$('#srceen').animate({opacity: '0.3'}, 'fast');
				var iTop = ($(window).height() - reMsg.height()) / 2 + (document.body.scrollTop || document.documentElement.scrollTop);
				var iLeft = ($(window).width() - reMsg.width()) / 2;
				$('#response_msg').css({display: 'block', position: 'absolute', top: iTop -30, left: iLeft, opacity: '0'});
				$('#response_msg').animate({top: iTop, opacity: '1'}, 'fast');

				$('#close').click(function(){
					$('#response_msg').animate({top: iTop-30, opacity: '0'}, 'fast');
					$('#srceen').animate({opacity: '0'}, 'fast');
					setTimeout(function(){
						$('#response_msg').css('display', 'none');
						$('#srceen').css('display', 'none');
					},200);
				});
			}
		};

		var sTitle = $('#title_input').val();
		var sContent = $('#content_input').val();
		var sDel_key = $('#delkey_input').val();

		var data = {
			'cat_id' : $('#category_input').val(),
			'title' : encodeURIComponent($('#title_input').val()),
			'content' : encodeURIComponent($('#content_input').val()),
			'contact' : $('#contact_input').val(),
			'del_time' : $('#deltime_input').val(),
			'del_key' : encodeURIComponent($('#delkey_input').val()),
			'chkcode' : $('#chkcode_input').val(),
			'unid' : $("input[name='unid']").val()
		};

		sUploadedPic = sUploadedPic.substring(0,sUploadedPic.lastIndexOf('|'));
		if(sUploadedPic){
			data['uploaded_pic'] = sUploadedPic;
		}

		var oPicAddress = $('input[name="filedata"]');
		var sPicAddress = '';
		for(var i = 0, len = oPicAddress.length; i < len; i++){
			sPicAddress += $(oPicAddress[i]).attr('picaddress') + '|';
		};
		sPicAddress = sPicAddress.substring(0,sPicAddress.lastIndexOf('|'));
		if(sPicAddress){
			data['pic_address'] = sPicAddress;
		}
		xhr.send(buildQuery(data));
	});
});

//离开页面时如果没有提交成功就发送请求删除图片
window.onbeforeunload = function(){
	if(bIsSubmit == true){
		return;
	}
	if(!sUploadedPic){
		return;
	}
	//var sUploadedPic = $('#uploadedpic').val();
	sUploadedPic = sUploadedPic.substring(0,sUploadedPic.lastIndexOf('|'));
	var xhr = createXHR();
	xhr.open('POST', '/private/delqiniufile.php', false);
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	var queryData = {
		'filename': sUploadedPic,
		'unid': $('input[name="unid"]').val()
	};
	xhr.send(buildQuery(queryData));
};


//是否点击了提交按钮，用在离开页面的时候删除已经上传但是并不需要的图片
var bIsSubmit = false;
var tag_pattern = /((<.*script.*>.*<.*\/.*script.*>)|(<.*div.*>.*<.*\/.*div.*>)|(<.*img.*>))/i;
var phonenumber_pattern = /^(1|0)(\d{10})$/i;
var qqnumber_pattern = /((qq|扣扣).{0,5}\d{6,})/i;
//ajax提交时验证字段
var bCategoryPass = false;
var bTitlePass = false;
var bContentPass = false;
var bContactPass = false;
var bDeltimePass = false;
var bDelkeyPass = false;

var sUploadedPic = '';

function getExt(str){
	var tmp = str.split('.');
	return tmp[tmp.length-1];
}

function buildQuery(json){
	var query = '';
	for(i in json){
		query += i+'='+json[i]+'&';
	}
	query = query.substring(0,query.lastIndexOf('&'))
	return query;
}

function createInput(name){
	var ie =!-[1,];
	if(ie){
		return document.createElement('<input name="'+name+'"/>');
	}else{
		var tmp = document.createElement('input');
		tmp.setAttribute('name',name);
		return tmp;
	}
}