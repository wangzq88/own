package main

import (
	"getarea/handler"
	pb "getarea/proto"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getarea"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetAreaHandler(srv.Server(), new(handler.GetArea))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
