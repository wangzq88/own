package util

import (
	"context"
	"fmt"
	"io"
	Services "learn_go_kit/lesson6-client/Service"
	"net/url"
	"os"

	"github.com/go-kit/kit/endpoint"
	"github.com/go-kit/kit/log"
	"github.com/go-kit/kit/sd"
	"github.com/go-kit/kit/sd/consul"
	"github.com/go-kit/kit/sd/lb"
	httptransport "github.com/go-kit/kit/transport/http"
	consulapi "github.com/hashicorp/consul/api"
)

func GetUser() (string, error) {
	//第一步创建client
	{
		config := consulapi.DefaultConfig()
		config.Address = "localhost:8500"
		api_client, err := consulapi.NewClient(config)
		if err != nil {
			return "", err
		}
		client := consul.NewClient(api_client)

		var logger log.Logger
		{
			logger = log.NewLogfmtLogger(os.Stdout)
			var Tag = []string{"primary"}
			//第二部创建一个consul的实例
			instancer := consul.NewInstancer(client, logger, "userservice", Tag, true) //最后的true表示只有通过健康检查的服务才能被得到
			{
				factory := func(service_url string) (endpoint.Endpoint, io.Closer, error) { //factory定义了如何获得服务端的endpoint,这里的service_url是从consul中读取到的service的address我这里是192.168.3.14:8000
					tart, _ := url.Parse("http://" + service_url)                                                                                 //server ip +8080真实服务的地址
					return httptransport.NewClient("GET", tart, Services.GetUserInfo_Request, Services.GetUserInfo_Response).Endpoint(), nil, nil //我再GetUserInfo_Request里面定义了访问哪一个api把url拼接成了http://192.168.3.14:8000/v1/user/{uid}的形式
				}
				endpointer := sd.NewEndpointer(instancer, factory, logger)
				endpoints, _ := endpointer.Endpoints()
				fmt.Println("服务有", len(endpoints), "条")

				mylb := lb.NewRoundRobin(endpointer) //使用go-kit自带的轮询
				for {
					getUserInfo, err := mylb.Endpoint() //写死获取第一个
					if err != nil {
						return "", err
					}
					ctx := context.Background() //第三步：创建一个context上下文对象

					//第四步：执行
					res, err := getUserInfo(ctx, Services.UserRequest{Uid: 1001})
					if err != nil {
						return "", err
					}
					//第五步：断言，得到响应值
					userinfo := res.(Services.UserResponse)
					return userinfo.Result, nil
				}

			}
		}
	}

}