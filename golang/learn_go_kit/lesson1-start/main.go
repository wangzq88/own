package main

import (
	. "learn_go_kit/lesson1-start/Service"
	"net/http"

	httptrans "github.com/go-kit/kit/transport/http"
)

func main() {
	user := UserService{}
	endp := GenUserEndPoint(user)

	serveHandler := httptrans.NewServer(endp, DecodeUserRequest, EncodeUserResponse) //使用go kit创建server传入我们之前定义的两个解析函数
	http.ListenAndServe(":8080", serveHandler)
}
