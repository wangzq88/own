package main

import "github.com/gin-gonic/gin"

func main() {
	// gin.Default() gin.New()
	r := gin.New()
	r.Use(gin.Logger())
	r.Use(gin.Recovery())

	r.GET("ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"code": 200,
		})
	})
	r.Run()
}
