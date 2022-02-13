package main

import (
	"context"
	"fmt"
	"net"

	pd "demo/myproto"

	"google.golang.org/grpc"
)

type server struct{}

func (this *server) Sayhello(ctx context.Context, in *pd.HelloReq) (out *pd.HelloRsp, err error) {
	return &pd.HelloRsp{Msg: "Hello" + in.Name}, nil
}
func (this *server) Sayname(ctx context.Context, in *pd.NameReq) (out *pd.NameRsp, err error) {
	return &pd.NameRsp{Msg: in.Name + " Fuck you"}, nil
}

func main() {
	//创建网络
	ln, err := net.Listen("tcp", ":10086")
	if err != nil {
		fmt.Println("网络错误", err)
	}
	//创建 grpc 的服务
	srv := grpc.NewServer()
	//注册服务
	pd.RegisterHelloserverServer(srv, &server{})
	//等待网络连接
	err = srv.Serve(ln)
	if err != nil {
		fmt.Println("网络错误", err)
	}
}
