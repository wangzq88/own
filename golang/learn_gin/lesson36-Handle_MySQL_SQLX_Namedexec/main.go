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

// batchInsert 批量插入
func batchInsert() {
	users := []user{
		{Name: "111", Age: 10},
		{Name: "222", Age: 10},
		{Name: "333", Age: 10},
	}
	sqlStr := "insert into student (name,age) values (:name,:age)"
	_, err := db.NamedExec(sqlStr, users)
	if err != nil {
		fmt.Printf("batchInsert failed, err:%v\n", err)
		return
	}
	fmt.Println("success")
}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")
	batchInsert()
}
