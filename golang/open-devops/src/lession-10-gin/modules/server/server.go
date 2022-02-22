package main

import (
	"context"
	"fmt"
	"github.com/go-kit/log"
	"github.com/go-kit/log/level"
	"github.com/prometheus/common/promlog"
	promlogflag "github.com/prometheus/common/promlog/flag"
	"github.com/prometheus/common/version"
	"github.com/oklog/run"
	_ "github.com/go-sql-driver/mysql"
	"gopkg.in/alecthomas/kingpin.v2"
	"open-devops/src/lession-10-gin/modules/server/web"
	"open-devops/src/lession-10-gin/modules/server/config"
	"open-devops/src/lession-10-gin/models"
	"open-devops/src/lession-10-gin/modules/server/rpc"
	"os"
	"os/signal"
	"path/filepath"
	"syscall"
	"time"
)

var (
	// 命令行解析
	app = kingpin.New(filepath.Base(os.Args[0]), "The open-devops-server")
	// 指定配置文件
	configFile = app.Flag("config.file", "open-devops-server configuration file path").Short('c').Default("open-devops-server.yml").String()
)

func main() {
	// 版本信息
	app.Version(version.Print("open-devops-server"))
	// 帮助信息
	app.HelpFlag.Short('h')

	promlogConfig := promlog.Config{}

	promlogflag.AddFlags(app, &promlogConfig)
	// 强制解析
	kingpin.MustParse(app.Parse(os.Args[1:]))
	fmt.Println(*configFile)
	// 设置logger
	var logger log.Logger
	logger = func(config *promlog.Config) log.Logger {
		var (
			l  log.Logger
			le level.Option
		)
		if config.Format.String() == "logfmt" {
			l = log.NewLogfmtLogger(log.NewSyncWriter(os.Stderr))
		} else {
			l = log.NewJSONLogger(log.NewSyncWriter(os.Stderr))
		}

		switch config.Level.String() {
		case "debug":
			le = level.AllowDebug()
		case "info":
			le = level.AllowInfo()
		case "warn":
			le = level.AllowWarn()
		case "error":
			le = level.AllowError()
		}
		l = level.NewFilter(l, le)
		l = log.With(l, "ts", log.TimestampFormat(
			func() time.Time { return time.Now().Local() },
			"2006-01-02T15:04:05.000Z07:00",
		), "caller", log.DefaultCaller)
		return l
	}(&promlogConfig)
	sConfig, err := config.LoadFile(*configFile)
	if err != nil {
		level.Error(logger).Log("msg", "config.LoadFile Error,Exiting ...", "error", err)
		return
	}
	level.Info(logger).Log("msg", "load.config.success", "file.path", *configFile, "content.mysql.num", len(sConfig.MysqlS))
	// 初始化mysql
	models.InitMySQL(sConfig.MysqlS)
	level.Info(logger).Log("msg", "load.mysql.success", "db.num", len(models.DB))
	//TODO 运行测试后就注释掉
	//models.StreePathAddTest(logger)
	//models.StreePathQueryTest1(logger)
	//models.StreePathQueryTest2(logger)
	//models.StreePathQueryTest3(logger)
	//models.StreePathDelTest(logger)
	// 编排开始
	var g run.Group
	ctxAll, cancelAll := context.WithCancel(context.Background())
	fmt.Println(ctxAll)
	{

		// 处理信号退出的handler
		term := make(chan os.Signal, 1)
		// 按ctrl+c停止程序
		signal.Notify(term, os.Interrupt, syscall.SIGTERM)
		cancelC := make(chan struct{})
		g.Add(
			func() error {
				select {
				case <-term:
					level.Warn(logger).Log("msg", "Receive SIGTERM ,exiting gracefully....")
					cancelAll()
					return nil
				case <-cancelC:
					level.Warn(logger).Log("msg", "other cancel exiting")
					return nil
				}
			},
			func(err error) {
				fmt.Println("做一些清理操作")
				close(cancelC)
			},
		)
	}
	{
		// rpc server
		g.Add(func() error {
			errChan := make(chan error, 1)
			go func() {
				errChan <- rpc.Start(sConfig.RpcAddr, logger)
			}()
			select {
			case err := <-errChan:
				level.Error(logger).Log("msg", "rpc server error", "err", err)
				return err
			case <-ctxAll.Done():
				level.Info(logger).Log("msg", "receive_quit_signal_rpc_server_exit")
				return nil
			}

		}, func(err error) {
			cancelAll()
		},
		)
	}
	{
		// http server
		g.Add(func() error {
			errChan := make(chan error, 1)
			go func() {
				errChan <- web.StartGin(sConfig.HttpAddr, logger)
			}()
			select {
			case err := <-errChan:
				level.Error(logger).Log("msg", "web server error", "err", err)
				return err
			case <-ctxAll.Done():
				level.Info(logger).Log("msg", "receive_quit_signal_web_server_exit")
				return nil
			}

		}, func(err error) {
			cancelAll()
		},
		)
	}
	g.Run()
}
