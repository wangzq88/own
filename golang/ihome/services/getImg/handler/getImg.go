package handler

import (
	"context"
	"getImg/model"
	"getImg/proto"
	"image/color"

	utils "getImg/utils"

	"github.com/afocus/captcha"
	log "github.com/micro/micro/v3/service/logger"
)

type GetImg struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *GetImg) MicroGetImg(ctx context.Context, req *proto.Request, rsp *proto.Response) error {
	log.Infof("Received GetImg.MicroGetImg request with uuid: %d", req.Uuid)
	//生成验证码图片,存储图片验证码,返回图片数据
	cap := captcha.New()

	//设置字符集
	if err := cap.SetFont("comic.ttf"); err != nil {
		panic(err.Error())
	}
	//设置验证码图片大小
	cap.SetSize(90, 41)
	//设置混淆程度
	cap.SetDisturbance(captcha.NORMAL)
	//设置字体颜色
	cap.SetFrontColor(color.RGBA{255, 255, 255, 255}, color.RGBA{255, 0, 0, 255})
	//设置背景色  background
	cap.SetBkgColor(color.RGBA{255, 0, 0, 255}, color.RGBA{0, 0, 255, 255}, color.RGBA{0, 153, 0, 255})

	//生成验证码图片
	//rand.Seed(time.Now().UnixNano())
	img, rnd := cap.Create(4, captcha.NUM)
	//存储验证码   redis
	err := model.SaveImgRnd(req.Uuid, rnd)
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return err
	}
	//传递图片信息给调用者
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)
	rsp.Pix = []byte(img.RGBA.Pix)
	rsp.Stride = int64(img.RGBA.Stride)
	rsp.Min = &proto.Point{X: int64(img.RGBA.Rect.Min.X), Y: int64(img.RGBA.Rect.Min.Y)}
	rsp.Max = &proto.Point{X: int64(img.RGBA.Rect.Max.X), Y: int64(img.RGBA.Rect.Max.Y)}
	return nil
}

// Stream is a server side stream handler called via client.Stream or the generated client code
func (e *GetImg) Stream(ctx context.Context, req *proto.StreamingRequest, stream proto.GetImg_StreamStream) error {
	log.Infof("Received GetImg.Stream request with count: %d", req.Count)

	for i := 0; i < int(req.Count); i++ {
		log.Infof("Responding: %d", i)
		if err := stream.Send(&proto.StreamingResponse{
			Count: int64(i),
		}); err != nil {
			return err
		}
	}

	return nil
}

// PingPong is a bidirectional stream handler called via client.Stream or the generated client code
func (e *GetImg) PingPong(ctx context.Context, stream proto.GetImg_PingPongStream) error {
	for {
		req, err := stream.Recv()
		if err != nil {
			return err
		}
		log.Infof("Got ping %v", req.Stroke)
		if err := stream.Send(&proto.Pong{Stroke: req.Stroke}); err != nil {
			return err
		}
	}
}
