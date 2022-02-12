package main

import (
	"fmt"

	_ "github.com/go-sql-driver/mysql"
	"github.com/jmoiron/sqlx"
)

type user struct {
	Id   uint64 `db:"id"`
	Name string `db:"name"`
	Age  uint32
}

var db *sqlx.DB

// initializeDatabase 初始化
func initializeDatabase() (err error) {
	dsn := "root:123456@tcp(localhost:4000)/student?charset=utf8mb4&parseTime=True&loc=Local"
	db, err = sqlx.Connect("mysql", dsn)
	if err != nil {
		fmt.Printf("connect 2 database failed, err:%v\n", err)
		return
	}
	db.SetMaxOpenConns(10)
	db.SetMaxIdleConns(10)
	return nil
}

// querySingleRow 查询单条数据
func querySingleRow() {
	sqlStr := "select * from student where id = ?"
	var u user
	if err := db.Get(&u, sqlStr, 2); err != nil {
		fmt.Printf("query failed err:%v\n", err)
		return
	}
	fmt.Printf("id:%d,name:%s,age:%d", u.Id, u.Name, u.Age)
}

// queryMultiRow 查询多条数据
func queryMultiRow() []user {
	sqlStr := "select * from student"
	var users []user
	if err := db.Select(&users, sqlStr); err != nil {
		fmt.Printf("query failed err:%v\n", err)
		return nil
	}
	return users
}

// updateRow 更新数据
func updateRow() {
	sqlStr := "update student set age = ? where id = ?"
	res, err := db.Exec(sqlStr, 100, 2)
	if err != nil {
		fmt.Printf("update failed err:%v\n", err)
		return
	}
	// 受影响行数
	n, err := res.RowsAffected()
	if err != nil {
		fmt.Printf("get RowsAffected failed err:%v\n", err)
		return
	}
	fmt.Printf("update success affected rows:%d\n", n)
}

// insertRow 插入操作
func insertRow() {
	sqlStr := "insert into student (name,age) values (?,?)"
	res, err := db.Exec(sqlStr, "老王牛批", 101)
	if err != nil {
		fmt.Printf("insert failed err:%v\n", err)
		return
	}
	id, err := res.LastInsertId()
	if err != nil {
		fmt.Printf("get  LastInsertId err:%v\n", err)
		return
	}
	fmt.Printf("insert success LastInsertId:%d\n", id)
}

// deleteRow 删除操作
func deleteRow() {
	sqlStr := "delete from student where id = ?"
	res, err := db.Exec(sqlStr, 2)
	if err != nil {
		fmt.Printf("delete failed err:%v\n", err)
		return
	}
	// 受影响行数
	n, err := res.RowsAffected()
	if err != nil {
		fmt.Printf("get RowsAffected failed err:%v\n", err)
		return
	}
	fmt.Printf("delete success affected rows:%d\n", n)
}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")
	// 查询单条数据
	//querySingleRow()

	// 更新数据
	//updateRow()

	// 插入数据
	//insertRow()

	// 删除数据
	//deleteRow()

	//r := gin.Default()
	//r.GET("ping", func(c *gin.Context) {
	//	c.JSON(200, gin.H{
	//		"data": queryMultiRow(),
	//	})
	//})
	//r.Run(":1234")
}
