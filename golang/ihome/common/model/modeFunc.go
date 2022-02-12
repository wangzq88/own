package model

import (
	"encoding/json"
	"fmt"
	"strconv"
	"time"
)

func GetSessionID(sessionid string) (string, error) {
	return GlobalRedis.Get(Ctx, sessionid+"user_id").Result()
}

func GetSessionName(sessionid string) (string, error) {
	return GlobalRedis.Get(Ctx, sessionid+"name").Result()
}

func GetSessionMobile(sessionid string) (string, error) {
	return GlobalRedis.Get(Ctx, sessionid+"mobile").Result()
}

func DelSession(sessionid string) (int64, error) {
	return GlobalRedis.Del(Ctx, sessionid+"user_id", sessionid+"name", sessionid+"mobile").Result()
}

func SaveSession(sessionid string, user User) error {
	GlobalRedis.Set(Ctx, sessionid+"user_id", user.ID, time.Second*3600)
	GlobalRedis.Set(Ctx, sessionid+"name", user.Name, time.Second*3600)
	GlobalRedis.Set(Ctx, sessionid+"mobile", user.Mobile, time.Second*3600)
	return nil
}

//存短信验证码
func SaveImgRnd(uuid, rnd string) error {
	return GlobalRedis.Set(Ctx, uuid, rnd, time.Second*3600).Err()
}

