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
	sqlStr := "select * from student where name = ?"
	// 命令部分
	stmt, err := db.Prepare(sqlStr)
	if err != nil {
		log.Println(err)
		return nil
	}
	defer stmt.Close()
	// 数据部分
	rows, _ := stmt.Query(name)
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

func insertData() {
	sqlStr := "insert into student (name,age) values (?,?)"
	stmt, err := db.Prepare(sqlStr)
	if err != nil {
		fmt.Printf("prepare failed err:%v\n", err)
		return
	}
	defer stmt.Close()

	_, err = stmt.Exec("老王", 31)
	if err != nil {
		fmt.Printf("insert failed err:%v\n", err)
		return
	}

	_, err = stmt.Exec("老王威武", 31)
	if err != nil {
		fmt.Printf("insert failed err:%v\n", err)
		return
	}

	fmt.Println("insert success")

}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")
	insertData()
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
