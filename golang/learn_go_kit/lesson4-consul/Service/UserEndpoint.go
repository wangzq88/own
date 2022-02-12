package Service

import (
	"context"
	"fmt"

	"github.com/go-kit/kit/endpoint"
)

type UserRequest struct {
	Uid    int `json:"uid"`
	Method string
}

type UserResponse struct {
	Result string `json:"result"`
}

func GenUserEndPoint(uservice IUserService) endpoint.Endpoint {
	return func(ctx context.Context, request interface{}) (response interface{}, err error) {
		r := request.(UserRequest)
		result := "nothings"
		if r.Method == "GET" { //通过判断请求方法走不通的处理方法
			result = uservice.GetName(r.Uid)
		} else if r.Method == "DELETE" {
			err := uservice.DelUser(r.Uid)
			if err != nil {
				result = err.Error()
			} else {
				result = fmt.Sprintf("userid为%d的用户已删除", r.Uid)
			}
		}
		return UserResponse{Result: result}, nil
	}
}