//获取所有地域信息
func GetArea() ([]Area, error) {
	//连接数据库
	var areas []Area
	v1, err := GlobalRedis.Get(Ctx, "areas").Result()
	if err != nil {
		if err := GlobalDB.Find(&areas).Error; err != nil {
			return areas, err
		}
		areaJson, err := json.Marshal(areas)
		if err != nil {
			return nil, err
		}
		err = GlobalRedis.Set(Ctx, "areas", areaJson, 0).Err()
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
	return GlobalRedis.Get(Ctx, phone+"_code").Result()
}

//获取图片验证码
func GetImgCode(uuid string) (string, error) {
	return GlobalRedis.Get(Ctx, uuid).Result()
}

//存短信验证码
func SaveSmsCode(phone, vcode string) error {
	return GlobalRedis.Set(Ctx, phone+"_code", vcode, time.Second*3600).Err()
}

//存储用户名和密码  mysql
func GetUser(mobile string) (User, error) {
	//链接数据库  gorm插入数据
	var user User
	err := GlobalDB.Where("mobile = ?", mobile).First(&user).Error
	return user, err
}

//存储用户头像   更新
func SaveUserAvatar(userID int, avatarUrl string) error {
	return GlobalDB.Model(new(User)).Where("id = ?", userID).Update("avatar_url", avatarUrl).Error
}

//存储用户头像   更新
func SaveUserName(userID int, name string) error {
	return GlobalDB.Model(new(User)).Where("id = ?", userID).Update("name", name).Error
}

//存储用户头像   更新
func SaveAuth(userID int, real_name, id_card string) error {
	return GlobalDB.Model(new(User)).Where("id = ?", userID).Updates(map[string]interface{}{"real_name": real_name, "id_card": id_card}).Error
}

//
func GetHouses(userID int) ([]House, error) {
	//链接数据库  gorm插入数据
	var houses []House
	var user User
	GlobalDB.Where("id = ?", userID).First(&user)
	err := GlobalDB.Model(&user).Related(&houses).Error
	return houses, err
}

//发布房屋
func AddHouse(housedata map[string]interface{}) (uint, error) {
	var houseInfo House
	//给house赋值
	fmt.Println("我的值是：%v", housedata)
	//sql中一对多插入,只是给外键赋值
	userId, _ := housedata["user_id"].(int)
	houseInfo.UserId = uint(userId)
	houseInfo.Title = housedata["title"].(string)
	houseInfo.Address = housedata["address"].(string)
	//类型转换
	price, _ := strconv.Atoi(housedata["price"].(string))
	roomCount, _ := strconv.Atoi(housedata["room_count"].(string))
	houseInfo.Price = price * 100
	houseInfo.Room_count = roomCount
	houseInfo.Unit = housedata["unit"].(string)
	houseInfo.Capacity, _ = strconv.Atoi(housedata["capacity"].(string))
	houseInfo.Beds = housedata["beds"].(string)
	houseInfo.Deposit, _ = strconv.Atoi(housedata["deposit"].(string))
	houseInfo.Min_days, _ = strconv.Atoi(housedata["min_days"].(string))
	houseInfo.Max_days, _ = strconv.Atoi(housedata["max_days"].(string))
	houseInfo.Acreage, _ = strconv.Atoi(housedata["acreage"].(string))
	//一对多插入
	areaId, _ := strconv.Atoi(housedata["area_id"].(string))
	houseInfo.AreaId = uint(areaId)

	//request.Facility    所有的家具  房屋
	for _, v := range housedata["facility"].([]interface{}) {
		id, _ := strconv.Atoi(v.(string))
		var fac Facility
		if err := GlobalDB.Where("id = ?", id).First(&fac).Error; err != nil {
			//fmt.Println("家具id错误", err)
			return 0, err
		}
		//查询到了数据
		houseInfo.Facilities = append(houseInfo.Facilities, &fac)
	}

	if err := GlobalDB.Create(&houseInfo).Error; err != nil {
		//fmt.Println("插入房屋信息失败", err)
		return 0, err
	}
	return houseInfo.ID, nil
}

func SaveHouseImage(houseID int, index_image_url string) error {
	var houseimg HouseImage
	houseimg.HouseId = uint(houseID)
	houseimg.Url = index_image_url
	GlobalDB.Create(&houseimg)
	return GlobalDB.Model(new(House)).Where("id = ?", houseID).Update("index_image_url", index_image_url).Error
}

func GetTotalHouseCount(areaId int, dur time.Duration) (int64, error) {
	var count int64
	err := GlobalDB.Model(&House{}).Where("area_id = ?", areaId).
		Where("min_days <= ?", dur.Hours()/24).
		Where("max_days >= ?", dur.Hours()/24).
		Count(&count).Error
	return count, err
}

func SearchHouses(areaId int, dur time.Duration, page int64) (houseList []interface{}, err error) {
	var houseInfos []House
	var limit int64 = 10
	start := (page - 1) * limit
	if err = GlobalDB.Where("area_id = ?", areaId).Limit(limit).Offset(start).
		Where("min_days <= ?", dur.Hours()/24).
		Where("max_days >= ?", dur.Hours()/24).
		Order("created_at desc").Find(&houseInfos).Error; err != nil {
		return
	}
	fmt.Println("houseInfos 的值 %+v", houseInfos)
	//解析数据
	for _, val := range houseInfos {
		var area Area
		var user User

		GlobalDB.Model(&val).Related(&area).Related(&user)
		house := make(map[string]interface{})
		house["area_name"] = area.Name
		house["address"] = val.Address
		house["ctime"] = val.CreatedAt.Format("2006-01-02 15:04:05")
		house["house_id"] = val.ID
		house["img_url"] = "http://" + AvatarDomain + "/" + val.Index_image_url
		house["order_count"] = val.Order_count
		house["price"] = val.Price
		house["room_count"] = val.Room_count
		house["title"] = val.Title
		house["user_avatar"] = "http://" + AvatarDomain + "/" + user.Avatar_url
		houseList = append(houseList, house)
	}
	return
}

func GetIndexHouse() (houseList []interface{}, err error) {
	var houseInfos []House
	if err = GlobalDB.Limit(5).Order("created_at desc").Find(&houseInfos).Error; err != nil {
		return
	}
	//解析数据
	for _, val := range houseInfos {
		var area Area
		var user User

		GlobalDB.Model(&val).Related(&area).Related(&user)
		house := make(map[string]interface{})
		house["area_name"] = area.Name
		house["address"] = val.Address
		house["ctime"] = val.CreatedAt.Format("2006-01-02 15:04:05")
		house["house_id"] = val.ID
		house["img_url"] = "http://" + AvatarDomain + "/" + val.Index_image_url
		house["order_count"] = val.Order_count
		house["price"] = val.Price
		house["room_count"] = val.Room_count
		house["title"] = val.Title
		house["user_avatar"] = "http://" + AvatarDomain + "/" + user.Avatar_url
		houseList = append(houseList, house)
	}
	return
}

func GetHouseDetail(houseID int64, house map[string]interface{}) error {
	var area Area
	var user User
	var val House

	if err := GlobalDB.Where("id = ?", houseID).First(&val).Error; err != nil {
		return err
	}
	GlobalDB.Model(&val).Related(&area).Related(&user)
	var img_urls []string
	house["area_name"] = area.Name
	house["address"] = val.Address
	house["ctime"] = val.CreatedAt.Format("2006-01-02 15:04:05")
	house["house_id"] = val.ID
	house["img_url"] = "http://" + AvatarDomain + "/" + val.Index_image_url
	img_urls = append(img_urls, house["img_url"].(string))
	house["img_urls"] = img_urls
	house["order_count"] = val.Order_count
	house["price"] = val.Price
	house["deposit"] = val.Deposit
	house["room_count"] = val.Room_count
	house["title"] = val.Title
	house["user_avatar"] = "http://" + AvatarDomain + "/" + user.Avatar_url
	house["user_id"] = user.ID
	house["min_days"] = val.Min_days
	house["max_days"] = val.Max_days
	house["capacity"] = val.Capacity
	house["beds"] = val.Beds
	house["acreage"] = val.Acreage
	house["unit"] = val.Unit
	//获取房屋的家具信息  多对多查询
	var facs []Facility
	if err := GlobalDB.Model(&val).Related(&facs, "Facilities").Error; err != nil {
		return err
	}
	var facilities []int32
	for _, v := range facs {
		facilities = append(facilities, int32(v.Id))
	}
	house["facilities"] = facilities
	//评论
	var orders []OrderHouse
	if err := GlobalDB.Model(&val).Related(&orders).Error; err != nil {
		return err
	}
	var commentList []interface{}
	for _, v := range orders {
		commentTemp := make(map[string]interface{})
		commentTemp["comment"] = v.Comment
		commentTemp["ctime"] = v.CreatedAt.Format("2006-01-02 15:04:05")
		var tempUser User
		GlobalDB.Model(&v).Related(&tempUser)
		commentTemp["userName"] = tempUser.Name
		commentList = append(commentList, &commentTemp)
	}
	house["comments"] = commentList
	return nil
}

func InsertOrder(userID, houseId int64, beginDate, endDate string) (int64, error) {
	//获取插入对象
	var order OrderHouse

	//给对象赋值
	order.HouseId = uint(houseId)

	//把string类型的时间转换为time类型
	bDate, _ := time.Parse("2006-01-02", beginDate)
	order.Begin_date = bDate

	eDate, _ := time.Parse("2006-01-02", endDate)
	order.End_date = eDate

	//获取days
	dur := eDate.Sub(bDate)
	order.Days = int(dur.Hours()) / 24
	order.Status = "WAIT_ACCEPT"

	//房屋的单价和总价
	var house House
	GlobalDB.Where("id = ?", houseId).Find(&house).Select("price")
	order.House_price = house.Price
	order.Amount = house.Price * order.Days

	order.UserId = uint(userID)
	if err := GlobalDB.Create(&order).Error; err != nil {
		return 0, err
	}
	return int64(order.ID), nil
}

func GetOrder(userID int64, role string) ([]interface{}, error) {
	var orderList []interface{}
	var orders []OrderHouse
	if role == "custom" {
		if err := GlobalDB.Where("user_id = ?", userID).Find(&orders).Error; err != nil {
			return nil, err
		}
	} else {
		var houses []House
		if err := GlobalDB.Where("user_id = ?", userID).Find(&houses).Error; err != nil {
			return nil, err
		}
		for _, v := range houses {
			var order []OrderHouse
			if err := GlobalDB.Model(&v).Related(&order).Error; err != nil {
				return nil, err
			}
			orders = append(orders, order...)
		}
	}
	//循环遍历一下orders
	for _, v := range orders {
		orderTemp := make(map[string]interface{})
		orderTemp["order_id"] = v.ID
		orderTemp["end_date"] = v.End_date.Format("2006-01-02")
		orderTemp["start_date"] = v.Begin_date.Format("2006-01-02")
		orderTemp["ctime"] = v.CreatedAt.Format("2006-01-02")
		orderTemp["amount"] = v.Amount
		orderTemp["comment"] = v.Comment
		orderTemp["days"] = v.Days
		orderTemp["status"] = v.Status

		//关联house表
		var house House
		GlobalDB.Model(&v).Related(&house).Select("index_image_url", "title")
		orderTemp["img_url"] = "http://" + AvatarDomain + "/" + house.Index_image_url
		orderTemp["title"] = house.Title

		orderList = append(orderList, orderTemp)
	}
	return orderList, nil
}

//更新订单状态
func UpdateStatus(id int64, action, reason string) error {
	db := GlobalDB.Model(new(OrderHouse)).Where("id = ?", id)

	if action == "accept" {
		//标示房东同意订单
		return db.Update("status", "WAIT_COMMENT").Error
	} else {
		//表示房东不同意订单  如果拒单把拒绝的原因写到comment中
		return db.Updates(map[string]interface{}{"status": "REJECTED", "comment": reason}).Error
	}
}

func AddOrderComment(id int64, comment string) error {
	return GlobalDB.Model(new(OrderHouse)).Where("id = ?", id).Update("comment", comment).Error
}
