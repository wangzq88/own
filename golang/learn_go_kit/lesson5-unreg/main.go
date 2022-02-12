package main

import (
	"fmt"
	. "learn_go_kit/lesson3-httpmethod/Service"
	"learn_go_kit/lesson5-unreg/util"
	"log"
	"net/http"
	"os"
	"os/signal"
	"syscall"

	httptrans "github.com/go-kit/kit/transport/http"
	"github.com/gorilla/mux"
)

func main() {
	user := UserService{}
	endp := GenUserEndPoint(user)

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
		err := http.ListenAndServe(":8080", router)
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
	http.ListenAndServe(":8080", router)
}
