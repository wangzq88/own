package main

import (
	"getuserhouses/handler"
	pb "go.micro.srv/common/proto/getuserhouses"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.getuserhouses"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterGetuserhousesHandler(srv.Server(), new(handler.Getuserhouses))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
