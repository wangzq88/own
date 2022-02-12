package main

import (
	"database/sql"
	"fmt"
	"log"

	"github.com/gin-gonic/gin"

	_ "github.com/go-sql-driver/mysql"
)

var db *sql.DB

func initializeDatabase() (err error) {
	dsn := "root:123456@tcp(localhost:4000)/student?charset=utf8mb4&parseTime=True&loc=Local"
	db, err = sql.Open("mysql", dsn)
	if err != nil {
		return
	}
	if err = db.Ping(); err != nil {
		return
	}
	return nil
}

type user struct {
	Id   uint64 `json:"id"`
	Name string `json:"name"`
	Age  uint32 `json:"age"`
}

type userForm struct {
	Name string `form:"name" binding:"required"`
}

// queryMultiRow 查询多条
func queryMultiRow(name string) []user {
	// name Micheal' or 1=1 #
	sqlStr := fmt.Sprintf("select * from student where name = '%s'", name)
	rows, err := db.Query(sqlStr)
	if err != nil {
		log.Println(err)
		return nil
	}
	defer rows.Close()
	users := make([]user, 0)
	for rows.Next() {
		var u user
		err := rows.Scan(&u.Id, &u.Name, &u.Age)
		if err != nil {
			log.Println(err)
			return nil
		}
		users = append(users, u)
	}
	return users
}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")
	r := gin.Default()

	// 查询用户列表
	r.GET("users", func(c *gin.Context) {
		var u userForm
		if err := c.ShouldBind(&u); err != nil {
			c.JSON(200, gin.H{
				"err": err.Error(),
			})
			return
		}
		data := queryMultiRow(u.Name)
		c.JSON(200, gin.H{
			"data": data,
		})
	})
	r.Run(":1234")
}
