var app=angular.module('app',[]);

app.directive('ngInput', [function () {
    return {
        restrict: 'A',
        require: '?ngModel',
        link: function(scope, element, attrs) {
            element.on('input',oninput);
            scope.$on('$destroy',function(){//销毁的时候取消事件监听
                element.off('input',oninput);
            });
            function oninput(event){
                scope.$evalAsync(attrs['ngInput'],{$event:event,$value:this.value});
            }
        }
    }
}]);

app.controller("myCtrl", function($scope,$http,$interval) {
    FastClick.attach(document.body);
    $scope.width=window.innerWidth;
    $scope.userInfo= userData;
    $scope.socket= socketData;
    $scope.dealerNum = dealerNum;
    $scope.selectArr = [];
    $scope.defaultScores = [];
    $scope.defaultTime5To10 = [];
    $scope.defaultTime5To20 = [];
    $scope.isShowBindPhone = false;
    $scope.sPhone='';
    $scope.sAuthcode='';
    $scope.authcodeType=1;
    $scope.authcodeTime=59;
    $scope.authcodeText='发送验证码';
    $scope.menuDatas=[
        {url:'',img:'game.png',name:'游戏',end: false},
        {url:currentUrl+'f/fri',img:'friend.png',name:'好友',end: false},
        {url:currentUrl+'f/box',img:'box.png',name:'包厢',end: false},
        {url:currentUrl+'f/yh',img:'user.png',name:'个人',end: true}
    ];
    $scope.selectBull1 = true;
    $scope.selectBull2 = true;
    $scope.selectBull3 = true;
    $scope.selectBull4 = true;
    $scope.selectBull5 = true;
    $scope.selectBull91 = true;
    $scope.selectBull92 = true;
    $scope.selectBull93 = true;
    $scope.selectBull94 = true;
    $scope.selectBull95 = true;
    $scope.selectTbull1 = true;
    $scope.selectTbull2 = true;
    $scope.selectTbull3 = true;
    $scope.selectTbull4 = true;
    $scope.selectTbull5 = true;
    $scope.selectFbull1 = true;
    $scope.selectFbull2 = true;
    $scope.selectFbull3 = true;
    $scope.selectFbull4 = true;
    $scope.selectFbull5 = true;
    $scope.selectLbull1 = true;
    $scope.selectLbull2 = true;
    $scope.selectLbull3 = true;
    $scope.selectLbull4 = true;
    $scope.selectLbull5 = true;
    $scope.selectBullValue = 1;
    $scope.selectBull9Value = 1;
    $scope.selectTbullValue = 1;
    $scope.selectFbullValue = 1;
    $scope.selectLbullValue = 1;
    //不能看牌比分的初始化值
    $scope.seenProgressValue = 0;
    //不能比牌的滑动框的初始值
    $scope.compareProgressValue = 0;
    $scope.seenTenProgressValue = 0;
    $scope.compareTenProgressValue = 0;
    $scope.seenBigProgressValue = 0;
    $scope.compareBigProgressValue = 0;
    //初始化牛下拉列表数据
    for(var i = 1; i <= 100; i++){
        $scope.selectArr.push(i);
    }
    for (var i = 0; i < 200; i++){
        $scope.defaultScores.push(i+1);
    }
    for (var i = 5; i < 11; i++){
        $scope.defaultTime5To10.push(i);
    }
    for (var i = 5; i < 21; i++){
        $scope.defaultTime5To20.push(i);
    }
    if (userData.card>=30&&userData.phone.length<1) {
        $scope.isShowBindPhone=true;
    }
    $scope.compareProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.flower.compareProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.compareRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.compareRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.compareRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.compareRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.compareRange').css({
                'background-size': progressValue
            })
        }
    }
    $scope.seenBigProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.bflower.seenProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.seenBigRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.seenBigRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.seenBigRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.seenBigRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.seenBigRange').css({
                'background-size': progressValue
            })
        }
    }
    $scope.compareBigProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.bflower.compareProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.compareBigRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.compareBigRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.compareBigRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.compareBigRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.compareBigRange').css({
                'background-size': progressValue
            })
        }
    }
    //不能看牌值改变时调用的函数
    $scope.seenProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.flower.seenProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.seenRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.seenRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.seenRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.seenRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.seenRange').css({
                'background-size': progressValue
            })
        }
    }
    $scope.compareTenProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.tflower.compareProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.compareTenRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.compareTenRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.compareTenRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.compareTenRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.compareTenRange').css({
                'background-size': progressValue
            })
        }
    }
    $scope.seenTenProgressChange = function(value){
        var valueToNumber = Number(value);
        $scope.createInfo.tflower.seenProgress = valueToNumber;
        var progressValue = Math.floor(valueToNumber/20) + '% 100%';
        if(valueToNumber === 1700){
            $('.seenTenRange').css({
                'background-size': '80% 100%'
            })
        }else if((valueToNumber === 1900) || (valueToNumber === 1800) ) {
            $('.seenTenRange').css({
                'background-size': '85% 100%'
            })
        }else if((valueToNumber>200) && (valueToNumber<=600)){
            $('.seenTenRange').css({
                'background-size': Math.floor((valueToNumber+100)/20) + '% 100%'
            })
        }else if((valueToNumber === 200) || (valueToNumber === 100)){
            $('.seenTenRange').css({
                'background-size': Math.floor((valueToNumber+200)/20) + '% 100%'
            })
        }else{
            $('.seenTenRange').css({
                'background-size': progressValue
            })
        }
    }
    $scope.seenBigAdd = function(){
        var value = $scope.createInfo.bflower.seenProgress;
        if(value === 2000){
            $('.seenBigRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.seenBigRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.bflower.seenProgress += 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.seenBigRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.bflower.seenProgress += 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.seenProgress += 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.seenProgress += 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.seenBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.seenProgress += 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }
    }
    $scope.seenBigReduce = function(){
        console.log($('.seenRange'))
        var value = $scope.createInfo.bflower.seenProgress;
        if(value === 0){
            $('.seenBigRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.seenBigRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.bflower.seenProgress -= 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else if(value === 1800){
            
            $('.seenBigRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.bflower.seenProgress -= 100;
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.bflower.seenProgress -= 100;
            var progressValue = ($scope.createInfo.bflower.seenProgress+200)/20 + '% 100%';
            $('.seenBigRange').css({
                'background-size': progressValue
            })
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }else{
            console.log(value)
            $scope.createInfo.bflower.seenProgress -= 100;
            var progressValue = $scope.createInfo.bflower.seenProgress/20 + '% 100%';
            $('.seenBigRange').css({
                'background-size': progressValue
            })
            $('.seenBigRange').val($scope.createInfo.bflower.seenProgress);
        }
    }
    $scope.compareBigAdd = function(){
        var value = $scope.createInfo.bflower.compareProgress;
        if(value === 2000){
            $('.compareBigRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.compareBigRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.bflower.compareProgress += 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.compareBigRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.bflower.compareProgress += 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.compareProgress += 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.compareProgress += 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.compareBigRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.bflower.compareProgress += 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }
    }
    $scope.compareBigReduce = function(){
        console.log($('.seenRange'))
        var value = $scope.createInfo.bflower.compareProgress;
        if(value === 0){
            $('.compareBigRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.compareBigRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.bflower.compareProgress -= 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else if(value === 1800){
            
            $('.compareBigRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.bflower.compareProgress -= 100;
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.bflower.compareProgress -= 100;
            var progressValue = ($scope.createInfo.bflower.compareProgress+200)/20 + '% 100%';
            $('.compareBigRange').css({
                'background-size': progressValue
            })
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }else{
            console.log(value)
            $scope.createInfo.bflower.compareProgress -= 100;
            var progressValue = $scope.createInfo.bflower.compareProgress/20 + '% 100%';
            $('.compareBigRange').css({
                'background-size': progressValue
            })
            $('.compareBigRange').val($scope.createInfo.bflower.compareProgress);
        }
    }
    $scope.seenTenAdd = function(){
        var value = $scope.createInfo.tflower.seenProgress;
        if(value === 2000){
            $('.seenTenRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.seenTenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.tflower.seenProgress += 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.seenTenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.tflower.seenProgress += 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.seenProgress += 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.seenProgress += 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.seenTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.seenProgress += 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }
    }
    $scope.seenTenReduce = function(){
        console.log($('.seenRange'))
        var value = $scope.createInfo.tflower.seenProgress;
        if(value === 0){
            $('.seenTenRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.seenTenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.tflower.seenProgress -= 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else if(value === 1800){
            
            $('.seenTenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.tflower.seenProgress -= 100;
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.tflower.seenProgress -= 100;
            var progressValue = ($scope.createInfo.tflower.seenProgress+200)/20 + '% 100%';
            $('.seenTenRange').css({
                'background-size': progressValue
            })
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }else{
            console.log(value)
            $scope.createInfo.tflower.seenProgress -= 100;
            var progressValue = $scope.createInfo.tflower.seenProgress/20 + '% 100%';
            $('.seenTenRange').css({
                'background-size': progressValue
            })
            $('.seenTenRange').val($scope.createInfo.tflower.seenProgress);
        }
    }
    $scope.compareTenAdd = function(){
        var value = $scope.createInfo.tflower.compareProgress;
        if(value === 2000){
            $('.compareTenRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.compareTenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.tflower.compareProgress += 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.compareTenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.tflower.compareProgress += 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.compareProgress += 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.compareProgress += 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.compareTenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.tflower.compareProgress += 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }
    }
    $scope.compareTenReduce = function(){
        console.log($('.seenRange'))
        var value = $scope.createInfo.tflower.compareProgress;
        if(value === 0){
            $('.compareTenRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.compareTenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.tflower.compareProgress -= 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else if(value === 1800){
            
            $('.compareTenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.tflower.compareProgress -= 100;
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.tflower.compareProgress -= 100;
            var progressValue = ($scope.createInfo.tflower.compareProgress+200)/20 + '% 100%';
            $('.compareTenRange').css({
                'background-size': progressValue
            })
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }else{
            console.log(value)
            $scope.createInfo.tflower.compareProgress -= 100;
            var progressValue = $scope.createInfo.tflower.compareProgress/20 + '% 100%';
            $('.compareTenRange').css({
                'background-size': progressValue
            })
            $('.compareTenRange').val($scope.createInfo.tflower.compareProgress);
        }
    }
    $scope.compareAdd = function(){
        var value = $scope.createInfo.flower.compareProgress;
        if(value === 2000){
            $('.compareRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.compareRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.flower.compareProgress += 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.compareRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.flower.compareProgress += 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.compareProgress += 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.compareRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.compareProgress += 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.compareRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.compareProgress += 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }
    }
    $scope.compareReduce = function(){
        var value = $scope.createInfo.flower.compareProgress;
        if(value === 0){
            $('.compareRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.compareRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.flower.compareProgress -= 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else if(value === 1800){
            
            $('.compareRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.flower.compareProgress -= 100;
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.flower.compareProgress -= 100;
            var progressValue = ($scope.createInfo.flower.compareProgress+200)/20 + '% 100%';
            $('.compareRange').css({
                'background-size': progressValue
            })
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }else{
            console.log(value)
            $scope.createInfo.flower.compareProgress -= 100;
            var progressValue = $scope.createInfo.flower.compareProgress/20 + '% 100%';
            $('.compareRange').css({
                'background-size': progressValue
            })
            $('.compareRange').val($scope.createInfo.flower.compareProgress);
        }
    }
    $scope.downUpperLimit = function(type){
        var limit;
        switch (type){
            case 't':
                limit = parseInt($scope.createInfo.tflower.upper_limit);
                $scope.createInfo.tflower.upper_limit = limit < 100? limit: limit - 100;
                return false;
            case 'b':
                limit = parseInt($scope.createInfo.bflower.upper_limit);
                $scope.createInfo.bflower.upper_limit = limit < 100? limit: limit - 100;
                return false;
            default:
                limit = parseInt($scope.createInfo.flower.upper_limit);
                $scope.createInfo.flower.upper_limit = limit < 100? limit: limit - 100;
                return false;
        }
    }
    $scope.upUpperLimit = function(type){
        var limit;
        switch (type){
            case 't':
                limit = parseInt($scope.createInfo.tflower.upper_limit);
                $scope.createInfo.tflower.upper_limit = limit == 2000? limit: limit + 100;
                return false;
            case 'b':
                limit = parseInt($scope.createInfo.bflower.upper_limit);
                $scope.createInfo.bflower.upper_limit = limit == 2000? limit: limit + 100;
                return false;
            default:
                limit = parseInt($scope.createInfo.flower.upper_limit);
                $scope.createInfo.flower.upper_limit = limit == 2000? limit: limit + 100;
                return false;
        }
    }
    $scope.seenAdd = function(){
        var value = $scope.createInfo.flower.seenProgress;
        if(value === 2000){
            $('.seenRange').css({
                'background-size': '100% 100%'
            })
        }else if(value === 1700){
            $('.seenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.flower.seenProgress += 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else if((value === 1800) || (value === 1900)){
            $('.seenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.flower.seenProgress += 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else if((value>100) && (value<=800)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.seenProgress += 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else if((value === 100) || (value=== 0)){
            var progressValue = (value+200)/20 + '% 100%';
            $('.seenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.seenProgress += 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else{
            var progressValue = value/20 + '% 100%';
            $('.seenRange').css({
                'background-size': progressValue
            })
            $scope.createInfo.flower.seenProgress += 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }
    }
    $scope.seenReduce = function(){
        console.log($('.seenRange'))
        var value = $scope.createInfo.flower.seenProgress;
        if(value === 0){
            $('.seenRange').css({
                'background-size': '0% 100%'
            })
        }else if((value === 2000) || (value === 1900)){
            $('.seenRange').css({
                'background-size': '85% 100%'
            })
            $scope.createInfo.flower.seenProgress -= 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else if(value === 1800){
            
            $('.seenRange').css({
                'background-size': '80% 100%'
            })
            $scope.createInfo.flower.seenProgress -= 100;
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else if((value<=500) && (value>=100)){
            $scope.createInfo.flower.seenProgress -= 100;
            var progressValue = ($scope.createInfo.flower.seenProgress+200)/20 + '% 100%';
            $('.seenRange').css({
                'background-size': progressValue
            })
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }else{
            console.log(value)
            $scope.createInfo.flower.seenProgress -= 100;
            var progressValue = $scope.createInfo.flower.seenProgress/20 + '% 100%';
            $('.seenRange').css({
                'background-size': progressValue
            })
            $('.seenRange').val($scope.createInfo.flower.seenProgress);
        }
    }
    $scope.progressChange = function(value){
        if(value === '0'){
            $scope.upLimitValue = '无上限';
            $('.range').css({
                'background-size': '0% 100%'
            })
        }else{
            $scope.upLimitValue = value;
            var progressValue = Math.floor($('.range').val()/20) + '% 100%';
            $('.range').css({
                'background-size': progressValue
            })
        }
    }
    $scope.reduceProgress = function(){
        var value = $('.range').val();
        if(value === '100'){
            $scope.upLimitValue = '无上限';
            $('.range').val(0);
            $('.range').css({
                'background-size': '0% 100%'
            })
        }else if(value === '0'){

        }else{
            var changeValue = Number(value) - 100;
            $('.range').val(changeValue);
            $scope.upLimitValue = changeValue.toString();
            var progressValue = Math.floor(changeValue/20) + '% 100%';
            $('.range').css({
                'background-size': progressValue
            })
        }
    }
    //增加进度条事件
    $scope.addProgress = function(){
        var value = $('.range').val();
        console.log(value)
        if(value === '0'){
            $scope.upLimitValue = '100';
            $('.range').val(100);
            $('.range').css({
                'background-size': '5% 100%'
            })
        }else if(value === '2000'){
            $scope.upLimitValue = value;
            $('.range').val(2000);
            $('.range').css({
                'background-size': '100% 100%'
            })
        }else{
            var changeValue = Number(value) + 100;
            $('.range').val(changeValue);
            $scope.upLimitValue = changeValue.toString();
            var progressValue = Math.floor(changeValue/20) + '% 100%';
            $('.range').css({
                'background-size': progressValue
            })
        }
    }
    $scope.phoneChangeValue=function () {
        var result = checkPhone($scope.sPhone);
        if (result) {
            $('#authcode').css('background-color','rgb(64,112,251)');
        } else {
            $('#authcode').css('background-color','lightgray');
        }
    };
    $scope.getAuthcode=function () {
        if ($scope.authcodeType != 1) {
            return;
        }
        var color = $('#authcode').css('background-color');
        if (color != 'rgb(64, 112, 251)') {
            return;
        }
        var validPhone = checkPhone($scope.sPhone);
        if (validPhone == false) {
            $scope.showAlert(1,'手机号码有误，请重填');
            return;
        }
        $scope.DoGetAuthcode($scope.sPhone);
    };
    $scope.DoGetAuthcode= function (phone) {
        $http({
            method:'POST',
            url:'/account/getMobileSms',
            data: {
                'phone': phone,
                'dealer_num': dealerNum
            }
        }).then(function (res) {
            if (res.data.result == 0) {
                var timerHandler = $interval(function () {
                    if ($scope.authcodeTime<=0) {
                        $interval.cancel(timerHandler);
                        $scope.authcodeTime=59;
                        $scope.authcodeText='获取验证码';
                        $scope.authcodeType=1;
                    }else{
                        $scope.authcodeText=$scope.authcodeTime+'s';
                        $scope.authcodeTime--;
                    }
                }, 1000);
                $scope.authcodeType = 2;
            } else {
                $scope.showAlert(1,res.data.result_message);
            }
        }, function() {
            $scope.showAlert(1,'获取验证码失败');
        });
    };
    $scope.bindPhone=function () {
        var validPhone = checkPhone($scope.sPhone);
        var validAuthcode = checkAuthcode($scope.sAuthcode);
        if (validPhone == false) {
            $scope.showAlert(1,'手机号码有误，请重填');
            return;
        }
        if (validAuthcode == false) {
            $scope.showAlert(1,'验证码有误，请重填');
            return;
        }
        $scope.DoBindPhone($scope.sPhone,$scope.sAuthcode);
    };
    $scope.DoBindPhone= function (phone, code) {
        $http({
            method:'POST',
            url:'/account/checkSmsCode',
            data:{"phone":phone, "code":code, "dealer_num":$scope.dealerNum}
        }).then(function(response) {
            var bodyData = response.data;
            if (bodyData.result == 0) {
                $scope.isShowBindPhone = false;
                $scope.isPhone = true;
                $scope.isAuthPhone = 0;
                $scope.phone = $scope.sPhone;

                if (bodyData.data.account_id != userData.id) {
                    $scope.showAlert(1,bodyData.result_message);
                } else {
                    $scope.showAlert(1,bodyData.result_message);
                }

                $scope.sPhone = '';
                $scope.sAuthcode = '';

            } else {
                $scope.showAlert(1,bodyData.result_message);
            }

        }, function() {
            $scope.authcodeTime = 59;
            $scope.showAlert(1,"绑定失败");
        });
    };
    $scope.homeImgRight=10;
    $scope.lenCard=$scope.userInfo.card.length;
    $scope.homeImgRight+=$scope.lenCard;

    var socketStatus=0;
    $(".main").show();
    $("#loading").hide();
    $scope.activity=new Array();
    $scope.isShowAlert=false;
    $scope.alertType=0;
    $scope.alertText="";
    $scope.showAlert=function(type,text){
        //$(".alertText").css("top","90px")
        $scope.alertType=type;
        $scope.alertText=text;
        $scope.isShowAlert=true;

        setTimeout(function() {
            $scope.$apply();
        }, 0);

        setTimeout(function(){
            var wHeight = window.innerHeight;
            var alertHeight = $(".alertText").height();
            var textHeight = $(".alertText").height();

            if (alertHeight < wHeight * 0.15) {
                alertHeight = wHeight * 0.15;
            }

            if (alertHeight > wHeight * 0.8) {
                alertHeight = wHeight * 0.8;
            }

            var mainHeight = alertHeight + wHeight * (0.022 + 0.034) * 2 + wHeight * 0.022 + wHeight * 0.056;
            if (type == 8) {
                mainHeight = mainHeight - wHeight * 0.022 - wHeight * 0.056
            }

            var blackHeight = alertHeight + wHeight * 0.034 * 2;
            var alertTop = wHeight * 0.022 + (blackHeight - textHeight) / 2;

            // $(".alert .mainPart").css('height', mainHeight + 'px');
            // $(".alert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
            // $(".alert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
            // $(".alert .mainPart .alertText").css('top', alertTop + 'px');

            $scope.$apply();
        },0)
    }
    $scope.closeAlert=function(){
        if($scope.alertType==1){
            $scope.isShowAlert=false;
            // $scope.showShop();
            if(!$scope.is_connect){
                $scope.is_connect=true;
            }
        }
        else{
            $scope.isShowAlert=false;
        }
    }

    setTimeout(function() {
        $scope.$apply();
    }, 100);

    $scope.reloadView = function () {
        window.location.href=window.location.href+"&id="+10000*Math.random();
    };

    $scope.is_operation=false;
    $scope.waiting=function(){
        $scope.is_operation=true;
        setTimeout(function(){
            if($scope.is_operation){
                $scope.is_operation=false;
                $scope.showAlert(6,"创建房间失败，请重新创建")
            }
        },15000)
    };

    $scope.socket_url="";
    $scope.socket_type="";
    $scope.connectSocket=function(socket,type){
        if(type == 1) {
            $scope.is_operation=false;
            if ($scope.createInfo.flower.chip_type.length !== 4) {
                $scope.showAlert(6, "请选择四组筹码（必须为四组）");
                return false;
            }
        } else if(type == 110) {
            $scope.is_operation=false;
            if($scope.createInfo.tflower.chip_type.length !== 4){
                $scope.showAlert(6,"请选择四组筹码（必须为四组）");
                return false;
            }
        } else if(type == 111) {
            $scope.is_operation=false;
            if($scope.createInfo.bflower.chip_type.length !== 4){
                $scope.showAlert(6,"请选择四组筹码（必须为四组）");
                return false;
            }
        }
        $scope.socket_url=socket;
        $scope.socket_type=type;
        $scope.ws = new WebSocket(socket);
        $scope.ws.onopen = function(){
            $scope.is_operation=true;
            var tiao=setInterval(function(){
                socketStatus=socketStatus+1;
                $scope.ws.send("@");
                if(socketStatus>3||socketStatus>3){
                    window.location.reload();
                }
            },4000);
            if(type==1){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.flower.chip_type,
                        'ticket_count':$scope.createInfo.flower.ticket_count,

                        'disable_pk_men':$scope.createInfo.flower.pkvalue2,
                        'upper_limit':$scope.createInfo.flower.upper_limit,
                        // 'seen':$scope.createInfo.flower.seen,
                        'game_type': type,
                        'raceCard': $scope.createInfo.flower.raceCard,
                        'seenProgress': $scope.createInfo.flower.seenProgress,
                        'compareProgress': $scope.createInfo.flower.compareProgress,
                        'extraRewards': $scope.createInfo.flower.extraRewards,
                        'default_score': $scope.createInfo.flower.default_score || $scope.createInfo.flower.default_score_select,
                        'allow235GTPanther': $scope.createInfo.flower.allow235GTPanther,
                        'countDown': $scope.createInfo.flower.countDown,
                    }
                }));
            }
            else if(type==92){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.vflower6.chip_type,
                        'disable_pk_100':$scope.createInfo.vflower6.pkvalue1,
                        'seen':$scope.createInfo.vflower6.seen,
                        'ticket_count':$scope.createInfo.vflower6.ticket_count,
                        'upper_limit':$scope.createInfo.vflower6.upper_limit,
                        'bean_type':$scope.createInfo.vflower6.bean_type
                    }
                }));
            }
            else if(type==2){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_count":$scope.createInfo.landlord.ticket_count,
                        "base_score":$scope.createInfo.landlord.base_score,
                        "ask_mode":$scope.createInfo.landlord.ask_mode,
                    }
                }));
            }
            else if(type==3){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.bull.ticket_type,
                        "score_type":$scope.createInfo.bull.score_type,
                        "rule_type":$scope.createInfo.bull.rule_type,
                        "is_cardfour":$scope.createInfo.bull.is_cardfour,
                        "is_cardfive":$scope.createInfo.bull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.bull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.bull.is_cardtiny,

                        "is_straight":$scope.createInfo.bull.is_straight,
                        "is_flush":$scope.createInfo.bull.is_flush,
                        "is_hulu":$scope.createInfo.bull.is_hulu,
                        "is_straightflush":$scope.createInfo.bull.is_straightflush,
                        "has_ghost":$scope.createInfo.bull.has_ghost,

                        "banker_mode":$scope.createInfo.bull.banker_mode,
                        "banker_score_type":$scope.createInfo.bull.banker_score,
                        "times_type":$scope.createInfo.bull.times_type,
                        "score_value": $scope.createInfo.bull.score_value,
                        "countDown": $scope.createInfo.bull.countDown,
                    }
                }));
            }
            else if(type==4){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "joker":$scope.createInfo.majiang.joker,
                        "horse_count":$scope.createInfo.majiang.horse_count,
                        "qianggang":$scope.createInfo.majiang.qianggang,
                        "chengbao":$scope.createInfo.majiang.chengbao,
                        "ticket_count":$scope.createInfo.majiang.ticket_count,
                    }
                }));
            }
            else if(type==5){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.bull9.ticket_type,
                        "score_type":$scope.createInfo.bull9.score_type,
                        "rule_type":$scope.createInfo.bull9.rule_type,
                        "is_cardfour":$scope.createInfo.bull9.is_cardfour,
                        "is_cardfive":$scope.createInfo.bull9.is_cardfive,
                        "is_cardbomb":$scope.createInfo.bull9.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.bull9.is_cardtiny,

                        "is_straight":$scope.createInfo.bull9.is_straight,
                        "is_flush":$scope.createInfo.bull9.is_flush,
                        "is_hulu":$scope.createInfo.bull9.is_hulu,
                        "is_straightflush":$scope.createInfo.bull9.is_straightflush,
                        "has_ghost":$scope.createInfo.bull9.has_ghost,

                        "banker_mode":$scope.createInfo.bull9.banker_mode,
                        "banker_score_type":$scope.createInfo.bull9.banker_score,
                        "times_type":$scope.createInfo.bull9.times_type,
                        "score_value": $scope.createInfo.bull9.score_value,
                        "countDown": $scope.createInfo.bull9.countDown,
                    }
                }));
            }
            else if(type==91){
                console.log($scope.createInfo.vbull9);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.vbull9.ticket_type,
                        "score_type":$scope.createInfo.vbull9.score_type,
                        "rule_type":$scope.createInfo.vbull9.rule_type,
                        "is_cardfour":$scope.createInfo.vbull9.is_cardfour,
                        "is_cardfive":$scope.createInfo.vbull9.is_cardfive,
                        "is_cardbomb":$scope.createInfo.vbull9.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.vbull9.is_cardtiny,
                        "banker_mode":$scope.createInfo.vbull9.banker_mode,
                        "banker_score_type":$scope.createInfo.vbull9.banker_score,
                        "times_type":$scope.createInfo.vbull9.times_type,
                        "bean_type":$scope.createInfo.vbull9.bean_type
                    }
                }));
            }
            else if(type==93){
                console.log($scope.createInfo.vbull6);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.vbull6.ticket_type,
                        "score_type":$scope.createInfo.vbull6.score_type,
                        "rule_type":$scope.createInfo.vbull6.rule_type,
                        "is_cardfour":$scope.createInfo.vbull6.is_cardfour,
                        "is_cardfive":$scope.createInfo.vbull6.is_cardfive,
                        "is_cardbomb":$scope.createInfo.vbull6.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.vbull6.is_cardtiny,
                        "banker_mode":$scope.createInfo.vbull6.banker_mode,
                        "banker_score_type":$scope.createInfo.vbull6.banker_score,
                        "times_type":$scope.createInfo.vbull6.times_type,
                        "bean_type":$scope.createInfo.vbull6.bean_type
                    }
                }));
            }
            else if(type==94){
                console.log($scope.createInfo.vbull12);
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.vbull12.ticket_type,
                        "score_type":$scope.createInfo.vbull12.score_type,
                        "rule_type":$scope.createInfo.vbull12.rule_type,
                        "is_cardfour":$scope.createInfo.vbull12.is_cardfour,
                        "is_cardfive":$scope.createInfo.vbull12.is_cardfive,
                        "is_cardbomb":$scope.createInfo.vbull12.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.vbull12.is_cardtiny,
                        "banker_mode":$scope.createInfo.vbull12.banker_mode,
                        "banker_score_type":$scope.createInfo.vbull12.banker_score,
                        "times_type":$scope.createInfo.vbull12.times_type,
                        "bean_type":$scope.createInfo.vbull12.bean_type
                    }
                }));
            }
            else if(type==8){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.tbull.ticket_type,
                        "score_type":$scope.createInfo.tbull.score_type,
                        "rule_type":$scope.createInfo.tbull.rule_type,
                        "is_cardfour":$scope.createInfo.tbull.is_cardfour,
                        "is_cardfive":$scope.createInfo.tbull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.tbull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.tbull.is_cardtiny,

                        "is_straight":$scope.createInfo.tbull.is_straight,
                        "is_flush":$scope.createInfo.tbull.is_flush,
                        "is_hulu":$scope.createInfo.tbull.is_hulu,
                        "is_straightflush":$scope.createInfo.tbull.is_straightflush,

                        "banker_mode":$scope.createInfo.tbull.banker_mode,
                        "banker_score_type":$scope.createInfo.tbull.banker_score,
                        "times_type":$scope.createInfo.tbull.times_type,
                        "score_value": $scope.createInfo.tbull.score_value,
                        "countDown": $scope.createInfo.tbull.countDown,
                    }
                }));
            }
            else if(type==9){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.fbull.ticket_type,
                        "score_type":$scope.createInfo.fbull.score_type,
                        "rule_type":$scope.createInfo.fbull.rule_type,
                        "is_cardfour":$scope.createInfo.fbull.is_cardfour,
                        "is_cardfive":$scope.createInfo.fbull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.fbull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.fbull.is_cardtiny,

                        "is_straight":$scope.createInfo.fbull.is_straight,
                        "is_flush":$scope.createInfo.fbull.is_flush,
                        "is_hulu":$scope.createInfo.fbull.is_hulu,
                        "is_straightflush":$scope.createInfo.fbull.is_straightflush,

                        "banker_mode":$scope.createInfo.fbull.banker_mode,
                        "banker_score_type":$scope.createInfo.fbull.banker_score,
                        "times_type":$scope.createInfo.fbull.times_type,
                        "score_value": $scope.createInfo.fbull.score_value,
                        "countDown": $scope.createInfo.fbull.countDown,
                    }
                }));
            }
            else if(type==92){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.vflower6.chip_type,
                        'ticket_count':$scope.createInfo.vflower6.ticket_count,
                        'disable_pk_100':$scope.createInfo.vflower6.pkvalue1,
                        'bean_type':$scope.createInfo.vflower6.bean_type,
                        'upper_limit':$scope.createInfo.vflower6.upper_limit,
                        'seen':$scope.createInfo.vflower6.seen
                    }
                }));
            }
            else if(type==95){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.vflower10.chip_type,
                        'ticket_count':$scope.createInfo.vflower10.ticket_count,
                        'disable_pk_100':$scope.createInfo.vflower10.pkvalue1,
                        'bean_type':$scope.createInfo.vflower10.bean_type,
                        'upper_limit':$scope.createInfo.vflower10.upper_limit,
                        'seen':$scope.createInfo.vflower10.seen
                    }
                }));
            }
            else if(type==110){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.tflower.chip_type,
                        'ticket_count':$scope.createInfo.tflower.ticket_count,
                        'disable_pk_men':$scope.createInfo.tflower.pkvalue2,
                        'upper_limit':$scope.createInfo.tflower.upper_limit,
                        // 'seen':$scope.createInfo.tflower.seen,
                        'game_type':type,
                        'raceCard':$scope.createInfo.tflower.raceCard,
                        'seenProgress': $scope.createInfo.tflower.seenProgress,
                        'compareProgress': $scope.createInfo.tflower.compareProgress,
                        'extraRewards': $scope.createInfo.tflower.extraRewards,
                        'default_score': $scope.createInfo.tflower.default_score || $scope.createInfo.tflower.default_score_select,
                        'allow235GTPanther': $scope.createInfo.tflower.allow235GTPanther,
                        'countDown': $scope.createInfo.tflower.countDown,
                    }
                }));
            }
            else if(type==111){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        'chip_type': $scope.createInfo.bflower.chip_type,
                        'ticket_count':$scope.createInfo.bflower.ticket_count,
                        'disable_pk_men':$scope.createInfo.bflower.pkvalue2,
                        'upper_limit':$scope.createInfo.bflower.upper_limit,
                        // 'seen':$scope.createInfo.bflower.seen,
                        'game_type':type,
                        'raceCard':$scope.createInfo.bflower.raceCard,
                        'seenProgress': $scope.createInfo.bflower.seenProgress,
                        'compareProgress': $scope.createInfo.bflower.compareProgress,
                        'extraRewards': $scope.createInfo.bflower.extraRewards,
                        'default_score': $scope.createInfo.bflower.default_score || $scope.createInfo.bflower.default_score_select,
                        'countDown': $scope.createInfo.bflower.countDown,
                        'allow235GTPanther': $scope.createInfo.bflower.allow235GTPanther,
                    }
                }));
            }
            else if(type==36){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.sangong.banker_mode,
                        "score_type":$scope.createInfo.sangong.score_type,
                        "is_joker":$scope.createInfo.sangong.is_joker,
                        "is_bj":$scope.createInfo.sangong.is_bj,
                        "ticket_type":$scope.createInfo.sangong.ticket_type,
                        "countDown": $scope.createInfo.sangong.countDown,
                    }
                }));
            }
            else if(type==37){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.nsangong.banker_mode,
                        "score_type":$scope.createInfo.nsangong.score_type,
                        "is_joker":$scope.createInfo.nsangong.is_joker,
                        "is_bj":$scope.createInfo.nsangong.is_bj,
                        "ticket_type":$scope.createInfo.nsangong.ticket_type,
                        "countDown": $scope.createInfo.nsangong.countDown,
                    }
                }));
            }
            else if(type==38){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "banker_mode":$scope.createInfo.tsangong.banker_mode,
                        "score_type":$scope.createInfo.tsangong.score_type,
                        "is_joker":$scope.createInfo.tsangong.is_joker,
                        "is_bj":$scope.createInfo.tsangong.is_bj,
                        "ticket_type":$scope.createInfo.tsangong.ticket_type
                    }
                }));
            }
            else if(type==71){
                $scope.ws.send(JSON.stringify({
                    "operation":"CreateRoom",
                    "account_id":accountId,
                    "session":session,
                    "data":{
                        "data_key":Date.parse(new Date())+randomString(5),
                        "ticket_type":$scope.createInfo.lbull.ticket_type,
                        "score_type":$scope.createInfo.lbull.score_type,
                        "rule_type":$scope.createInfo.lbull.rule_type,
                        "is_cardfour":$scope.createInfo.lbull.is_cardfour,
                        "is_cardfive":$scope.createInfo.lbull.is_cardfive,
                        "is_cardbomb":$scope.createInfo.lbull.is_cardbomb,
                        "is_cardtiny":$scope.createInfo.lbull.is_cardtiny,

                        "is_straight":$scope.createInfo.lbull.is_straight,
                        "is_flush":$scope.createInfo.lbull.is_flush,
                        "is_hulu":$scope.createInfo.lbull.is_hulu,
                        "is_straightflush":$scope.createInfo.lbull.is_straightflush,

                        "banker_mode":$scope.createInfo.lbull.banker_mode,
                        "banker_score_type":$scope.createInfo.lbull.banker_score,
                        "times_type":$scope.createInfo.lbull.times_type,
                        "score_value": $scope.createInfo.lbull.score_value,
                        "countDown": $scope.createInfo.lbull.countDown,
                    }
                }));
            }
        };
        $scope.ws.onmessage = function(evt){
            if(evt.data=="@"){
                socketStatus=0;
                return 0;
            }
            var obj = eval('(' + evt.data + ')');
            if (obj.result==1){
                $scope.is_operation=false;
                $scope.showAlert(1,obj.result_message);
            } else if (obj.result == 0){
                if(type==1)
                    window.location.href = baseUrl + "f/yf?i="+obj.data.room_number+"_";
                else if(type==2)
                    window.location.href = baseUrl + "f/l?i="+obj.data.room_number+"_";
                else if(type==3)
                    window.location.href = baseUrl + "f/b?i="+obj.data.room_number+"_";
                else if(type==4){
                    window.location.href = baseUrl + "f/ma?i="+obj.data.room_number+"_";
                } else if(type==5){
                    window.location.href = baseUrl + "f/nb?i="+obj.data.room_number+"_";
                } else if(type==91){
                    window.location.href = baseUrl + "f/nbv?i="+obj.data.room_number+"_";
                } else if(type==93){
                    window.location.href = baseUrl + "f/bv?i="+obj.data.room_number+"_";
                } else if(type==94){
                    window.location.href = baseUrl + "f/tbv?i="+obj.data.room_number+"_";
                } else if(type==92){
                    window.location.href = baseUrl + "f/vf?i="+obj.data.room_number+"_";
                } else if(type==95){
                    window.location.href = baseUrl + "f/vtf?i="+obj.data.room_number+"_";
                } else if(type==8){
                    window.location.href = baseUrl + "f/tb?i="+obj.data.room_number+"_";
                }else if(type==9){
                    window.location.href = baseUrl + "f/fb?i="+obj.data.room_number+"_";
                } else if(type==110){
                    window.location.href = baseUrl + "f/tf?i="+obj.data.room_number+"_";
                } else if(type==111){
                    window.location.href = baseUrl + "f/bf?i="+obj.data.room_number+"_";
                } else if(type==36){
                    window.location.href = baseUrl + "f/sg?i="+obj.data.room_number+"_";
                } else if(type==37){
                    window.location.href = baseUrl + "f/nsg?i="+obj.data.room_number+"_";
                } else if(type==38){
                    window.location.href = baseUrl + "f/tsg?i="+obj.data.room_number+"_";
                } else if(type==71){
                    window.location.href = baseUrl + "f/lb?i="+obj.data.room_number+"_";
                }

            }  else if (obj.result == -201){
                $scope.is_operation=false;
                $scope.showAlert(31,obj.result_message);
            }  else {
                $scope.is_operation=false;
                $scope.showAlert(6,obj.result_message);
            }
        };
        $scope.ws.onclose = function(evt){
            // errorAPI("connectFailed");
            if($scope.is_operation){
                $scope.connectSocket($scope.socket_url,$scope.socket_type);
            }
            else
                return 0;
            //	window.location.reload();
        }
        $scope.ws.onerror = function(evt){console.log("WebSocketError!");};
    }

    $scope.createInfo={
        "isShow":0,
        "isShowGame":0,
        "gameList":'bulls',
        "flower":{
            'chip_type': [2, 4, 8, 10],
            'ticket_count': 1,
            'pkvalue2': 0,
            'upper_limit': 1000,
            // 'seen':0,
            'raceCard': false,
            'seenProgress': 0,
            'compareProgress': 0,
            'extraRewards': 0,
            'default_score': 2,
            'default_score_select': 1,
            'allow235GTPanther': 0,
            'countDown': [10, 10],
        },
        "vflower6":{
            'chip_type': 1,
            'pkvalue1': 0,
            'seen': 0,
            'ticket_count': 1,
            'upper_limit': 1000,
            'bean_type': 1
        },
        "vflower10":{
            'chip_type': 1,
            'pkvalue1': 0,
            'seen': 0,
            'ticket_count': 1,
            'upper_limit': 1000,
            'bean_type': 1
        },
        "bflower":{
            'chip_type': [2, 4, 8, 10],
            'ticket_count': 1,
            'pkvalue2': 0,
            'upper_limit': 1000,
            // 'seen': 0,
            'raceCard': false,
            'seenProgress': 0,
            'compareProgress': 0,
            'extraRewards': 0,
            'default_score': 2,
            'default_score_select': 1,
            'allow235GTPanther': 0,
            'countDown': [10, 10],
        },
        "landlord":{
            "ticket_count":1,
            "base_score":1,
            "ask_mode":1,
        },
        "majiang":{
            "joker":0,
            "horse_count":0,
            "qianggang":0,
            "ticket_count":1,
            "chengbao":0,
        },
        //六人斗牛默认建房选项
        "bull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "times_type":1,
            "is_straight":1, //牌型 顺子牛（5倍）
            "is_cardfour":1,//牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛（6倍）
            "is_hulu":1, //牌型 葫芦牛（7倍）
            "is_cardbomb":1, //牌型 炸弹牛(8倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "is_straightflush":1, //牌型 同花顺（10倍）
            "has_ghost": 1, //有无癞子
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
            "score_value": 1,
            "countDown": [10, 10, 10, 10],
        },
        //九人斗牛 默认建房选项
        "bull9":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "times_type":1,
            "is_straight":1, //牌型 顺子牛（5倍）
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛（6倍）
            "is_hulu":1, //牌型 葫芦牛（7倍）
            "is_cardbomb":1, //牌型 炸弹牛(8倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "is_straightflush":1, //牌型 同花顺（10倍）
            "has_ghost": 1, //有无癞子
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
            "score_value": 1,
            "countDown": [10, 10, 10, 10],
        },
        "vbf": {
            "game":91,
            "game91":"selected",
            "game92":"unselected",
            "game93":"unselected",
            "game94":"unselected",
            "game95":"unselected"
        },
        "vbull6":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,
            "times_type":1,
            "is_cardfour":1,
            "is_cardfive":1,
            "is_cardbomb":1,
            "is_cardtiny":1,
            "bean_type":1
        },
        "vbull9":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,
            "times_type":1,
            "is_cardfour":1,
            "is_cardfive":1,
            "is_cardbomb":1,
            "is_cardtiny":1,
            "bean_type":1
        },
        "vbull12":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,
            "times_type":1,
            "is_cardfour":1,
            "is_cardfive":1,
            "is_cardbomb":1,
            "is_cardtiny":1,
            "bean_type":1
        },
        "tbull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "times_type":1,
            "is_straight":1, //牌型 顺子牛（5倍）
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛（6倍）
            "is_hulu":1, //牌型 葫芦牛（7倍）
            "is_cardbomb":1, //牌型 炸弹牛(8倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "is_straightflush":1, //牌型 同花顺（10倍）
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
            "score_value": 1,
            "countDown": [10, 10, 10, 10],
        },
        "fbull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "times_type":1,
            "is_straight":1, //牌型 顺子牛（5倍）
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛（6倍）
            "is_hulu":1, //牌型 葫芦牛（7倍）
            "is_cardbomb":1, //牌型 炸弹牛(8倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "is_straightflush":1, //牌型 同花顺（10倍）
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
            "score_value": 1,
            "countDown": [10, 10, 10, 10],
        },
        //十人炸金花
        "tflower":{
            "chip_type": [2, 4, 8, 10],
            "ticket_count": 2, // 局数 2: 10局X2张房卡 4: 20局X4张房卡 默认2
            "pkvalue2": 0,
            "upper_limit": 1000,
            // "seen": 0,
            'raceCard': false,
            "seenProgress": 0,
            "compareProgress": 0,
            'extraRewards': 0,
            'default_score': 2,
            'default_score_select': 1,
            'allow235GTPanther': 0,
            'countDown': [10, 10],
        },
        // 六人三公
        "sangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected",
            "countDown": [10, 10, 10, 10],
        },
        // 九人三公
        "nsangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected",
            "countDown": [10, 10, 10, 10],
        },
        // 12人三公
        "tsangong":{
            "ticket_type":1,
            "score_type":1,
            "is_joker":0,
            "is_bj":0,
            "banker_mode":1,
            "banker1":"selected",
            "banker2":"unselected"
        },
        "lbull":{
            "ticket_type":1,
            "score_type":1,
            "rule_type":2,   //规则 1: 牛牛x3牛九x2牛八x2      2: 牛牛x4牛九x3牛八x2牛七x2
            "times_type":1,
            "is_cardfour":1, //牌型 四花牛(4倍)  1表示默认勾选
            "is_cardfive":1, //牌型 五花牛(5倍)  1表示默认勾选
            "is_straight":1, //牌型 顺子牛(5倍)  1表示默认勾选
            "is_flush":1, //牌型 同花牛(6倍)
            "is_hulu":1, //牌型 葫芦牛(7倍)
            "is_cardbomb":1, //牌型 炸弹牛(8倍)
            "is_cardtiny":1, //牌型 五小牛(8倍)
            "is_straightflush":1, //牌型 同花顺（10倍）
            "banker_mode":2, //模式 2 明牌抢庄
            "banker_score":4,
            "banker1":"unselected",
            "banker2":"selected",
            "banker3":"unselected",
            "banker4":"unselected",
            "banker5":"unselected",
            "score_value": 1,
            "countDown": [10, 10, 10, 10],
        }
    };

    $scope.createInfo = getSetting($scope.createInfo);
    $scope.createInfo.flower.pkvalue2=0;
    $scope.createInfo.bflower.pkvalue2=0;
    $scope.createInfo.tflower.pkvalue2=0;
    $scope.createInfo.vflower6.pkvalue2=0;
    $scope.createInfo.vflower10.pkvalue2=0;
        if($scope.createInfo.bull){
            var sort = $scope.createInfo.bull;
            $scope.selectBullValue = sort.score_value;
            var mode = sort.banker_mode;
            if(sort.score_type == 7) $scope['selectBull'+mode] = false;
        }
        if( $scope.createInfo.bull9 ){
            var sort = $scope.createInfo.bull9;
            $scope.selectBull9Value = sort.score_value;
            var mode = sort.banker_mode;
            if(sort.score_type == 7) $scope['selectBull9'+mode] = false;
        }
        if( $scope.createInfo.tbull ){
            var sort = $scope.createInfo.tbull;
            $scope.selectTbullValue = sort.score_value;
            var mode = sort.banker_mode;
            if(sort.score_type == 7) $scope['selectTbull'+mode] = false;
        }
        if( $scope.createInfo.fbull ){
            var sort = $scope.createInfo.fbull;
            $scope.selectFbullValue = sort.score_value;
            var mode = sort.banker_mode;
            if(sort.score_type == 7) $scope['selectFbull'+mode] = false;
        }
        if($scope.createInfo.lbull){
            var sort = $scope.createInfo.lbull;
            $scope.selectLbullValue = sort.score_value;
            var mode = sort.banker_mode;
            if(sort.score_type == 7) $scope['selectLbull'+mode] = false;
        }
    $scope.listChange = function(value, bull){
        $scope.createInfo[bull].score_value = value;
        if( bull === 'bull' ){
            $scope.selectBullValue = value;
        }else if( bull === 'bull9' ){
            $scope.selectBull9Value = value;
        }else if( bull === 'tbull' ){
            $scope.selectTbullValue = value;
        }else if( bull === 'fbull' ){
            $scope.selectFbullValue = value;
        }else{
            $scope.selectLbullValue = value;
        }
    }
    $scope.selectChange=function(type,num){
        if($scope.createInfo.isShow==1){
            if(type==1){
                var ind = $scope.createInfo.flower.chip_type.indexOf(num);
                if(ind === -1 && $scope.createInfo.flower.chip_type.length < 4){
                    $scope.createInfo.flower.chip_type.push(num);
                    $scope.createInfo.flower.chip_type.sort(function(a, b){return a - b;});
                } else if(ind !== -1){
                    $scope.createInfo.flower.chip_type.splice(ind, 1);
                }
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.flower.pkvalue1 == 0) {
                        $scope.createInfo.flower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.flower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.flower.pkvalue2 == 0) {
                        $scope.createInfo.flower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.flower.pkvalue2 = 0;
                    }
                }

            }
            else if(type==3){
                $scope.createInfo.flower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.flower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.flower.seen=num;
            }
            else if(type === 7){
                $scope.createInfo.flower.raceCard = !$scope.createInfo.flower.raceCard;
            }
            else if(type === 9){
                $scope.createInfo.flower.extraRewards = num;
            }
            else if(type === 10){
                $scope.createInfo.flower.default_score = num;
            }
            else if(type === 11){
                $scope.createInfo.flower.allow235GTPanther = (parseInt($scope.createInfo.flower.allow235GTPanther) + 1)%2;
            }
        }
        else if($scope.createInfo.isShow==111){
            if(type==1){
                var ind = $scope.createInfo.bflower.chip_type.indexOf(num);
                if(ind === -1 && $scope.createInfo.bflower.chip_type.length < 4){
                    $scope.createInfo.bflower.chip_type.push(num);
                    $scope.createInfo.bflower.chip_type.sort(function(a, b){return a - b;});
                } else if(ind !== -1){
                    $scope.createInfo.bflower.chip_type.splice(ind, 1);
                }
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.bflower.pkvalue1 == 0) {
                        $scope.createInfo.bflower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.bflower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.bflower.pkvalue2 == 0) {
                        $scope.createInfo.bflower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.bflower.pkvalue2 = 0;
                    }
                }
            }
            else if(type==3){
                $scope.createInfo.bflower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.bflower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.bflower.seen=num;
            }
            else if(type == 7){
                $scope.createInfo.bflower.raceCard = !$scope.createInfo.bflower.raceCard;
            }
            else if(type == 9){
                $scope.createInfo.bflower.extraRewards = num;
            }
            else if(type == 10){
                $scope.createInfo.bflower.default_score = num;
            }
        }
        else if($scope.createInfo.isShow==2){
            if (type==1) {
                $scope.createInfo.landlord.base_score = num;
            } else if (type==2) {
                $scope.createInfo.landlord.ask_mode = num;
            } else if(type==3) {
                $scope.createInfo.landlord.ticket_count = num;
            }
        }
        else if($scope.createInfo.isShow==3){
            if(type==1){
                if(num === 7){
                    $scope.createInfo.bull.score_type=num;
                    $scope['selectBull'+$scope.createInfo.bull.banker_mode] = false;
                }else{
                    $scope.createInfo.bull.score_type=num;
                    $scope['selectBull'+$scope.createInfo.bull.banker_mode] = true;
                }
            }
            else if(type==2){
                $scope.createInfo.bull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.bull.is_cardfive=($scope.createInfo.bull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.bull.is_straight=($scope.createInfo.bull.is_straight+1)%2;
                else if(num==3)
                    $scope.createInfo.bull.is_flush=($scope.createInfo.bull.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.bull.is_hulu=($scope.createInfo.bull.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.bull.is_cardbomb=($scope.createInfo.bull.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.bull.is_cardtiny=($scope.createInfo.bull.is_cardtiny+1)%2;
                else if(num==7)
                    $scope.createInfo.bull.is_straightflush=($scope.createInfo.bull.is_straightflush+1)%2;
                else if(num==8)
                    $scope.createInfo.bull.has_ghost=($scope.createInfo.bull.has_ghost+1)%2;
                else if(num==9)
                    $scope.createInfo.bull.is_cardfour=($scope.createInfo.bull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.bull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.bull.banker_score=num;
            } else if (type == 6) {
                $scope.createInfo.bull.times_type=num;
            }
        }
        else if($scope.createInfo.isShow==4){
            if(type==1){
                $scope.createInfo.majiang.joker=num;
            }
            else if(type==2){
                $scope.createInfo.majiang.horse_count=num;
            }
            else if(type==3){
                $scope.createInfo.majiang.qianggang=($scope.createInfo.majiang.qianggang+1)%2;
            }
            else if(type==4){
                $scope.createInfo.majiang.ticket_count=num;
            }
            else if(type==5){
                $scope.createInfo.majiang.chengbao=($scope.createInfo.majiang.chengbao+1)%2;
            }
        }
        else if($scope.createInfo.isShow==5){
            if(type==1){
                if(num === 7){
                    $scope.createInfo.bull9.score_type=num;
                    $scope['selectBull9'+$scope.createInfo.bull9.banker_mode] = false;
                }else{
                    $scope.createInfo.bull9.score_type=num;
                    $scope['selectBull9'+$scope.createInfo.bull9.banker_mode] = true;
                }
            }
            else if(type==2){
                $scope.createInfo.bull9.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.bull9.is_cardfive=($scope.createInfo.bull9.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.bull9.is_straight=($scope.createInfo.bull9.is_straight+1)%2;
                else if(num==3)
                    $scope.createInfo.bull9.is_flush=($scope.createInfo.bull9.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.bull9.is_hulu=($scope.createInfo.bull9.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.bull9.is_cardbomb=($scope.createInfo.bull9.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.bull9.is_cardtiny=($scope.createInfo.bull9.is_cardtiny+1)%2;
                else if(num==7)
                    $scope.createInfo.bull9.is_straightflush=($scope.createInfo.bull9.is_straightflush+1)%2;
                else if(num==8)
                    $scope.createInfo.bull9.has_ghost=($scope.createInfo.bull9.has_ghost+1)%2;
                else if(num==9)
                    $scope.createInfo.bull9.is_cardfour=($scope.createInfo.bull9.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.bull9.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.bull9.banker_score=num;
            } else if (type == 6) {
                $scope.createInfo.bull9.times_type=num;
            }
        }
        else if($scope.createInfo.isShow==90&&$scope.createInfo.vbf.game==91){
            if(type==1){
                $scope.createInfo.vbull9.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.vbull9.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.vbull9.is_cardfive=($scope.createInfo.vbull9.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.vbull9.is_cardbomb=($scope.createInfo.vbull9.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.vbull9.is_cardtiny=($scope.createInfo.vbull9.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.vbull9.is_cardfour=($scope.createInfo.vbull9.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.vbull9.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.vbull9.times_type=num;
            } else if (type == 6) {
                $scope.createInfo.vbull9.bean_type=num;
            }
        }
        else if($scope.createInfo.isShow==90&&$scope.createInfo.vbf.game==93){
            if(type==1){
                $scope.createInfo.vbull6.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.vbull6.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.vbull6.is_cardfive=($scope.createInfo.vbull6.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.vbull6.is_cardbomb=($scope.createInfo.vbull6.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.vbull6.is_cardtiny=($scope.createInfo.vbull6.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.vbull6.is_cardfour=($scope.createInfo.vbull6.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.vbull6.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.vbull6.times_type=num;
            } else if (type == 6) {
                $scope.createInfo.vbull6.bean_type=num;
            }
        }
        else if($scope.createInfo.isShow==90&&$scope.createInfo.vbf.game==94){
            if(type==1){
                $scope.createInfo.vbull12.score_type=num;
            }
            else if(type==2){
                $scope.createInfo.vbull12.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.vbull12.is_cardfive=($scope.createInfo.vbull12.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.vbull12.is_cardbomb=($scope.createInfo.vbull12.is_cardbomb+1)%2;
                else if(num==3)
                    $scope.createInfo.vbull12.is_cardtiny=($scope.createInfo.vbull12.is_cardtiny+1)%2;
                else if(num==9)
                    $scope.createInfo.vbull12.is_cardfour=($scope.createInfo.vbull12.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.vbull12.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.vbull12.times_type=num;
            } else if (type == 6) {
                $scope.createInfo.vbull12.bean_type=num;
            }
        }
        else if($scope.createInfo.isShow==90&&$scope.createInfo.vbf.game==92){
            if(type==1){
                $scope.createInfo.vflower6.chip_type=num;
            }
            else if(type==2){
                if (num == 1) {
                    if ($scope.createInfo.vflower6.pkvalue1 == 0) {
                        $scope.createInfo.vflower6.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.vflower6.pkvalue1 = 0;
                    }
                }
            }
            else if(type==3){
                $scope.createInfo.vflower6.seen=num;
            }
            else if(type==4){
                $scope.createInfo.vflower6.ticket_count=num;
            }
            else if(type==5){
                $scope.createInfo.vflower6.upper_limit=num;
            }
            else if(type==6){
                $scope.createInfo.vflower6.bean_type=num;
            }
        }
        else if($scope.createInfo.isShow==90&&$scope.createInfo.vbf.game==95){
            if(type==1){
                $scope.createInfo.vflower10.chip_type=num;
            }
            else if(type==2){
                if (num == 1) {
                    if ($scope.createInfo.vflower10.pkvalue1 == 0) {
                        $scope.createInfo.vflower10.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.vflower10.pkvalue1 = 0;
                    }
                }
            }
            else if(type==3){
                $scope.createInfo.vflower10.seen=num;
            }
            else if(type==4){
                $scope.createInfo.vflower10.ticket_count=num;
            }
            else if(type==5){
                $scope.createInfo.vflower10.upper_limit=num;
            }
            else if(type==6){
                $scope.createInfo.vflower10.bean_type=num;
            }
        }
        else if($scope.createInfo.isShow==8){
            if(type==1){
                if(num === 7){
                    $scope.createInfo.tbull.score_type=num;
                    $scope['selectTbull'+$scope.createInfo.tbull.banker_mode] = false;
                }else{
                    $scope.createInfo.tbull.score_type=num;
                    $scope['selectTbull'+$scope.createInfo.tbull.banker_mode] = true;
                }
            }
            else if(type==2){
                $scope.createInfo.tbull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.tbull.is_cardfive=($scope.createInfo.tbull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.tbull.is_straight=($scope.createInfo.tbull.is_straight+1)%2;
                else if(num==3)
                    $scope.createInfo.tbull.is_flush=($scope.createInfo.tbull.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.tbull.is_hulu=($scope.createInfo.tbull.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.tbull.is_cardbomb=($scope.createInfo.tbull.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.tbull.is_cardtiny=($scope.createInfo.tbull.is_cardtiny+1)%2;
                else if(num==7)
                    $scope.createInfo.tbull.is_straightflush=($scope.createInfo.tbull.is_straightflush+1)%2;
                else if(num==9)
                    $scope.createInfo.tbull.is_cardfour=($scope.createInfo.tbull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.tbull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.tbull.banker_score=num;
            } else if (type == 6) {
                $scope.createInfo.tbull.times_type=num;
            }
        }
        else if($scope.createInfo.isShow==9){
            if(type==1){
                if(num === 7){
                    $scope.createInfo.fbull.score_type=num;
                    $scope['selectFbull'+$scope.createInfo.fbull.banker_mode] = false;
                }else{
                    $scope.createInfo.fbull.score_type=num;
                    $scope['selectFbull'+$scope.createInfo.fbull.banker_mode] = true;
                }
            }
            else if(type==2){
                $scope.createInfo.fbull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.fbull.is_cardfive=($scope.createInfo.fbull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.fbull.is_straight=($scope.createInfo.fbull.is_straight+1)%2;
                else if(num==3)
                    $scope.createInfo.fbull.is_flush=($scope.createInfo.fbull.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.fbull.is_hulu=($scope.createInfo.fbull.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.fbull.is_cardbomb=($scope.createInfo.fbull.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.fbull.is_cardtiny=($scope.createInfo.fbull.is_cardtiny+1)%2;
                else if(num==7)
                    $scope.createInfo.fbull.is_straightflush=($scope.createInfo.fbull.is_straightflush+1)%2;
                else if(num==9)
                    $scope.createInfo.fbull.is_cardfour=($scope.createInfo.fbull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.fbull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.fbull.banker_score=num;
            } else if (type == 6) {
                $scope.createInfo.fbull.times_type=num;
            }
        }
        else if($scope.createInfo.isShow==71){
            if(type==1){
                if(num === 7){
                    $scope.createInfo.lbull.score_type=num;
                    $scope['selectLbull'+$scope.createInfo.lbull.banker_mode] = false;
                }else{
                    $scope.createInfo.lbull.score_type=num;
                    $scope['selectLbull'+$scope.createInfo.lbull.banker_mode] = true;
                }
            }
            else if(type==2){
                $scope.createInfo.lbull.rule_type=num;
            }
            else if(type==3){
                if(num==1)
                    $scope.createInfo.lbull.is_cardfive=($scope.createInfo.lbull.is_cardfive+1)%2;
                else if(num==2)
                    $scope.createInfo.lbull.is_straight=($scope.createInfo.lbull.is_straight+1)%2;
                else if(num==3)
                    $scope.createInfo.lbull.is_flush=($scope.createInfo.lbull.is_flush+1)%2;
                else if(num==4)
                    $scope.createInfo.lbull.is_hulu=($scope.createInfo.lbull.is_hulu+1)%2;
                else if(num==5)
                    $scope.createInfo.lbull.is_cardbomb=($scope.createInfo.lbull.is_cardbomb+1)%2;
                else if(num==6)
                    $scope.createInfo.lbull.is_cardtiny=($scope.createInfo.lbull.is_cardtiny+1)%2;
                else if(num==7)
                    $scope.createInfo.lbull.is_straightflush = ($scope.createInfo.lbull.is_straightflush+1)%2;
                else if(num==9)
                    $scope.createInfo.lbull.is_cardfour = ($scope.createInfo.lbull.is_cardfour+1)%2;
            }
            else if(type==4){
                $scope.createInfo.lbull.ticket_type=num;
            } else if (type == 5) {
                $scope.createInfo.lbull.banker_score=num;
            } else if (type == 6) {
                $scope.createInfo.lbull.times_type=num;
            }
        }
        else if($scope.createInfo.isShow==110){
            if(type==1){
                var ind = $scope.createInfo.tflower.chip_type.indexOf(num);
                if(ind === -1 && $scope.createInfo.tflower.chip_type.length < 4){
                    $scope.createInfo.tflower.chip_type.push(num);
                    $scope.createInfo.tflower.chip_type.sort(function(a, b){return a - b;});
                } else if(ind !== -1){
                    $scope.createInfo.tflower.chip_type.splice(ind, 1);
                }
            }
            else if(type==2){

                if (num == 1) {
                    if ($scope.createInfo.tflower.pkvalue1 == 0) {
                        $scope.createInfo.tflower.pkvalue1 = 1;
                    } else {
                        $scope.createInfo.tflower.pkvalue1 = 0;
                    }
                } else if (num == 2) {
                    if ($scope.createInfo.tflower.pkvalue2 == 0) {
                        $scope.createInfo.tflower.pkvalue2 = 1;
                    } else {
                        $scope.createInfo.tflower.pkvalue2 = 0;
                    }
                }

            }
            else if(type==3){
                $scope.createInfo.tflower.ticket_count=num;
            }
            else if(type==4){
                $scope.createInfo.tflower.upper_limit=num;
            }
            else if(type==5){
                $scope.createInfo.tflower.seen=num;
            }
            else if(type==7){
                $scope.createInfo.tflower.raceCard = !$scope.createInfo.tflower.raceCard;
            }
            else if(type === 9){
                $scope.createInfo.tflower.extraRewards = num;
            }
            else if(type === 10){
                $scope.createInfo.tflower.default_score = num;
            }
            else if(type === 11){
                $scope.createInfo.tflower.allow235GTPanther = (parseInt($scope.createInfo.tflower.allow235GTPanther) + 1)%2;
            }
        }
        else if($scope.createInfo.isShow==36){
            if(type==1){
                $scope.createInfo.sangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.sangong.is_joker = Math.abs($scope.createInfo.sangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.sangong.is_bj = Math.abs($scope.createInfo.sangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.sangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.sangong));
        }
        else if($scope.createInfo.isShow==37){
            if(type==1){
                $scope.createInfo.nsangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.nsangong.is_joker = Math.abs($scope.createInfo.nsangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.nsangong.is_bj = Math.abs($scope.createInfo.nsangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.nsangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.nsangong));
        }
        else if($scope.createInfo.isShow==38){
            if(type==1){
                $scope.createInfo.tsangong.score_type=num;
            }
            else if(type==2){
                if (num==1) {
                    $scope.createInfo.tsangong.is_joker = Math.abs($scope.createInfo.tsangong.is_joker - 1);
                }
                if (num==2) {
                    $scope.createInfo.tsangong.is_bj = Math.abs($scope.createInfo.tsangong.is_bj - 1);
                }
            }
            else if(type==3){
                $scope.createInfo.tsangong.ticket_type=num;
            }
            console.log(JSON.stringify($scope.createInfo.tsangong));
        }
    };
    $scope.showGameList = function(type){
        // $event.stopPropagation();
        $scope.createInfo.isShowGame = 1;//显示游戏列表
        $scope.createInfo.gameList = type;//显示相应的列表游戏
    }
    $scope.hideGameList = function(){
        if($scope.createInfo.isShow<=0) {
            $scope.createInfo.isShowGame = 0;//关闭游戏列表弹框
        }
    }
    $scope.developing = function(){
        $scope.showAlert(1,"该功能正在研发，请耐心等待！");
    }

    $scope.createFlower=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({
            'height':'80vh',
            'padding': '5px 5px',
            'background':'rgba(255,255,255,0.3)',
            'border-radius': '5px'
        });
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'60vh',
            'overflow': 'auto',
            'height': '60vh',
            'background-image':''
        });
        /***************开始********************这一段是用来当再次进入相同游戏中可以保持相同的配置**********************************************/
        var seenValue = $scope.createInfo.flower.seenProgress;
        var compareValue = $scope.createInfo.flower.compareProgress;
        console.log(seenValue);
        console.log(compareValue)
        $scope.createInfo.isShow=1;
        $scope.seenProgressValue = seenValue;
        $scope.compareProgressValue = compareValue;
        setTimeout( function(){
            var seenProgressValue = Math.floor(seenValue/20) + '% 100%';
            var compareProgressValue = Math.floor(compareValue/20) + '% 100%';
            //看牌的进度条
            console.log($('.seenRange'))
            console.log(seenProgressValue)
            console.log(compareProgressValue)
            if(seenValue === 1700){
                $('.seenRange').css({
                    'background-size': '80% 100%'
                })
            }else if((seenValue === 1900) || (seenValue === 1800) ) {
                $('.seenRange').css({
                    'background-size': '85% 100%'
                })
            }else if((seenValue>200) && (seenValue<=600)){
                $('.seenRange').css({
                    'background-size': Math.floor((seenValue+100)/20) + '% 100%'
                })
            }else if((seenValue === 200) || (seenValue === 100)){
                $('.seenRange').css({
                    'background-size': Math.floor((seenValue+200)/20) + '% 100%'
                })
            }else{
                console.log(seenProgressValue)
                $('.seenRange').css({
                    'background-size': seenProgressValue
                })
            }
            //比牌的进度条
            if(compareValue === 1700){
                $('.compareRange').css({
                    'background-size': '80% 100%'
                })
            }else if((compareValue === 1900) || (compareValue === 1800) ) {
                $('.compareRange').css({
                    'background-size': '85% 100%'
                })
            }else if((compareValue>200) && (compareValue<=600)){
                $('.compareRange').css({
                    'background-size': Math.floor((compareValue+100)/20) + '% 100%'
                })
            }else if((compareValue === 200) || (compareValue === 100)){
                $('.compareRange').css({
                    'background-size': Math.floor((compareValue+200)/20) + '% 100%'
                })
            }else{
                $('.compareRange').css({
                    'background-size': compareProgressValue
                })
            }
        }, 0 ) 
        /*************结束********************************************************************************************/
        var $seen = Math.ceil($scope.createInfo.flower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.flower.seen = 0;
        }
    };
    $scope.createBFlower=function($event){   
        $event.stopPropagation();
        $(".createRoom .mainPart").css({
            'height':'80vh',
            'padding': '5px 5px',
            'background':'rgba(255,255,255,0.3)',
            'border-radius': '5px'
        });
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'60vh',
            'overflow': 'auto',
            'height': '60vh',
            'background-image': ''
        });
        var seenValue = $scope.createInfo.bflower.seenProgress;
        var compareValue = $scope.createInfo.bflower.compareProgress;
        $scope.createInfo.isShow=111;
        $scope.seenBigProgressValue = seenValue;
        $scope.compareBigProgressValue = compareValue;
        /************************重新进入游戏保存配置***********************************/
        setTimeout( function(){
            var seenProgressValue = Math.floor(seenValue/20) + '% 100%';
            var compareProgressValue = Math.floor(compareValue/20) + '% 100%';
            //看牌的进度条
            if(seenValue === 1700){
                $('.seenBigRange').css({
                    'background-size': '80% 100%'
                })
            }else if((seenValue === 1900) || (seenValue === 1800) ) {
                $('.seenBigRange').css({
                    'background-size': '85% 100%'
                })
            }else if((seenValue>200) && (seenValue<=600)){
                $('.seenBigRange').css({
                    'background-size': Math.floor((seenValue+100)/20) + '% 100%'
                })
            }else if((seenValue === 200) || (seenValue === 100)){
                $('.seenBigRange').css({
                    'background-size': Math.floor((seenValue+200)/20) + '% 100%'
                })
            }else{
                console.log(seenProgressValue)
                $('.seenBigRange').css({
                    'background-size': seenProgressValue
                })
            }
            //比牌的进度条
            if(compareValue === 1700){
                $('.compareBigRange').css({
                    'background-size': '80% 100%'
                })
            }else if((compareValue === 1900) || (compareValue === 1800) ) {
                $('.compareBigRange').css({
                    'background-size': '85% 100%'
                })
            }else if((compareValue>200) && (compareValue<=600)){
                $('.compareBigRange').css({
                    'background-size': Math.floor((compareValue+100)/20) + '% 100%'
                })
            }else if((compareValue === 200) || (compareValue === 100)){
                $('.compareBigRange').css({
                    'background-size': Math.floor((compareValue+200)/20) + '% 100%'
                })
            }else{
                $('.compareBigRange').css({
                    'background-size': compareProgressValue
                })
            }
        }, 0 ) 
        /*****************************************************************************/

        var $seen = Math.ceil($scope.createInfo.bflower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.bflower.seen = 0;
        }
    };
    $scope.createTenFlower=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({
            'height':'80vh',
            'padding': '5px 5px',
            'background':'rgba(255,255,255,0.3)',
            'border-radius': '5px'
        });
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'60vh',
            'overflow': 'auto',
            'height': '60vh',
            'background-image': ''
        });
        var seenValue = $scope.createInfo.tflower.seenProgress;
        var compareValue = $scope.createInfo.tflower.compareProgress;
        $scope.createInfo.isShow=110;
        $scope.seenTenProgressValue = seenValue;
        $scope.compareTenProgressValue = compareValue;
        /************************重新进入游戏保存配置***********************************/
        setTimeout( function(){
            var seenProgressValue = Math.floor(seenValue/20) + '% 100%';
            var compareProgressValue = Math.floor(compareValue/20) + '% 100%';
            //看牌的进度条
            if(seenValue === 1700){
                $('.seenTenRange').css({
                    'background-size': '80% 100%'
                })
            }else if((seenValue === 1900) || (seenValue === 1800) ) {
                $('.seenTenRange').css({
                    'background-size': '85% 100%'
                })
            }else if((seenValue>200) && (seenValue<=600)){
                $('.seenTenRange').css({
                    'background-size': Math.floor((seenValue+100)/20) + '% 100%'
                })
            }else if((seenValue === 200) || (seenValue === 100)){
                $('.seenTenRange').css({
                    'background-size': Math.floor((seenValue+200)/20) + '% 100%'
                })
            }else{
                console.log(seenProgressValue)
                $('.seenTenRange').css({
                    'background-size': seenProgressValue
                })
            }
            //比牌的进度条
            if(compareValue === 1700){
                $('.compareTenRange').css({
                    'background-size': '80% 100%'
                })
            }else if((compareValue === 1900) || (compareValue === 1800) ) {
                $('.compareTenRange').css({
                    'background-size': '85% 100%'
                })
            }else if((compareValue>200) && (compareValue<=600)){
                $('.compareTenRange').css({
                    'background-size': Math.floor((compareValue+100)/20) + '% 100%'
                })
            }else if((compareValue === 200) || (compareValue === 100)){
                $('.compareTenRange').css({
                    'background-size': Math.floor((compareValue+200)/20) + '% 100%'
                })
            }else{
                $('.compareTenRange').css({
                    'background-size': compareProgressValue
                })
            }
        }, 0 ) 
        /*****************************************************************************/
        var $seen = Math.ceil($scope.createInfo.tflower.seen);
        if ($seen != 0 && $seen != 20 && $seen != 50 && $seen != 100) {
            $scope.createInfo.tflower.seen = 0;
        }
    };
    $scope.createLandlord=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=2;
    }
    $scope.createBull=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow=3;
        $times_type=Math.ceil($scope.createInfo.bull.times_type);
        if ($times_type!=1&&$times_type!=2&&$times_type!=3) {
            $scope.createInfo.bull.times_type=1;
        }
    }
    $scope.createMajiang=function(){
        $(".createRoom .mainPart").css('height','');
        $(".createRoom .mainPart .blueBack").css('height','');
        $scope.createInfo.isShow=4;
    }
    $scope.createBull9=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow=5;
        $times_type=Math.ceil($scope.createInfo.bull9.times_type);
        if ($times_type!=1&&$times_type!=2&&$times_type!=3) {
            $scope.createInfo.bull9.times_type=1;
        }
    };
    $scope.createVBF=function(){
        $scope.createInfo.isShow=90;
        if (typeof($scope.createInfo.vbf.game93) === 'undefined') {
            $scope.createInfo.vbf.game93 = 'unselected';
        }
        if (typeof($scope.createInfo.vbf.game94) === 'undefined') {
            $scope.createInfo.vbf.game94 = 'unselected';
        }
        if (typeof($scope.createInfo.vbf.game95) === 'undefined') {
            $scope.createInfo.vbf.game95 = 'unselected';
        }
        $(".createRoom .mainPart").css('height','84vh');
        $(".createRoom .mainPart .blueBack").css('height','64vh');
    };
    $scope.createBull13=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow=9;
        $times_type=Math.ceil($scope.createInfo.fbull.times_type);
        if ($times_type!=1&&$times_type!=2&&$times_type!=3) {
            $scope.createInfo.fbull.times_type=1;
        }
    };
    $scope.createBull12=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow=8;
        $times_type=Math.ceil($scope.createInfo.tbull.times_type);
        if ($times_type!=1&&$times_type!=2&&$times_type!=3) {
            $scope.createInfo.tbull.times_type=1;
        }
    };
    $scope.createSangong = function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow = 36;
    };
    $scope.createNSangong = function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow = 37;
    };
    $scope.createTSangong = function(){
        $(".createRoom .mainPart").css('height','50.5vh');
        $(".createRoom .mainPart .blueBack").css('height','30vh');
        $scope.createInfo.isShow = 38;
    };
    $scope.createLBull=function($event){
        $event.stopPropagation();
        $(".createRoom .mainPart").css({'height':'80vh',
        'padding': '5px 5px',
        'background':'rgba(255,255,255,0.3)',
        'border-radius': '5px'});
        $(".createRoom .mainPart .blueBack").css({
            'max-height':'54vh',
            'overflow': 'auto',
            'height': '54vh',
            'background-image': 'url('+image_url+'files/images/common/bullInnerborder.png)'
        });
        $scope.createInfo.isShow=71;
        $times_type=Math.ceil($scope.createInfo.lbull.times_type);
        if ($times_type!=1&&$times_type!=2&&$times_type!=3) {
            $scope.createInfo.lbull.times_type=1;
        }
    };
    $scope.selectBankerMode = function (type) {
        if (type == 1) {
            $scope.createInfo.bull.score_type = 1;
            $scope.selectBull1 = true;
            $scope.createInfo.bull.banker1 = "selected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.bull.score_type = 1;
            $scope.selectBull2 = true;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "selected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.bull.score_type = 1;
            $scope.selectBull3 = true;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "selected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.bull.score_type = 4;
            $scope.selectBull4 = true;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "selected";
            $scope.createInfo.bull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.bull.score_type = 1;
            $scope.selectBull5 = true;
            $scope.createInfo.bull.banker1 = "unselected";
            $scope.createInfo.bull.banker2 = "unselected";
            $scope.createInfo.bull.banker3 = "unselected";
            $scope.createInfo.bull.banker4 = "unselected";
            $scope.createInfo.bull.banker5 = "selected";
        }

        $scope.createInfo.bull.banker_mode = type;
    };
    $scope.selectBankerMode9 = function (type) {
        if (type == 1) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.selectBull91 = true;
            $scope.createInfo.bull9.banker1 = "selected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.selectBull92 = true;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "selected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.selectBull93 = true;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "selected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.bull9.score_type = 4;
            $scope.selectBull94 = true;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "selected";
            $scope.createInfo.bull9.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.bull9.score_type = 1;
            $scope.selectBull95 = true;
            $scope.createInfo.bull9.banker1 = "unselected";
            $scope.createInfo.bull9.banker2 = "unselected";
            $scope.createInfo.bull9.banker3 = "unselected";
            $scope.createInfo.bull9.banker4 = "unselected";
            $scope.createInfo.bull9.banker5 = "selected";
        }

        $scope.createInfo.bull9.banker_mode = type;
    };
    $scope.selectVBF = function (type) {
        if (type == 91) {
            $scope.createInfo.vbf.game = type;
            $scope.createInfo.vbf.game91 = "selected";
            $scope.createInfo.vbf.game92 = "unselected";
            $scope.createInfo.vbf.game93 = "unselected";
            $scope.createInfo.vbf.game94 = "unselected";
            $scope.createInfo.vbf.game95 = "unselected";
        } else if (type == 92) {
            $scope.createInfo.vbf.game = type;
            $scope.createInfo.vbf.game91 = "unselected";
            $scope.createInfo.vbf.game92 = "selected";
            $scope.createInfo.vbf.game93 = "unselected";
            $scope.createInfo.vbf.game94 = "unselected";
            $scope.createInfo.vbf.game95 = "unselected";
        } else if (type == 93) {
            $scope.createInfo.vbf.game = type;
            $scope.createInfo.vbf.game91 = "unselected";
            $scope.createInfo.vbf.game92 = "unselected";
            $scope.createInfo.vbf.game93 = "selected";
            $scope.createInfo.vbf.game94 = "unselected";
            $scope.createInfo.vbf.game95 = "unselected";
        } else if (type == 94) {
            $scope.createInfo.vbf.game = type;
            $scope.createInfo.vbf.game91 = "unselected";
            $scope.createInfo.vbf.game92 = "unselected";
            $scope.createInfo.vbf.game93 = "unselected";
            $scope.createInfo.vbf.game94 = "selected";
            $scope.createInfo.vbf.game95 = "unselected";
        } else if (type == 95) {
            $scope.createInfo.vbf.game = type;
            $scope.createInfo.vbf.game91 = "unselected";
            $scope.createInfo.vbf.game92 = "unselected";
            $scope.createInfo.vbf.game93 = "unselected";
            $scope.createInfo.vbf.game94 = "unselected";
            $scope.createInfo.vbf.game95 = "selected";
        }
    };
    $scope.selectBankerTBull = function (type) {
        if (type == 1) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.selectTbull1 = true;
            $scope.createInfo.tbull.banker1 = "selected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.selectTbull2 = true;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "selected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.selectTbull3 = true;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "selected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.tbull.score_type = 4;
            $scope.selectTbull4 = true;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "selected";
            $scope.createInfo.tbull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.tbull.score_type = 1;
            $scope.selectTbull5 = true;
            $scope.createInfo.tbull.banker1 = "unselected";
            $scope.createInfo.tbull.banker2 = "unselected";
            $scope.createInfo.tbull.banker3 = "unselected";
            $scope.createInfo.tbull.banker4 = "unselected";
            $scope.createInfo.tbull.banker5 = "selected";
        }

        $scope.createInfo.tbull.banker_mode = type;
    };
    $scope.selectBankerFBull = function (type) {
        if (type == 1) {
            $scope.createInfo.fbull.score_type = 1;
            $scope.selectTbull1 = true;
            $scope.createInfo.fbull.banker1 = "selected";
            $scope.createInfo.fbull.banker2 = "unselected";
            $scope.createInfo.fbull.banker3 = "unselected";
            $scope.createInfo.fbull.banker4 = "unselected";
            $scope.createInfo.fbull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.fbull.score_type = 1;
            $scope.selectTbull2 = true;
            $scope.createInfo.fbull.banker1 = "unselected";
            $scope.createInfo.fbull.banker2 = "selected";
            $scope.createInfo.fbull.banker3 = "unselected";
            $scope.createInfo.fbull.banker4 = "unselected";
            $scope.createInfo.fbull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.fbull.score_type = 1;
            $scope.selectTbull3 = true;
            $scope.createInfo.fbull.banker1 = "unselected";
            $scope.createInfo.fbull.banker2 = "unselected";
            $scope.createInfo.fbull.banker3 = "selected";
            $scope.createInfo.fbull.banker4 = "unselected";
            $scope.createInfo.fbull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.fbull.score_type = 4;
            $scope.selectTbull4 = true;
            $scope.createInfo.fbull.banker1 = "unselected";
            $scope.createInfo.fbull.banker2 = "unselected";
            $scope.createInfo.fbull.banker3 = "unselected";
            $scope.createInfo.fbull.banker4 = "selected";
            $scope.createInfo.fbull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.fbull.score_type = 1;
            $scope.selectTbull5 = true;
            $scope.createInfo.fbull.banker1 = "unselected";
            $scope.createInfo.fbull.banker2 = "unselected";
            $scope.createInfo.fbull.banker3 = "unselected";
            $scope.createInfo.fbull.banker4 = "unselected";
            $scope.createInfo.fbull.banker5 = "selected";
        }

        $scope.createInfo.fbull.banker_mode = type;
    };
    $scope.selectBankerSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.sangong.banker1 = "selected";
            $scope.createInfo.sangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.sangong.banker1 = "unselected";
            $scope.createInfo.sangong.banker2 = "selected";
        }
        $scope.createInfo.sangong.banker_mode = type;
    };
    $scope.selectBankerNSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.nsangong.banker1 = "selected";
            $scope.createInfo.nsangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.nsangong.banker1 = "unselected";
            $scope.createInfo.nsangong.banker2 = "selected";
        }
        $scope.createInfo.nsangong.banker_mode = type;
    };
    $scope.selectBankerTSangong = function (type) {
        if (type == 1) {
            $scope.createInfo.tsangong.banker1 = "selected";
            $scope.createInfo.tsangong.banker2 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.tsangong.banker1 = "unselected";
            $scope.createInfo.tsangong.banker2 = "selected";
        }
        $scope.createInfo.tsangong.banker_mode = type;
    };
    $scope.selectBankerLBull = function (type) {
        if (type == 1) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.selectLbull1 = true;
            $scope.createInfo.lbull.banker1 = "selected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 2) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.selectLbull2 = true;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "selected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 3) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.selectLbull3 = true;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "selected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 4) {
            $scope.createInfo.lbull.score_type = 4;
            $scope.selectLbull4 = true;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "selected";
            $scope.createInfo.lbull.banker5 = "unselected";
        } else if (type == 5) {
            $scope.createInfo.lbull.score_type = 1;
            $scope.selectLbull5 = true;
            $scope.createInfo.lbull.banker1 = "unselected";
            $scope.createInfo.lbull.banker2 = "unselected";
            $scope.createInfo.lbull.banker3 = "unselected";
            $scope.createInfo.lbull.banker4 = "unselected";
            $scope.createInfo.lbull.banker5 = "selected";
        }

        $scope.createInfo.lbull.banker_mode = type;
    };
    $scope.createCommit = function () {
        if ($scope.userInfo.card>0){
            if($scope.is_operation){
                return 0;
            }

            $scope.waiting();
            //$scope.createHttpRoom();
            storeSetting($scope.createInfo);
            $http({method:'POST', url:'/f/ci', data: {'account_id': accountId,'create_info': $scope.createInfo}}).then(function (res) {console.log(res);});

            //socket创建房间，暂时废弃
            if($scope.createInfo.isShow==1){
                $scope.connectSocket($scope.socket.flower,1);
            }
            else if($scope.createInfo.isShow==111){
                $scope.connectSocket($scope.socket.bflower,111);
            }
            else if($scope.createInfo.isShow==2){
                $scope.connectSocket($scope.socket.landlord,2);
            }
            else if($scope.createInfo.isShow==3){
                $scope.connectSocket($scope.socket.bull,3);
            }
            else if($scope.createInfo.isShow==4){
                $scope.connectSocket($scope.socket.majiang,4);
            }
            else if($scope.createInfo.isShow==5){
                $scope.connectSocket($scope.socket.bull9,5);
            }
            else if($scope.createInfo.isShow==90){
                if ($scope.createInfo.vbf.game==91) {
                    $scope.connectSocket($scope.socket.vbull9,91);
                } else if ($scope.createInfo.vbf.game==93) {
                    $scope.connectSocket($scope.socket.vbull6,93);
                } else if ($scope.createInfo.vbf.game==94) {
                    $scope.connectSocket($scope.socket.vbull12,94);
                } else if ($scope.createInfo.vbf.game==92) {
                    $scope.connectSocket($scope.socket.vflower6,92);
                } else if ($scope.createInfo.vbf.game==95) {
                    $scope.connectSocket($scope.socket.vflower10,95);
                }
            }
            else if($scope.createInfo.isShow==8){
                $scope.connectSocket($scope.socket.tbull,8);
            }
            else if($scope.createInfo.isShow==9){
                $scope.connectSocket($scope.socket.fbull,9);
            }
            else if($scope.createInfo.isShow==110){
                $scope.connectSocket($scope.socket.tflower,110);
            }
            else if($scope.createInfo.isShow==36){
                $scope.connectSocket($scope.socket.sangong,36);
            }
            else if($scope.createInfo.isShow==37){
                $scope.connectSocket($scope.socket.nsangong,37);
            }
            else if($scope.createInfo.isShow==38){
                $scope.connectSocket($scope.socket.tsangong,38);
            }
            else if($scope.createInfo.isShow==71){
                $scope.connectSocket($scope.socket.lbull,71);
            }

        }
        else{
            $scope.showAlert(1,"房卡不足");
        }
    };
    $scope.cancelCreate=function(){
        $scope.createInfo.isShow=0;
        $scope.createInfo.isShowGame=0;
        document.body.style.overflow='';
    };
});
//手机绑定******
function checkPhone(phone) {
    if (!(/^1\d{10}$/.test(phone))) {
        return false;
    } else {
        return true;
    }
}
function checkAuthcode(code) {
    if (code == '' || code == undefined) {
        return false;
    }

    var reg = new RegExp("^[0-9]*$");
    if (!reg.test(code)) {
        return false;
    } else {
        return true;
    }
}
//******手机绑定
function randomString(len) {
    len = len || 32;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function storeSetting(createInfo) {
    localStorage.createInfo = JSON.stringify(createInfo);
}

// 将obj里面属性值赋值给createInfo, 不直接赋值给createInfo防止添加新配置的时候，创建不了房间
function cloneObj (createInfo, obj) {
    for (var key in createInfo) {
        if (createInfo[key] instanceof Object && obj[key] instanceof Object) {
            cloneObj(createInfo[key], obj[key])
        } else {
            createInfo[key] = obj[key] !== undefined? obj[key]: createInfo[key];
        }
    }
}

function getSetting(default_setting) {
    var createInfo = localStorage.createInfo;
    if(createInfo){
        createInfo = JSON.parse(createInfo);
        if(typeof(createInfo.bull) !== "undefined"){
            cloneObj(default_setting.bull, createInfo.bull);
        }
        if(typeof(createInfo.bull9) !== "undefined"){
            cloneObj(default_setting.bull9, createInfo.bull9);
        }
        if(typeof(createInfo.tbull) !== "undefined"){
            cloneObj(default_setting.tbull, createInfo.tbull);
        }
        if(typeof(createInfo.fbull) !== "undefined"){
            cloneObj(default_setting.fbull, createInfo.fbull);
        }
        if(typeof(createInfo.tbull8x) !== "undefined"){
            cloneObj(default_setting.tbull8x, createInfo.tbull8x);
        }
        if(typeof(createInfo.tflower) !== "undefined"){
            cloneObj(default_setting.tflower, createInfo.tflower);
            if((typeof default_setting.tflower.chip_type).toLowerCase() != 'object' || default_setting.tflower.chip_type.indexOf(0) !== -1){
                default_setting.tflower.chip_type = [];
            }
        }
        if(typeof(createInfo.sangong) !== "undefined"){
            cloneObj(default_setting.sangong, createInfo.sangong);
        }
        if(typeof(createInfo.nsangong) !== "undefined"){
            cloneObj(default_setting.nsangong, createInfo.nsangong);
        }
        if(typeof(createInfo.tsangong) !== "undefined"){
            cloneObj(default_setting.tsangong, createInfo.tsangong);
        }
        if(typeof(createInfo.lbull) !== "undefined"){
            cloneObj(default_setting.lbull, createInfo.lbull);
        }
        if(typeof(createInfo.flower) !== "undefined"){
            cloneObj(default_setting.flower, createInfo.flower);
            if((typeof default_setting.flower.chip_type).toLowerCase() != 'object' || default_setting.flower.chip_type.indexOf(0) !== -1){
                default_setting.flower.chip_type = [];
            }
        }
        if(typeof(createInfo.bflower) !== "undefined"){
            cloneObj(default_setting.bflower, createInfo.bflower);
            if((typeof default_setting.bflower.chip_type).toLowerCase() != 'object' || default_setting.bflower.chip_type.indexOf(0) !== -1){
                default_setting.bflower.chip_type = [];
            }
        }
        if(typeof(createInfo.vbf) !== "undefined"){
            cloneObj(default_setting.vbf, createInfo.vbf);
        }
        if(typeof(createInfo.vbull6) !== "undefined"){
            cloneObj(default_setting.vbull6, createInfo.vbull6);
        }
        if(typeof(createInfo.vbull9) !== "undefined"){
            cloneObj(default_setting.vbull9, createInfo.vbull9);
        }
        if(typeof(createInfo.vbull12) !== "undefined"){
            cloneObj(default_setting.vbull12, createInfo.vbull12);
        }
        if(typeof(createInfo.vflower6) !== "undefined"){
            cloneObj(default_setting.vflower6, createInfo.vflower6);
        }
        if(typeof(createInfo.vflower10) !== "undefined"){
            cloneObj(default_setting.vflower10, createInfo.vflower10);
        }
    }
    return default_setting;
}
