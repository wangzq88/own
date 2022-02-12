package main

import (
	"log"

	"github.com/gin-gonic/gin"
	"github.com/gofrs/uuid"
)

func RequestIdMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		id := c.Request.Header.Get("Micheal")
		if id == "" {
			u, _ := uuid.NewV4()
			id = u.String()
			log.Println(id)
		}
		c.Writer.Header().Set("Micheal", id)
		c.Next()
	}
}

func main() {
	r := gin.Default()
	r.Use(RequestIdMiddleware())
	r.GET("ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"code": 0,
		})
	})
	r.Run()
}
