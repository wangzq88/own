package main

import (
	"getuserinfo/handler"
	pb "go.micro.srv/common/proto/getuserinfo"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getuserinfo"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetuserinfoHandler(srv.Server(), new(handler.Getuserinfo))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
