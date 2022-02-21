# 多个goroutine 协作，共同进退

注入`user` `version` 等信息再编译（Windows 下的 git bash 客户端输入命令）

```sh
$ echo 5.0 > VERSION
```

```sh
$ go build -o server.exe  -ldflags "-X 'github.com/prometheus/common/version.BuildUser=root@n9e'  -X 'github.com/prometheus/common/version.BuildDate=`date`' -X 'github.com/prometheus/common/version.Version=`cat VERSION`'" modules/server/server.go
```

```sh
$ ./server.exe --version
```

设置 debug level 打印日志

```sh
$ ./server.exe --log.format=json
```

#  用法

- g.add 第一个参数为run函数，返回error，要求改函数长时间运行，遇到错误再退出
  - 具体场景有两个1. for +ticker +ctx
  - 长时间运行的 http 和rpc 用error chan 在g.add中做
- 第二个参数为interrupt函数，作用是退出时做一些清理操作
