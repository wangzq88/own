# 爱家租房
## 基本介绍

爱家租房是用微服务框架go-micro 3.0 和Gin框架搭建的项目

ahome 是用 gin 搭建的 http 服务

services 目录里包含各个微服务项目

common 目录包含公共的组件，其中 ihome\common\model\config.go 是数据库和域名相关的配置文件

go_fdfs_client 是分布式文件系统FastDFS 的 Go 客户端实现。安装 FastDFS 查看这篇文章：https://blog.csdn.net/weixin_45735355/article/details/120363282

shell 脚本 ihome.sh 是启动各个服务的脚本，由于在运行 `micro run .` 编译过程中， ahome 文件夹内静态文件不会自动复制到临时目录，所以用shell脚本来复制。ihome\services\getImg 服务里的 comic.ttf 字体格式在编译不会自动复制到临时目录，所以得人工复制，也是用 shell 脚本来实现



## 安装

```sh
# Download latest proto releaes
# https://github.com/protocolbuffers/protobuf/releases
$ go get github.com/golang/protobuf/protoc-gen-go
$ go get github.com/micro/micro/v3/cmd/protoc-gen-micro
$ go get github.com/micro/micro/v3
```

## 运行服务

启动micro相关的服务进程

```sh
$ micro server
```

接下来需要登录账号，做身份验证。**不然后续操作会出现提示运行服务权限不足问题**。username固定为admin,password固定为micro

```sh
$ micro login
Enter username: admin
Enter password:
Successfully logged in.
```

查看运行哪些服务

```sh
$ micro services
api
auth
broker
config
events
network
proxy
registry
runtime
server
store
```

接下来切换到项目 ihome 的目录下，运行 `./ihome.sh` 脚本启动爱家租房的服务。

![20220213224320](attachment\20220213224320.png)

如果出现 `Rpc error: code = Canceled desc = grpc: the client connection is closing`的错误，说明提示信息下一行的服务没有启动成功，可输入 `micro logs postauth` 查看服务日志信息。再到该服务目录单独启动服务，`micro run .`

也可以逐个切换到 ahome 目录，services 下的各个子目录运行以下命令启动爱家租房的服务，效果等同。

```sh
$ micro run .
```

查看爱家租房的微服务，输入以下命令

```sh
$ micro status
NAME            VERSION SOURCE                                  STATUS  BUILD   UPDATED         METADATA
ahome           latest  /home/www/go/src/ahome                  running n/a     51m39s ago      owner=admin, group=micro
getImg          latest  /home/www/go/src/services/getImg        running n/a     51m33s ago      owner=admin, group=micro
getarea         latest  /home/www/go/src/services/getarea       running n/a     51m37s ago      owner=admin, group=micro
getorder        latest  /home/www/go/src/services/getorder      running n/a     9s ago          owner=admin, group=micro
getsession      latest  /home/www/go/src/services/getsession    running n/a     50m48s ago      owner=admin, group=micro
getsms          latest  /home/www/go/src/services/getsms        running n/a     50m41s ago      owner=admin, group=micro
getuserhouses   latest  /home/www/go/src/services/getuserhouses running n/a     50m33s ago      owner=admin, group=micro
getuserinfo     latest  /home/www/go/src/services/getuserinfo   running n/a     50m23s ago      owner=admin, group=micro
postauth        latest  /home/www/go/src/services/postauth      running n/a     50m2s ago       owner=admin, group=micro
postlogin       latest  /home/www/go/src/services/postlogin     running n/a     47s ago         owner=admin, group=micro
postret         latest  /home/www/go/src/services/postret       running n/a     49m12s ago      owner=admin, group=micro
```

查看某个微服务的日志输出，ahome 为我们项目的服务

```sh
$ micro logs ahome
```

停止某个微服务，ahome 为我们项目的服务

```sh
$ micro kill ahome
```

官方go-micro 3.0 的上手文档：https://micro.dev/getting-started

go-micro 框架的原理参考文档：[Go快速上手-微服务框架go-micro](https://zhuanlan.zhihu.com/p/372796932)