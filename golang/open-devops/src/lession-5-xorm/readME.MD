# 使用xorm

编译后执行 `--version`

```sh
go build -o server.exe modules\server\server.go
```

```
server.exe --version
```

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

#  xorm介绍

xorm是一个简单而强大的Go语言ORM库. 通过它可以使数据库操作非常简便。xorm的目标并不是让你完全不去学习SQL，我们认为SQL并不会为ORM所替代，但是ORM将可以解决绝大部分的简单SQL需求。xorm支持两种风格的混用。

http://xorm.topgoer.com/

https://xorm.io/zh/
