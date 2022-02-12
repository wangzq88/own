module ahome

go 1.15

require (
	github.com/afocus/captcha v0.0.0-20191010092841-4bd1f21c8868
	github.com/gin-gonic/gin v1.7.7
	github.com/go-playground/validator/v10 v10.4.1
	github.com/go-redis/redis/v8 v8.11.4 // indirect
	github.com/go-sql-driver/mysql v1.6.0 // indirect
	github.com/golang/freetype v0.0.0-20170609003504-e2365dfdc4a0 // indirect
	github.com/golang/protobuf v1.5.2
	github.com/jinzhu/gorm v1.9.16 // indirect
	github.com/micro/micro/v3 v3.0.0
	go.micro.srv/common v0.0.0-incompatible
	google.golang.org/protobuf v1.26.0
)

// This can be removed once etcd becomes go gettable, version 3.4 and 3.5 is not,
// see https://github.com/etcd-io/etcd/issues/11154 and https://github.com/etcd-io/etcd/issues/11931.
replace google.golang.org/grpc => google.golang.org/grpc v1.26.0

replace go.micro.srv/common => ../common
