$(document).ready(function(){
	$('#myform').submit(function(){
		return false;
	});
	var xhr = new XMLHttpRequest();
	$(document).bind('keydown click',function(ev){
		if((ev.which == 13 && ev.ctrlKey) || (ev.target.type == 'submit' && ev.target.value == '提交')){
			xhr.open('POST','/imanage/info_Edit_Act.php',true);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4 && xhr.status == 200){
					var response_json = eval('('+xhr.responseText+')');
					if(response_json.status == 0){
						$('#view').css('border', '1px solid red');
						$('#view_content').html(response_json.text);
					}else if(response_json.status == 1){
						$('#view').css('border', '2px solid lawngreen');
						//把添加成功的信息放到右边好查看
						$('#view_title').html($('input[name="title"]').val());
						$('#view_content').html('<pre>' + $('textarea[name="content"]').val() + '</pre>');
						$('#view_contact').html($('input[name="contact"]').val());
						$('#view_category').html($('input[name="cat_id"]:checked').val());

						$('#myform')[0].reset();
						$('input[name="title"]').focus();
					}
				}else{
					$('#view').css('border', '2px solid darkred');
					$('#view_content').html(xhr.status);
				}
			};
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			var data = {
				'act' : 'adminadd',
				'cat_id' : $('input[name="cat_id"]:checked').val(),
				'title' : encodeURIComponent($('input[name="title"]').val()),
				'content' : encodeURIComponent($('textarea[name="content"]').val()),
				'contact' : $('input[name="contact"]').val()
			};
			xhr.send(buildQuery(data));
		}else if(ev.which == 13 && ev.target.tagName != 'TEXTAREA'){
			return false;
		}
	});
});

function buildQuery(json){
	var query = '';
	for(i in json){
		query += i+'='+json[i]+'&';
	}
	query = query.substring(0,query.lastIndexOf('&'))
	return query;
}
