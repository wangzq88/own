package main

import "fmt"

func GetIndex (arr []int,target int) []int {
	var indexArr []int
	for i:=0;i < len(arr); i++ {
		if arr[i] == target {
			indexArr = append(indexArr, i)
		}
	}
	return indexArr
}

func main() {
	arr := []int{7,1,5,3,6,3,4}
	target := 3
	list := GetIndex(arr,target)
	fmt.Println(list)
}