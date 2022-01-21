package model

import (
	"encoding/json"
	"fmt"
)

//获取所有地域信息
func GetArea() ([]Area, error) {
	//连接数据库
	var areas []Area
	v1, err := GlobalRedis.Get(ctx, "areas").Result()
	if err != nil {
		if err := GlobalDB.Find(&areas).Error; err != nil {
			return areas, err
		}
		areaJson, err := json.Marshal(areas)
		if err != nil {
			return nil, err
		}
		err = GlobalRedis.Set(ctx, "areas", areaJson, 0).Err()
		fmt.Println("写入 redis 中")
		if err != nil {
			return nil, err
		}
		fmt.Println("从mysql中获取数据")
	} else {
		json.Unmarshal([]byte(v1), &areas)
		fmt.Println("从redis中获取数据")
	}

	return areas, nil
}
