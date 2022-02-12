package Service

import "errors"

type IUserService interface {
	GetName(userid int) string
	DelUser(userid int) error
}

type UserService struct{}

func (ser UserService) GetName(userid int) string {
	if userid == 1001 {
		return "wangzq"
	}
	return "no"
}

func (ser UserService) DelUser(userid int) error {
	if userid == 1001 {
		return errors.New("无权限")
	}
	return nil
}
