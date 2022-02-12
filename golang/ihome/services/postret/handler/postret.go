package handler

import (
	"context"
	"crypto/sha256"
	"fmt"

	log "github.com/micro/micro/v3/service/logger"

	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/postret"
	"go.micro.srv/common/utils"
)

type Postret struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Postret) Register(ctx context.Context, req *postret.Request, rsp *postret.Response) error {
	//实现具体的业务  把数据存储到mysql中  校验短信验证码是否正确

	//校验短信验证码会否正确
	smsCode, err := model.GetSmsCode(req.Mobile)
	if err != nil {
		rsp.Errno = utils.RECODE_DATAERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DATAERR)
		//return err
		return nil
	}

	if smsCode != req.SmsCode {
		rsp.Errno = utils.RECODE_SMSERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SMSERR)
		//return errors.New("验证码错误")
		return nil
	}

	//存储用户数据到Mysql上
	//给密码加密
	pwdByte := sha256.Sum256([]byte(req.Password))
	pwd_hash := string(pwdByte[:])
	//要把sha256得到的数据转换之后存储  转换16进制的
	pwdHash := fmt.Sprintf("%x", pwd_hash)

	user, err := model.SaveUser(req.Mobile, pwdHash)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		//return err
		return nil
	}
	sessByte := sha256.Sum256([]byte(req.Mobile))
	sessionid := fmt.Sprintf("%x", string(sessByte[:]))
	log.Infof("sessionid 是什么: %s", sessionid)
	model.SaveSession(sessionid, user)
	log.Infof("这是出错吗:%v", user)
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Name = sessionid
	return nil
}

// Stream is a server side stream handler called via client.Stream or the generated client code
func (e *Postret) Stream(ctx context.Context, req *postret.StreamingRequest, stream postret.Postret_StreamStream) error {
	log.Infof("Received Postret.Stream request with count: %d", req.Count)

	for i := 0; i < int(req.Count); i++ {
		log.Infof("Responding: %d", i)
		if err := stream.Send(&postret.StreamingResponse{
			Count: int64(i),
		}); err != nil {
			return err
		}
	}

	return nil
}

// PingPong is a bidirectional stream handler called via client.Stream or the generated client code
func (e *Postret) PingPong(ctx context.Context, stream postret.Postret_PingPongStream) error {
	for {
		req, err := stream.Recv()
		if err != nil {
			return err
		}
		log.Infof("Got ping %v", req.Stroke)
		if err := stream.Send(&postret.Pong{Stroke: req.Stroke}); err != nil {
			return err
		}
	}
}
