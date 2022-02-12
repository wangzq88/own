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
		Addr:     "localhost:6390",
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

	// set
	err := rdb.Set(ctx, "name", "value1", 0).Err()
	if err != nil {
		panic(err)
	}
	// get
	/*
		func (cmd *StringCmd) Val() string {
			return cmd.val
		}

		func (cmd *StringCmd) Result() (string, error) {
			return cmd.Val(), cmd.err
		}
	*/
	v1, err := rdb.Get(ctx, "name").Result()
	if err != nil {
		panic(err)
	}
	fmt.Println("name", v1)

	// get val 不返回err
	v2 := rdb.Get(ctx, "name").Val()
	fmt.Println("name", v2)

	// redis.nil
	val2, err := rdb.Get(ctx, "key2").Result()
	if err == redis.Nil {
		fmt.Println("key2 does not exist")
	} else if err != nil {
		panic(err)
	} else {
		fmt.Println("key2", val2)
	}

	hGetAllDemo()
}

func hGetAllDemo() {
	v := rdb.HGetAll(ctx, "user").Val()
	fmt.Println(v)

	v2 := rdb.HMGet(ctx, "user", "name", "age").Val()
	fmt.Println(v2)

	v3 := rdb.HGet(ctx, "user", "age")
	fmt.Println(v3)
}
