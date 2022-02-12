package Service

type IUserService interface {
	GetName(userid int) string
}

type UserService struct{}

func (ser UserService) GetName(userid int) string {
	if userid == 1001 {
		return "wangzq"
	}
	return "no"
}
