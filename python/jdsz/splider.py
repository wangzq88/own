# -*- coding: utf-8 -*-
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.select import Select
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException,TimeoutException,ElementNotInteractableException
import time,configparser,os,random,traceback

class Splider:

    __browser = None

    def __init__(self):
        chrome_options = webdriver.ChromeOptions()                   
        chrome_options.add_experimental_option('excludeSwitches', ['enable-automation'])  # 切换到开发者模式
        #chrome_options.add_experimental_option("useAutomationExtension", False)
        chrome_options.add_argument('--start-maximized')
        chrome_options.add_argument('--disable-gpu')
        chrome_options.add_argument("--disable-blink-features=AutomationControlled")
        chrome_options.add_argument('--ignore-certificate-errors')
        chrome_options.add_argument('--ignore-ssl-errors')
        #hrome_options.add_argument("--headless")
        self.__browser = webdriver.Chrome(executable_path='chromedriver.exe',options=chrome_options)
        self.__browser.maximize_window()#确保窗口最大化确保坐标正确
        # 设置执行js代码转换模式
        self.__browser.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
            "source": """Object.defineProperty(navigator, 'webdriver', {get: () => undefined})""",
        })
     

    def getBrandGoods(self):
#        if not canuse():
#            print('过期了') 
#            return 
        config = configparser.ConfigParser()
        # 读取配置文件
        config.read('config.ini', encoding='utf-8')       
        sleepsec = config.get('basic', 'sleep_sec').strip()   
        brand_sec = config.get('basic', 'brand_sec').strip()                
        try:
            self.__browser.get('https://sz.jd.com/sz/view/industryBrand/brandDetailNews.html') 
            print('输入账号和密码，%s秒钟之后会自动跳转，请不要操作该窗口……' % sleepsec)
            time.sleep(int(sleepsec))            
            href = 'https://sz.jd.com/sz/view/industryBrand/brandBillBoards.html' 
            self.__browser.get(href) 
            time.sleep(5)
            try:
                print('你有%s秒的时间在当前页面操作搜索框，操作完毕之后，不要操作该窗口……' % brand_sec)
                time.sleep(int(brand_sec))                
                #点击分页框
                selector = "#container > div > div.content-body > div:nth-child(1) > div.grid-content > div.grace-grid-container > div.grace-grid-pagination > div:nth-child(2) > div > div:nth-child(3)"
                menu = self.__browser.find_element_by_css_selector(selector)
                submenu = self.__browser.find_element_by_css_selector(selector +' > span')
                ActionChains(self.__browser).move_to_element(menu).click(submenu).perform()
                time.sleep(2)  
                submenu = self.__browser.find_element_by_css_selector('.grace-select-dropdown')
                hidden_submenu = self.__browser.find_element_by_css_selector('.grace-select-dropdown > li:last-child')
                ActionChains(self.__browser).move_to_element(submenu).click(hidden_submenu).perform()                
                time.sleep(10)
                #移动到顶部位置
                selector = '#container > div > div.content-body > div:nth-child(1) > div.industry-grid-title > h2'
                h2menu = self.__browser.find_element_by_css_selector(selector)
                ActionChains(self.__browser).move_to_element(h2menu).perform()
                selector = '#container > div > div.content-body > div:nth-child(1) > div.grid-content > div > div.grace-grid-wrapper > div > section > div > div.normal-body > table > tbody > tr'
                rowList = self.__browser.find_elements_by_css_selector(selector)
                for i,row in enumerate(rowList):      
                    title = row.find_element_by_css_selector('td:nth-child(2) > div.cell-content > span > span.cell-content-html > div.wq-proname > span').text.strip()
                    cloBut = row.find_element_by_css_selector('td:last-child > div.cell-content > span > span.cell-content-html > span')
                    j = i + 1
                    path = 'csv/{0}/{1}-{2}.txt'.format(time.strftime("%Y-%m-%d", time.localtime()),j,title)
                    print('path:',path)
                    if os.path.exists(path):
                        continue                      
                    ActionChains(self.__browser).move_to_element(cloBut).click(cloBut).perform()
                    if len(self.__browser.window_handles) > 1:
                        for handle in self.__browser.window_handles:
                            self.__browser.switch_to.window(handle)
                            print('当前地址：' + self.__browser.current_url)
                            print('当前品牌：' + title)
                            if self.__browser.current_url != 'https://sz.jd.com/sz/view/industryBrand/brandBillBoards.html':
                                brand = {'title':title,'list':self.__getGoods()}
                                self.__browser.close()
                                yield brand   
                    self.__browser.switch_to.window(self.__browser.window_handles[0])
                    a = int(random.uniform(6,10))
                    time.sleep(a)	                   
            except (NoSuchElementException,ElementNotInteractableException):
                traceback.print_exc() 
                pass                   
        except NoSuchElementException as e:  
            print('捕获不到当前元素{0}，当前地址：{1}'.format(e,href))
            insertLogo('捕获不到当前元素{0}，当前地址：{1}'.format(e,href))       
        except TimeoutException:
            print('超时')                      

    def __getGoods(self):
        goodsUrl = set()
        goodsList = []
        try:  
            #点击商品交易榜单
            selector = '#container > div > div.content-header > span > span:last-child'
            WebDriverWait(self.__browser, 10).until(EC.presence_of_element_located((By.CSS_SELECTOR, selector)))              
            button = self.__browser.find_element_by_css_selector(selector)
            ActionChains(self.__browser).move_to_element(button).click(button).perform()
            time.sleep(8)
            #点击页面显示 100 条记录
            selector = "#container > div > div.content-body > div.pro-grid > div > div.grace-grid-container > div.grace-grid-pagination > div:nth-child(2) > div > div:nth-child(3)"
            menu = self.__browser.find_element_by_css_selector(selector)
            submenu = self.__browser.find_element_by_css_selector(selector +' > span')
            ActionChains(self.__browser).move_to_element(menu).click(submenu).perform()
            time.sleep(2)
            submenu = self.__browser.find_element_by_css_selector('.grace-select-dropdown')
            hidden_submenu = self.__browser.find_element_by_css_selector('.grace-select-dropdown > li:last-child')
            ActionChains(self.__browser).move_to_element(submenu).click(hidden_submenu).perform()   
            time.sleep(9)
            selector = '#container > div > div.content-body > div.pro-grid > div > div.grace-grid-container > div.grace-grid-wrapper > div > section > div > div.normal-body > table > tbody > tr'
            rowList = self.__browser.find_elements_by_css_selector(selector)
            for row in rowList:
                goods = {}
                selector = 'td:nth-child(2) > .cell-content > span > .cell-content-html > .wq-proname > a'
                ele = row.find_element_by_css_selector(selector)
                goods['title'] = ele.get_attribute('title')
                goods['href'] = ele.get_attribute('href')
                selector = 'td:nth-child(3) > div > span > span'
                goods['deal_amount'] = row.find_element_by_css_selector(selector).text.strip()
                selector = 'td:nth-child(4) > div > span > span'
                goods['deal_number'] = row.find_element_by_css_selector(selector).text.strip()       
                selector = 'td:nth-child(5) > div > span > span'
                goods['follow_number'] = row.find_element_by_css_selector(selector).text.strip()
                selector = 'td:nth-child(6) > div > span > span'
                goods['visitor_number'] = row.find_element_by_css_selector(selector).text.strip()                                       
                if not goods['href'] in goodsUrl:
                    goodsUrl.add(goods['href'])
                    goodsList.append(goods)
        except TimeoutException:
            print('超时')    
        return goodsList      

    def quit(self):
        self.__browser.quit()

def insertLogo(content):
    timeArray = time.localtime()   
    logName = time.strftime("%Y-%m-%d", timeArray)      
    path = 'logs/'
    if os.path.isdir(path) == False:
        os.makedirs(path,0o777)      
    with open(f"{path}{logName}_except.log", mode='a') as f:
        timeText = time.strftime("%Y-%m-%d %H:%M:%S", timeArray)      
        f.write('\n'+timeText+'\n'+content)       

def canuse():
    now = int(time.time())
    return 1635831163 + 259200 > now           