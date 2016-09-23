<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport"
	content="initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="format-detection" content="telephone=no">
<title>聊天室</title>
<link rel="stylesheet" href="./css/frozen.css">
<link rel="stylesheet" href="./css/bootstrap.min.css">
<link rel="stylesheet" href="./css/chat.css">
</head>
<body ontouchstart="">
	<header class="ui-header ui-header-positive ui-border-b">
		<h1>聊天室</h1>
	</header>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
     <div class="modal-dialog">
        <div class="modal-content">
           <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">
                 请输入你的名字
              </h4>
           </div>
           <div class="modal-body">
              <input id="nickname" type="text" class="form-control" />
           </div>
           <div class="modal-footer">
              <button type="button" id="set-name" class="btn btn-primary">
                 确定
              </button>
           </div>
        </div><!-- /.modal-content -->
     </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

	<section class="ui-container" id="content">
		<ul class="ui-list-text border-list ui-border-tb">
		</ul>
	</section>

	<footer class="footer">
		<section class="ui-input-wrap ui-border-t">
			<div class="ui-input ui-border-radius">
				<input type="text" name="" value="" placeholder="我也说一句..." id="input">
			</div>
			<button class="ui-btn" id="submit">发送</button>
		</section>
	</footer>

  
  <script src="./js/jquery-1.11.3.min.js"></script>
	<script src="./js/zepto.min.js"></script>
	<script src="./js/frozen.js"></script>
  <script src="./js/bootstrap.min.js"></script>
	<script>
		$(function() {
       //change the margin of content
      $("#content").css('margin-bottom',$("footer").height());

      var username=0;
      //set username
      function setName(){
        $('#myModal').modal({
          keyboard: false
       })
      }
      setName();
			//socket
			var Socket = new WebSocket("ws://127.0.0.1:9501");
			Socket.onmessage = function(event) {
				var data=eval('['+event.data+']');
        data=data[0];
				if (data.status == 1) {
          if(data.type==1){
            $('#content ul').append('<li class="ui-border-tb"><span class="username">系统消息:</span><span class="message">'+data.message+'</span></li>');
          }else if(data.type==2){
            $('#content ul').append('<li class="ui-border-tb"><span class="username">'+data.username+':</span><span class="message">'+data.message+'</span></li>');
          }
					$('#content').scrollTop($('#content')[0].scrollHeight);
				}
			}

     
			function sendMessage(message) {
				Socket.send(message);
			}
     //set name
			$('#set-name').click(function() {
				var nickname = $("#nickname").val();
        if(nickname){
          sendMessage(nickname);
          $('#myModal').modal('hide');
          username=1;
        }else{
          alert('名字不能为空');
        }
			});
      //send  message
      $("#submit").click(function(){
        var message = $("#input").val();
        $("#input").val('');
        if(username==0){
          setName();
        }else if(message){
          sendMessage(message);
        }
      });
			$("#input").keypress(function() {
				if (event.keyCode == 13) {
          if(username==0){
            setName();
          }else{
            var message = $("#input").val();
            $("#input").val('');
            sendMessage(message);
          }
					event.keyCode = 0;
          return false;
				}
			});
		});
	</script>
</body>
</html>
