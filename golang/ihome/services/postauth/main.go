package main

import (
	"postauth/handler"
	pb "go.micro.srv/common/proto/postauth"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.postauth"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterPostauthHandler(srv.Server(), new(handler.Postauth))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
