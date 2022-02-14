# douyinhuoshan
抓取抖音火山版APP直播间的数据。

## 环境搭建

### 一、mitmproxy抓包工具的安装

### 介绍：

1、和正常的代理一样转发请求，保障服务端与客户端的通信

2、拦截请求，修改请求，拦截返回，修改返回

3、可以载入自定义 python 脚本



<img src="attachment\20220214150835.png" alt="20220214150835" style="zoom:20%;" />

### 安装环境：

1、基于python

2、windows操作系统需要安装Microsoft Visual C＋＋V14.0以上 

3、linux操作系统则直接基于python安装即可

### 安装：

基于 Python 进行安装

```sh
$ pip install mitmproxy
```

安装完成查看版本号

```sh
$ mitmproxy --version
Mitmproxy: 7.0.4
Python:    3.8.3
OpenSSL:   OpenSSL 1.1.1l  24 Aug 2021
Platform:  Windows-10-10.0.19041-SP0
```

注意：mitmproxy 只支持 Linux 或者 Windows 10 以上版本。Windows 10 以下，只能用 mitmdump

```sh
$ mitmdump --version
Mitmproxy: 7.0.4
Python:    3.8.3
OpenSSL:   OpenSSL 1.1.1l  24 Aug 2021
Platform:  Windows-10-10.0.19041-SP0
```

```sh
$ mitmweb --version
Mitmproxy: 7.0.4
Python:    3.8.3
OpenSSL:   OpenSSL 1.1.1l  24 Aug 2021
Platform:  Windows-10-10.0.19041-SP0
```

启动 mitmproxy ，默认占用的是 8080 端口

```sh
$ mitmproxy
```

改用别的端口

```sh
$ mitmproxy -p 8888
```

设置浏览器代理为 127.0.0.1:8080 ，然后浏览器访问 http://mitm.it/ ，下载相应平台的证书（如果是 Windows 就下 Windows 证书，Linux 就下 Linux 证书），然后点击安装。安装完成，mitmproxy 就可以解析 https 类型的网站了。

<img src="attachment\20220214162804.png" alt="20220214162804" style="zoom:50%;" />

mitmdump 是 mitmproxy 的命令行接口，同时还可以对接 Python 对数据包进行解析，存储等工作，这些过程都可以通过Python实现。

把抓包数据保存到 text.txt 文件

```sh
$ mitmdump -w test.txt
```

指定`Python`脚本来处理截获的数据，使用`-s`参数即可：

```sh
$ mitmdump -s script.py
```

终止掉 `mitmproxy` 打开的窗口，输入以下命令，在浏览器打开 http://127.0.0.1:8081/ ， `mitmweb` 是`mitmproxy` 网页版

```sh
$ mitmweb
```



### 二、Appium 移动端自动化测试工具安装

#### 介绍：

1. appium 是一个自动化测试开源工具，支持iOS平台和Android平台上的原生应用，web应用和混合应用。
2. appium是一个跨平台的工具：它允许测试人员在不同的平台（iOS， Android ）使用同一套API来写自动化测试脚本，这样大大增加了iOS和Android测试套件间代码的复用性

#### Selenium

appium类库封装了标准Selenium客户端类库

appium客户端类库实现了Mobile JSON Wire Protocol、W3C WebDriver spec

appium服务端定义了官方协议的扩展，为appium用户提供了方便的接口来执行各种设备动作

#### Appium 特点

跨平台，多语言

appium选择了Client／Server的设计模式

appium扩展了WebDriver的协议

<img src="attachment\20220214171507.png" alt="20220214171507" style="zoom:50%;" />

<img src="attachment\2022021418155000001.jpg" alt="2022021418155000001" style="zoom:50%;" />

#### 安装

参考官方网址：https://appium.io/downloads.html

##### 安装 Appium 服务端

到 [github](https://github.com/appium/appium-desktop/releases/latest) 下载对应平台的 Appium-Server-GUI

##### 安装 Appium 客户端，用 pip 命令安装

```sh
pip install Appium-Python-Client
```



### 三、安装夜神模拟器（Android 模拟器）

#### 设置代理

<img src="attachment\20220214173941.png" alt="20220214173941" style="zoom:60%;" />

#### 安装证书

先启动 `mitmproxy`，`$ mitmproxy -p 8888`，打开浏览器访问 http://mitm.it/ ，下载Android平台的证书 

<img src="attachment\20220214174653.png" alt="20220214174653" style="zoom:60%;" />

查看安装证书

<img src="attachment\20220214174922.png" alt="20220214174922" style="zoom:50%;" />

### 四、安装 Android SDK

<img src="attachment\20220214180748.png" alt="20220214180748" style="zoom:50%;" />

参见：https://developer.android.google.cn/studio/releases/platforms?hl=zh-cn

https://www.runoob.com/w3cnote/android-tutorial-development-environment-build.html