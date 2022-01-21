package main

import (
	"ahome/proto"
	"context"
	"fmt"
	"time"

	"github.com/micro/micro/v3/service"
)

func main() {
	// create and initialise a new service
	srv := service.New()

	// create the proto client for helloworld
	client := proto.NewGetAreaService("getarea", srv.Client())

	// call an endpoint on the service
	rsp, err := client.MicroGetArea(context.Background(), &proto.Request{})
	if err != nil {
		fmt.Println("Error calling helloworld: ", err)
		return
	}

	// print the response
	fmt.Println("Response: ", rsp.Errmsg)

	// let's delay the process for exiting for reasons you'll see below
	time.Sleep(time.Second * 5)
}
