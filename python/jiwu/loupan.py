# -*- coding: utf-8 -*-
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException,TimeoutException,StaleElementReferenceException,ElementClickInterceptedException,JavascriptException,ElementNotInteractableException
from urllib import parse
from datetime import datetime
from hyper.contrib import HTTP20Adapter
import os,re,time,sys,sqlite3,pandas,pymongo,requests,configparser

class Splider:

    __browser = None

    def __init__(self):
        self.options = webdriver.ChromeOptions()
        self.options.add_experimental_option('excludeSwitches', ['enable-automation'])  # 切换到开发者模式
        self.options.add_experimental_option("useAutomationExtension", False)
        self.options.add_argument('--disable-gpu')
        self.options.add_argument("--disable-blink-features=AutomationControlled")
        self.options.add_argument('--ignore-certificate-errors')
        self.options.add_argument('--ignore-ssl-errors')
        self.options.add_argument("--headless")
        self.__browser = webdriver.Chrome(options=self.options)
        self.__browser.maximize_window()#确保窗口最大化确保坐标正确
        # 设置执行js代码转换模式
        self.__browser.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
            "source": """Object.defineProperty(navigator, 'webdriver', {get: () => undefined})""",
        })     


    def __clearHref(self,href):
        parsed_result = parse.urlparse(href)
        params = parse.parse_qs(parsed_result.query)
        pathInfo = os.path.split(parsed_result.path)
        paramStr = parse.urlencode({'version':params['version'][0],'reqfr':params['reqfr'][0],'dictId':params['dictId'][0],'nid':params['nid'][0]})
        url = parsed_result.scheme + '://' + parsed_result.netloc + pathInfo[0] + '/' + pathInfo[1]+ '?'+paramStr
        return url           
       
    def getAllLoupanHref(self,href):
        linkList = []
        try:
            self.__browser.get(href)
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'maincon'))) 
            selector = "body > div.content > div.wrap > div.maincon.clearfix > div.listcon > div.house-list > div.box.new_house > div.text > div.tit.clearfix > span > a"
            for ele in self.__browser.find_elements_by_css_selector(selector):
                href = ele.get_attribute('href')
                if href != 'javascript:;':
                    linkList.append(href)
                else:
                    print(ele.get_attribute('onclick'))
                    matchObj = re.match(r"trackClick\('.*?','.*?','.*?',\s*'(.*?)'\);", ele.get_attribute('onclick'))
                    if matchObj:
                        print(matchObj.group(1))
                        linkList.append(matchObj.group(1))
            nextPage = self.__browser.find_element_by_css_selector('body > div.content > div.wrap > div.maincon.clearfix > div.listcon > div.pagination-box.clearfix > div.pagination.pull-right > div.pagination > a.next')
            ActionChains(self.__browser).click(nextPage).perform() 
            linkList += self.getAllLoupanHref(self.__browser.current_url)            
        except NoSuchElementException as e:  
            print('已经是最后一页{0}'.format(e))
        finally:
            return linkList           
       
    def loupan(self,href):
        groupList = [] 
        try:
            self.__browser.get(href)
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'public-lpnav1')))     
            ActionChains(self.__browser).click(self.__browser.find_element_by_css_selector('#builddetail_li')).perform()
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'lpm-section4-table')))     
            mydict = {'title':'楼盘名称'}   
            selector = 'body > div.public-header > div.content-center.public-lpm1 > h1'
            mydict['content'] = self.__browser.find_element_by_css_selector(selector).text.strip()            
            groupList.append(mydict)
            mydict = {'title':'楼盘区域'}
            selector = 'body > div.public-header > div.crumbs > a:nth-child(6)'
            mydict['content'] = self.__browser.find_element_by_css_selector(selector).get_attribute('title').strip() 
            groupList.append(mydict)
            selector = 'body > div.mess-param.mt40 > div > table > tbody > tr > td'
            resultList = self.__browser.find_elements_by_css_selector(selector)
            for i, value in enumerate(resultList):
                if i%2 == 0:
                    mydict = {'title':re.sub(r'\s','',value.text.strip())}
                elif not re.search(r'(售价待定|暂无资料|暂无信息)', value.text.strip()):    
                    mydict['content'] = value.text.strip()
                    mydict['content'] = re.sub(r'(\[降价通知我\]|\[开盘通知我\])','',mydict['content'])
                    groupList.append(mydict)        
            mydict = {} 
            selector = 'body > div.pro-intro.mt40 > div > .mess-param-title'         
            mydict['title'] = self.__browser.find_element_by_css_selector(selector).text.strip()
            selector = 'body > div.pro-intro.mt40 > div' 
            mydict['content'] = self.__browser.find_element_by_css_selector(selector).text.strip()                              
            mydict['content'] = re.sub(mydict['title'],'',mydict['content'])
            groupList.append(mydict)
            mydict = {'title':'周边设施'}   
            selector = 'body > div.pro-special:last-child > div.container-super.lpxq > .mess-param-title'
            title = self.__browser.find_element_by_css_selector(selector).text.strip()
            selector = 'body > div.pro-special:last-child > div.container-super.lpxq'
            mydict['content'] = self.__browser.find_element_by_css_selector(selector).text.strip().replace(title,'')
            groupList.append(mydict)  
            mydict = {'title':'核心卖点'}
            selector = 'body > div.public-header > div.content-center.public-lpm1 > div.public-lpm4 > span'     
            mydict['content'] = [ele.text.strip() for ele in self.__browser.find_elements_by_css_selector(selector)]
            groupList.append(mydict)
            for item in ["在售","待售","不可售","已售完","在租"]:
                if item in mydict['content']:
                    mydict = {'title':'售卖状态','content':item}
                    groupList.append(mydict)
            ActionChains(self.__browser).click(self.__browser.find_element_by_css_selector('#licence')).perform()
            time.sleep(1)   
            mydict = {'title':'预售许可证','content':[]}
            selector = '#licencebox > table > tbody > tr'
            contentList = self.__browser.find_elements_by_css_selector(selector)
            for i, item in enumerate(contentList):
                if i > 0:
                    tdList = [ele.text.strip() for ele in item.find_elements_by_css_selector('td')]
                    mydict['content'].append(tdList)
                groupList.append(mydict)                    
        except NoSuchElementException as e:  
            print('没有捕获到元素{0}'.format(e))  
        except TimeoutException:
            print('超时')
        except:
            print("Unexpected error:", sys.exc_info()[0])
            raise    
        finally:
            return groupList          

    def album(self,href):
        groupList = []
        try:
            self.__browser.get(href)
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'public-lpnav1')))     
            ActionChains(self.__browser).click(self.__browser.find_element_by_css_selector('#album_li')).perform()
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'album-list')))     
            selector = 'body > div.public-header > div.content-center.public-lpm1 > h1'
            name = self.__browser.find_element_by_css_selector(selector).text.strip() 
            selector = 'body > div.content-center > div.album-list > div.album-list'
            xiangceInfoList = self.__browser.find_elements_by_css_selector(selector)
            selector = 'body > div.content-center > div.album-list > div.common-title >h2'
            xiangceH3List = self.__browser.find_elements_by_css_selector(selector)
            for title,xiangce in zip(xiangceH3List,xiangceInfoList):
                itemTitle = re.sub(name, "", title.text.strip())
                itemList = [f.get_attribute('src') for f in xiangce.find_elements_by_css_selector('.album-pic > a > img')] 
                itemLargeList = [parse.urlparse(f).scheme + '://' + parse.urlparse(f).netloc + os.path.dirname(parse.urlparse(f).path) for f in itemList]  
                groupList.append({'title':itemTitle,'imgList':{'thumb':itemList,'origin':itemLargeList}})
        except NoSuchElementException as e:  
            print('没有捕获到元素{0}'.format(e))  
        except TimeoutException:
            print('超时')
        except:
            print("Unexpected error:", sys.exc_info()[0])
            raise    
        finally:
            return groupList  

    def dongtai(self,href):    
        groupList = []
        try:
            self.__browser.get(href)
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'public-lpnav1')))     
            ActionChains(self.__browser).click(self.__browser.find_element_by_css_selector('#info_li')).perform()
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'lpm-section2-1')))     
            selector = 'body > div.content-center > div.left870 > div.lpm-section2-1 > dl.lpm-section2-3 > .lpm-section2-4 > a'
            hrefList = [ele.get_attribute('href') for ele in self.__browser.find_elements_by_css_selector(selector)]
            for href in hrefList:
                self.__browser.get(href)
                WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'left870')))     
                news = {}
                news['title'] = self.__browser.find_element_by_css_selector('body > div.content-center.clearfix > div.left870 > div.mt23 > strong').text.strip()
                news['content'] = self.__browser.find_element_by_css_selector('body > div.content-center.clearfix > div.left870 > div.lpm-c4-article').text.strip()
                news['time'] = self.__browser.find_element_by_css_selector('body > div.content-center.clearfix > div.left870 > div.lpm-c1 > p.lpm-c2.fl > span.lpm-c2-time').text.strip()
                groupList.append(news)
        except NoSuchElementException as e:  
            print('没有捕获到元素{0}'.format(e))  
        except TimeoutException:
            print('超时')
        except:
            print("Unexpected error:", sys.exc_info()[0])
            raise    
        finally:
            return groupList  


    def huxing(self,href):
        huxingList = []
        try:
            self.__browser.get(href)
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'public-lpnav1')))     
            ActionChains(self.__browser).click(self.__browser.find_element_by_css_selector('#housetu_li')).perform()
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.ID, 'changetabcon')))     
            selector = '#changetabcon > .v4hxcont > .house-type-s1'
            elementList = self.__browser.find_elements_by_css_selector(selector)
            for ele in elementList:
                house = {}
                try:
                    house['img'] = ele.find_element_by_css_selector('a > img').get_attribute('src')
                    house['title'] = ele.find_element_by_css_selector('.lpm-section3-1 > .house-type-s2 > a').text.strip()
                    house['allPrice'] = ele.find_element_by_css_selector('.lpm-section3-1 > .house-type-s4 > .orange').text.strip()
                    house['size'] = ele.find_element_by_css_selector('.lpm-section3-1 > .house-type-s3').text.strip().replace('建筑面积：','')       
                    house['down_payment'] = ele.find_element_by_css_selector('.lpm-section3-1 > .house-type-s5').text.strip().replace('参考首付：','')       
                    house['desc'] = ele.find_element_by_css_selector('.show-house-text > .autoHeight').get_attribute('textContent').strip()  
                except NoSuchElementException as e: 
                    pass
                huxingList.append(house)
        except ElementClickInterceptedException as e:
            print('不可点击{0}'.format(e))           
        except NoSuchElementException as e:  
            print('没有捕获到元素{0}'.format(e))
        except:
            print("Unexpected error:", sys.exc_info())
            raise
        finally:
            return huxingList      

    def getCityList(self):
        cityList = []
        try:
            self.__browser.get('http://www.jiwu.com/')
            WebDriverWait(self.__browser, 20).until(EC.presence_of_element_located((By.CLASS_NAME, 'fivindexcont')))     
            selector = "body > div.fivindexcont.citymar > dl.newscity > dd > a"
            liList = self.__browser.find_elements_by_css_selector(selector)
            for ele in liList:
                city = {}
                city['name'] = ele.text.strip()
                city['href'] = ele.get_attribute('href')
                cityList.append(city)       
        except NoSuchElementException as e:  
            print('没有捕获到元素{0}'.format(e))  
        except TimeoutException as e:
            print('超时{0}'.format(e))  
        return cityList         

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

