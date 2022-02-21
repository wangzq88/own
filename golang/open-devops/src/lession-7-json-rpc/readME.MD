# server端和agent RPC通信

## 启动 server 端

注入`user` `version` 等信息再编译（Windows 下的 git bash 客户端输入命令）

```sh
$ echo 7.0 > VERSION
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

> MessagePack

- 是一种高效的二进制序列化格式。它允许你在多种语言(如JSON)之间交换数据。但它更快更小。

## 启动 agent 端

```sh
$ go build -o agent.exe  -ldflags "-X 'github.com/prometheus/common/version.BuildUser=root@n9e'  -X 'github.com/prometheus/common/version.BuildDate=`date`' -X 'github.com/prometheus/common/version.Version=`cat VERSION`'" modules/agent/agent.go
```

```sh
$ ./agent.exe --log.format=json
```

