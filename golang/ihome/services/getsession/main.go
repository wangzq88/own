package main

import (
	"getsession/handler"
	pb "go.micro.srv/common/proto/getsession"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getsession"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetsessionHandler(srv.Server(), new(handler.Getsession))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
