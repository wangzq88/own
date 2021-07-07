# -*- coding: utf-8 -*-
import urllib.parse
import requests,sqlite3,os,re
import bsloupan

def saveImage(src):
    #有 cookie
    parsed_result = urllib.parse.urlparse(src)
    filename = parsed_result.netloc+parsed_result.path
    dir = parsed_result.netloc + os.path.dirname(parsed_result.path)
    if dir == filename or re.search('uhouseimg',parsed_result.netloc) != None:
        return ''
    if os.path.isdir(dir) == False:
        try:
            os.makedirs(dir,0o777)
        except FileExistsError:
            os.remove(dir)
            os.makedirs(dir,0o777)
    if not os.path.exists(filename) or os.path.getsize(filename) == 0:          
        with open(filename, 'wb+') as f:
            try:
                header = {
                    'User-Agent':'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Language': 'zh-CN,zh;q=0.9',
                    'Accept-Encoding': 'gzip, deflate',
                    'Connection': 'keep-alive',
                    'Upgrade-Insecure-Requests': '1',
                    'Host': parsed_result.netloc
                }                    
                sessions = requests.session()
                response = sessions.get(src,headers=header,timeout=5) 
                if response.status_code == requests.codes.ok:
                    f.write(response.content)
                elif response.status_code == 301 and b'Location' in response.headers.keys():
                    url = response.headers.get(b'Location').decode('utf-8')  
                    return saveImage(url)
                else:
                    raise Exception('读取图片出现错误 {}'.format(response.status_code))
            except requests.exceptions.Timeout:
                print('超时，重新来过')  
                return saveImage(src)        
            except Exception as e:
                print('Unexpected error:{0}'.format(e)) 
                return ''
    return filename            

if __name__ == '__main__':
    conn = sqlite3.connect('jiwupic.db')
    cur = conn.cursor()        
    sql = "DELETE FROM attr_info WHERE `group` = 'rewrite'"
    conn.execute(sql)
    conn.commit()    
    cursor = cur.execute("SELECT * from attr_info where `group` = 'rewrite'")
    infoList = cursor.fetchall()        
    pageInfo = {'page':1,'limit':4000}
    for row in infoList:
        pageInfo[row[1]] = int(row[2])    
    page,limit = pageInfo['page'],pageInfo['limit']    
    start = page * limit          
   
    cursor = cur.execute("SELECT count(*) from imgsrc where href != '' and local = '' and `flag`=0 ")
    count = cursor.fetchone()[0]
    while start < count:
        cursor = cur.execute(f"SELECT * from imgsrc where href != '' and local = '' and `flag`=0 order by id desc limit {start},{limit}")
        for row in cursor.fetchall():
            print(row)
            parsed_result = urllib.parse.urlparse(row[1])
            pathInfo = os.path.split(parsed_result.path)    
            if re.search(r'(\.jpeg|\.jpg|\.gif|\.png)', pathInfo[1]) != None:        
                res = saveImage(row[1])
                if res == '':   
                    print('重新读取')      
                    res = bsloupan.saveImage(row[1])
                print('本地图片路径',res)    
                cur.execute("UPDATE imgsrc set local = '%s' where id = %d" % (res,row[0]))  
                conn.commit()                     
        page += 1
        start = page * limit    
        sql = "DELETE FROM attr_info WHERE `group` = 'rewrite'"
        conn.execute(sql)
        conn.commit()
        sql = "INSERT INTO attr_info (`name`,`value`,`group`) VALUES ('page','%d','rewrite'),('limit','%d','rewrite')" % (page,limit) 
        conn.execute(sql)
        conn.commit()
    conn.close() 
        
    
