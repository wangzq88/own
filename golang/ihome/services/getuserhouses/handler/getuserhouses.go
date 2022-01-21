package handler

import (
	"context"
	"encoding/json"
	"math"
	"strconv"
	"time"

	log "github.com/micro/micro/v3/service/logger"
	"github.com/weilaihui/fdfs_client"
	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/getuserhouses"
	"go.micro.srv/common/utils"
)

type Getuserhouses struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getuserhouses) Getuserhousescd(ctx context.Context, req *getuserhouses.Request, rsp *getuserhouses.Response) error {
	log.Info("Received Getuserinfo.Getuserhousescd request")
	mobile, err := model.GetSessionMobile(req.Sessionid)
	if err != nil {
		rsp.Errno = utils.RECODE_SESSIONERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SESSIONERR)
		return nil
	}

	user, err := model.GetUser(mobile)
	if err != nil {
		rsp.Errno = utils.RECODE_USERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERERR)
		return nil
	}
	houses, err := model.GetHouses(user.ID)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	log.Infof("houses 的值 %v", houses)
	result := model.UserHouseRsp{
		user,
		houses,
	}
	jsonstr, _ := json.Marshal(&result)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Mix = jsonstr
	return nil
}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getuserhouses) Postuserhousecd(ctx context.Context, req *getuserhouses.PostRequest, rsp *getuserhouses.PostResponse) error {
	log.Info("Received Getuserinfo.Postuserhousecd request")
	mobile, err := model.GetSessionMobile(req.Sessionid)
	if err != nil {
		rsp.Errno = utils.RECODE_SESSIONERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SESSIONERR)
		return nil
	}

	user, err := model.GetUser(mobile)
	if err != nil {
		rsp.Errno = utils.RECODE_USERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERERR)
		return nil
	}
	housedata := make(map[string]interface{})
	housedata["user_id"] = user.ID
	if err = json.Unmarshal(req.Housejson, &housedata); err != nil {
		rsp.Errno = utils.RECODE_PARAMERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_PARAMERR)
		return nil
	}
	house_id, err := model.AddHouse(housedata)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.HouseId = int64(house_id)
	return nil
}

func (e *Getuserhouses) UploadHouseImg(ctx context.Context, req *getuserhouses.UploadImgRequest, rsp *getuserhouses.UploadImgResponse) error {
	log.Info("Received Getuserinfo.UploadHouseImg request")
	fdfsClient, err := fdfs_client.NewFdfsClient("/etc/fdfs/client.conf")
	if err != nil {
		rsp.Errno = utils.RECODE_USERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERERR)
		return nil
	}
	fdfsResp, err := fdfsClient.UploadByBuffer(req.HouseImage, req.FileExt)
	if err != nil {
		rsp.Errno = utils.RECODE_IOERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_IOERR)
		return nil
	}
	model.SaveHouseImage(int(req.HouseId), fdfsResp.RemoteFileId)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.AvatarUrl = fdfsResp.RemoteFileId
	return nil

}

func (e *Getuserhouses) SearchHouses(ctx context.Context, req *getuserhouses.SearchRequest, rsp *getuserhouses.SearchResponse) error {
	log.Info("Received Getuserinfo.SearchHouses request")
	//获取参数
	aid, _ := strconv.Atoi(req.Aid)
	//先把string类型转为time类型
	sdTime, _ := time.Parse("2006-01-02", req.Sd)
	edTime, _ := time.Parse("2006-01-02", req.Ed)
	dur := edTime.Sub(sdTime)
	page := req.P
	total, _ := model.GetTotalHouseCount(aid, dur)
	totalPage := math.Ceil(float64(total) / float64(10))
	if page > int64(totalPage) {
		page = int64(totalPage)
	}
	//查询数据库
	houseList, err := model.SearchHouses(aid, dur, page)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	log.Infof("houseList 的值 %+v", houseList)
	jsonstr, _ := json.Marshal(&houseList)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Houses = jsonstr
	rsp.TotalPage = int64(totalPage)
	return nil
}

func (e *Getuserhouses) GetIndex(ctx context.Context, req *getuserhouses.IndexRequest, rsp *getuserhouses.IndexResponse) error {
	log.Info("Received Getuserinfo.GetIndex request")
	houseList, err := model.GetIndexHouse()
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	jsonstr, _ := json.Marshal(&houseList)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Houses = jsonstr
	return nil
}

func (e *Getuserhouses) GetHousesDetail(ctx context.Context, req *getuserhouses.DetailRequest, rsp *getuserhouses.DetailResponse) error {
	log.Info("Received Getuserinfo.GetHousesDetail request")
	var user_id int64
	if req.Sessionid != "" {
		mobile, err := model.GetSessionMobile(req.Sessionid)
		if err == nil {
			user, err := model.GetUser(mobile)
			if err == nil {
				user_id = int64(user.ID)
			}
		}
	}
	//查询数据库
	house := make(map[string]interface{})
	err := model.GetHouseDetail(req.HouseId, house)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	jsonstr, _ := json.Marshal(&house)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Houses = jsonstr
	rsp.UserId = user_id
	return nil
}
