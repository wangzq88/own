package main

import (
	. "learn_go_kit/lesson3-httpmethod/Service"
	"learn_go_kit/lesson4-consul/util"
	"net/http"

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
		util.RegService() //调用注册服务程序
	}

	http.ListenAndServe(":8080", router)
}
