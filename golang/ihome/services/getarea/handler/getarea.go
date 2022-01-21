package handler

import (
	"context"

	model "getarea/model"
	proto "getarea/proto"
	utils "getarea/utils"
	log "github.com/micro/micro/v3/service/logger"
)

type GetArea struct{}

// Call is a single request handler called via client.Call or the generated client code
func (e *GetArea) MicroGetArea(ctx context.Context, req *proto.Request, rsp *proto.Response) error {
	//获取数据并返回给调用者
	areas, err := model.GetArea()
	if err != nil {
		rsp.Errno = utils.RECODE_DBERR
		rsp.Errmsg = utils.RecodeText(utils.RECODE_DBERR)
		return err
	}
	rsp.Errno = utils.RECODE_OK
	rsp.Errmsg = utils.RecodeText(utils.RECODE_OK)

	for _, v := range areas {
		var areaInfo proto.AreaInfo
		areaInfo.Aid = int32(v.Id)
		areaInfo.Aname = v.Name

		rsp.Data = append(rsp.Data, &areaInfo)
	}

	return nil
}

// Stream is a server side stream handler called via client.Stream or the generated client code
func (e *GetArea) Stream(ctx context.Context, req *proto.StreamingRequest, stream proto.GetArea_StreamStream) error {
	log.Infof("Received GetArea.Stream request with count: %d", req.Count)

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
func (e *GetArea) PingPong(ctx context.Context, stream proto.GetArea_PingPongStream) error {
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
