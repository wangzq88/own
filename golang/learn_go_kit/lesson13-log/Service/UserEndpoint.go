package Service

import (
	"context"
	"fmt"
	"learn_go_kit/lesson11-exception/util"
	"os"
	"strconv"

	"github.com/go-kit/kit/endpoint"
	"github.com/go-kit/kit/log"
	"golang.org/x/time/rate"
)

type UserRequest struct {
	Uid    int `json:"uid"`
	Method string
}

type UserResponse struct {
	Result string `json:"result"`
}

//加入限流功能中间件
func RateLimit(limit *rate.Limiter) endpoint.Middleware { //Middleware type Middleware func(Endpoint) Endpoint
	return func(next endpoint.Endpoint) endpoint.Endpoint { //Endpoint type Endpoint func(ctx context.Context, request interface{}) (response interface{}, err error)
		return func(ctx context.Context, request interface{}) (response interface{}, err error) {
			if !limit.Allow() {
				//return nil, errors.New("too many request")
				return nil, util.NewMyError(429, "too many request")
			}
			return next(ctx, request)
		}
	}
}

func GenUserEndPoint(uservice IUserService) endpoint.Endpoint {
	return func(ctx context.Context, request interface{}) (response interface{}, err error) {
		var logger log.Logger
		{
			logger = log.NewLogfmtLogger(os.Stdout)
			logger = log.WithPrefix(logger, "mykit", "1.0")
			logger = log.WithPrefix(logger, "time", log.DefaultTimestampUTC) //加上前缀时间
			logger = log.WithPrefix(logger, "caller", log.DefaultCaller)     //加上前缀，日志输出时的文件和第几行代码

		}
		r := request.(UserRequest)
		result := "nothings"
		if r.Method == "GET" { //通过判断请求方法走不通的处理方法
			result = uservice.GetName(r.Uid) + strconv.Itoa(util.ServicePort)
			logger.Log("method", r.Method, "event", "get user", "userid", r.Uid)
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
