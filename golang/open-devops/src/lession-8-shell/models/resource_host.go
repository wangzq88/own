package models

// 机器上shell 采集到的字段

type AgentCollectInfo struct {
	SN       string `json:"sn"`       // sn号
	CPU      string `json:"cpu"`      // cpu核数
	Mem      string `json:"mem"`      // 内存g数
	Disk     string `json:"disk"`     // 磁盘g数
	IpAddr   string `json:"ip_addr"`  // ip
	HostName string `json:"hostname"` // hostname

}