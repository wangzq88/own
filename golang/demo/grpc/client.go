package main

import (
	"context"
	"fmt"
	"log"

	pd "demo/myproto"

	"google.golang.org/grpc"
)

func main() {
	conn, err := grpc.Dial("127.0.0.1:10086", grpc.WithInsecure())
	if err != nil {
		log.Fatalln("网络异常", err)
	}
	defer conn.Close()
	//获得句柄
	c := pd.NewHelloserverClient(conn)
	res, err := c.Sayhello(context.Background(), &pd.HelloReq{Name: "熊猫"})
	if err != nil {
		fmt.Println("Sayhello 服务调用失败", err)
	}
	fmt.Println("调用Sayhello的返回", res.Msg)
	res2, err := c.Sayname(context.Background(), &pd.NameReq{Name: "托你傻蛋可"})
	if err != nil {
		fmt.Println("Sayname 服务调用失败", err)
	}
	fmt.Println("调用Sayname的返回", res2.Msg)
}
