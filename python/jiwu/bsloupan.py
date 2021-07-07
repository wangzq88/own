# -*- coding: utf-8 -*-
from requests_html import HTMLSession
from urllib import parse
from datetime import datetime
from hyper.contrib import HTTP20Adapter
import os,re,time,sys,sqlite3,pymongo,requests,configparser

class Splider:

    __browser = None

    def __init__(self):
        self.__browser = HTMLSession()
        

    def __clearHref(self,href):
        parsed_result = parse.urlparse(href)
        params = parse.parse_qs(parsed_result.query)
        pathInfo = os.path.split(parsed_result.path)
        paramStr = parse.urlencode({'version':params['version'][0],'reqfr':params['reqfr'][0],'dictId':params['dictId'][0],'nid':params['nid'][0]})
        url = parsed_result.scheme + '://' + parsed_result.netloc + pathInfo[0] + '/' + pathInfo[1]+ '?'+paramStr
        return url                   
       
    def loupan(self,href):
        groupList = [] 
        try:
            print(href)
            parsed_result = parse.urlparse(href)
            header = {
                'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml',
                'Accept-Language': 'zh-CN,zh;q=0.9',
                'Accept-Encoding': 'gzip, deflate, br',
                'cache-control': 'max-age=0',
            #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
            #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                'Upgrade-Insecure-Requests': '1',
                'Connection': 'keep-alive',
                'Host': parsed_result.netloc
            }              
            time.sleep(1) 
            html = self.__browser.get(href.replace('loupan','detail'),headers=header).html
            mydict = {'title':'楼盘名称'}   
            selector = 'body > div.public-header > div.content-center.public-lpm1 > h1'
            mydict['content'] = html.find(selector,first=True).text.strip()            
            groupList.append(mydict)
            try:
                mydict = {'title':'楼盘区域'}
                selector = 'body > div.public-header > div.crumbs > a:nth-child(6)'
                mydict['content'] = html.find(selector,first=True).attrs['title'].strip() 
                groupList.append(mydict)
            except:
                pass             
            selector = 'div.mess-param > div.container-super > table > tr > td'
            resultList = html.find(selector)

            for i, value in enumerate(resultList):
                if i%2 == 0:
                    mydict = {'title':re.sub(r'\s','',value.text.strip())}
                elif not re.search(r'(售价待定|暂无资料|暂无信息)', value.text.strip()):    
                    mydict['content'] = value.text.strip()
                    mydict['content'] = re.sub(r'(\[降价通知我\]|\[开盘通知我\])','',mydict['content'])
                    groupList.append(mydict)  
            try:              
                mydict = {} 
                selector = 'div.pro-intro.mt40 > div > .mess-param-title'         
                mydict['title'] = html.find(selector,first=True).text.strip()
                selector = 'div.pro-intro.mt40 > div' 
                mydict['content'] = html.find(selector,first=True).text.strip()                              
                mydict['content'] = re.sub(mydict['title'],'',mydict['content'])
                groupList.append(mydict)
            except:
                pass   
            try:    
                mydict = {'title':'周边设施'}   
                selector = 'div.pro-special:last-child > div.container-super.lpxq > .mess-param-title'
                title = html.find(selector,first=True).text.strip()
                selector = 'div.pro-special:last-child > div.container-super.lpxq'
                mydict['content'] = html.find(selector,first=True).text.strip().replace(title,'')
                groupList.append(mydict)  
            except:
                pass  
            try:                 
                mydict = {'title':'核心卖点'}
                selector = 'div.public-header > div.content-center.public-lpm1 > div.public-lpm4 > span'     
                mydict['content'] = [ele.text.strip() for ele in html.find(selector)]
                groupList.append(mydict)
                for item in ["在售","待售","不可售","售完","在租"]:
                    if item in mydict['content']:
                        item = "已售完" if item == '售完' else item 
                        mydict = {'title':'售卖状态','content':item}
                        groupList.append(mydict)
            except:
                pass                          
            mydict = {'title':'预售许可证','content':[]}
            selector = '#licencebox > table > tbody > tr'
            contentList = html.find(selector)
            for i, item in enumerate(contentList):
                if i > 0:
                    tdList = [ele.text.strip() for ele in item.find('td')]
                    mydict['content'].append(tdList)
                    groupList.append(mydict)                    
        except:
            print("Unexpected error:", sys.exc_info()[0])
            raise    
        finally:
            return groupList          

    def album(self,href):
        groupList = []
        try:
            parsed_result = parse.urlparse(href)
            header = {
                'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml',
                'Accept-Language': 'zh-CN,zh;q=0.9',
                'Accept-Encoding': 'gzip, deflate, br',
                'cache-control': 'max-age=0',
            #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
            #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                'Upgrade-Insecure-Requests': '1',
                'Connection': 'keep-alive',
                'Host': parsed_result.netloc
            }
            time.sleep(1) 
            html = self.__browser.get(href.replace('/loupan/','/tu/list-loupan'),headers=header).html
            selector = 'div.public-header > div.content-center.public-lpm1 > h1'
            name = html.find(selector,first=True).text.strip() 
            selector = 'div.content-center > div.album-list > div.album-list'
            xiangceInfoList = html.find(selector)
            selector = 'div.content-center > div.album-list > div.common-title >h2'
            xiangceH3List = html.find(selector)
            for title,xiangce in zip(xiangceH3List,xiangceInfoList):
                itemTitle = re.sub(name, "", title.text.strip())
                itemList = [f.attrs['src'] for f in xiangce.find('.album-pic > a > img')] 
                itemLargeList = [parse.urlparse(f).scheme + '://' + parse.urlparse(f).netloc + os.path.dirname(parse.urlparse(f).path) for f in itemList]  
                groupList.append({'title':itemTitle,'imgList':{'thumb':itemList,'origin':itemLargeList}})
        except:
            print("基本信息出错:", sys.exc_info()[0])
            raise    
        finally:
            return groupList  

    def dongtai(self,href):
        groupList = []
        try:
            parsed_result = parse.urlparse(href)
            header = {
                'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml',
                'Accept-Language': 'zh-CN,zh;q=0.9',
                'Accept-Encoding': 'gzip, deflate, br',
                'cache-control': 'max-age=0',
            #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
            #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                'Upgrade-Insecure-Requests': '1',
                'Connection': 'keep-alive',
                'Host': parsed_result.netloc
            }
            time.sleep(1) 
            html = self.__browser.get(href.replace('/loupan/','/news/list-loupan'),headers=header).html
            selector = 'div.content-center > div.left870 > div.lpm-section2-1 > dl.lpm-section2-3'
            for ele in html.find(selector):
                news = {}
                try:
                    news['title'] = ele.find('dt > a',first=True).text.strip()
                    news['content'] = ele.find('dd',first=True).text.strip()
                    news['time'] = ele.find('dt > span',first=True).text.strip()
                    news['url'] = ele.find('dt > a',first=True).attrs['href']
                except:
                    pass
                groupList.append(news)                    
        except:
            print("动态出错:", sys.exc_info()[0])
            raise    
        finally:
            return groupList  

    def dongtaiDetail(self,href):    
        groupList = []
        try:
            parsed_result = parse.urlparse(href)
            header = {
                'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml',
                'Accept-Language': 'zh-CN,zh;q=0.9',
                'Accept-Encoding': 'gzip, deflate, br',
                'cache-control': 'max-age=0',
            #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
            #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                'Upgrade-Insecure-Requests': '1',
                'Connection': 'keep-alive',
                'Host': parsed_result.netloc
            }
            html = self.__browser.get(href.replace('/loupan/','/news/list-loupan'),headers=header).html
            selector = 'div.content-center > div.left870 > div.lpm-section2-1 > dl.lpm-section2-3 > .lpm-section2-4 > a'
            hrefList = [ele.attrs['href'] for ele in html.find(selector)]
            for href in hrefList:
                html = self.__browser.get(href,headers=header).html
                news = {}
                try:
                    news['title'] = html.find('div.content-center.clearfix > div.left870 > div.mt23 > strong',first=True).text.strip()
                    news['content'] = html.find('div.content-center.clearfix > div.left870 > div.lpm-c4-article',first=True).text.strip()
                    news['time'] = html.find('div.content-center.clearfix > div.left870 > div.lpm-c1 > p.lpm-c2 > span.lpm-c2-time',first=True).text.strip()
                except:
                    pass
                groupList.append(news)                    
        except:
            print("动态出错:", sys.exc_info()[0])
            raise    
        finally:
            return groupList  


    def huxing(self,href):
        huxingList = []
        try:
            parsed_result = parse.urlparse(href)
            header = {
                'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                'Accept': 'text/html,application/xhtml+xml,application/xml',
                'Accept-Language': 'zh-CN,zh;q=0.9',
                'Accept-Encoding': 'gzip, deflate, br',
                'cache-control': 'max-age=0',
            #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
            #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                'Upgrade-Insecure-Requests': '1',
                'Connection': 'keep-alive',
                'Host': parsed_result.netloc
            }
            time.sleep(1) 
            html = self.__browser.get(href.replace('/loupan/','/huxing/list-loupan'),headers=header).html
            selector = '#changetabcon > .v4hxcont > .house-type-s1'
            elementList = html.find(selector)
            for ele in elementList:
                house = {}
                try:
                    house['img'] = ele.find('a > img',first=True).attrs['src']
                    house['title'] = ele.find('.lpm-section3-1 > .house-type-s2 > a',first=True).text.strip()
                    house['allPrice'] = ele.find('.lpm-section3-1 > .house-type-s4 > .orange',first=True).text.strip()
                    house['size'] = ele.find('.lpm-section3-1 > .house-type-s3',first=True).text.strip().replace('建筑面积：','')       
                    house['down_payment'] = ele.find('.lpm-section3-1 > .house-type-s5',first=True).text.strip().replace('参考首付：','')       
                    house['desc'] = ele.find('.show-house-text > .autoHeight',first=True).attrs['textContent'].strip()  
                except:
                    pass
                huxingList.append(house)
        except:
            print("户型出错:", sys.exc_info())
            raise
        finally:
            return huxingList            

