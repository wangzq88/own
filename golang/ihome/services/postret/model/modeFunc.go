package model

import "time"

//存储用户名和密码  mysql
func SaveUser(mobile, password_hash string) (User, error) {
	//链接数据库  gorm插入数据
	var user User
	user.Mobile = mobile
	user.Password_hash = password_hash
	user.Name = mobile

	return user, GlobalDB.Create(&user).Error
}

//存短信验证码
func GetSmsCode(phone string) (string, error) {
	return GlobalRedis.Get(ctx, phone+"_code").Result()
}

//存短信验证码
func SaveSession(sessionid string, user User) error {
	GlobalRedis.Set(ctx, sessionid+"user_id", user.ID, time.Second*3600)
	GlobalRedis.Set(ctx, sessionid+"name", user.Name, time.Second*3600)
	GlobalRedis.Set(ctx, sessionid+"mobile", user.Mobile, time.Second*3600)
	return nil
}
