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

func selectNamedQuery() {
	tmpU := user{
		Age: 18,
	}
	sqlStr := "select * from student where age = :age"
	rows, err := db.NamedQuery(sqlStr, tmpU)
	if err != nil {
		fmt.Printf("named query failed err:%v\n", err)
		return
	}
	defer rows.Close()
	for rows.Next() {
		var u user
		if err := rows.StructScan(&u); err != nil {
			fmt.Printf("StructScan failed err:%v\n", err)
			continue
		}
		fmt.Println(u)
	}
}

func main() {
	if err := initializeDatabase(); err != nil {
		panic(err)
	}
	fmt.Println("connect 2 database success")

	selectNamedQuery()
}
