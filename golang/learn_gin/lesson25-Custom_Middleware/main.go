package main

import (
	"fmt"

	"github.com/gin-gonic/gin"
)

// 第一种写法
//func middleware1(c *gin.Context) {
//
//}

// 第二种写法
//func middleware2() gin.HandlerFunc {
//	return func(c *gin.Context) {
//		// *.baidu.com
//	}
//}

func RefererMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		fmt.Println("first in....")
		// 取到 referer
		ref := c.GetHeader("Referer")
		if ref == "" {
			c.AbortWithStatusJSON(200, gin.H{
				"msg": "非法访问",
			})
			return
		}
		c.Next()
		fmt.Println("first done....")
	}
}

func main() {
	r := gin.Default()
	// 第一种写法的使用
	//r.Use(middleware1)
	// 第二种写法的使用
	//r.Use(middleware2())
	r.Use(RefererMiddleware())
	r.Use(func(c *gin.Context) {
		fmt.Println("我是第二个中间件")
		c.Next()
		fmt.Println("second done")
	})

	r.GET("ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"code": 0,
		})
	})
	r.Run()
}
