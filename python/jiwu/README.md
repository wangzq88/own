# 抓取吉屋房产信息

## 一、介绍

loupan.py 是用 `selenium` 自动化测试工具写的爬虫

bsloupan.py 是用 `requests_html` 包写的爬虫，`requests_html` 包的介绍查看 https://requests-html.kennethreitz.org/index.html

## 二、环境安装

- 运行程序必须到以下网址 https://registry.npmmirror.com/binary.html?path=chromedriver/ 下载和 google chrome 浏览器版本一致的  chromedriver ，chromedriver 放到 Python 目录 C:\Python38\Scripts 下。

- 程序运行在 python 3.7 以上环境，必须安装 selenium 库

```sh
pip install selenium 
```



## 三、代码走读

用到 sqlite 数据库

用到 mongoDB 数据库

用到 requests 包

用到正则表达式 re 包

用到网址的解析 urllib 包

时间的解析 datetime 和 time 包

远程图片抓取