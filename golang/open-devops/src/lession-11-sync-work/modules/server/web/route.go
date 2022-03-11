package web

import (
	"fmt"
	"github.com/go-kit/log"
	"github.com/gin-gonic/gin"
	"open-devops/src/common"
	"open-devops/src/lession-11-sync-work/models"
	"strings"
	"time"
)

func configRoutes(r *gin.Engine) {
	api := r.Group("/api/v1")
	{
		api.GET("/ping", func(c *gin.Context) {
			c.String(200, "pong")
		})
		api.GET("/now-ts", GetNowTs)
		api.POST("/node-path", NodePathAdd)
		api.GET("/node-path", NodePathQuery)
		api.POST("/resource-mount", ResourceMount)
		api.DELETE("/resource-unmount", ResourceUnMount)
	}
}

func GetNowTs(c *gin.Context) {
	c.String(200, time.Now().Format("2006-01-02 15:04:05"))
}

func NodePathQuery(c *gin.Context) {

	var inputs common.NodeCommonReq
	if err := c.BindJSON(&inputs); err != nil {
		common.JSONR(c, 400, err)
		return
	}
	logger := c.MustGet("logger").(log.Logger)

	if inputs.QueryType == 3 {
		if len(strings.Split(inputs.Node, ".")) != 2 {
			common.JSONR(c, 400, fmt.Errorf("query_type=3 path should be a.b:%v", inputs.Node))
			return
		}
	}
	res := models.StreePathQuery(&inputs, logger)
	common.JSONR(c, res)

}