def transDateToTime(date):
    matchObj = re.match( r'(\d{4})年(\d{2})月(\d{2})日', date)
    if matchObj:
        str_time = matchObj.group(1)+'-'+matchObj.group(2)+'-'+matchObj.group(3) 
        str_time = time.mktime(time.strptime(str_time, '%Y-%m-%d'))
        return int(str_time) 
    matchObj = re.match( r'(\d{4})年(\d{2})月', date)
    if matchObj:         
        str_time = matchObj.group(1)+'-'+matchObj.group(2)+'-01'
        str_time = time.mktime(time.strptime(str_time, '%Y-%m-%d'))
        return int(str_time)  
    matchObj = re.match( r'(\d{2})年(\d{2})月', date)
    if matchObj:         
        str_time = '20'+matchObj.group(1)+'-'+matchObj.group(2)+'-01'
        str_time = time.mktime(time.strptime(str_time, '%Y-%m-%d'))
        return int(str_time)                   
    matchObj = re.match( r'(\d{4})年', date)
    if matchObj:         
        str_time = matchObj.group(1)+'-01-01'
        str_time = time.mktime(time.strptime(str_time, '%Y-%m-%d'))
        return int(str_time)         
    return 0

def getKeyByField(name,value,desclist):
    value = list(set(value))
    if '' in value:
        value.remove('')
    for desc in desclist:
        if desc['name'] == name:
            return  [desc['value'].index(item) for item in value]
    return None                         

