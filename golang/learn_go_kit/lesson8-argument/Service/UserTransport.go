package Service

import (
	"context"
	"encoding/json"
	"errors"
	"net/http"
	"strconv"

	"github.com/gorilla/mux"
)

func DecodeUserRequest(c context.Context, r *http.Request) (interface{}, error) { //这个函数决定了使用哪个request结构体来请求
	vars := mux.Vars(r)             //通过这个返回一个map，map中存放的是参数key和值，因为我们路由地址是这样的/user/{uid:\d+}，索引参数是uid,访问Examp: http://localhost:8080/user/121，所以值为121
	if uid, ok := vars["uid"]; ok { //
		uid, _ := strconv.Atoi(uid)
		return UserRequest{Uid: uid, Method: r.Method}, nil
	}
	return nil, errors.New("参数错误")
}

func EncodeUserResponse(ctx context.Context, w http.ResponseWriter, response interface{}) error {
	w.Header().Set("Content-type", "application/json") //设置响应格式为json，这样客户端接收到的值就是json，就是把我们设置的UserResponse给json化了

	return json.NewEncoder(w).Encode(response) //判断响应格式是否正确
}
