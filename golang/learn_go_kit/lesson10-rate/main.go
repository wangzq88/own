package main

import (
	"flag"
	"fmt"
	. "learn_go_kit/lesson10-rate/Service"
	"learn_go_kit/lesson8-argument/util"
	"log"
	"net/http"
	"os"
	"os/signal"
	"strconv"
	"syscall"

	httptrans "github.com/go-kit/kit/transport/http"
	"github.com/gorilla/mux"
	"golang.org/x/time/rate"
)

func main() {
	name := flag.String("name", "", "服务名称")
	port := flag.Int("port", 0, "服务端口")
	flag.Parse()
	if *name == "" {
		log.Fatal("请指定服务名")
	}
	if *port == 0 {
		log.Fatal("请指定服务端口")
	}
	util.SetServiceNameAndPort(*name, *port) //设置服务名和端口
	user := UserService{}
	limit := rate.NewLimiter(1, 5)
	endp := RateLimit(limit)(GenUserEndPoint(user)) //调用限流代码生成的中间件

	serverHandler := httptrans.NewServer(endp, DecodeUserRequest, EncodeUserResponse) //使用go kit创建server传入我们之前定义的两个解析函数
	//使用mux来使服务支持路由
	router := mux.NewRouter()
	{
		router.Methods("GET", "DELETE").Path(`/user/{uid:\d+}`).Handler(serverHandler)
		router.Methods("GET").Path("/health").HandlerFunc(func(writer http.ResponseWriter, request *http.Request) {
			writer.Header().Set("Content-type", "application/json")
			writer.Write([]byte(`{"status":"ok"}`))
		})
	}
	errChan := make(chan error)
	go func() {
		util.RegService() //调用注册服务程序
		err := http.ListenAndServe(":"+strconv.Itoa(*port), router)
		if err != nil {
			log.Println(err)
			errChan <- err
		}
	}()
	go func() {
		sigChan := make(chan os.Signal)
		signal.Notify(sigChan, syscall.SIGINT, syscall.SIGTERM)
		errChan <- fmt.Errorf("%s", <-sigChan)
	}()
	getErr := <-errChan //只要报错 或者service关闭阻塞在这里的会进行下去
	util.UnRegService()
	log.Println(getErr)
	//http.ListenAndServe(":8080", router)
}
