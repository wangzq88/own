package handler

import (
	"context"

	log "github.com/micro/micro/v3/service/logger"

	"go.micro.srv/common/model"
	"go.micro.srv/common/proto/postauth"
	"go.micro.srv/common/utils"
)

type Postauth struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Postauth) Auth(ctx context.Context, req *postauth.Request, rsp *postauth.Response) error {
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
	err = model.SaveAuth(user.ID, req.RealName, req.IdCard)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return nil
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	return nil
}
