package handler

import (
	"context"
	"encoding/json"

	log "github.com/micro/micro/v3/service/logger"
	"github.com/weilaihui/fdfs_client"
	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/getuserinfo"
	"go.micro.srv/common/utils"
)

type Getuserinfo struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getuserinfo) Getuserinfocd(ctx context.Context, req *getuserinfo.Request, rsp *getuserinfo.Response) error {
	log.Info("Received Getuserinfo.Getuserinfocd request")
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

	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.UserId = int64(user.ID)
	rsp.Name = user.Name
	rsp.Mobile = user.Mobile
	rsp.RealName = user.Real_name
	rsp.IdCard = user.Id_card
	rsp.AvatarUrl = user.Avatar_url

	return nil
}

func (e *Getuserinfo) UploadAvatar(ctx context.Context, req *getuserinfo.UploadReq, rsp *getuserinfo.UploadResp) error {
	log.Info("Received Getuserinfo.UploadAvatar request")
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
	fdfsClient, err := fdfs_client.NewFdfsClient("/etc/fdfs/client.conf")
	if err != nil {
		rsp.Errno = utils.RECODE_USERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERERR)
		return nil
	}
	fdfsResp, err := fdfsClient.UploadByBuffer(req.Avatar, req.FileExt)
	if err != nil {
		rsp.Errno = utils.RECODE_IOERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_IOERR)
		return nil
	}
	err = model.SaveUserAvatar(user.ID, fdfsResp.RemoteFileId)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.AvatarUrl = fdfsResp.RemoteFileId
	return nil
}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getuserinfo) GetUserHouses(ctx context.Context, req *getuserinfo.HousesRequest, rsp *getuserinfo.HousesResponse) error {
	log.Info("Received Getuserinfo.GetUserHouses request")
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
	result, _ := json.Marshal(houses)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Mix = result
	return nil
}

func (e *Getuserinfo) PutUserName(ctx context.Context, req *getuserinfo.SetNameRequest, rsp *getuserinfo.SetNameResponse) error {
	log.Info("Received Getuserinfo.PutUserName request")
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
	if err := model.SaveUserName(user.ID, req.Name); err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	return nil
}
