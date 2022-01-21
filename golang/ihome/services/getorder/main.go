package main

import (
	"getorder/handler"

	pb "go.micro.srv/common/proto/getorder"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getorder"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetUserOrderHandler(srv.Server(), new(handler.Getorder))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
