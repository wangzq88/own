package handler

import (
	"context"
	"crypto/sha256"
	"fmt"
	"postlogin/utils"

	log "github.com/micro/micro/v3/service/logger"

	"postlogin/model"
	postlogin "postlogin/proto"
)

type Postlogin struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Postlogin) Login(ctx context.Context, req *postlogin.Request, rsp *postlogin.Response) error {
	user, err := model.GetUser(req.Mobile)
	if err != nil {
		rsp.Errno = utils.RECODE_USERERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERERR)
		//return errors.New("没有该记录")
		return nil
	} else {
		sessByte := sha256.Sum256([]byte(req.Password))
		password := fmt.Sprintf("%x", string(sessByte[:]))
		if password != user.Password_hash {
			rsp.Errno = utils.RECODE_PWDERR
			rsp.Errmsg = utils.RecodeText(utils.RECODE_PWDERR)
			return nil
			//return errors.New("密码错误")
		}
	}
	sessByte := sha256.Sum256([]byte(req.Mobile))
	sessionid := fmt.Sprintf("%x", string(sessByte[:]))
	log.Infof("sessionid 是什么: %s", sessionid)
	model.SaveSession(sessionid, user)
	log.Infof("这是出错吗:%v", user)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Sessionid = sessionid
	return nil
}

// Stream is a server side stream handler called via client.Stream or the generated client code
func (e *Postlogin) Stream(ctx context.Context, req *postlogin.StreamingRequest, stream postlogin.Postlogin_StreamStream) error {
	log.Infof("Received Postlogin.Stream request with count: %d", req.Count)

	for i := 0; i < int(req.Count); i++ {
		log.Infof("Responding: %d", i)
		if err := stream.Send(&postlogin.StreamingResponse{
			Count: int64(i),
		}); err != nil {
			return err
		}
	}

	return nil
}

// PingPong is a bidirectional stream handler called via client.Stream or the generated client code
func (e *Postlogin) PingPong(ctx context.Context, stream postlogin.Postlogin_PingPongStream) error {
	for {
		req, err := stream.Recv()
		if err != nil {
			return err
		}
		log.Infof("Got ping %v", req.Stroke)
		if err := stream.Send(&postlogin.Pong{Stroke: req.Stroke}); err != nil {
			return err
		}
	}
}
