$(document).ready(function(){
    var oOperate = $('#operate');
    var oDel = $('#delete');
    var oScreen = $('#screen');
    var oCancel = $('#cancel');
    var oDel_sub = $('#del_sub');

    $(oOperate).mouseover(function(){
        $(this).css({backgroundColor: '#e5e5e5', border: '1px solid #d1d1d1', borderRadius: '3px'});
        for(var i=0 ; i < this.children[0].children.length;i++){
            this.children[0].children[i].className = 'operate_display';
        }
    });
    $(oOperate).mouseout(function(){
        $(this).css({backgroundColor: '', border: '', borderRadius: ''});
        for(var i=0 ; i < this.children[0].children.length;i++){
            this.children[0].children[i].className = '';
        }
        this.children[0].children[0].className = 'operate_display';
    });

    //弹出删除信息的按钮
    //var tmp = $(oOperate).find('ul li')[1];
    $($(oOperate).find('ul li')[1]).click(function(){
        //打开遮罩
        $(oScreen).css({display: 'block', opacity: '0'});
        //判断遮罩的大小
        $(oScreen).width($(document).width());
        $(oScreen).height($(document).height());
        $(oScreen).animate({opacity: '0.3'});
        //打开删除输入框
        $(oDel).css({display: 'block', opacity: '0'});
        var left = ($(window).width() - $(oDel).width()) / 2;
        var top = ($(window).height() - $(oDel).height()) / 2;
        //设施输入框的位置居中
        $(oDel).css({top: top - 10, left: left});
        $(oDel).animate({top: top, opacity: '1'}, 'fast');
        //设置输入框的状态，用于重置窗口的大小
        oDel.flag = true;
    });

    $(window).resize(function(){
        if(oDel.flag){
            $(oScreen).width($(document).width());
            $(oScreen).height($(document).height());
            var left = ($(window).width() - $(oDel).width()) / 2;
            var top = ($(window).height() - $(oDel).height()) / 2;
            $(oDel).offset({top: top, left: left});
        }
    });

    $(oCancel).click(function(){
        if(oDel.flag == true){
            $(oScreen).css('display', 'none');
            $(oDel).css('display', 'none');
            oDel.flag = false;
        }
    });

    $(oDel_sub).click(function(){
        if(confirm('确定要删除这条信息信息？')){
            var info_id = this.getAttribute('iid');
            var oDel_key = $('#del_key');
            var xhr = createXHR();
            if(xhr == null){
                alert('你的浏览器不支持Ajax，请更换浏览器');
                return;
            }
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    if(xhr.responseText == 1){
                        var msg = $('#del_msg');
                        $('#input_area').css('display', 'none');
                        $('#del_sub').css('display', 'none');
                        msg.html('信息删除成功，3秒后跳转至首页');
                        msg.css('display', 'block');
                        setTimeout(function(){
                            window.location = '/';
                        },3000);
                    }else if(xhr.responseText == 2){
                        alert('信息传输错误');
                    }else if(xhr.responseText == 3){
                        alert('请输入密码');
                    }else if(xhr.responseText == 0){
                        alert('密码错误');
                    }
                }
            };
            xhr.open('POST','/info_edit.php',true);
            xhr.setRequestHeader('Content-type','application/x-www-form-urlencoded');
            xhr.send('act=del&iid='+info_id+'&del_key='+oDel_key.val());
            oDel_key.value = '';
        }
    });
});