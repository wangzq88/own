package main

import (
	. "learn_go_kit/lesson3-httpmethod/Service"
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
	}

	http.ListenAndServe(":8080", router)
}
