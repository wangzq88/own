package main

import (
	"getImg/handler"
	pb "getImg/proto"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getimg"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetImgHandler(srv.Server(), new(handler.GetImg))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
