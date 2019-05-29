<!DOCTYPE html>
<html>
<head>
    <title>Web sockets test</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf8">

    <script type="text/javascript">
      var ws;
      var host_addr = "";
      var online_count = "";

      var SocketCreated = false;
      function WSonOpen() {
        console.log("连接成功");
        SocketCreated = true;

        obj = {
          operation:"Observer",
          session:"",
          account_id:99999,
          data:{
          }
        };
        msg = JSON.stringify(obj)
        ws.send(msg + "\n");
        
      };
      function WSonClose() {
          console.log("断开连接");
          SocketCreated = false;
      };

      function WSonMessage(event) {
          console.log(event.data); 

          obj = eval('(' + event.data + ')'); 
          if(obj.operation == 'Observer'){
            document.getElementById(online_count).value=obj.data.client_count;
          } 

          console.log("socket关闭");
          ws.close();
      };

      function WSonError() {
        console.log("WSonError");
      };

      function observerClicked() {  //观战

        if(!SocketCreated){
          try {
              if ("WebSocket" in window) {
                ws = new WebSocket(host_addr);
              } else if("MozWebSocket" in window) {
                ws = new MozWebSocket(host_addr);
              }
              ws.onopen = WSonOpen;
              ws.onmessage = WSonMessage;
              ws.onclose = WSonClose;
              ws.onerror = WSonError;
              
              SocketCreated = true;
          } catch (ex) {
              console.log("创建ws失败");
              return;
          }
        } 

      };

      function py2(){
        host_addr = "wss://friendgame.kgtouzi.com:10012";
        online_count = "py2";
        observerClicked();
      }
      function py9(){
        host_addr = "wss://friendgame.kgtouzi.com:10042";
        online_count = "py9";
        observerClicked();
      }

      function cw1(){
        host_addr = "wss://qinghualiuxue.cn:20002";
        online_count = "cw1";
        observerClicked();
      }
      function cw2(){
        host_addr = "wss://qinghualiuxue.cn:20012";
        online_count = "cw2";
        observerClicked();
      }
      function cw5(){
        host_addr = "wss://qinghualiuxue.cn:20022";
        online_count = "cw5";
        observerClicked();
      }
      function cw9(){
        host_addr = "wss://qinghualiuxue.cn:20042";
        online_count = "cw9";
        observerClicked();
      }
      function total(){
        var x = document.getElementsByTagName("input");
        for (var i = x.length - 1; i >= 0; i--) {
          x[i].value="";
        }
        py2();
        setTimeout("py9()", 500);
        setTimeout("cw1()", 1000);
         setTimeout("cw2()",1500);
          setTimeout("cw5()",2000);
           setTimeout("cw9()", 2500);
      }
      
    </script>
</head>
<body>
<div><button type="button" onclick='py2();'>py002</button><input type="text" id="py2" value="" /></div>
<div><button type="button" onclick='py9();'>py009</button><input type="text" id="py9" value="" /></div>
<br/>
<div><button type="button" onclick='cw1();'>cw001</button><input type="text" id="cw1" value="" /></div>
<div><button type="button" onclick='cw2();'>cw002</button><input type="text" id="cw2" value="" /></div>
<div><button type="button" onclick='cw5();'>cw005</button><input type="text" id="cw5" value="" /></div>
<div><button type="button" onclick='cw9();'>cw009</button><input type="text" id="cw9" value="" /></div>

<br/>
<div><button type="button" onclick='total();'>go</button></div>
      
</body> 
   
</html>