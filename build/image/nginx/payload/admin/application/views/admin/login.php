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
		if(test1*test2==0 ){
			return 0
		}
		else{
			$.post('<?php echo $base_url;?>admin/loginOpt',{
				"account":$("#account").val(),"pwd":hex_md5($("#password").val())
			},function(data)
			{
				var obj = eval('(' + data + ')');	
				if(obj.result==0){
					window.location.href="<?php echo $base_url;?>admin/index";
				}
				else if(obj.result==-1){
					$("#account").parent().css("border-color","#ac1f1f");
					$(".accountIntro").show();
					$(".accountIntro a").html(obj.result_message);
				}
				else if(obj.result==-2){
					$("#password").parent().css("border-color","#ac1f1f");
					$(".passwordIntro").show();
					$(".passwordIntro a").html(obj.result_message); 
				}
			});	
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
<div style="width: 100%;min-width: 1226px;height: 500px;background: #1077ff;">
	<div style="width: 1226px;margin:0 auto;position: relative;">
		<div style="width:345px;height:330px;margin-top:30px;background:#fff;margin-right:118px;position:absolute;left:200px;top:0;box-shadow: 0 0 20px #888; ">
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

