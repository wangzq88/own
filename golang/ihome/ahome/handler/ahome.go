package handler

import (
	"ahome/proto/getarea"
	"ahome/proto/getimg"
	"ahome/proto/getsms"
	"ahome/proto/postlogin"
	"ahome/proto/postret"
	"context"
	"encoding/json"
	"fmt"
	"image"
	"image/png"
	"io/ioutil"
	"net/http"
	"path"
	"reflect"
	"strconv"
	"time"

	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/getorder"
	"go.micro.srv/common/proto/getsession"
	"go.micro.srv/common/proto/getuserhouses"
	"go.micro.srv/common/proto/getuserinfo"
	"go.micro.srv/common/proto/postauth"
	"go.micro.srv/common/utils"

	"github.com/afocus/captcha"
	"github.com/gin-gonic/gin"
	"github.com/gin-gonic/gin/binding"
	"github.com/go-playground/validator/v10"
	"github.com/micro/micro/v3/service"
)

//获取所有地区信息
func GetArea(ctx *gin.Context) {
	// create and initialise a new service
	srv := service.New()

	// create the proto client for getarea
	client := getarea.NewGetAreaService("go.micro.srv.getarea", srv.Client())

	// call an endpoint on the service
	rsp, err := client.MicroGetArea(context.Background(), &getarea.Request{})
	if err != nil {
		fmt.Println("Error calling getarea: ", err)
		return
	}

	// print the response
	fmt.Println("Response: ", rsp.Errmsg)
	areaList := []model.Area{}
	for _, value := range rsp.Data {
		tmp := model.Area{Id: int(value.Aid), Name: value.Aname}
		areaList = append(areaList, tmp)
	}
	result := map[string]interface{}{
		"errno":  rsp.Errno,
		"errmsg": rsp.Errmsg,
		"data":   areaList,
	}
	fmt.Println("result: ", result)
	//把int 的0值  json的特性,如果字段是零值,不对这个字段做序列化
	ctx.JSON(http.StatusOK, result)
}

func GetImgCd(ctx *gin.Context) {
	uuid := ctx.Param("uuid")
	// create and initialise a new service
	srv := service.New()

	// create the proto client for getarea
	client := getimg.NewGetImgService("go.micro.srv.getimg", srv.Client())

	// call an endpoint on the service
	rsp, err := client.MicroGetImg(context.Background(), &getimg.Request{Uuid: uuid})
	if err != nil {
		fmt.Println("Error calling getarea: ", err)
		return
	}
	var rgba image.RGBA
	rgba.Pix = []uint8(rsp.Pix)
	rgba.Stride = int(rsp.Stride)
	rgba.Rect.Min.X = int(rsp.Min.X)
	rgba.Rect.Min.Y = int(rsp.Min.Y)
	rgba.Rect.Max.X = int(rsp.Max.X)
	rgba.Rect.Max.Y = int(rsp.Max.Y)
	var image captcha.Image
	image.RGBA = &rgba
	png.Encode(ctx.Writer, image)
}

func GetSmscd(ctx *gin.Context) {
	//获取数据
	mobile := ctx.Param("mobile")
	//获取输入的图片验证码
	text := ctx.Query("text")
	//获取验证码图片的uuid
	uuid := ctx.Query("id")

	//校验数据
	if mobile == "" || text == "" || uuid == "" {
		fmt.Println("传入数据不完整")
		return
	}

	//处理数据  放在服务端处理
	// create and initialise a new service
	srv := service.New()
	//初始化客户端
	client := getsms.NewGetsmsService("go.micro.srv.getsms", srv.Client())
	//调用远程客户端
	rsp, err := client.SmsCode(context.Background(), &getsms.Request{
		Mobile: mobile,
		Text:   text,
		Uuid:   uuid,
	})
	if err != nil {
		fmt.Println("Error calling getsms: ", err)
		return
	}
	result := map[string]interface{}{
		"errno":  rsp.Errno,
		"errmsg": rsp.Errmsg,
	}
	ctx.JSON(http.StatusOK, result)
}

//注册方法
type RegStu struct {
	Mobile   string `json:"mobile"`
	PassWord string `json:"password"`
	SmsCode  string `json:"sms_code"`
}

