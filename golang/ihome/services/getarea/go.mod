module getarea

go 1.15

require (
	github.com/go-redis/redis/v8 v8.11.4 // indirect
	github.com/golang/protobuf v1.5.2 // indirect
	github.com/jinzhu/gorm v1.9.16 // indirect
	github.com/micro/micro/v3 v3.0.0
	go.micro.srv/common v0.0.0-incompatible
	google.golang.org/protobuf v1.26.0 // indirect
)

// This can be removed once etcd becomes go gettable, version 3.4 and 3.5 is not,
// see https://github.com/etcd-io/etcd/issues/11154 and https://github.com/etcd-io/etcd/issues/11931.
replace google.golang.org/grpc => google.golang.org/grpc v1.26.0

replace go.micro.srv/common => ../../common
