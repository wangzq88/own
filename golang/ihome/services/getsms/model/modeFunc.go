package model

import (
	"time"
)

//获取图片验证码
func GetImgCode(uuid string) (string, error) {
	return GlobalRedis.Get(ctx, uuid).Result()
}

//存短信验证码
func SaveSmsCode(phone, vcode string) error {
	return GlobalRedis.Set(ctx, phone+"_code", vcode, time.Second*300).Err()
}
