<!DOCTYPE html>
<html >
<head>
<meta charset="utf-8">
<title>登录</title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $base_url;?>files/images/zhtC.ico" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>files/css/main.css" />
<script src="<?php echo $base_url;?>files/js/angular.min.js" ></script>
<script src="<?php echo $base_url;?>files/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo $base_url;?>files/js/jquery.sortable.js"></script>
<script src="<?php echo $base_url;?>files/js/plupload/plupload.full.min.js"></script>
<script src="<?php echo $base_url;?>files/js/qiniu.js"></script>
<script type="text/javascript" src="<?php echo $base_url;?>files/js/md5.js"></script>
<script >
$(function(){
	setTimeout(function(){
		$("#account").val("");
		$("#password").val("");
	},10)
	
	
	$("#login").click(function(){
		var account=$("#account").val();
		var pwd=$("#password").val();
		var test1=chkvalue1();
		var test2=chkvalue2();
		var test3 = phoneBlur();
		var test4 = captchaBlur();
		if(test1*test2*test3*test4==0 ){
			return 0
		}
		else{
			$.post('<?php echo $base_url;?>admin/loginOpt',{
				"account":$("#account").val(),"pwd":hex_md5($("#password").val()),
				"phone": $('#phone').val(), "auth_code": $('#captcha').val()
			},function(data)
			{
				var obj = eval('(' + data + ')');	
				if(obj.result==0){
					window.location.href="<?php echo $base_url;?>admin/index";
				}
				else if(obj.data.err_type==-1){
					$("#account").parent().css("border-color","#ac1f1f");
					$(".accountIntro").show();
					$(".accountIntro a").html(obj.result_message);
				}
				else if(obj.data.err_type==-2){
					$("#password").parent().css("border-color","#ac1f1f");
					$(".passwordIntro").show();
					$(".passwordIntro a").html(obj.result_message); 
				}else if(obj.data.err_type == -3){
					$("#phone").parent().css("border-color","#ac1f1f");
					$(".phone-info").show();
					$(".phone-info a").html(obj.result_message); 
				}else if(obj.data.err_type == -4){
					$("#captcha").parent().css("border-color","#ac1f1f");
					$(".captcha-info").show();
					$(".captcha-info a").html(obj.result_message); 
				}
			});	
		}
	})

	$("#getSMS").click(function(){
		var account=$("#account").val();
	
		if($("#account").val()=="" ){
			return 0
		}
		else{
			$.post('<?php echo $base_url;?>admin/getMobileSmsForLogin',{
				"account":$("#account").val()
			},function(data)
			{
				console.log(data);
			});	
		}
	})
	$('#captchaBtn').click( function(){
		if(phoneBlur() === 1){
			$.post('<?php echo $base_url;?>admin/getMobileSmsForLogin',{
				"account":$("#account").val(),
				"phone": $("#phone").val(),
			}, function(data){
				let obj = data;//JSON.parse(data);
				if(obj.result === 0){
					$('.tips').html('验证码已发送');
					$('.tips').show();
					$('.tips').fadeOut(2000);
					let times = 60;
						$('#captchaBtn').attr("disabled", "true");
						$('#captchaBtn').css('background', 'grey');
						let timer = setInterval(() => {
							$('#captchaBtn').html(times);
							times--;
							if(times === 0){
								clearInterval(timer);
								$('#captchaBtn').html('重新发送');
								$('#captchaBtn').removeAttr("disabled");
								$('#captchaBtn').css('background', '#21a1f6');
							}
						}, 1000);
				}else{
					$('.tips').html(obj.result_message);
					$('.tips').css('color', 'red');
					$('.tips').show();
					$('.tips').fadeOut(2000);
				}
			},"json")
		}
})

})
function chkvalue1(){
	$("#account").parent().css("border-color","#ac1f1f");
	$(".accountIntro").show();
	var value=$("#account").val();
	if(value==''){
		$(".accountIntro a").html('用户名不能为空');
		return 0;
	}
	// else if(!(/^1[3|4|5|7|8]\d{9}$/.test(value))){
	// 	$(".accountIntro a").html('用户名不是手机号');
	// 	return 0;
	// }
	else{
		$("#account").parent().css("border-color","#cdcdcd");
		$(".accountIntro").hide();
		return 1;
	}
} 
function chkvalue2(){
	$("#password").parent().css("border-color","#ac1f1f");
	$(".passwordIntro").show();
	var value=$("#password").val();
	var reg = /^[0-9a-zA-Z]+$/;
	if(value.length<6||value.length>18){
		$(".passwordIntro a").html("请输入6~18位密码");
		return 0;
	}

	else{
		$("#password").parent().css("border-color","#cdcdcd");
		$(".passwordIntro").hide();
		return 1;
	}
}
function focus1(){
	$("#account").parent().css("border-color","#cdcdcd");
	$(".accountIntro").hide();
} 
function focus2(){
	$("#password").parent().css("border-color","#cdcdcd");
	$(".passwordIntro").hide();
} 
function ifLogin(e){
	if(e.which==13&&e.keyCode==13)
		$("#login").click();
} 
function phoneChange(e){
	let reg = /^(13[0-9]|14[5-9]|15[012356789]|166|17[0-8]|18[0-9]|19[8-9])[0-9]{8}$/;
	if(!reg.test(e.target.value)){
		$('#phone').parent().css("border-color", "#ac1f1f");
		$('.phone-info a').html('无效的手机号');
		$('.phone-info').show();
	}else{
		$('#phone').parent().css("border-color", "#cdcdcd");
		$('.phone-info').hide();
	}
}
function phoneFocus(){
	$('#phone').parent().css("border-color", "#cdcdcd");
	$('.phone-info').hide();
}
function phoneBlur(){
	let value = $('#phone').val();
	let reg = /^(13[0-9]|14[5-9]|15[012356789]|166|17[0-8]|18[0-9]|19[8-9])[0-9]{8}$/;
	if(!reg.test(value)){
		$('#phone').parent().css("border-color", "#ac1f1f");
		$('.phone-info a').html('无效的手机号');
		$('.phone-info').show();
		return 0;
	}else{
		$('#phone').parent().css("border-color", "#cdcdcd");
		$('.phone-info').hide();
		return 1;
	}
}
function captchaInput(event){
	if(event.target.value.length!==6){
		$('#captcha').parent().css("border-color", "#ac1f1f");
		$('.captcha-info a').html('验证码位数错误');
		$('.captcha-info').show();
	}else{
		$('#captcha').parent().css("border-color", "#cdcdcd");
		$('.captcha-info').hide();
	}
}
function captchaFocus(){
	$('#captcha').parent().css("border-color", "#cdcdcd");
	$('.captcha-info').hide();
}
function captchaBlur(){
	let value = $('#captcha').val();
	if(value.length!==6){
		$('#captcha').parent().css("border-color", "#ac1f1f");
		$('.captcha-info a').html('验证码位数错误');
		$('.captcha-info').show();
		return 0;
	}else{
		$('#captcha').parent().css("border-color", "#cdcdcd");
		$('.captcha-info').hide();
		return 1;
	}
}
</script>
</head>
<style>
input:-webkit-autofill {
-webkit-box-shadow: 0 0 0px 1000px white inset;
}
.accountIntro{height:22px;line-height:22px;font-size:14px;color:#ac1f1f;margin-left: 45px;width: 253px;margin-top: 5px;display: none;}
.passwordIntro{height:22px;line-height:22px;font-size:14px;color:#ac1f1f;margin-left: 45px;width: 253px;margin-top: 5px;display: none;}
</style>
<body  id="body" style="background: #fff;">
<div style="height: 86px;background: #fff;box-shadow: 0 0 20px #888;position: relative;z-index: 9999;min-width: 1226px;">
	<div style="width: 1226px;margin:0 auto;">
		<img src="<?php echo $base_url;?>files/images/logo.png" style="float: left;height: 50px;margin-top: 18px;"> 
		<div style="height: 40px;width:170px;text-align: center;line-height: 40px;margin-top: 23px;margin-left: 30px;border-left:1px solid #cdcdcd;float: left;font-size: 26px;font-family: 黑体;">欢迎登录</div>
		
	</div>
</div>
<div style="width: 100%;min-width: 1226px;height: 600px;background: #1077ff;">
	<div style="width: 1226px;margin:0 auto;position: relative;">
		<div style="width:345px;height:500px;margin-top:30px;background:#fff;margin-right:118px;position:absolute;left:200px;top:0;box-shadow: 0 0 20px #888; ">
			<div class="tips">

			</div>
			<div style="margin-top: 45px;font-size: 20px;margin-left: 45px;">登录游戏管理后台</div>
			<div style="height: 70px;margin-top: 40px;">
				<div style="margin-left: 45px;height: 42px;border:1px solid #cdcdcd;width: 253px;">
					<img src="<?php echo $base_url;?>files/images/user.png" style="width:20px;height:20px;vertical-align:middle;margin-left:8px; "> 
					<input style="width: 200px;padding: 0 5px;height: 42px;font-size: 16px;" id="account" onblur="chkvalue1()" onfocus="focus1()" >
				</div>
				<div class="accountIntro">
					<img src="<?php echo $base_url;?>files/images/false.png" style="width:16px;height:16px;vertical-align:middle; "> 
					<a></a>
				</div>
			</div>
			<div style="height: 70px;">
				<div style="margin-top: 5px;margin-left: 45px;height: 42px;border:1px solid #cdcdcd;width: 253px;">
					<img src="<?php echo $base_url;?>files/images/password.png" style="width:20px;height:20px;vertical-align:middle;margin-left:8px; "> 
					<input type="password" style="width: 200px;padding: 0 5px;height: 42px;font-size: 16px;" id="password" onblur="chkvalue2()" onfocus="focus2()" onkeydown="ifLogin(event)" autocomplete="off">
				</div>
				<div class="passwordIntro">
					<img src="<?php echo $base_url;?>files/images/false.png" style="width:16px;height:16px;vertical-align:middle; "> 
					<a></a>
				</div>
			</div>
			<div class="phone-wrapper">
			    <div class="phone">
					<img src="<?php echo $base_url;?>files/images/phone.png" alt="电话号码" style="width:20px;height:20px;vertical-align:middle;margin-left:8px;">
					<input type="text" style="width: 200px;padding: 0 5px;height: 42px;font-size: 16px;" id="phone" oninput="phoneChange(event)" onfocus="phoneFocus()" onblur="phoneBlur()">
				</div>
				<div class="phone-info">
					<img src="<?php echo $base_url;?>files/images/false.png" style="width:16px;height:16px;vertical-align:middle; ">
					<a></a>
				</div>
			</div>
			<div class="captcha-wrapper">
				<div class="captcha">
					<input type="text" style="width: 160px;padding: 0 5px;height: 42px;font-size: 16px;" id="captcha" oninput="captchaInput(event)" onfocus="captchaFocus()" onblur="captchaBlur()">
					<button style="font-size:15px; background:#21a1f6; height:42px; width:77px; color:white; cursor:pointer; border-top-right-radius:5px;border-bottom-right-radius:5px;" id="captchaBtn" >获取验证码</button>
				</div>
				<div class="captcha-info">
				<img src="<?php echo $base_url;?>files/images/false.png" style="width:16px;height:16px;vertical-align:middle; ">
					<a></a>
				</div>
			</div>
			<div style="margin-top: 5px;margin-left: 45px;height: 42px;width:255px; ">
				<!-- <div style="color: #2ebcbc;line-height: 42px;font-size: 14px;float: left;cursor: pointer;" id="forget">忘记密码？</div> -->
				<div style="width: 112px;height:42px;color:#fff;float: right;line-height: 42px;background: #1077ff;text-align: center;border-radius:5px;cursor: pointer;" id="login">登录</div>
			</div>
			<!-- <div style="position: absolute;bottom:0;height: 50px;border-top:1px solid #cdcdcd;line-height: 50px;text-align: center;font-size: 20px;color:#999;background:#f5f5f5;width:100%;cursor:pointer; " id="register">立即注册</div> -->
		</div>
		<!-- <img src="<?php echo $base_url;?>files/images/main.png" style="float: left;"> -->
	</div>
	<div style="height: 500px;"></div>

</div>
</body>
</html>

