from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException,TimeoutException,StaleElementReferenceException,ElementClickInterceptedException
from urllib.parse import urlparse
import pymongo,json,time,sys,re,os

def splider(browser,col):
    weiboList = browser.find_elements_by_css_selector("#v6_pl_content_likelistoutbox > div.WB_feed.WB_feed_v3.WB_feed_v4 > div.WB_cardwrap.WB_feed_type.S_bg2")
    print(len(weiboList))
    for i in range(len(weiboList)):
        dict = {'type':1,'source':'weibo','ext':0}
        ele = weiboList[i]
        dict['content'] = ele.find_element_by_css_selector('.WB_detail > .WB_text').text.strip()
        matchObj = re.search( r'展开全文', dict['content'], re.M|re.I)
        print(dict)
        if matchObj:
            link = ele.find_element_by_css_selector('.WB_detail > .WB_text > a.WB_text_opt')
            eleChild = ele.find_element_by_css_selector('.WB_detail > .WB_text > a.WB_text_opt > i')
            ActionChains(browser).move_to_element(link).click(eleChild).perform()
            #v6_pl_content_likelistoutbox > div.WB_feed.WB_feed_v3.WB_feed_v4 > div:nth-child(8) > div.WB_feed_detail.clearfix > div.WB_detail > div:nth-child(5)
            cssSel = '#v6_pl_content_likelistoutbox > div.WB_feed.WB_feed_v3.WB_feed_v4 > div:nth-child('+str(i+1)+') > div.WB_feed_detail.clearfix > div.WB_detail > div:nth-child(5)'
            #WebDriverWait(browser, 5).until(EC.presence_of_element_located((By.CSS_SELECTOR, cssSel)))
            #还没立即插入DOM，睡3秒
            time.sleep(8)
            content = browser.find_element_by_css_selector(cssSel).text.strip()
            dict['content'] = content[0:-5]
        dict['content'] = re.sub(r'\n+', "\n", dict['content'], flags = re.M|re.I)    
        #视频    
        aTagList = ele.find_elements_by_tag_name('a')
        for aTag in aTagList:
            if aTag.get_attribute('action-type') == 'feed_list_url':
                dict['img'] = aTag.get_attribute('href')
                break
        #图片
        if 'img' not in dict :
            imgTagList = ele.find_elements_by_css_selector('div.WB_media_wrap > div.media_box > ul > li > img')
            for imgTag in imgTagList:    
                dict['img'] = imgTag.get_attribute('src')
                break
        #时间
        aTag = ele.find_element_by_tag_name('.WB_detail > .WB_from > a ')
        dict['date'] = aTag.get_attribute('title')+':00'
        #微博链接
        url = aTag.get_attribute('href')
        parsed_result = urlparse(url)
        dict['url'] = parsed_result.scheme + '://' + parsed_result.netloc + parsed_result.path
        if dict['date'] <= '2021-01-27 00:00:00':
            continue   
        '''
        isNew = True
        x = col.find_one({"url":dict['url'],"date":dict['date']})
        if(x == None): 
            x = col.insert_one(dict)
        '''    
       

client = pymongo.MongoClient('mongodb://106.52.40.114:27017/',ssl=True,tlsCertificateKeyFile='client.pem',tlsCAFile='ca.pem',tlsAllowInvalidHostnames=True)
mydb = client["cms"]
mydb.authenticate("root", "ADmin888")
mycol = mydb["news"]

chrome_options = Options()
chrome_options.add_argument('--disable-gpu')
browser = webdriver.Chrome(options=chrome_options)
try:
    browser.get('https://weibo.com/like/outbox?leftnav=1')
    WebDriverWait(browser,20).until(EC.presence_of_element_located((By.ID, 'pl_login_form')))
#    elem = browser.find_element_by_css_selector(".username > .input_wrap > .W_input")
#    elem.send_keys("wangzhiqiang2012@gmail.com")
#    elem = browser.find_element_by_css_selector(".password > .input_wrap > .W_input")
#    elem.send_keys("hsaifql2013")
#    elem.send_keys(Keys.ENTER)
    time.sleep(30)
    browser.get('https://weibo.com/like/outbox?leftnav=1')
    time.sleep(10)
    while(True):
        i = 0
        while(i < 4):
            time.sleep(10)
            browser.execute_script('window.scrollTo(0, document.body.scrollHeight)')
            i = i + 1        
        splider(browser,mycol)
        menu = browser.find_element_by_css_selector("#v6_pl_content_likelistoutbox > div.WB_feed.WB_feed_v3.WB_feed_v4 > div.WB_cardwrap.S_bg2 > div.W_pages > a.page.next.S_txt1.S_line1")
        ActionChains(browser).move_to_element(menu).click(menu).perform()
        time.sleep(15)
except TimeoutException:
    print('超时')   