package service

import (
	"gin_learn/lesson38-Login/model"
	"gin_learn/lesson38-Login/serializer"
)

type UserLoginService struct {
	UserName string `json:"username" binding:"required,email"`
	Password string `json:"password" binding:"required,len=8"`
}

func (service *UserLoginService) Login() serializer.Response {
	sqlStr := `select count(id) from user where email = ? and password = ?`
	var count int
	_ = model.DB.Get(&count, sqlStr, service.UserName, service.Password)
	if count == 0 {
		return serializer.Response{
			Code: 40003,
			Msg:  "账号或密码错误",
		}
	}

	return serializer.Response{
		Code: 0,
		Msg:  "登录成功",
	}
}