func PostRet(ctx *gin.Context) {
	var reg RegStu
	err := ctx.Bind(&reg)
	result := map[string]interface{}{
		"errno":  utils.RECODE_PARAMERR,
		"errmsg": utils.RecodeText(utils.RECODE_PARAMERR),
	}
	//defer ctx.JSON(http.StatusOK, result)
	//校验数据
	if err != nil {
		ctx.JSON(http.StatusOK, result)
		return
	}
	srv := service.New()
	//初始化客户端
	client := postret.NewPostretService("go.micro.srv.postret", srv.Client())
	//调用远程客户端
	rsp, _ := client.Register(context.Background(), &postret.Request{
		Mobile:   reg.Mobile,
		Password: reg.PassWord,
		SmsCode:  reg.SmsCode,
	})
	val, err := ctx.Cookie("sessionid")
	if err != nil || val == "" {
		ctx.SetCookie("sessionid", rsp.Name, 3600, "/", "129.226.134.90", false, true)
	}
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	ctx.JSON(http.StatusOK, result)
}

func Session(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_LOGINERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_LOGINERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := getsession.NewGetsessionService("go.micro.srv.getsession", srv.Client())
	//调用远程客户端
	rsp, _ := client.Getssessioncd(context.Background(), &getsession.Request{
		Sessionid: sessionid,
	})
	data := make(map[string]interface{})
	data["name"] = rsp.Name
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func DelSession(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := getsession.NewGetsessionService("go.micro.srv.getsession", srv.Client())
	//调用远程客户端
	rsp, _ := client.Delssessioncd(context.Background(), &getsession.Request{
		Sessionid: sessionid,
	})
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
}

func Login(ctx *gin.Context) {
	var reg RegStu
	err := ctx.Bind(&reg)
	result := map[string]interface{}{
		"errno":  utils.RECODE_PARAMERR,
		"errmsg": utils.RecodeText(utils.RECODE_PARAMERR),
	}
	if reg.Mobile == "" || reg.PassWord == "" {
		fmt.Println("传入数据不完整")
		ctx.JSON(http.StatusOK, result)
		return
	}
	//defer ctx.JSON(http.StatusOK, result)
	if err != nil {
		result["data"] = fmt.Sprintf("值%v", reg)
		ctx.JSON(http.StatusOK, result)
		return
	}
	sessionid, err := ctx.Cookie("sessionid")
	if err == nil || sessionid != "" {
		result["errno"] = utils.RECODE_OK
		result["errmsg"] = utils.RecodeText(utils.RECODE_OK)
		ctx.JSON(http.StatusOK, result)
		return
	}
	srv := service.New()
	//初始化客户端
	client := postlogin.NewPostloginService("go.micro.srv.postlogin", srv.Client())
	//调用远程客户端
	rsp, err := client.Login(context.Background(), &postlogin.Request{
		Mobile:   reg.Mobile,
		Password: reg.PassWord,
	})
	if err == nil {
		ctx.SetCookie("sessionid", rsp.Sessionid, 3600, "/", "129.226.134.90", false, true)
	}
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	ctx.JSON(http.StatusOK, result)
}

func GetUser(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := getuserinfo.NewGetuserinfoService("go.micro.srv.getuserinfo", srv.Client())
	//调用远程客户端
	rsp, _ := client.Getuserinfocd(context.Background(), &getuserinfo.Request{
		Sessionid: sessionid,
	})
	data := make(map[string]interface{})
	data["name"] = rsp.Name
	data["mobile"] = rsp.Mobile
	data["real_name"] = rsp.RealName
	data["id_card"] = rsp.IdCard
	data["avatar_url"] = rsp.AvatarUrl
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func UploadAvatar(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	fileHeader, err := ctx.FormFile("avatar")
	//检验数据
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	//三种校验 大小,类型,防止重名  fastdfs
	if fileHeader.Size > 50000000 {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	fileExt := path.Ext(fileHeader.Filename)
	if fileExt != ".png" && fileExt != ".jpg" {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	file, _ := fileHeader.Open()
	buf := make([]byte, fileHeader.Size)
	file.Read(buf)
	srv := service.New()
	//初始化客户端
	client := getuserinfo.NewGetuserinfoService("go.micro.srv.getuserinfo", srv.Client())
	//调用远程客户端
	rsp, _ := client.UploadAvatar(context.Background(), &getuserinfo.UploadReq{
		Sessionid: sessionid,
		Avatar:    buf,
		FileExt:   fileExt[1:],
	})
	data := make(map[string]interface{})
	data["avatar_url"] = "http://" + model.AvatarDomain + "/" + rsp.AvatarUrl
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

type AuthParams struct {
	IdCard   string `json:"id_card"`
	RealName string `json:"real_name"`
}

func Auth(ctx *gin.Context) {
	var param AuthParams
	err := ctx.Bind(&param)
	result := map[string]interface{}{
		"errno":  utils.RECODE_PARAMERR,
		"errmsg": utils.RecodeText(utils.RECODE_PARAMERR),
	}
	defer ctx.JSON(http.StatusOK, result)
	if param.IdCard == "" || param.RealName == "" {
		fmt.Println("传入数据不完整")
		ctx.JSON(http.StatusOK, result)
		return
	}
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := postauth.NewPostauthService("go.micro.srv.postauth", srv.Client())
	//调用远程客户端
	rsp, err := client.Auth(context.Background(), &postauth.Request{
		Sessionid: sessionid,
		RealName:  param.RealName,
		IdCard:    param.IdCard,
	})
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
}

/*
func (this *House) ToHouseInfo() interface{} {
	house_info := map[string]interface{}{
		"house_id":    this.Id,
		"title":       this.Title,
		"price":       this.Price,
		"area_name":   this.Area.Name,
		"img_url":     this.Index_image_url,
		"room_count":  this.Room_count,
		"order_count": this.Order_count,
		"address":     this.Address,
		"user_avatar": this.User.Avatar_url,
		"ctime":       this.CreatedAt,
	}
	return house_info
}*/

func GetUserHouses(ctx *gin.Context) {
	result := make(map[string]interface{})
	sessionid, err := ctx.Cookie("sessionid")
	defer ctx.JSON(http.StatusOK, result)
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	fmt.Print("有运行到这里")
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, err := client.Getuserhousescd(context.Background(), &getuserhouses.Request{
		Sessionid: sessionid,
	})
	houseData := model.UserHouseRsp{}
	json.Unmarshal(rsp.Mix, &houseData)
	fmt.Printf("哈哈%v", houseData)
	//house_info := make(map[string]interface{})

	var houseList []interface{}
	for _, value := range houseData.Houses {
		house_info := map[string]interface{}{
			"house_id":    value.ID,
			"title":       value.Title,
			"price":       value.Price,
			"area_name":   value.AreaId,
			"img_url":     value.Index_image_url,
			"room_count":  value.Room_count,
			"order_count": value.Order_count,
			"address":     value.Address,
			"user_avatar": houseData.User.Avatar_url,
			"ctime":       value.CreatedAt,
		}
		houseList = append(houseList, house_info)
	}
	data := make(map[string]interface{})
	data["houses"] = houseList

	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func PostHouses(ctx *gin.Context) {
	body, _ := ioutil.ReadAll(ctx.Request.Body)
	result := map[string]interface{}{
		"errno":  utils.RECODE_SESSIONERR,
		"errmsg": utils.RecodeText(utils.RECODE_SESSIONERR),
	}
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, err := client.Postuserhousecd(context.Background(), &getuserhouses.PostRequest{
		Sessionid: sessionid,
		Housejson: body,
	})
	data := make(map[string]interface{})
	data["house_id"] = rsp.HouseId
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func PostHousesImages(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	house_id, _ := strconv.Atoi(ctx.Param("house_id"))
	fileHeader, err := ctx.FormFile("house_image")
	//检验数据
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	//三种校验 大小,类型,防止重名  fastdfs
	if fileHeader.Size > 50000000 {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	fileExt := path.Ext(fileHeader.Filename)
	if fileExt != ".png" && fileExt != ".jpg" {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	file, _ := fileHeader.Open()
	buf := make([]byte, fileHeader.Size)
	file.Read(buf)
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, _ := client.UploadHouseImg(context.Background(), &getuserhouses.UploadImgRequest{
		Sessionid:  sessionid,
		HouseImage: buf,
		FileExt:    fileExt[1:],
		HouseId:    int64(house_id),
	})
	data := make(map[string]interface{})
	data["url"] = "http://" + model.AvatarDomain + "/" + rsp.AvatarUrl
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func GetHousesDetail(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	house_id, _ := strconv.Atoi(ctx.Param("house_id"))
	sessionid, _ := ctx.Cookie("sessionid")
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, _ := client.GetHousesDetail(context.Background(), &getuserhouses.DetailRequest{
		HouseId:   int64(house_id),
		Sessionid: sessionid,
	})
	houses := make(map[string]interface{})
	json.Unmarshal(rsp.Houses, &houses)
	data := make(map[string]interface{})
	data["house"] = houses
	data["user_id"] = rsp.UserId
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func SearchHouses(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	aid := ctx.Query("aid")
	sd := ctx.Query("sd")
	ed := ctx.Query("ed")
	sk := ctx.Query("sk")
	page := ctx.Query("p")
	p, _ := strconv.Atoi(page)
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, _ := client.SearchHouses(context.Background(), &getuserhouses.SearchRequest{
		Aid: aid,
		Sd:  sd,
		Ed:  ed,
		Sk:  sk,
		P:   int64(p),
	})
	var houses []interface{}
	json.Unmarshal(rsp.Houses, &houses)
	data := make(map[string]interface{})
	data["houses"] = houses
	data["total_page"] = rsp.TotalPage
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func GetIndex(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	srv := service.New()
	//初始化客户端
	client := getuserhouses.NewGetuserhousesService("go.micro.srv.getuserhouses", srv.Client())
	//调用远程客户端
	rsp, _ := client.GetIndex(context.Background(), &getuserhouses.IndexRequest{})
	var houses []interface{}
	json.Unmarshal(rsp.Houses, &houses)
	data := make(map[string]interface{})
	data["houses"] = houses
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

//参考 https://juejin.cn/post/6844904114699108365
type OrderParams struct {
	HouseId   int64           `json:"house_id" binding:"required"`
	StartDate model.LocalTime `json:"start_date" binding:"required" time_format:"2006-01-02"`
	EndDate   model.LocalTime `json:"end_date" binding:"required" time_format:"2006-01-02"`
}

func ValidateJSONDateType(field reflect.Value) interface{} {
	if field.Type() == reflect.TypeOf(model.LocalTime{}) {
		timeStr := field.Interface().(model.LocalTime).String()
		// 0001-01-01 00:00:00 是 go 中 time.Time 类型的空值
		// 这里返回 Nil 则会被 validator 判定为空值，而无法通过 `binding:"required"` 规则
		if timeStr == "0001-01-01" {
			return nil
		}
		return timeStr
	}
	return nil
}

func PostOrders(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	if v, ok := binding.Validator.Engine().(*validator.Validate); ok {
		// 注册 model.LocalTime 类型的自定义校验规则
		v.RegisterCustomTypeFunc(ValidateJSONDateType, model.LocalTime{})
	}

	var param OrderParams
	if err := ctx.ShouldBindJSON(&param); err != nil {
		result["errno"] = utils.RECODE_DATAERR
		result["errmsg"] = err.Error()
		return
	}
	fmt.Printf("打印请求参数%+v", param)
	srv := service.New()
	//初始化客户端
	client := getorder.NewGetUserOrderService("go.micro.srv.getorder", srv.Client())
	//调用远程客户端
	rsp, _ := client.PostOrder(context.Background(), &getorder.PostRequest{
		HouseId:   param.HouseId,
		StartDate: time.Time(param.StartDate).Format("2006-01-02"),
		EndDate:   time.Time(param.EndDate).Format("2006-01-02"),
		Sessionid: sessionid,
	})
	data := make(map[string]interface{})
	data["order_id"] = rsp.OrderId
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

func GetOrders(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	sessionid, err := ctx.Cookie("sessionid")
	if err != nil {
		result["errno"] = utils.RECODE_SESSIONERR
		result["errmsg"] = utils.RecodeText(utils.RECODE_SESSIONERR)
		return
	}
	srv := service.New()
	//初始化客户端
	client := getorder.NewGetUserOrderService("go.micro.srv.getorder", srv.Client())
	//调用远程客户端
	rsp, _ := client.GetMyOrder(context.Background(), &getorder.MyRequest{
		Sessionid: sessionid,
		Role:      ctx.Query("role"),
	})
	var orders []interface{}
	json.Unmarshal(rsp.Orderjson, &orders)
	data := make(map[string]interface{})
	data["orders"] = orders
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
	result["data"] = data
}

type StatusStu struct {
	Action string `json:"action"`
	Reason string `json:"reason"`
}

func SetOrderStatus(ctx *gin.Context) {
	result := make(map[string]interface{})
	defer ctx.JSON(http.StatusOK, result)
	var statusStu StatusStu
	ctx.Bind(&statusStu)
	order_id, _ := strconv.Atoi(ctx.Param("order_id"))
	srv := service.New()
	//初始化客户端
	client := getorder.NewGetUserOrderService("go.micro.srv.getorder", srv.Client())
	//调用远程客户端
	rsp, _ := client.PutOrderStatus(context.Background(), &getorder.SetStatusRequest{
		OrderId: int64(order_id),
		Action:  statusStu.Action,
		Reason:  statusStu.Reason,
	})
	result["errno"] = rsp.Errno
	result["errmsg"] = rsp.Errmsg
}
