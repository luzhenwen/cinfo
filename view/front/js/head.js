function addEvent(obj, type, fn){
	if(obj.addEventListener){
		obj.addEventListener(type, fn, false);
	}else if(obj.attachEvent){
		obj.attachEvent('on'+type, function(){
			fn.apply(obj);
		});
	}
}

function createXHR(){
	var xhr = null;
	if(window.XMLHttpRequest){
		xhr = new XMLHttpRequest();
	}else if(window.ActiveXObject){
		xhr = new ActiveXObject('Microsoft.XMLHTTP');
	}
	return xhr;
}