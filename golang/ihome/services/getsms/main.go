package main

import (
	"getsms/handler"

	pb "go.micro.srv/common/proto/getsms"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getsms"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetsmsHandler(srv.Server(), new(handler.Getsms))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
