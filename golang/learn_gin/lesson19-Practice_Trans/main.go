package main

import (
	"fmt"
	"reflect"
	"strings"

	"github.com/gin-gonic/gin"
	"github.com/gin-gonic/gin/binding"
	"github.com/go-playground/locales/zh"
	ut "github.com/go-playground/universal-translator"
	"github.com/go-playground/validator/v10"
	zhTranslations "github.com/go-playground/validator/v10/translations/zh"
)

type LoginForm struct {
	UserName   string `json:"username" binding:"required,min=3,max=7"`
	Password   string `json:"password" binding:"required,len=8"`
	RePassword string `json:"re_password" binding:"required,len=8,eqfield=Password"`
}
type RegisterForm struct {
	UserName string `json:"username" binding:"required,min=3,max=7"`
	Password string `json:"password" binding:"required,len=8"`
	Age      uint32 `json:"age" binding:"required,gte=1,lte=150"`
	Sex      uint32 `json:"sex" binding:"required"`
	Email    string `json:"email" binding:"required,email"`
}

var trans ut.Translator

func main() {
	if err := InitializeTrans(); err != nil {
		fmt.Println(err.Error())
		panic(err)
	}
	r := gin.Default()
	// 登录
	r.POST("login", loginHandler)
	// 注册
	r.POST("register", registerHandler)
	r.Run()
}

// registerHandler  注册
func registerHandler(c *gin.Context) {
	var r RegisterForm
	if err := c.ShouldBindJSON(&r); err != nil {
		err, ok := err.(validator.ValidationErrors)
		if !ok {
			c.JSON(200, gin.H{
				"code": 40010,
				"msg":  "注册失败",
				"err":  err.Error(),
			})
			return
		}
		c.JSON(200, gin.H{
			"code": 40004,
			"msg":  "注册失败，请检测参数",
			"err":  removeTopStruct(err.Translate(trans)),
		})
		return
	}
	// 注册成功
	c.JSON(200, gin.H{
		"code": 0,
		"msg":  "success",
		"data": r,
	})
}

// loginHandler 登录
func loginHandler(c *gin.Context) {
	var l LoginForm
	if err := c.ShouldBindJSON(&l); err != nil {
		err, ok := err.(validator.ValidationErrors)
		if !ok {
			c.JSON(200, gin.H{
				"code": 40010,
				"msg":  "登录失败",
				"err":  err.Error(),
			})
			return
		}
		c.JSON(200, gin.H{
			"code": 40004,
			"msg":  "登录失败，请检测参数",
			"err":  removeTopStruct(err.Translate(trans)),
		})
		return
	}
	// 登录成功
	c.JSON(200, gin.H{
		"code": 0,
		"msg":  "success",
		"data": l.UserName,
	})
}

func InitializeTrans() (err error) {
	// Accept-Language
	// 修改gin框架validator引擎属性
	if v, ok := binding.Validator.Engine().(*validator.Validate); ok {
		v.RegisterTagNameFunc(func(fld reflect.StructField) string {
			name := fld.Tag.Get("json")
			return name
		})
		zhT := zh.New()
		uni := ut.New(zhT, zhT)
		trans, _ = uni.GetTranslator("zh")
		err = zhTranslations.RegisterDefaultTranslations(v, trans)
		return
	}
	return
}

func removeTopStruct(fields validator.ValidationErrorsTranslations) validator.ValidationErrorsTranslations {
	r := make(validator.ValidationErrorsTranslations)
	for f, v := range fields {
		r[f[strings.Index(f, ".")+1:]] = v
	}
	return r
}
