#!/bin/bash
micro kill getarea
micro kill getImg
micro kill getorder
micro kill getsession
micro kill getsms
micro kill getuserhouses
micro kill getuserinfo
micro kill postauth
micro kill postlogin
micro kill postret
micro kill ahome
rm -rf /tmp/blob-*
rm -rf /tmp/go-build*
basepath=$(pwd)
cd "$basepath/ahome"
micro run .
echo "ahome启动成功"
cd "$basepath/services/getarea"
micro run .
echo "getarea服务启动成功"
cd "$basepath/services/getImg"
micro run .
echo "getImg服务启动成功"
cd "$basepath/services/getorder"
micro run .
echo "getorder服务启动成功"
cd "$basepath/services/getsession"
micro run .
echo "getsession服务启动成功"
cd "$basepath/services/getsms"
micro run .
echo "getsms服务启动成功"
cd "$basepath/services/getuserhouses"
micro run .
echo "getuserhouses服务启动成功"
cd "$basepath/services/getuserinfo"
micro run .
echo "getuserinfo服务启动成功"
cd "$basepath/services/postauth"
micro run .
echo "postauth服务启动成功"
cd "$basepath/services/postlogin"
micro run .
echo "postlogin服务启动成功"
cd "$basepath/services/postret"
micro run .
echo "postret服务启动成功"
result=$(find /tmp/ -name blob-*)
for item in $result
do
    files=$(ls $item)
    for file in $files
    do
       if [ $file == cmd ]
       then
           $(/bin/cp -rf $basepath/ahome/view $item/)
           echo "客户端目录:$item"
       fi
    done

    files=$(ls $item/handler)
    for file in $files
    do
       if [ $file == getImg.go ]
       then
           $(/bin/cp -rf $basepath/services/getImg/comic.ttf $item/)
           echo "图片目录:$item"
       fi
    done
done