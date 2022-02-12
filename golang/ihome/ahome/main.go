package main

import (
	"ahome/handler"
	"fmt"
	"os"

	"github.com/gin-gonic/gin"
)

func main() {
	//初始化路由
	router := gin.Default()
	//静态路由
	dir, _ := os.Getwd()
	fmt.Println("现在代码运行的目录：", dir)
	router.Static("/home", dir+"/view")
	r1 := router.Group("/api/v1.0")
	{
		// //路由规范
		r1.GET("/user/auth", handler.GetUser)
		r1.GET("/user", handler.GetUser)
		r1.GET("/areas", handler.GetArea)
		r1.GET("/session", handler.Session)
		r1.GET("/imagecode/:uuid", handler.GetImgCd)
		r1.GET("/smscode/:mobile", handler.GetSmscd)
		r1.POST("/users", handler.PostRet)
		r1.POST("/sessions", handler.Login)
		r1.DELETE("/session", handler.DelSession)
		r1.POST("/user/avatar", handler.UploadAvatar)
		r1.POST("/user/auth", handler.Auth)
		r1.GET("/user/houses", handler.GetUserHouses)
		r1.POST("/houses", handler.PostHouses)
		r1.GET("/houses/:house_id", handler.GetHousesDetail)
		r1.POST("/houses/:uuid/images", handler.PostHousesImages)
		r1.GET("/houses", handler.SearchHouses)
		r1.GET("/house/index", handler.GetIndex)
		r1.POST("/orders", handler.PostOrders)
		r1.GET("/user/orders", handler.GetOrders)
		r1.PUT("/orders/:order_id/status", handler.SetOrderStatus)
		r1.PUT("/orders/:order_id/comment", handler.SetComment)
		r1.PUT("/user/name", handler.SetUserName)
	}
	router.Run(":10086")
}
