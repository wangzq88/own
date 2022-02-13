// Code generated by protoc-gen-micro. DO NOT EDIT.
// source: proto/getsession/getsession.proto

package getsession

import (
	fmt "fmt"
	proto "github.com/golang/protobuf/proto"
	math "math"
)

import (
	context "context"
	api "github.com/micro/micro/v3/service/api"
	client "github.com/micro/micro/v3/service/client"
	server "github.com/micro/micro/v3/service/server"
)

// Reference imports to suppress errors if they are not otherwise used.
var _ = proto.Marshal
var _ = fmt.Errorf
var _ = math.Inf

// This is a compile-time assertion to ensure that this generated file
// is compatible with the proto package it is being compiled against.
// A compilation error at this line likely means your copy of the
// proto package needs to be updated.
const _ = proto.ProtoPackageIsVersion3 // please upgrade the proto package

// Reference imports to suppress errors if they are not otherwise used.
var _ api.Endpoint
var _ context.Context
var _ client.Option
var _ server.Option

// Api Endpoints for Getsession service

func NewGetsessionEndpoints() []*api.Endpoint {
	return []*api.Endpoint{}
}

// Client API for Getsession service

type GetsessionService interface {
	Getssessioncd(ctx context.Context, in *Request, opts ...client.CallOption) (*Response, error)
	Delssessioncd(ctx context.Context, in *Request, opts ...client.CallOption) (*Response, error)
	PingPong(ctx context.Context, opts ...client.CallOption) (Getsession_PingPongService, error)
}

type getsessionService struct {
	c    client.Client
	name string
}

func NewGetsessionService(name string, c client.Client) GetsessionService {
	return &getsessionService{
		c:    c,
		name: name,
	}
}

func (c *getsessionService) Getssessioncd(ctx context.Context, in *Request, opts ...client.CallOption) (*Response, error) {
	req := c.c.NewRequest(c.name, "Getsession.Getssessioncd", in)
	out := new(Response)
	err := c.c.Call(ctx, req, out, opts...)
	if err != nil {
		return nil, err
	}
	return out, nil
}

func (c *getsessionService) Delssessioncd(ctx context.Context, in *Request, opts ...client.CallOption) (*Response, error) {
	req := c.c.NewRequest(c.name, "Getsession.Delssessioncd", in)
	out := new(Response)
	err := c.c.Call(ctx, req, out, opts...)
	if err != nil {
		return nil, err
	}
	return out, nil
}

func (c *getsessionService) PingPong(ctx context.Context, opts ...client.CallOption) (Getsession_PingPongService, error) {
	req := c.c.NewRequest(c.name, "Getsession.PingPong", &Ping{})
	stream, err := c.c.Stream(ctx, req, opts...)
	if err != nil {
		return nil, err
	}
	return &getsessionServicePingPong{stream}, nil
}

type Getsession_PingPongService interface {
	Context() context.Context
	SendMsg(interface{}) error
	RecvMsg(interface{}) error
	Close() error
	Send(*Ping) error
	Recv() (*Pong, error)
}

type getsessionServicePingPong struct {
	stream client.Stream
}

func (x *getsessionServicePingPong) Close() error {
	return x.stream.Close()
}

func (x *getsessionServicePingPong) Context() context.Context {
	return x.stream.Context()
}

func (x *getsessionServicePingPong) SendMsg(m interface{}) error {
	return x.stream.Send(m)
}

func (x *getsessionServicePingPong) RecvMsg(m interface{}) error {
	return x.stream.Recv(m)
}

func (x *getsessionServicePingPong) Send(m *Ping) error {
	return x.stream.Send(m)
}

func (x *getsessionServicePingPong) Recv() (*Pong, error) {
	m := new(Pong)
	err := x.stream.Recv(m)
	if err != nil {
		return nil, err
	}
	return m, nil
}

// Server API for Getsession service

type GetsessionHandler interface {
	Getssessioncd(context.Context, *Request, *Response) error
	Delssessioncd(context.Context, *Request, *Response) error
	PingPong(context.Context, Getsession_PingPongStream) error
}

func RegisterGetsessionHandler(s server.Server, hdlr GetsessionHandler, opts ...server.HandlerOption) error {
	type getsession interface {
		Getssessioncd(ctx context.Context, in *Request, out *Response) error
		Delssessioncd(ctx context.Context, in *Request, out *Response) error
		PingPong(ctx context.Context, stream server.Stream) error
	}
	type Getsession struct {
		getsession
	}
	h := &getsessionHandler{hdlr}
	return s.Handle(s.NewHandler(&Getsession{h}, opts...))
}

type getsessionHandler struct {
	GetsessionHandler
}

func (h *getsessionHandler) Getssessioncd(ctx context.Context, in *Request, out *Response) error {
	return h.GetsessionHandler.Getssessioncd(ctx, in, out)
}

func (h *getsessionHandler) Delssessioncd(ctx context.Context, in *Request, out *Response) error {
	return h.GetsessionHandler.Delssessioncd(ctx, in, out)
}

func (h *getsessionHandler) PingPong(ctx context.Context, stream server.Stream) error {
	return h.GetsessionHandler.PingPong(ctx, &getsessionPingPongStream{stream})
}

type Getsession_PingPongStream interface {
	Context() context.Context
	SendMsg(interface{}) error
	RecvMsg(interface{}) error
	Close() error
	Send(*Pong) error
	Recv() (*Ping, error)
}

type getsessionPingPongStream struct {
	stream server.Stream
}

func (x *getsessionPingPongStream) Close() error {
	return x.stream.Close()
}

func (x *getsessionPingPongStream) Context() context.Context {
	return x.stream.Context()
}

func (x *getsessionPingPongStream) SendMsg(m interface{}) error {
	return x.stream.Send(m)
}

func (x *getsessionPingPongStream) RecvMsg(m interface{}) error {
	return x.stream.Recv(m)
}

func (x *getsessionPingPongStream) Send(m *Pong) error {
	return x.stream.Send(m)
}

func (x *getsessionPingPongStream) Recv() (*Ping, error) {
	m := new(Ping)
	if err := x.stream.Recv(m); err != nil {
		return nil, err
	}
	return m, nil
}