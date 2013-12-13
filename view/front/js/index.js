window.onload = function(){
    var ie =!-[1,];
    var page = document.getElementById('page');
    var page_a = page.getElementsByTagName('a');
    var oMain = document.getElementById('main');
    var oBox = oMain.getElementsByTagName('div');

    for(var b = 0 ,len = oBox.length; b < len ; b++){
        if(oBox[b].className == 'box'){
            oBox[b].onmouseover = function(){
                this.style.background = '#fff';
                this.style.cursor = 'pointer';
            }
            oBox[b].onmouseout = function(){
                this.style.background = '#fcfcfc';
            }
        }
    }


    for(var i = 0 , len = page_a.length ; i < len ; i++){
        if(page_a[i].className == 'curr'){
            continue;
        }
        page_a[i].onmouseover = function(){
            this.style.background = '#f18c19';
            this.children[0].style.color = '#fff';
        }
        page_a[i].onmouseout = function(){
            this.style.background = 'none';
            this.children[0].style.color = '#000';
        }
    }


};
