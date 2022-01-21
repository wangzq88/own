package model

import "time"

//存储用户名和密码  mysql
func GetUser(mobile string) (User, error) {
	//链接数据库  gorm插入数据
	var user User
	err := GlobalDB.Where("mobile = ?", mobile).First(&user).Error
	return user, err
}

func SaveSession(sessionid string, user User) error {
	GlobalRedis.Set(ctx, sessionid+"user_id", user.ID, time.Second*3600)
	GlobalRedis.Set(ctx, sessionid+"name", user.Name, time.Second*3600)
	GlobalRedis.Set(ctx, sessionid+"mobile", user.Mobile, time.Second*3600)
	return nil
}
