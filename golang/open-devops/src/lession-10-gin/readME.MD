# node_path操作的函数添加gin的支持

## 启动 server 端

注入`user` `version` 等信息再编译（该项目必须在 Linux 中才能执行）

```sh
$ echo 7.0 > VERSION
```

```sh
$ go build -o server  -ldflags "-X 'github.com/prometheus/common/version.BuildUser=root@n9e'  -X 'github.com/prometheus/common/version.BuildDate=`date`' -X 'github.com/prometheus/common/version.Version=`cat VERSION`'" modules/server/server.go
```

```sh
$ ./server --version
```

设置 debug level 打印日志

```sh
$ ./server --log.format=json
```

> MessagePack

- 是一种高效的二进制序列化格式。它允许你在多种语言(如JSON)之间交换数据。但它更快更小。

## 启动 agent 端

```sh
$ go build -o agent  -ldflags "-X 'github.com/prometheus/common/version.BuildUser=root@n9e'  -X 'github.com/prometheus/common/version.BuildDate=`date`' -X 'github.com/prometheus/common/version.Version=`cat VERSION`'" modules/agent/agent.go
```

```sh
$ ./agent --log.format=json
```



- exec.CommandContext 加上一个 带超时时间的context
- 使用方直接传入shell命令即可
- 有些信息通过shell获取更方便

> 获取sn号的命令

```shell
# 公有云
curl -s http://169.254.169.254/a/meta-data/instance-id

# 自己的机器
dmidecode -s system-serial-number |tail -n 1


```

**用 md5 生成函数**

models/resource_host.go

**如何将 gin 整合到 net/http 包中**

## Postman 验证

**增加 nodepath**

url:  http://129.226.134.90:9998/api/v1/node-path     

method: post

Body: {"node": "a1.b1.c1"}

**查询 nodepath**

url:  http://129.226.134.90:9998/api/v1/node-path     

method: post

Body: {"node": "a1","query_type":2}



url: http://129.226.134.90:9998/api/v1/resource-unmount

method: delete

Body: {"target_path": "waimai.ditu.es","resource_type":"resource_host","resource_ids":[1]}
