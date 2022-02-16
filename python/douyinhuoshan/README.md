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

##### 安装 appium-inspector

到 [github](https://github.com/appium/appium-inspector/tags) 下载压缩包。随着Appium Desktop升级到1.22.0版本，服务和元素查看器已经分开了，查看元素信息就需要下载Appium Inspector。参考资料：[Appium-Inspector安装及使用方法](https://blog.csdn.net/delia_1/article/details/122247259)

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

**什么是uiautomator**

类似于网页查看元素的工具

Android 4.3发布的时候发布的测试工具。uiautomator是用来做UI测试的。也就是普通的手工测试，点击每个控件元素看看输出的结果是否符合预期。比如登陆界面分别输入正确和错误的用户名密码，然后点击登陆按钮看看是否能否登陆以及是否有错误提示等

**uiautomator工具的组成**

uiautomatorviewer —— 一个图形界面工具来扫描和分析应用的UI控件。存放在tools目录

uiautomator —— 一个测试的Java库，包含了创建UI测试的各种API和执行自动化测试的引擎。

uiautomator 在 Android Sdk 目录里  C:\Users\linzh\AppData\Local\Android\sdk\tools\bin

uiautomator 没有提供类似 xpath 路径，所以可以用第三方插件破解升级一下

### 五、安装xponsed框架 + JustTruestMe组件

由于打开抓包工具，抖音火山版会连不上网，这是由于校验服务端的证书无法通过，打开抓包工具会进行中间人劫持，APP发出的数据被抓包工具截获了，然后抓包工具内置的证书进行加密，传输到抖音的服务器，服务器发现发送过来的数据并不是熟悉的算法和熟悉的证书，所以就拒绝了数据的返回。这就需要安装 xponsed框架 + JustTruestMe 组件来突破 SSL Pinning。

SSL Pinning，即SSL证书绑定，是验证服务器身份的一种方式，是在https协议建立通信时增加的代码逻辑，它通过自己的方式验证服务器身份，然后决定通信是否继续下去。它唯一指定了服务器的身份，所以安全性较高。

https 验证服务器的身份有三种

1、根据浏览器或者说操作系统（Android）自带的证书链（需要到证书机构购买证书）

2、二是使用自签名证书（多见于局域网、内网使用）

3、三是自签名证书加上SSL Pinning特性（安全性最高的，需要客户端或者浏览器添加SSL Pinning特性）

Xposed是一个框架，它可以改变系统和应用程序的行为，而不接触任何APK。它支持很多模块，每个模块可以用来帮助实现不同的功能。

JustTrustMe是一个用来禁用、绕过SSL证书检查的，它基于Xposed 模块。 JustTrustMe是将APK中所有用于校验SSL证书的API都进行了 屏蔽，从而绕过证书检查。

**注意事项：**

手机必须获取root权限

安装xposed框架有手机变砖危险！！！手机可以直接刷带有xposed框架的系统

目前 xposed框架 支持 android 最高版本只到 5.0

[xposed 框架下载](https://repo.xposed.info/module/de.robv.android.xposed.installer)

[JustTrustMe下载](https://github.com/Fuzion24/JustTrustMe/releases)

[安装参见此链接](https://crifan.github.io/app_capture_package_tool_charles/website/how_capture_app/complex_https/https_ssl_pinning/root_android_xposed_justtrustme.html)

## 配置

打开 main.py 文件，定位到以下代码

```
desired_caps = {}
desired_caps['platformName'] = 'Android'
desired_caps['deviceName'] = '127.0.0.1:62025'
desired_caps['platformVersion'] = '5.1.1'
desired_caps['appPackage'] = 'com.ss.android.ugc.live'
desired_caps['appActivity'] = '.main.MainActivity'
desired_caps['noReset'] = True
desired_caps['unicodeKeyboard'] = True
desired_caps['resetKeyboard'] = True
```

desired capability 的功能是配置Appium会话。他们告诉Appium服务 器您想要自动化的平台和应用程序。

[常用Capability配置讲解](https://www.zhihu.com/question/21453695?sort=created#:~:text=Desired,Capabilities%E6%98%AF%E4%B8%80%E7%BB%84%E8%AE%BE%E7%BD%AE%E7%9A%84%E9%94%AE%E5%80%BC%E5%AF%B9%E7%9A%84%E9%9B%86%E5%90%88%EF%BC%8C%E5%85%B6%E4%B8%AD%E9%94%AE%E5%AF%B9%E5%BA%94%E8%AE%BE%E7%BD%AE%E7%9A%84%E5%90%8D%E7%A7%B0%EF%BC%8C%E8%80%8C%E5%80%BC%E5%AF%B9%E5%BA%94%E8%AE%BE%E7%BD%AE%E7%9A%84%E5%80%BC%E3%80%82)

adb（Android Debug Bridge）是一个通用命令行工具，其允许您与模拟器实例或连接的 Android 设备进行通信。它可为各种设备操作提供便利如安装和调试应用。

**关于 deviceName 的获取**，查看 [ADB 命令大全](https://zhuanlan.zhihu.com/p/89060003)，就能查看到 `deviceName` 的值。模拟器实例或连接的 Android 设备要*启用开发者模式*，并且*允许USB调试模式*

```sh
$ adb devices
```

如果出现类似的错误信息

```
adb server version (36) doesn't match this client (40); killing... 
```

把`Android SDK` 目录下的 `platform-tools` 目录下的 `adb.exe` ，`AdbWinApi.dll` ，`AdbWinUsbApi.dll` 这三个文件复制替换夜神模拟器安装目录下 `C:\Program Files (x86)\Nox\bin` 的同样三个文件。

把目录下`C:\Program Files (x86)\Nox\bin` 的 `nox_adb.exe` 备份为 `nox_adb_bak.exe`，把 `adb.exe` 复制一份改为 nox_adb.exe。

这样操作之后版本就一致了，不会出现上面那个错误。重启安卓模拟器，再次输入 `adb devices`，就可以看到连接的安卓设备了

进入设备的底层操作系统

```sh
$ adb -s  127.0.0.1:62025 shell  
```

**关于 appPackage 和 appActivity 的获取**，有两种方式进行查看。

1.通过 `aapt`，在 Android SDK 目录里的 build-tools 目录里面有个 aapt 工具 

```sh
$ aapt dump badging  XXX.apk
```

XXX.apk 是APP的包名，输出的信息第一行信息就包含有 appPackage 的信息

在输出的信息找到 `launchable-activity` 里 `name` 的值就是 appActivity  的信息，或者输入以下信息

```
aapt dump badging  XXX.apk | find "launchable-activity"
```

2.通过 `adb shell` 。

```sh
$ adb shell  
```

会进入到 Android 操作系统命令行界面

```sh
# logcat | grep cmp=
```

然后打开夜神模拟器（安卓模拟器），打开要抓取的 app，查找到有 `cmp=` 的信息，就可以看到 `appPackage` 和 `appActivity`  了

<img src="attachment\20220215124600.png" alt="20220215124600" style="zoom:50%;" />