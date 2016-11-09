<?php include(dirname(__FILE__) . '/config.php') ?>
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
				<input id="nickname" type="text" class="form-control"/>
			</div>
			<div class="modal-footer">
				<button type="button" id="set-name" class="btn btn-primary">
					确定
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="ui-container" id="content">
	<div id="chat-list">
		<ul class="ui-list-text border-list ui-border-tb" id="chat-list2">
		</ul>
	</div>
</div>

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
	$(function () {
		//change the margin of content
		$("#content").css('margin-bottom', $("footer").height());

		var username = 0;
		//set username
		function setName() {
			$('#myModal').modal({
				keyboard: false
			})
		}

		setName();
		//socket
		try {
			var Socket = new WebSocket("ws://<?php $config = get_config();echo $config['sys']['server_address'] . ':' . $config['sys']['server_port'];?>");
		}
		catch (err) {
			alert('服务器在开小差哦，待会再来看看呗~');
		}
		Socket.onerror = function (event) {
			alert('服务器在开小差哦，待会再来看看呗~');
			return false;
		};
		Socket.onclose = function (event) {
			alert('聊天室关闭了，下次再来看看吧');
			return false;
		};
		Socket.onmessage = function (event) {
			var data = eval('[' + event.data + ']');
			data = data[0];
			if (data.status == 1) {
				if (data.type == 1) {
					$('#content ul').append('<li class="ui-border-tb"><span class="username">系统消息:</span><span class="message">' + data.message + '</span></li>');
				} else if (data.type == 2) {
					$('#content ul').append('<li class="ui-border-tb"><span class="username">' + data.username + ':</span><span class="message">' + data.message + '</span></li>');
				}

				var chatList = document.getElementById('chat-list');
				window.scrollTo(0,chatList.scrollHeight);
			}
		};

		//发送消息
		function sendMessage(data, action='message') {
			data.action = action;
			Socket.send(JSON.stringify(data));
		}

		//自定义用户名
		$('#set-name').click(function () {
			var nickname = $("#nickname").val();
			if (nickname) {
				sendMessage({username: nickname}, 'init');
				$('#myModal').modal('hide');
				username = 1;
			} else {
				alert('名字不能为空');
			}
		});
		//触发发送消息事件
		$("#submit").click(function () {
			var message = $("#input").val();
			$("#input").val('');
			if (username == 0) {
				setName();
			} else if (message) {
				if (!message) {
					return false;
				}
				sendMessage({message: message}, 'message');
			}
		});
		//回车发送消息
		$("#input").keypress(function () {
			if (event.keyCode == 13) {
				if (username == 0) {
					setName();
				} else {
					var message = $("#input").val();
					$("#input").val('');
					if (!message) {
						return false;
					}
					sendMessage({message: message}, 'message');
				}
				event.keyCode = 0;
				return false;
			}
		});
	});
</script>
</body>
</html>
