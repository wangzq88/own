package main

import (
	"fmt"
	"html"
	"log"
	"net/http"
	"net/rpc"
)

type Panda int

/**
 * -方法是导出的
   - 方法有两个参数，都是导出类型或内建类型
   - 方法的第二个参数是指针
   - 方法只有一个error接口类型的返回值
**/
func (this *Panda) Getinfo(argType int, replyType *int) error {
	fmt.Println("打印发送过来的内容", argType)
	*replyType = argType + 12306
	return nil
}

func main() {
	pd := new(Panda)
	//服务端注册一个对象
	rpc.Register(pd)
	rpc.HandleHTTP()
	http.HandleFunc("/bar", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "Hello, %q", html.EscapeString(r.URL.Path))
	})
	log.Fatal(http.ListenAndServe(":10086", nil))
}
