package model

import gb "go.micro.srv/common/model"

func GetSessionID(sessionid string) (string, error) {
	return gb.GlobalRedis.Get(gb.Ctx, sessionid+"user_id").Result()
}

func GetSessionName(sessionid string) (string, error) {
	return gb.GlobalRedis.Get(gb.Ctx, sessionid+"name").Result()
}

func DelSession(sessionid string) (int64, error) {
	return gb.GlobalRedis.Del(gb.Ctx, sessionid+"user_id", sessionid+"name", sessionid+"mobile").Result()
}
