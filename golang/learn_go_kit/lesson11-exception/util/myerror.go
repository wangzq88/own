package util

type MyError struct {
	Code    int
	Message string
}

func (err *MyError) Error() string {
	return err.Message
}

func NewMyError(code int, mes string) error {
	return &MyError{code, mes}
}
