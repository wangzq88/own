# go-kit

go-kit 是一个微服务工具包合集。利用它提供的**API**和**规范**可以创建健壮、可维护性高的微服务体系

## 微服务体系的基本需求

1、HTTP REST、RPC

2、日志功能

3、限流

4、API 监控

5、服务注册与发现

6、API 网关

7、服务链路追踪

8、服务熔断

## Go-kit 的三层架构

**1、Transport**

主要负责与 HTTP、gRPC、thrift等相关的逻辑

**2、Endpoint**

定义Request和Response格式，并可以使用装饰器包装函数，以此来实现各种中间件嵌套。

**3、Service**

这里就是我们的业务类、接口等



安装：go get github.com/go-kit/kit



视频学习教程：https://www.bilibili.com/video/av96566709?p=1

课件参考地址：https://www.cnblogs.com/hualou/category/1617965.html?page=2