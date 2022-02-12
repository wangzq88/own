package main

import (
	"context"
	"fmt"

	"github.com/go-redis/redis/v8"
)

var ctx = context.Background()
var rdb *redis.Client

func initializeRedisClient() (err error) {
	rdb = redis.NewClient(&redis.Options{
		Addr:     "localhost:6391",
		Password: "",
		DB:       0,
		// 连接池大小
		PoolSize: 100,
	})
	_, err = rdb.Ping(ctx).Result()
	return
}

func main() {
	if err := initializeRedisClient(); err != nil {
		fmt.Printf("connect 2 redis failed err:%v\n", err)
		panic(err)
	}

	fmt.Println("connect 2 redis success")

}
