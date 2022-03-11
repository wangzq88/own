# 同步模块主逻辑

# 主逻辑说明 增量更新

> 通过一个sync-worker组件定时同步，然后在sync中做to_add,to_del,to_mod的set 做增量更新

> 阿里云

- https://help.aliyun.com/document_detail/63640.html

# uid和hash字段说明

- uuid 判断资源唯一的key
- hash 所有需要关注变化key v的hash，判读其是否改变

# 模型演化成两个uuid集合的关系

- set_a 表示本地的 ，set_b表示公有云，先比较uuid
  - 在a中不在b中说明 ，说明要删掉，to_del
  - 在b中不在a中说明 ，说明要新增，to_add
- uuid一致，但hash发生变化，说明要更新，to_mo



## 启动 server 端

注入`user` `version` 等信息再编译（该项目必须在 Linux 中才能执行）

```sh
$ echo 11.0 > VERSION
```

```sh
$ go build -o server  -ldflags "-X 'github.com/prometheus/common/version.BuildUser=root@wzq'  -X 'github.com/prometheus/common/version.BuildDate=`date`' -X 'github.com/prometheus/common/version.Version=`cat VERSION`'" modules/server/server.go
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