def insLoupanHrefAction(splider,conn):
    cursor = conn.execute("SELECT * from city_href where flag=0")
    for item in cursor.fetchall():
        hrefList = splider.getAllLoupanHref(item[2]+'loupan/')    
        for href in hrefList:
            sql = "INSERT INTO loupan_href (href,city) VALUES "
            subSQL = []            
            cursor = conn.execute("SELECT * from loupan_href where href='%s' limit 1" % href)
            if cursor.fetchone() != None:
                continue
            subSQL.append("('%s','%s')" % (href,item[3]))
            sql = sql + ",".join(subSQL)    
            conn.execute(sql)
            conn.commit()
        conn.execute("update city_href set flag=1 where id = %d" % item[0])
        conn.commit()        

def saveImage(src):
    #有 cookie
    parsed_result = parse.urlparse(src)
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
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                    'Upgrade-Insecure-Requests': '1'
                }   
                #request = urllib.request.Request(src,headers=header,method='GET')    
                #reponse = urllib.request.urlopen(request)
                #buf = reponse.read()
                sessions = requests.session()
                sessions.mount(parsed_result.scheme+'://'+parsed_result.netloc, HTTP20Adapter())
                response = sessions.get(src,headers=header) 
                
                if response.status_code == requests.codes.ok:
                    f.write(response.content)
                elif response.status_code == 301 and b'Location' in response.headers.keys():
                    url = response.headers.get(b'Location').decode('utf-8')  
                    return saveImage(url)
                else:
                    raise Exception('读取图片出现错误 {}'.format(response.status_code))
            except Exception as e:
                print('Unexpected error:{0}'.format(e)) 
                return ''
    return filename            

