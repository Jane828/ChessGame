var tempFileData={
                message_id: "10",
                create_time: "1460287467",
                u_id: "2",
                avatar_url_small:"",
                content: "",
                msg_type: "1",
                group_id: "10",
                team_id: "5",
                from_message_id: "0",
                from_content: "",
                is_delete: "0",
                isUploaded:"0",
                progress:"0",
                file_dict: {
                    file_id: "",
                    file_name: "040=",
                    file_size: "31KB",
                    file_type: "1",
                    file_key: "",
                    file_url: "",
                    file_remark: "",
                    file_avatar_url: "http://oss.fexteam.com/0437f4e43c23f4757606270e7938a8ae.png",
                    file_comefrom: "5p2l6IeqWVnnmoTogYrlpKk=",
                    file_isdelete: "0"
                }
    }
var list=new Array();
var list1=new Array();
$(function() {
    var uploader = Qiniu.uploader({
        runtimes: 'html5,flash,html4',
        browse_button: 'pickfiles',
        container: 'container',
        drop_element: 'container',
        max_file_size: '100mb',
        flash_swf_url: 'js/plupload/Moxie.swf',
        dragdrop: true,
        chunk_size: '4mb',
        uptoken:$('#uptoken_url').val(),
        domain: $('#domain').val(),
        auto_start: true,
        init: {
            'FilesAdded': function(up, files) {
                $('#success').hide();           
   			for (var i = 0; i < files.length; i++) {
      			showPreview (files[i]);
      			list1[list1.length]=files[i].name;
      			//console.log(list1);
   			}
                plupload.each(files, function(file) {	
                		
  					tempFileData.isUploaded=0;
  					tempFileData.progress=0;
  					tempFileData.create_time=Math.ceil(new Date().getTime()/1000);
  					tempFileData.msg_type=4;
  					tempFileData.file_dict.file_name=file.name;
  					tempFileData.file_dict.file_size=file.size; 					
  					tempFileData.file_dict.file_id=file.id; 
  					list.push(tempFileData);
  				//	console.log(file);
  				//	console.log(list);
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    progress.setStatus("等待...");
                });
            },
            'BeforeUpload': function(up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                if (up.runtime === 'html5' && chunk_size) {
                    progress.setChunkProgess(chunk_size);
                }
            },
            'UploadProgress': function(up, file) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                progress.setProgress(file.percent + "%", up.total.bytesPerSec, chunk_size);

            },
            'UploadComplete': function() {
                $('#success').show();
            },
            'FileUploaded': function(up, file, info) {
                var progress = new FileProgress(file, 'fsUploadProgress');
                progress.setComplete(up, info);
            },
            'Error': function(up, err, errTip) {

                var progress = new FileProgress(err.file, 'fsUploadProgress');
                progress.setError();
                progress.setStatus(errTip);
            }
             ,
            'Key': function(up, file) {
            var index1=file.name.lastIndexOf(".");
      	        var index2=file.name.length;
				var postf=file.name.substring(index1,index2);//后缀名  
                var time= Date.parse(new Date());
                var a=Math.round(Math.random() * 9) ;   
                var b=Math.round(Math.random() * 9) ;   
                var c=Math.round(Math.random() * 9) ;   
                var d=Math.round(Math.random() * 9) ;   
              var key =hex_md5(time+''+a+''+b+''+c+''+d)+postf ;
                // do something with key

          //      console.log(key);
                 return key;
             }
        }
    });

    uploader.bind('FileUploaded', function() {
        console.log('hello man,a file is uploaded');
    });
    $('#container').on(
        'dragenter',
        function(e) {
            e.preventDefault();
            $('#container').addClass('draging');
            e.stopPropagation();
        }
    ).on('drop', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragleave', function(e) {
        e.preventDefault();
        $('#container').removeClass('draging');
        e.stopPropagation();
    }).on('dragover', function(e) {
        e.preventDefault();
        $('#container').addClass('draging');
        e.stopPropagation();
    });
    
    $('#show_code').on('click', function() {
        $('#myModal-code').modal();
        $('pre code').each(function(i, e) {
            hljs.highlightBlock(e);
        });
    });
    $('body').on('click', 'table button.btn', function() {
        $(this).parents('tr').next().toggle();
    });
    var getRotate = function(url) {
        if (!url) {
            return 0;
        }
        var arr = url.split('/');
        for (var i = 0, len = arr.length; i < len; i++) {
            if (arr[i] === 'rotate') {
                return parseInt(arr[i + 1], 10);
            }
        }
        return 0;
    };
    $('#myModal-img .modal-body-footer').find('a').on('click', function() {
        var img = $('#myModal-img').find('.modal-body img');
        var key = img.data('key');
        var oldUrl = img.attr('src');
        var originHeight = parseInt(img.data('h'), 10);
        var fopArr = [];
        var rotate = getRotate(oldUrl);
        if (!$(this).hasClass('no-disable-click')) {
            $(this).addClass('disabled').siblings().removeClass('disabled');
            if ($(this).data('imagemogr') !== 'no-rotate') {
                fopArr.push({
                    'fop': 'imageMogr2',
                    'auto-orient': true,
                    'strip': true,
                    'rotate': rotate,
                    'format': 'png'
                });
            }
        } else {
            $(this).siblings().removeClass('disabled');
            var imageMogr = $(this).data('imagemogr');
            if (imageMogr === 'left') {
                rotate = rotate - 90 < 0 ? rotate + 270 : rotate - 90;
            } else if (imageMogr === 'right') {
                rotate = rotate + 90 > 360 ? rotate - 270 : rotate + 90;
            }
            fopArr.push({
                'fop': 'imageMogr2',
                'auto-orient': true,
                'strip': true,
                'rotate': rotate,
                'format': 'png'
            });
        }

        $('#myModal-img .modal-body-footer').find('a.disabled').each(function() {

            var watermark = $(this).data('watermark');
            var imageView = $(this).data('imageview');
            var imageMogr = $(this).data('imagemogr');

            if (watermark) {
                fopArr.push({
                    fop: 'watermark',
                    mode: 1,
                    image: 'http://www.b1.qiniudn.com/images/logo-2.png',
                    dissolve: 100,
                    gravity: watermark,
                    dx: 100,
                    dy: 100
                });
            }

            if (imageView) {
                var height;
                switch (imageView) {
                    case 'large':
                        height = originHeight;
                        break;
                    case 'middle':
                        height = originHeight * 0.5;
                        break;
                    case 'small':
                        height = originHeight * 0.1;
                        break;
                    default:
                        height = originHeight;
                        break;
                }
                fopArr.push({
                    fop: 'imageView2',
                    mode: 3,
                    h: parseInt(height, 10),
                    q: 100,
                    format: 'png'
                });
            }

            if (imageMogr === 'no-rotate') {
                fopArr.push({
                    'fop': 'imageMogr2',
                    'auto-orient': true,
                    'strip': true,
                    'rotate': 0,
                    'format': 'png'
                });
            }
        });

        var newUrl = Qiniu.pipeline(fopArr, key);

        var newImg = new Image();
        img.attr('src', 'loading.gif');
        newImg.onload = function() {
            img.attr('src', newUrl);
            img.parent('a').attr('href', newUrl);
        };
        newImg.src = newUrl;
        return false;
    });
});
function showPreview (file) {
	console.log(1);
    var image = new Image();
    var preloader = new mOxie.Image();
    preloader.onload = function() {
    	console.log(2);
        preloader.downsize( 300, 300 );
        image.setAttribute( "src", preloader.getAsDataURL() );
        $('#preview').append(image);
    };
    preloader.load( file.getSource() );
}