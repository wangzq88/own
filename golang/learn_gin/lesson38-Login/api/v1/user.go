package v1

import (
	"gin_learn/lesson38-Login/service"

	"github.com/gin-gonic/gin"
)

// UserLoginHandler 登录
func UserLoginHandler(c *gin.Context) {
	var s service.UserLoginService
	if err := c.ShouldBindJSON(&s); err != nil {
		c.JSON(200, gin.H{
			"msg": err.Error(),
		})
	} else {
		res := s.Login()
		c.JSON(200, res)
	}
}

// UserRegisterHandler 注册
func UserRegisterHandler(c *gin.Context) {
	var s service.UserRegisterService
	if err := c.ShouldBindJSON(&s); err != nil {
		c.JSON(200, gin.H{
			"msg": "err",
		})
	} else {
		res := s.Register()
		c.JSON(200, res)
	}
}
