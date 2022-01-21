package handler

import (
	"context"
	"errors"
	"fmt"
	"math/rand"
	"time"

	log "github.com/micro/micro/v3/service/logger"

	"getsms/model"
	"getsms/proto"
	"getsms/utils"

	"github.com/aliyun/alibaba-cloud-sdk-go/sdk"
	"github.com/aliyun/alibaba-cloud-sdk-go/sdk/requests"
)

type Getsms struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *Getsms) SmsCode(ctx context.Context, req *proto.Request, rsp *proto.Response) error {
	rnd, err := model.GetImgCode(req.Uuid)
	if err != nil {
		rsp.Errno = utils.RECODE_NODATA
		rsp.Errmsg = utils.RecodeText(utils.RECODE_NODATA)
		return err
	}
	//判断手机号码是否存在
	var user model.User
	result := model.GlobalDB.First(&user, "mobile = ?", req.Mobile) // 查找 code 字段值为 D42 的记录
	if result.Error == nil {
		rsp.Errno = utils.RECODE_USERONERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_USERONERR)
		//返回自定义的error数据
		return errors.New("用户已经注册")
	}
	//判断输入的图片验证码是否正确
	if req.Text != rnd {
		rsp.Errno = utils.RECODE_DATAERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DATAERR)
		//返回自定义的error数据
		return errors.New("验证码输入错误")
	}
	//如果成功,发送短信,存储短信验证码  阿里云短信接口
	client, err := sdk.NewClientWithAccessKey("default", "LTAI4FexwrAFbn4ua4DHAyXh", "AltI2inQ1I5TqAEwAfrJNgP54VnVOx")
	if err != nil {
		rsp.Errno = utils.RECODE_DATAERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DATAERR)
		return err
	}
	//获取6位数随机码
	myRnd := rand.New(rand.NewSource(time.Now().UnixNano()))
	vcode := fmt.Sprintf("%06d", myRnd.Int31n(1000000))

	//初始化请求对象
	request := requests.NewCommonRequest()
	request.Method = "POST"                                         //设置请求方法
	request.Scheme = "https"                                        // https | http   //设置请求协议
	request.Domain = "dysmsapi.aliyuncs.com"                        //域名
	request.Version = "2017-05-25"                                  //版本号
	request.ApiName = "SendSms"                                     //api名称
	request.QueryParams["PhoneNumbers"] = req.Mobile                //需要发送的电话号码
	request.QueryParams["SignName"] = "北京5期区块链"                     //签名名称   需要申请
	request.QueryParams["TemplateCode"] = "SMS_176375357"           //模板号   需要申请
	request.QueryParams["TemplateParam"] = `{"code":` + vcode + `}` //发送短信验证码

	response, err := client.ProcessCommonRequest(request) //发送短信
	//如果不成功
	if !response.IsSuccess() {
		rsp.Errno = utils.RECODE_SMSERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_SMSERR)
		//return errors.New("发送短信失败")
	}
	log.Infof("短信验证码: %s", vcode)
	//存储短信验证码  存redis中
	err = model.SaveSmsCode(req.Mobile, vcode)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return err
	}

	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	return nil
}
