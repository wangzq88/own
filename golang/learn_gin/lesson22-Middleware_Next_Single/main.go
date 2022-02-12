package main

import (
	"log"

	"github.com/gin-gonic/gin"
)

func middleware1(c *gin.Context) {
	log.Println("middleware1 in ....")
	c.Set("key", 1000)
	log.Println("before next ....")
	c.Next()
	log.Println("next after .....")
	log.Println("middleware done ....")
	c.JSON(200, gin.H{
		"msg": c.GetInt("key"),
	})
}

func main() {
	r := gin.Default()
	r.Use(middleware1)
	r.GET("ping", func(c *gin.Context) {
		log.Println("func in .....")
		k := c.GetInt("key") // 1000
		// 3000
		c.Set("key", k+2000)
		log.Println("func done .....")

	})
	r.Run()
}
