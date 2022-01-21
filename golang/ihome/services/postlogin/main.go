package main

import (
	"postlogin/handler"
	pb "postlogin/proto"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.postlogin"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterPostloginHandler(srv.Server(), new(handler.Postlogin))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
