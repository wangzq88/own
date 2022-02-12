package main

import (
	. "learn_go_kit/lesson2-route/Service"
	"net/http"

	httptrans "github.com/go-kit/kit/transport/http"
	"github.com/gorilla/mux"
)

func main() {
	user := UserService{}
	endp := GenUserEndPoint(user)

	serverHandler := httptrans.NewServer(endp, DecodeUserRequest, EncodeUserResponse) //使用go kit创建server传入我们之前定义的两个解析函数
	r := mux.NewRouter()                                                              //使用mux来使服务支持路由
	r.Handle(`/user/{uid:\d+}`, serverHandler)                                        //这种写法支持多种请求方法，访问Examp: http://localhost:8080/user/121便可以访问
	//r.Methods("GET").Path(`/user/{uid:\d+}`).Handler(serverHandler)
	http.ListenAndServe(":8080", r)
}
