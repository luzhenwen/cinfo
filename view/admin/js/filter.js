function createXHR(){
	var xhr = null;
	if(window.XMLHttpRequest){
		xhr = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		xhr = new ActiveXObject('Microsoft.XMLHTTP');
	}
	return xhr;
}

function addEvent(obj, type, fn){
	if(obj.addEventListener){
		obj.addEventListener(type, fn, false);
	}else if(obj.attachEvent){
		obj.attachEvent('on'+type, function(){
			fn.apply(obj);
		});
	}
}


addEvent(window,'load',function(){
	var oBtnNew = document.getElementsByName('newbtn')[0];
	var tbody = document.getElementsByTagName('tbody')[0];

	//事件委托
	addEvent(tbody, 'click', function(ev){
		var ev = ev || window.event;
		var srcElement = ev.target || ev.srcElement;

		//点击编辑按钮触发事件
		if(srcElement.name == 'edit'){
			var srcId = srcElement.getAttribute('filter_id');
			var srcVal = document.getElementsByName('val'+srcId)[0];
			//alert(srcVal);return;
			var xhr = createXHR();
			xhr.open('POST', 'filter.php?math='+Math.random(),true);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					if(xhr.status == 200){
						if(xhr.responseText == 1){
							srcElement.style.backgroundColor = 'green';
						}else{
							alert(xhr.responseText);
						}
					}else{
						alert('服务器返回状态码：'+xhr.status);
					}
				}
			};
			xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
			xhr.send('act=edit&filter_id='+srcId+'&filter_word='+srcVal.value);
		}

		//删除按钮的事件
		if(srcElement.name == 'delete'){
			var srcId = srcElement.getAttribute('filter_id');
			//var srcVal = document.getElementsByName('val'+srcId)[0];
			var xhr = createXHR();
			xhr.open('POST', 'filter.php?math='+Math.random(), true);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					if(xhr.status == 200){
						if(xhr.responseText == 1){
							srcElement.style.backgroundColor = 'red';
						}
					}
				}
			};
			xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
			xhr.send('act=del&filter_id='+srcId);
		}
	});

	addEvent(oBtnNew, 'click', function(){
		var val = document.getElementsByName('newval')[0];
		if(val.value == ''){
			return;
		}


		var new_tr = document.createElement('tr');

		//创建第一个td，用来存放新添加的关键词
		var new_td1 = document.createElement('td');
		var new_inputarea = document.createElement('input');
		new_inputarea.setAttribute('type', 'text');
		new_inputarea.setAttribute('value', val.value);
		new_td1.appendChild(new_inputarea);
		new_tr.appendChild(new_td1);

		//创建第二个td，用来存放操作按钮
		var new_td2 = document.createElement('td');

		//创建编辑按钮
		var new_inputedit = document.createElement('input');
		new_inputedit.setAttribute('type', 'button');
		new_inputedit.setAttribute('name', 'edit');
		new_inputedit.setAttribute('value', '编辑');
		new_td2.appendChild(new_inputedit);

		//创建删除按钮
		var new_inputdel = document.createElement('input');
		new_inputdel.setAttribute('type', 'button');
		new_inputdel.setAttribute('name', 'delete')
		new_inputdel.setAttribute('value', '删除' );
		new_td2.appendChild(new_inputdel);
		new_tr.appendChild(new_td2);

		tbody.insertBefore(new_tr,tbody.children[tbody.children.length-1]);

		var xhr = createXHR();
		xhr.open('POST', 'filter.php?math='+Math.random(),true);
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4){
				if(xhr.status == 200){
					new_tr.style.backgroundColor = 'green';
					new_inputdel.setAttribute('filter_id', xhr.responseText);
					new_inputedit.setAttribute('filter_id', xhr.responseText);
					new_inputarea.setAttribute('name', 'val'+xhr.responseText);
				}else{
					new_tr.style.backgroundColor = 'red';
				}
			}
		};
		xhr.setRequestHeader('content-type','application/x-www-form-urlencoded');
		xhr.send('act=add&filter_word='+val.value);
		val.value = '';
	});
});