def autoDownLoadImg(mydict):
    imgList = []
    keys = ['projectImg','sandTable','realistic','effectId','prototype','mating','traffic','businessLicense','salesLicenceImg','modelPic']
    for key in keys:
        if key in mydict:    
            if type(mydict[key]) != dict or len(mydict[key]) == 0 :
                continue
            for img in mydict[key]['thumb']:
                parsed_result = parse.urlparse(img)
                if parsed_result.netloc == 'cdn.lifeat.cn':
                    continue
                print('thumb',img)
                pic = {'src':img,'local':''}
                pathInfo = os.path.split(parsed_result.path)  
                if re.search(r'(\.jpeg|\.jpg|\.gif|\.png)', pathInfo[1]) == None:
                    #pic['local'] = saveImage(img)
                    pic['src'] = parsed_result.scheme + '://' + parsed_result.netloc + os.path.dirname(parsed_result.path)      
                imgList.append(pic)
    if 'estateDevBuildHouses' in mydict: 
        for item in mydict['estateDevBuildHouses']:
            if item['img'] != None:
                parsed_result = parse.urlparse(item['img'])
                if parsed_result.netloc == 'cdn.lifeat.cn':
                    continue
                pathInfo = os.path.split(parsed_result.path)
                pic = {'src':item['img'],'local':''}        
                if re.search(r'(\.jpeg|\.jpg|\.gif|\.png)', pathInfo[1]) == None:
                    #pic['local'] = saveImage(item['img'])
                    pic['src'] = parsed_result.scheme + '://' + parsed_result.netloc + os.path.dirname(parsed_result.path)    
                imgList.append(pic)
    return imgList

