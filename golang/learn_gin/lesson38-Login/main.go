package main

import (
	v1 "gin_learn/lesson38-Login/api/v1"
	"gin_learn/lesson38-Login/model"

	"github.com/gin-gonic/gin"
)

func main() {
	// InitializeDatabase 初始化数据库
	if err := model.InitializeDatabase(); err != nil {
		panic(err)
	}
	r := gin.Default()
	v := r.Group("api/v1")
	{
		// ping
		v.GET("ping", func(c *gin.Context) {
			c.JSON(200, gin.H{
				"msg": "pong",
			})
		})
		// 注册
		v.POST("user/register", v1.UserRegisterHandler)
		// 登录
		v.POST("user/login", v1.UserLoginHandler)

	}
	r.Run(":1234")
}
