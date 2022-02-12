package main

import (
	"database/sql"
	"fmt"
	"log"

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

// querySingleRow 查询单条数据
func querySingleRow() user {
	sqlStr := "select * from student where id = ?"
	var u user
	if err := db.QueryRow(sqlStr, 1).Scan(&u.Id, &u.Name, &u.Age); err != nil {
		log.Printf("scan failed err : %v\n", err)
		return u
	}
	log.Println(u.Id, u.Name, u.Age)
	return u
}

// queryMultiRow 查询多条
func queryMultiRow() []user {
	sqlStr := "select * from student"
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

// updateRow 更新数据
func updateRow() {
	sqlStr := "update student set name = ? where id = ?"
	res, err := db.Exec(sqlStr, "zhangsan", 1)
	if err != nil {
		fmt.Printf("update failed err: %v\n", err)
	}
	// n受影响的行数
	n, err := res.RowsAffected()
	if err != nil {
		fmt.Printf("get RowsAffected failed %v\n", err)
	}
	fmt.Println(n)
	fmt.Println("update success")
}

// deleteRow 删除数据
func deleteRow() {
	sqlStr := "delete from student where id < ?"
	res, err := db.Exec(sqlStr, 1)
	if err != nil {
		fmt.Printf("delete failed err: %v\n", err)
	}
	// 受影响的行数
	n, err := res.RowsAffected()
	if err != nil {
		fmt.Printf("get RowsAffected failed %v\n", err)
	}
	fmt.Println(n)
	fmt.Println("delete success")
}

// insertRow 插入操作
func insertRow() {
	sqlStr := "insert into student (name,age) values (?,?)"
	res, err := db.Exec(sqlStr, "给这节课好评", 18)
	if err != nil {
		fmt.Printf("insert failed err:%v\n", err)
	}
	// 是不是应该吧这个id返回给前端
	id, err := res.LastInsertId()
	if err != nil {
		fmt.Printf("get LastInsertId failed err:%v\n", err)
	}
	fmt.Println(id)
}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")
	//updateRow()
	//deleteRow()
	insertRow()

	//r := gin.Default()
	//
	//// 查询用户详情
	//r.GET("user", func(c *gin.Context) {
	//	u := querySingleRow()
	//	c.JSON(200, gin.H{
	//		"data": u,
	//	})
	//})
	//// 查询用户列表
	//r.GET("users", func(c *gin.Context) {
	//	u := queryMultiRow()
	//	c.JSON(200, gin.H{
	//		"data": u,
	//	})
	//})
	//r.Run(":1234")
}
