package handler

import (
	"context"
	"encoding/json"

	log "github.com/micro/micro/v3/service/logger"
	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/getorder"
	"go.micro.srv/common/utils"
)

type Getorder struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getorder) PostOrder(ctx context.Context, req *getorder.PostRequest, rsp *getorder.PostResponse) error {
	log.Info("Received Getorder.PostOrder request")
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
	orderID, err := model.InsertOrder(int64(user.ID), req.HouseId, req.StartDate, req.EndDate)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.OrderId = orderID
	return nil
}

func (e *Getorder) GetMyOrder(ctx context.Context, req *getorder.MyRequest, rsp *getorder.MyResponse) error {
	log.Info("Received Getorder.GetMyOrder request")
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
	orderList, err := model.GetOrder(int64(user.ID), req.Role)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	jsonstr, _ := json.Marshal(&orderList)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Orderjson = jsonstr
	return nil
}

func (e *Getorder) PutOrderStatus(ctx context.Context, req *getorder.SetStatusRequest, rsp *getorder.SetStatusResponse) error {
	log.Info("Received Getorder.PutOrderStatus request")
	err := model.UpdateStatus(req.OrderId, req.Action, req.Reason)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	return nil
}