def insLoupanInfoAction(spd,conn,db):
    desclist = [x for x in db["project_desc"].find()]
    cur = conn.cursor()
    cursor = cur.execute("SELECT * from loupan_href where flag=0 and city in ('湛江','阳江','韶关','三亚','海口') order by id")
    loupanResult = cursor.fetchall()
    for row in loupanResult:
        cursor = cur.execute("SELECT * from city_href where city = '%s' " % row[3])
        city = cursor.fetchone()
        project = {'city':city[3],'cityId':city[4],'projectName':'','feature':None,'propertyType':None,'houseType':None,
        'sellingPoint':None,'expires':None,'decorateLevel':None,'areaId':None,'developerTagName':None,'addressDetail':None,
        'saleStatus':None,'openTime':None,'liveTime':None,'startTime':None,'finishTime':None,'bookingTime':None,'saleAddressDetail':None,'saleBuild':None,'landArea':None,'buildingArea':None,
        'plotRatio':None,'greenRatio':None,'parkRatio':None,'propertyCompany':None,'propertyFee':None,
        'isElevator':None,'averPrice':None,'peripheral':None,'introduce':None,'waterSupply':None,'heatingSupply':None,
        'powerSupply':None,'phone':None,'salesLicence':None,'broker':None,'salesStage':None,'model':None,'houseNumber':None,'trafficPosition':None,'bussinessPlace':None,'toward':None,'projectImg':{},'sandTable':{},
        'realistic':{},'effectId':{},'prototype':{},'mating':{},'traffic':{},'salesLicenceImg':{},'planning':{},'modelPic':{},
        'businessLicense':None,'url':row[1],'estateDevBuildHouses':[]}                           
        basicList = spd.loupan(row[1]) 
        for item in basicList:
            if item['title'] == '楼盘名称':
                project['projectName'] = item['content'] 
            elif item['title'] == '核心卖点':
                project['sellingPoint'] = item['content']                          
            elif item['title'] == '物业类型':
                project['propertyType'] = item['content']
            elif item['title'] in ['建筑形式']:
                project['houseType'] = item['content']
            elif item['title'] == '房屋产权':
                project['expires'] = item['content']
            elif item['title'] in ['装修状况']:
                project['decorateLevel'] = item['content']
            elif item['title'] == '楼盘区域':
                project['areaId'] = item['content']
            elif item['title'] == '开发商':
                item['content'] = re.sub('详情>','', item['content'])
                project['developerTagName'] = item['content']
            elif item['title'] == '楼盘地址':
                project['addressDetail'] = item['content']
            elif item['title'] in ['售卖状态','销售状态']:                        
                project['saleStatus'] = getKeyByField('saleStatus',re.split('、|,',item['content']),desclist) 
            elif item['title'] == '开盘时间':                         
                project['openTime'] = item['content']
            elif item['title'] in ['交房时间']:
                project['liveTime'] = item['content']                  
            elif item['title'] in ['售楼处地址']:
                project['saleAddressDetail'] = item['content']      
            elif item['title'] == '售卖楼栋':
                project['saleBuild'] = item['content']
            elif item['title'] == '规划面积':
                project['landArea'] = item['content']
            elif item['title'] == '建筑面积':
                project['buildingArea'] = item['content']
            elif item['title'] == '容积率':
                project['plotRatio'] = item['content']
            elif item['title'] == '绿化率':
                project['greenRatio'] = item['content'] 
            elif item['title'] == '车位配比':
                project['parkRatio'] = item['content']                                                                                                                                                                                                                                                                              
            elif item['title'] in ['物业公司']:
                project['propertyCompany'] = project['propertyCompany']+','+item['content'] if project['propertyCompany'] != None and project['propertyCompany'] != item['content'] else item['content']
            elif item['title'] == '物业费':
                project['propertyFee'] = item['content'] 
            elif item['title'] == '电梯房':
                project['isElevator'] = 1 if item['content'] == '是' else 0
            elif item['title'] == '单价':    
                project['averPrice'] = item['content']
            elif item['title'] in ['周边配套','周边规划']:    
                project['peripheral'] = item['content']
            elif item['title'] in ['项目介绍','楼盘简介']:    
                project['introduce'] = item['content']                
            elif item['title'] == '咨询电话':    
                project['phone'] = item['content']                
            elif item['title'] == '主力户型':
                project['model'] = item['content']    
            elif item['title'] == '预售许可证':    
                licenceList = item['content']     
                project['salesLicence'] = []
                for i,val in enumerate(licenceList):
                    if len(val) == 3:
                        tmp = {'licence':val[0],'date':val[1],'bulid':val[2]}
                    elif len(val) == 2:
                        tmp = {'licence':val[0],'date':val[1],'bulid':None}
                    elif len(val) == 1:
                        tmp = {'licence':val[0],'date':None,'bulid':None}                            
                    project['salesLicence'].append(tmp) 
            elif item['title'] == '销售代理':
                project['broker'] = item['content']
            elif item['title'] in ['规划户数','总户数']:
                project['houseNumber'] = item['content']
            elif item['title'] == '栋数':    
                project['buildingNumber'] = item['content']                
            elif item['title'] == '周边设施':    
                project['bussinessPlace'] = item['content'] 
            elif item['title'] == '楼盘朝向':    
                project['toward'] = item['content']
            elif item['title'] == '工程进度':    
                project['progress'] = item['content']                         
        xiangceList = spd.album(row[1])

        for item in xiangceList:
            if item['title'] in ['项目现场','楼盘图']:
                project['projectImg'] = item['imgList']
            elif item['title'] == '沙盘图':    
                project['sandTable'] = item['imgList']
            elif item['title'] == '规划图':
                project['planning'] = item['imgList']
            elif item['title'] == '户型图':
                project['modelPic'] = item['imgList']                    
            elif item['title'] == '实景图':
                project['realistic'] = item['imgList'] 
            elif item['title'] == '效果图':
                project['effectId'] = item['imgList']
            elif item['title'] == '样板间':
                project['prototype'] = item['imgList']      
            elif item['title'] == '配套图':
                project['mating'] = item['imgList'] 
            elif item['title'] in ['位置交通图','位置图','交通图']:
                project['traffic'] = item['imgList']  
            elif item['title'] == '楼盘证照':
                project['businessLicense'] = item['imgList']
            elif item['title'] == '预售许可证':
                project['salesLicenceImg'] = item['imgList'] 
        project['estateDevBuildHouses'] = spd.huxing(row[1])        
        project['activity'] = spd.dongtai(row[1])
        print(project) 
        db["jwproject"].insert_one(project)  
        cur.execute("UPDATE loupan_href set flag = 1 where id = %d" % row[0])  
        conn.commit()    
    conn.close()     
    

