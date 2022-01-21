package handler

import (
	"context"
	"getsession/model"
	"go.micro.srv/common/proto/getsession"
	"go.micro.srv/common/utils"
	"strconv"

	log "github.com/micro/micro/v3/service/logger"
)

type Getsession struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getsession) Delssessioncd(ctx context.Context, req *getsession.Request, rsp *getsession.Response) error {
	log.Info("Received Delsession.Delssessioncd request")
	_, err := model.DelSession(req.Sessionid)
	if err != nil {
		rsp.Errno = utils.RECODE_SERVERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SERVERERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	return nil
}

func (e *Getsession) Getssessioncd(ctx context.Context, req *getsession.Request, rsp *getsession.Response) error {
	log.Info("Received Getsession.Getssessioncd request")
	uid, err := model.GetSessionID(req.Sessionid)
	name, _ := model.GetSessionName(req.Sessionid)
	log.Infof("Sessionid %s", req.Sessionid)
	log.Infof("uid %s", uid)
	log.Infof("name %s", name)
	if err != nil {
		rsp.Errno = utils.RECODE_SESSIONERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SESSIONERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Id, _ = strconv.ParseInt(uid, 10, 64)
	rsp.Name = name
	return nil
}

// PingPong is a bidirectional stream handler called via client.Stream or the generated client code
func (e *Getsession) PingPong(ctx context.Context, stream getsession.Getsession_PingPongStream) error {
	for {
		req, err := stream.Recv()
		if err != nil {
			return err
		}
		log.Infof("Got ping %v", req.Stroke)
		if err := stream.Send(&getsession.Pong{Stroke: req.Stroke}); err != nil {
			return err
		}
	}
}
