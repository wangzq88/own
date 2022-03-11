package web

import (
	"fmt"
	"github.com/gin-gonic/gin"
	"github.com/go-kit/log"
	"open-devops/src/common"
	"open-devops/src/lession-11-sync-work/models"
	"strings"
)

func NodePathAdd(c *gin.Context) {

	var inputs common.NodeCommonReq
	if err := c.Bind(&inputs); err != nil {
		common.JSONR(c, 400, err)
		return
	}
	logger := c.MustGet("logger").(log.Logger)

	res := strings.Split(inputs.Node, ".")
	if len(res) != 3 {
		common.JSONR(c, 400, fmt.Errorf("path_invalidate:%v", inputs.Node))
		return
	}
	err := models.StreePathAddOne(&inputs, logger)

	if err != nil {
		common.JSONR(c, 500, err)
		return
	}
	common.JSONR(c, 200, "path_add_success")
}