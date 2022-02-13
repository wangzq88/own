package main

import (
	"fmt"
	"net/rpc"
)

func main() {
	cli, err := rpc.DialHTTP("tcp", "127.0.0.1:10086")
	if err != nil {
		fmt.Println("网络连接失败")
	}
	var pd int
	err = cli.Call("Panda.Getinfo", 10086, &pd)
	if err != nil {
		fmt.Println("打 call 失败")
	}
	fmt.Println("最后得到的值", pd)
}
