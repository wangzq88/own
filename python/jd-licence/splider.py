# -*- coding: utf-8 -*-
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.select import Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException,TimeoutException
import ddddocr,onnxruntime,time,configparser,hashlib,os,random
from browsermobproxy import Server

class Splider:

    __browser = None
    __server = None
    codePath = ''

    def __init__(self):
        chrome_options = webdriver.ChromeOptions()        
        config = configparser.ConfigParser()
        self.ocr = ddddocr.DdddOcr()
        # 读取配置文件
        config.read('config.ini', encoding='utf-8')
        ip = config.get('http_proxy', 'ip').strip()  
        port = config.get('http_proxy', 'port').strip()              
        orderno = config.get('http_proxy', 'orderno').strip()    
        secret = config.get('http_proxy', 'secret').strip()    
        browsermob = config.get('http_proxy', 'browsermob').strip() 
        if ip != '' and port != '' and orderno != '' and secret != '':
            options = {'host':ip,'port':int(port)}
            self.__server = Server(path=browsermob,options=options)
            self.__server.start()
            proxy = self.__server.create_proxy()         
            timestamp = str(int(time.time()))              
            string = "orderno=" + orderno + "," + "secret=" + secret + "," + "timestamp=" + timestamp
            string = string.encode()
            md5_string = hashlib.md5(string).hexdigest()                
            sign = md5_string.upper()                             
            auth = "sign=" + sign + "&" + "orderno=" + orderno + "&" + "timestamp=" + timestamp
            proxy.headers({'Proxy-Authorization':auth})
            chrome_options.add_argument('--proxy-server={0}'.format(proxy.proxy))

        chrome_options.add_experimental_option('excludeSwitches', ['enable-automation'])  # 切换到开发者模式
        #chrome_options.add_experimental_option("useAutomationExtension", False)
        chrome_options.add_argument('--start-maximized')
        chrome_options.add_argument('--disable-gpu')
        chrome_options.add_argument("--disable-blink-features=AutomationControlled")
        chrome_options.add_argument('--ignore-certificate-errors')
        chrome_options.add_argument('--ignore-ssl-errors')
        chrome_options.add_argument("--headless")
        self.__browser = webdriver.Chrome(options=chrome_options)
        self.__browser.maximize_window()#确保窗口最大化确保坐标正确
        # 设置执行js代码转换模式
        self.__browser.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
            "source": """Object.defineProperty(navigator, 'webdriver', {get: () => undefined})""",
        })
        self.codePath = 'image/'+str(random.randint(100000,999999))
        if os.path.isdir(self.codePath) == False:
            try:
                os.makedirs(self.codePath,0o777)  
            except FileExistsError:
                os.remove(self.codePath)
                os.makedirs(self.codePath,0o777)         

    def showLicence(self,href):
        result = {}
        try:
            self.__browser.get(href) 
            time.sleep(1)
            if self.__browser.current_url == 'https://www.jd.com/error.aspx':
                raise TimeoutException
            WebDriverWait(self.__browser, 8).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#verifyCodeImg'))) 
            codeFile = self.codePath +'/'+ str(random.randint(100000000,999999999))+'.jpg'
            self.__browser.find_element_by_xpath('//*[@id="verifyCodeImg"]').screenshot(codeFile)
            #屏蔽错误
            onnxruntime.set_default_logger_severity(3)
            with open(codeFile, 'rb') as f:
                img_bytes = f.read()  
            res = self.ocr.classification(img_bytes)
            elem = self.__browser.find_element_by_id("verifyCode")
            elem.clear()
            elem.send_keys(res)
            elem.send_keys(Keys.RETURN)     
            selector = '#pop > div.forBack > div:nth-child(1) > div.jHeader > div.jLogo > em'
            result['shop'] = self.__browser.find_element_by_css_selector(selector).text.strip()  
            WebDriverWait(self.__browser, 10).until(EC.presence_of_element_located((By.CSS_SELECTOR, '#wrap > div > div.jRatingMore > div > ul'))) 
            try:
                selector = '#wrap > div > div.jRatingMore > div > ul > li:nth-child(3) > span'
                result['company'] = self.__browser.find_element_by_css_selector(selector).text.strip()          
            except NoSuchElementException:
                selector = '#wrap > div > div.jRatingMore > div > ul > li:nth-child(4) > span' 
                result['company'] = self.__browser.find_element_by_css_selector(selector).text.strip()                    
        except NoSuchElementException as e:  
            print('捕获不到当前元素{0}，当前地址：{1}'.format(e,href))
            insertLogo('捕获不到当前元素{0}，当前地址：{1}'.format(e,href))       
        except TimeoutException:
            print('超时')                   
        return result  

    def quit(self):
        self.__browser.quit()
        if self.__server != None:
            self.__server.stop()

def insertLogo(content):
    now = int(time.time())
    timeArray = time.localtime(now)   
    logName = time.strftime("%Y-%m-%d", timeArray)      
    path = 'logs/'
    if os.path.isdir(path) == False:
        os.makedirs(path,0o777)      
    with open(f"{path}{logName}_except.log", mode='a') as f:
        timeText = time.strftime("%Y-%m-%d %H:%M:%S", timeArray)      
        f.write('\n'+timeText+'\n'+content)