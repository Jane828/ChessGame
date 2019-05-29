;(function(root, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (root.iNotify = factory());
}(this, function(root, undefined) {	
    var repeatableEffects = ['flash', 'scroll'],
    	iNotify,
    	defaultNotification={
                title:"通知！",
                body:'您来了一条新消息'
            },iconURL = "";

    function iNotify(config){
        if(config) this.init(config);
    }
    iNotify.prototype = {
        init:function(config){
            if(!config) config = {}
            this.interval = config.interval || 200//响应时长
            this.effect = config.effect || 'flash' //效果
            this.title = config.title || document.title; //标题
            this.message = config.message || this.title; //原来的标题
            this.updateFavicon = config.updateFavicon || {
                id: "favicon",
                url:""
            }
            this.audio = config.audio || '';
            this.favicon = getFavicon(this.updateFavicon);
            
           
            this.cloneFavicon = this.favicon.cloneNode(true);
            this.notification = config.notification || defaultNotification;
            //初始化生成声音文件节点
            if(this.audio && this.audio.file) this.setURL(this.audio.file);
            return this;
        },
        render: function() {
            switch (this.effect) {
                case 'flash':
                    document.title = (this.title === document.title) ? this.message : this.title;
                    break;
                case 'scroll':
                    document.title = document.title.slice(1);
                    if (0 === document.title.length) {
                        document.title = this.message
                    }
                    break;
            }
        },
        setURL:function(url){
            if(url){
                if(this.audioElm) this.audioElm.remove();
                this.audioElm = createAudio(url);
                document.body.appendChild(this.audioElm);
            }
            return this
        },
        loopPlay:function(){
            this.setURL()
            this.audioElm.loop = true
            this.player()
            return this
        },
        stopPlay:function(){
            this.audioElm && (this.audioElm.loop = false,this.audioElm.pause())
            return this
        },
        //播放声音s
        player:function(){
            var adi = this.audio.file,source = null;
            if(!this.audio || !this.audio.file) return;
            if(!this.audioElm){
                this.audioElm = createAudio(this.audio.file);
                document.body.appendChild(this.audioElm)
            }
            this.audioElm.play();
            return this
        },
        notify:function(json){
            var nt = this.notification;      
            if(window.Notification){
            	iconURL=json.url;
                if(json) nt = jsonArguments(json,nt);
                else nt = defaultNotification;
                new Notification(nt.title, {
                    icon: iconURL,
                    body: nt.body,
                    tag:"cgner"
                });
            }

            return this
        },
        //是否许可弹框通知
        isPermission:function(){
            return window.Notification && Notification.permission === "granted" ? true : false ;
        },
        //设置标题
        setTitle:function(str){
            if(str === true){
                if ( 0 <= repeatableEffects.indexOf(this.effect)) return this.addTimer(); 
            }else if(str) {
                this.message = str,this.addTimer()
            }else{
                this.clearTimer(),
                this.title = this.title
            }
            return this
        },
        //设置时间间隔
        setInterval:function(num){
            if(num) this.interval = num,this.addTimer()
            return this
        },
        //设置网页Icon
        setFavicon:function(num){
            if(!num&&num!==0) return this.faviconClear();
            var oldicon = document.getElementById('new'+this.updateFavicon.id)
            if(this.favicon) this.favicon.remove();
            if(oldicon) oldicon.remove();
            changeFavicon(num,this.updateFavicon)
            return this
        },
        //s添加计数器
        addTimer:function(){
            this.clearTimer()
            if ( 0 <= repeatableEffects.indexOf(this.effect)) {
                this.timer = setInterval(this.render.bind(this), this.interval);
            }
            return this
        },
        //清除Icon
        faviconClear:function(){
            var newicon = document.getElementById('new'+this.updateFavicon.id)
                head = document.getElementsByTagName('head')[0],
                ficon = document.querySelectorAll('link[rel~=shortcut]')
            newicon&&newicon.remove()
            if(ficon.length>0) for (var i = 0; i < ficon.length; i++) {
                ficon[i].remove()
            };
            head.appendChild(this.cloneFavicon);
            iconURL = this.cloneFavicon.href;
            this.favicon = this.cloneFavicon;
            return this
        },
        //清除计数器
        clearTimer:function(){
            clearInterval(this.timer);
            document.title = this.title;
            return this
        }
    };
    // 获取 favicon
    function getFavicon(setting){
        var ic = document.querySelectorAll('link[rel~=shortcut]')[0];
        if(!ic) ic = changeFavicon('O',setting);
        return ic;
    }
    function createAudio(url){
        var audioElm = document.createElement('audio'),source;
        if(isArray(url) && url.length>0){
            for (var i = 0; i < url.length; i++) {
                source = document.createElement('source')
                source.src = url[i]
                source.type = 'audio/'+ getExtension(url[i])
                audioElm.appendChild(source)
            }
        }else{
            audioElm.src = url
        }
        return audioElm
    }
    function isArray(value) { return Object.prototype.toString.call(value) === '[object Array]';}
    function changeFavicon(num,settings){
 		head = document.getElementsByTagName('head')[0],
		linkTag = document.createElement('link'),
        //生成到
        linkTag.setAttribute('rel','shortcut icon');
        linkTag.setAttribute('type','image/x-icon');
        linkTag.setAttribute('id', 'new'+settings.id);
        linkTag.setAttribute('href',"");
        return head.appendChild(linkTag); 
    };
    //获取文件后缀
    function getExtension (file_name) { return file_name.match(/\.([^\.]+)$/)[1];}
    function jsonArguments (news,olds) {
        for (var a in olds) if(news[a]) olds[a]=news[a];
        return olds
    }
    //window.Notification
    if (window.Notification&&window.Notification.permission !== "granted"){
    	
		window.Notification.requestPermission();
		
	}
     	
    return iNotify;
}));


//myNotification(Object options,String title,Number time)功能桌面提醒功能,只支持高版本chrome和firefox
//参数：options 为对象{} 属性有body、icon、newUrl(跳转URL)
//参数：title 为标题部分, 有默认值
//参数：time 毫秒  默认：5秒

function myNotification(data,title,body,icon,time,callback){
	var options = {body:body,data:data,icon:icon};
  if(window.Notification && window.Notification.permission === "granted"){//支持Notification且开启桌面提醒
      var notify=new Notification(title,options);
      notify.onshow = function(){
          setTimeout(function(){
              notify.close();
          }, time);
      };
      notify.onclick = function(){
      	console.log(this.data);
		window.focus();
      	inotifyTest1();
      	notify.close();
      };
      notify.onclose = function(){
      }
      notify.onerror = function(){}
  }
}
var  onWindow=true;
var  msgNum=0;


window.onblur = function() {
    onWindow=false;
}
     
// window 每次获得焦点
window.onfocus = function() {
	 msgNum=0;
	 iN.clearTimer();
	 onWindow=true;
}
