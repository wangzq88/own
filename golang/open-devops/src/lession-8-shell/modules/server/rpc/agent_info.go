package rpc

import (
	"log"
	"open-devops/src/lession-8-shell/models"
)

func (*Server) HostInfoReport(input models.AgentCollectInfo, output *string) error {
	log.Printf("[HostInfoReport][input:%+v]", input)
	*output = "i know 了"
	return nil
}
