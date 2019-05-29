
function FileProgress(file, targetID) {
    this.FileProgressID = file.id;
    this.file = file;
    this.opacity = 100;
    this.height = 0;
    this.FileProgressWrapper = $('#' + this.FileProgressID);
    if (!this.FileProgressWrapper.length) {
         var Wrappeer= $("<div/>"); 
        Wrappeer.attr('id', this.FileProgressID).addClass('progressContainer').text(this.FileProgressID);
        var fileName = $("<div/>");
        fileName.addClass('fileName').text(file.name);    
        var fileSize = $("<div/>");    
        fileSize.addClass('fileSize').text(file.size);           
        var fileUrl = $("<div/>");    
       // fileUrl.addClass('fileUrl').text(this.FileProgressWrapper.content.URL);        

  //      $('#' + targetID).append(Wrappeer);
 //       $('#' + targetID).append(fileName);
  //      $('#' + targetID).append(fileSize);
  //      $('#' + targetID).append(fileUrl);
    } else {
        this.reset();
    }
    this.setTimer(null);
}
FileProgress.prototype.setTimer = function(timer) {
    this.FileProgressWrapper.FP_TIMER = timer;
};
FileProgress.prototype.getTimer = function(timer) {
    return this.FileProgressWrapper.FP_TIMER || null;
};
FileProgress.prototype.reset = function() {
};
FileProgress.prototype.setChunkProgess = function(chunk_size) {

};
FileProgress.prototype.setProgress = function(percentage, speed, chunk_size) {
    this.FileProgressWrapper.attr('class', "progressContainer green");
    var file = this.file;
    var uploaded = file.loaded;
    var size = plupload.formatSize(uploaded).toUpperCase();
    var formatSpeed = plupload.formatSize(speed).toUpperCase();
    var progressbar = this.FileProgressWrapper.find('td .progress').find('.progress-bar-info');
    this.FileProgressWrapper.find('.status').text("已上传: " + size + " 上传速度： " + formatSpeed + "/s");
    var width=160*file.loaded/file.size+"px";
  
    $('#bar1').css("width",width);
};
FileProgress.prototype.setComplete = function(up, info) {

    var res = $.parseJSON(info);
    var url;
    
    
     if (res.url) {
        url = res.url;
       } else {
       var domain = up.getOption('domain');
      var http='http://';
       url = http+domain + encodeURI(res.key);
  }
      var isImage = function(url) {
        var res, suffix = "";
        var imageSuffixes = ["png", "jpg", "jpeg", "gif", "bmp"];
        var suffixMatch = /\.([a-zA-Z0-9]+)(\?|\@|$)/;

        if (!url || !suffixMatch.test(url)) {
            return false;
        }
        res = suffixMatch.exec(url);
        suffix = res[1].toLowerCase();
        for (var i = 0, l = imageSuffixes.length; i < l; i++) {
            if (suffix === imageSuffixes[i]) {
                return true;
            }
        }
        return false;
    };

    var isImg = isImage(url);
    
    if(isImg){	
 		 $("#img").attr('src',url);
 	//	 console.log(encodeURI(res.key));
   		 $("#image").html(encodeURI(res.key));
  		 $("#img").show(); 
  		var fileURL = $("<div/>");
        fileURL.addClass('fileURL').text(url);    
  		 $('#fsUploadProgress').append(fileURL);
  	     
  		 
        }
  
   else
   alert("图片格式不对");
                 
};
FileProgress.prototype.setError = function() {

};
FileProgress.prototype.setCancelled = function(manual) {
    var progressContainer = 'progressContainer';
    if (!manual) {
        progressContainer += ' red';
    }
    this.FileProgressWrapper.attr('class', progressContainer);
    this.FileProgressWrapper.find('td .progress .progress-bar-info').css('width', 0);
};
FileProgress.prototype.setStatus = function(status, isUploading) {
    if (!isUploading) {
        this.FileProgressWrapper.find('.status').text(status).attr('class', 'status text-left');
    }
};
FileProgress.prototype.appear = function() {
    if (this.getTimer() !== null) {
        clearTimeout(this.getTimer());
        this.setTimer(null);
    }

    if (this.FileProgressWrapper[0].filters) {
        try {
            this.FileProgressWrapper[0].filters.item("DXImageTransform.Microsoft.Alpha").opacity = 100;
        } catch (e) {
            // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
            this.FileProgressWrapper.css('filter', "progid:DXImageTransform.Microsoft.Alpha(opacity=100)");
        }
    } else {
        this.FileProgressWrapper.css('opacity', 1);
    }

    this.opacity = 100;
    this.FileProgressWrapper.show();

};