if __name__ == '__main__':
    config = configparser.ConfigParser()
    # 读取配置文件
    config.read('config.ini', encoding='utf-8')     
    conn = sqlite3.connect(config.get('sqlite', 'db'))
    cur = conn.cursor()
    create_tb_cmd = '''
        CREATE TABLE IF NOT EXISTS city_href(
        id INTEGER PRIMARY KEY AUTOINCREMENT,    
        flag BOOLEAN DEFAULT 0,
        href VARCHAR(255) NOT NULL UNIQUE,
        city VARCHAR(15) NOT NULL UNIQUE,
        city_id TINYINT NOT NULL);
    '''
    cur.execute(create_tb_cmd)
    conn.commit()   

    create_tb_cmd = '''
        CREATE TABLE IF NOT EXISTS loupan_href(
        id INTEGER PRIMARY KEY AUTOINCREMENT,    
        href VARCHAR(255) NOT NULL UNIQUE,
        flag BOOLEAN DEFAULT 0,
        city VARCHAR(30) NOT NULL);
    '''
    cur.execute(create_tb_cmd)
    conn.commit()
    spd = Splider()
    #basicList = spd.loupan('http://bj.jiwu.com/detail/1296498.html')
    #basicList = spd.dongtai('http://bj.jiwu.com/loupan/1296498.html')
    #print(basicList)
    #insLoupanHrefAction(spd,conn)
    #insCityHrefAction(spd,conn)
    client = pymongo.MongoClient('mongodb://'+config.get('mongodb', 'ip')+'/',ssl=config.get('mongodb', 'ssl'),tlsCertificateKeyFile=config.get('mongodb', 'certificate'),tlsCAFile=config.get('mongodb', 'cafile'),tlsAllowInvalidHostnames=True)
    db = client[config.get('mongodb', 'db')]
    db.authenticate(config.get('mongodb', 'name'), config.get('mongodb', 'password')) 
    insLoupanInfoAction(spd,conn,db)
         