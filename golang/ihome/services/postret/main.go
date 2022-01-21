package main

import (
	"postret/handler"
	pb "postret/proto"

	"github.com/micro/micro/v3/service"
	"github.com/micro/micro/v3/service/logger"
)

func main() {
	// Create service
	srv := service.New(
		service.Name("go.micro.srv.postret"),
		service.Version("latest"),
	)

	// Register handler
	pb.RegisterPostretHandler(srv.Server(), new(handler.Postret))

	// Run service
	if err := srv.Run(); err != nil {
		logger.Fatal(err)
	}
}
