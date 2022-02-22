package main

import (
	"demo/xorm_json/src/model"
	"encoding/json"
	"fmt"
)

func main() {
	m := map[string]string{"region": "bj", "app": "live"}
	ips := []string{"1.1.1.1", "2.2.2.2"}
	mRaw, _ := json.Marshal(m)
	ipRaw, _ := json.Marshal(ips)

	rh := model.ResourceHostTest{
		Name:       "abc",
		PrivateIps: ipRaw,
		Tags:       mRaw,
	}
	err := rh.AddOne()
	fmt.Println(err)
	rhNew := model.ResourceHostTest{
		Name: "abc",
	}
	rhDb, err := rhNew.GetOne()
	mTag := make(map[string]string)
	err = json.Unmarshal(rhDb.Tags, &mTag)

	ipsN := make([]string, 0)
	err = json.Unmarshal(rhDb.PrivateIps, &ipsN)

	fmt.Println(mTag, err)
	fmt.Println(ipsN, err)

}

