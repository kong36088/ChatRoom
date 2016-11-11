# ChatRoom
安装PHP`swoole`拓展：`pecl install swoole`

或到[swoole官网](http://www.swoole.com/)获取安装帮助

本应用是一个在线聊天室。利用了swoole高并发并且异步非阻塞的特点提高了程序的性能。
该应用需要swoole拓展的支持。

Demo: [聊天室](http://chat.jwlchina.cn)

# 运行

开启服务：
将client目录配置到Nginx/Apache的虚拟主机目录中，使index.php可访问。 修改`config.php`中，IP和端口为对应的配置。
``` bash
cd /path/to/your/application/
php server.php
```

## Ningx/Apache配置

nginx
``` bash
server 
{
    listen       80;
    server_name  im.swoole.com;
    index index.shtml index.html index.htm index.php;
    root  /path/to/PHPWebIM/client;
    location ~ .*\.(php|php5)?$
    {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    access_log  /Library/WebServer/nginx/logs/im.swoole.com  access;
}
```
apache
``` bash
<VirtualHost *:80>
    DocumentRoot "path/to/PHPWebIM/client"
    ServerName im.swoole.com
    AddType application/x-httpd-php .php
    <Directory />
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
        DirectoryIndex index.php
    </Directory>
</VirtualHost>
```

# Docker

利用`docker`快速搭建项目环境

`docker pull kong36088/nginx-php7-swoole`

`docker run --name chat -p 9501:9501 -p 80:80 -itd kong36088/nginx-php7-swoole bash`

我的swoole docker镜像地址:[swoole镜像](https://hub.docker.com/r/kong36088/nginx-php7-swoole/)
里面有该镜像的详细使用说明
