# ChatRoom

本应用是一个在线聊天室。利用了swoole高并发并且异步非阻塞的特点提高了程序的性能。
该应用需要swoole拓展的支持。

Demo: [聊天室](http://chat.jwlchina.cn)

Open service `php server.php`


An online chat room powered by swoole , written in PHP .This requires  swoole extension

# Docker

利用`docker`快速搭建项目环境
using `docker` to build your running environment

`docker pull kong36088/nginx-php7-swoole`

`docker run --name chat -p 9501:9501 -p 80:80 -itd kong36088/nginx-php7-swoole bash`

我的swoole docker镜像地址:[swoole镜像](https://hub.docker.com/r/kong36088/nginx-php7-swoole/)
里面有该镜像的详细使用说明
