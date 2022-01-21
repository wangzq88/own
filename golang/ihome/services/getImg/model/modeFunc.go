package model

import (
	"context"
	"time"

	"github.com/go-redis/redis/v8"
)

var ctx = context.Background()
var GlobalRedis *redis.Client

//初始化redis链接
func initializeRedisClient() (err error) {
	GlobalRedis = redis.NewClient(&redis.Options{
		Addr:     "localhost:6873",
		Password: "msghiplus@!6973",
		DB:       1,
		// 连接池大小
		PoolSize: 100,
	})
	_, err = GlobalRedis.Ping(ctx).Result()
	return
}

func init() {
	initializeRedisClient()
}

//存短信验证码
func SaveImgRnd(uuid, rnd string) error {
	return GlobalRedis.Set(ctx, uuid, rnd, time.Second*3600).Err()
}
