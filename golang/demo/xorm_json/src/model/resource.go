package model

import (
	"xorm.io/xorm"
	_ "github.com/go-sql-driver/mysql"
	"encoding/json"
	"fmt"
	"time"
)

var DB = map[string]*xorm.Engine{}
var conf = struct {
	Name string
	Addr string
	Idle int
	Max int
	Debug bool
} {"test","root:root@tcp(127.0.0.1:3306)/test?charset=utf8&parseTime=True&loc=Asia%2FShanghai",5,5,true}

func init() {
	db, err := xorm.NewEngine("mysql", conf.Addr)
	if err != nil {
		fmt.Printf("init.mysql.error:cannot connect to mysql addr:%v err:%v \n", conf.Addr, err)
		return
	}
	db.SetMaxIdleConns(conf.Idle)
	db.SetMaxOpenConns(conf.Max)
	db.SetConnMaxLifetime(time.Hour)
	db.ShowSQL(conf.Debug)
	DB[conf.Name] = db
}

type ResourceHostTest struct {
	Id         int64           `json:"id"`
	Name       string          `json:"name"`
	PrivateIps json.RawMessage `json:"private_ips"`
	Tags       json.RawMessage `json:"tags"`
}

func (rh *ResourceHostTest) AddOne() error {
	_, err := DB[conf.Name].InsertOne(rh)
	return err
}

func (rh *ResourceHostTest) GetOne() (*ResourceHostTest, error) {

	has, err := DB[conf.Name].Get(rh)
	if err != nil {
		return nil, err
	}
	if !has {
		return nil, nil
	}
	return rh, nil

}