def insCityHrefAction(splider,conn):
    cityList = splider.getCityList()   
    sql = "INSERT INTO city_href (href,city,city_id) VALUES "
    subSQL = []
      
    df = pandas.read_excel("city.xlsx")
    for item in cityList:    
        for row in df.values :
            city = re.sub(r'(市|省直辖县级行政单位|)', "", row[0])
            if item['name'] == city:
                subSQL.append("('%s','%s',%d)" % (item['href'],item['name'],row[1]))
    sql = sql + ",".join(subSQL)
    conn.execute(sql)
    conn.commit()               

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
    if dir == filename:
        return ''
    if os.path.isdir(dir) == False:
        os.makedirs(dir,0o777)  
    if not os.path.exists(filename) or os.path.getsize(filename) == 0:          
        with open(filename, 'wb+') as f:
            try:
                header = {
                    'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36',
                    'Accept': 'text/html,application/xhtml+xml,application/xml',
                    'Accept-Language': 'zh-CN,zh;q=0.9',
                    'Accept-Encoding': 'gzip, deflate, br',
                    'cache-control': 'max-age=0',
                #    'if-modified-since':'Tue, 05 Jan 2021 06:12:25 GMT',
                #    'if-none-match': '"fdf93d003dfe7d706165304b54291237"',
                    'Sec-Fetch-Dest': 'document',
                    'Sec-Fetch-Mode': 'navigate',
                    'Sec-Fetch-Site': 'none',
                    'Sec-Fetch-User': '?1',
                    'Upgrade-Insecure-Requests': '1',
                    ':scheme': parsed_result.scheme,
                    ':path': parsed_result.path,
                    ':method': 'GET',
                    ':authority': parsed_result.netloc
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
            if type(mydict[key]) != list or len(mydict[key]) == 0 :
                continue
            for img in mydict[key]:              
                pic = {'src':img}
                pic['local'] = saveImage(img)
                imgList.append(pic)
                    
    if 'estateDevBuildHouses' in mydict: 
        for item in mydict['estateDevBuildHouses']:
            if item['img'] != None:
                pic = {'src':item['img']}
                pic['local'] = saveImage(item['img'])
                imgList.append(pic) 
    if 'broker' in mydict: 
        if mydict['broker'] != None and mydict['broker']['img'] != None:
            pic = {'src':mydict['broker']['img']}
            pic['local'] = saveImage(mydict['broker']['img'])
            imgList.append(pic)                                
    return imgList

def insLoupanInfoAction(spd,conn,db):
    desclist = [x for x in db["project_desc"].find()]
    cur = conn.cursor()
    cursor = cur.execute("SELECT * from loupan_href where flag=0 and city='深圳' order by id desc")
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
                item['content'] = re.sub(r'(企业|标准|高档|花园|其他|酒店式|酒店|住宅型|市场类|总部|底商|LOFT|SOHO)', "", item['content'])
                item['content'] = item['content'].replace('|', ',')
                item['content'] = re.sub(r'(商用|商住)','商业,公寓', item['content'])
                item['content'] = re.sub(r'办公别墅','办公,别墅', item['content'])
                item['content'] = re.sub(r'写字楼商铺','写字楼,商铺', item['content'])
                item['content'] = re.sub(r'住宅','普通住宅', item['content'])
                project['propertyType'] = getKeyByField('propertyType',re.split('、|,',item['content']),desclist)
            elif item['title'] in ['建筑形式']:
                item['content'] = re.sub(r'(小|中|花园|两幢|其他)', "", item['content'])
                item['content'] = re.sub(r'(板塔结合|塔板结合)', "塔楼,板楼", item['content'])
                item['content'] = re.sub('叠加', "叠拼", item['content'])
                item['content'] = re.sub('平房', "平层", item['content'])
                if item['content'].strip() != '':
                    project['houseType'] = getKeyByField('houseType',re.split('、|,|，|\s',item['content']),desclist)
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
                if item['content'].strip() in ['待定','未知','暂无资料','顺销','暂无']:
                    continue
                matchObj = re.match(r'(\d{4})-(\d{1,2})-(\d{1,2})', item['content'].strip())    
                if matchObj: 
                    month = matchObj[2] if len(matchObj[2]) == 2  else '0'+ matchObj[2]
                    date = matchObj[3] if len(matchObj[3]) == 2  else '0'+ matchObj[3]
                    item['content'] = matchObj[1] + '-' + month + '-' + date                            
                project['openTime'] = datetime.strptime(item['content'],'%Y-%m-%d').timestamp() if transDateToTime(item['content']) == 0 else transDateToTime(item['content'])
                project['openTime'] = int(project['openTime'])
            elif item['title'] in ['交房时间']:
                matchObj = re.match(r'预计(\d{4})年(\d{1,2})月交付', item['content'].strip())
                if matchObj: 
                    month = matchObj[2] if len(matchObj[2]) == 2  else '0'+ matchObj[2]
                    item['content'] = matchObj[1] + '年' + month + '月'
                matchObj = re.match(r'预计(\d{4})年中部分交房', item['content'].strip())
                if matchObj: 
                    item['content'] = matchObj[1] + '年'                       
                matchObj = re.match(r'(\d{4})-(\d{1,2})-(\d{1,2})', item['content'].strip())
                if matchObj: 
                    month = matchObj[2] if len(matchObj[2]) == 2  else '0'+ matchObj[2]
                    date = matchObj[3] if len(matchObj[3]) == 2  else '0'+ matchObj[3]
                    date = '30' if date == '31' else date
                    item['content'] = matchObj[1] + '-' + month + '-' + date                        
                if item['content'].strip() in ['待定','未知','暂无资料','暂无']:
                    continue
                item['content'] = re.sub(r'(交付|份)', "", item['content'])
                item['content'] = datetime.strptime(item['content'],'%Y-%m-%d').timestamp() if transDateToTime(item['content']) == 0 else transDateToTime(item['content'])
                project['liveTime'] = int(item['content'])                    
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
            elif item['title'] in ['项目介绍','楼盘简介','景观特色']:    
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
        #exit()   
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
         