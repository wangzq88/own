package util

import (
	"log"

	consulapi "github.com/hashicorp/consul/api"
)

func RegService() {
	config := consulapi.DefaultConfig()
	config.Address = "127.0.0.1:8500"
	reg := consulapi.AgentServiceRegistration{}
	reg.Name = "userservice"  //注册service的名字
	reg.Address = "127.0.0.1" //注册service的ip
	reg.Port = 8080           //注册service的端口
	reg.Tags = []string{"primary"}

	check := consulapi.AgentServiceCheck{}      //创建consul的检查器
	check.Interval = "5s"                       //设置consul心跳检查时间间隔
	check.HTTP = "http://127.0.0.1:8080/health" //设置检查使用的url

	reg.Check = &check

	client, err := consulapi.NewClient(config) //创建客户端
	if err != nil {
		log.Fatal(err)
	}
	err = client.Agent().ServiceRegister(&reg)
	if err != nil {
		log.Fatal(err)
	}
}
