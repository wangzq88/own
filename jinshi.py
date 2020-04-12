from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException
from selenium.common.exceptions import TimeoutException
import pymongo
import json
import time

client = pymongo.MongoClient('mongodb://localhost:27017/')
db = client["test"]
col = db["news"]  
browser = webdriver.Chrome()
try:
    browser.get('https://www.jin10.com/')
    WebDriverWait(browser, 10).until(EC.presence_of_element_located((By.CLASS_NAME, 'jin-flash_wrap')))
    i = 0
    while i < 200 :
        browser.find_element_by_id('J_flashMoreBtn').click()
        time.sleep(30)
        i += 1
    commentList = browser.find_elements_by_class_name('J_flash_item')
    for c in commentList:
        dict = {'type':1,'source':'jinshi','ext':0}
        dict['date'] = c.get_attribute('data-id')
        dict['url'] = c.find_element_by_css_selector('div.jin-flash_h > div.jin-flash_icon > a').get_attribute('href')
        try:
            dict['content'] = c.find_element_by_css_selector('p.J_flash_text').text.strip()
        except NoSuchElementException:
            dict['title'] = c.find_element_by_css_selector('.jin-flash_data > .jin-flash_data-title').text.strip()
            dict['type'] = 2
            pList = c.find_elements_by_css_selector('.jin-flash_b > .jin-flash_data > .jin-flash_data-text > p')
            num = [p.text.strip() for p in pList]
            dict['num_json'] = json.dumps(num)
            dict['data_tag'] = c.find_element_by_css_selector('.jin-flash_b > .jin-flash_data > .jin-flash_data-wrap > .jin-flash_data-tag').text.strip()
        try:
            remarkList = c.find_elements_by_css_selector('.J_flash_remark_item')
            dict['ext'] = 1 if len(remarkList) > 0 else 0
            for remark in remarkList:
                remark.find_element_by_css_selector('.jin-icon_remark-link').click() 
                browser.implicitly_wait(3) 
                for handle in browser.window_handles:
                    browser.switch_to.window(handle)		
                    if browser.current_url != 'https://www.jin10.com/':
                        #添加链接
                        dict['new_content'] = dict['content'] + "\n" + browser.current_url
                        browser.close()
                        browser.switch_to.window(browser.window_handles[0])	
        except NoSuchElementException:
            pass             
        try:
            dict['img'] = c.find_element_by_css_selector('div.jin-flash_b > div.jin-flash_attach > a').get_attribute('href')
        except NoSuchElementException:
            pass          
        isNew = True
        for x in col.find({"date":dict['date']}):
            if ((x['type'] == 1 and 'new_content' not in dict and x['content'] == dict['content']) 
                or (x['type'] == 1 and 'new_content' in dict and x['content'] == dict['new_content']) 
                or (x['type'] == 2 and x['title'] == dict['title'])):
                print(x) 
                if ('new_content' in dict and x['content'] != dict['new_content']):
                    newvalues = {'$set':{'content':dict['new_content']}}
                    col.update_one({"_id":x['_id']}, newvalues)                  
#                dict['content'] = dict['new_content'] if 'new_content' in dict else dict['content']
                if(('img' not in x or x['img'] != dict['img']) and 'img' in dict):
                    newvalues = {'$set':{"img":dict['img'].strip(),'ext':dict['ext']}}
                    col.update_one({"_id":x['_id']}, newvalues)                        
                if(('url' not in x or x['url'].isspace()) and 'url' in dict):
                    newvalues = {'$set':{"url":dict['url'].strip(),'ext':dict['ext']}}
                    col.update_one({"_id":x['_id']}, newvalues)
                isNew = False
                break
        if 'new_content' in dict:
            dict['content'] = dict['new_content']
            del dict['new_content']
        print(dict)    
        if isNew :
            x = col.insert_one(dict)    
    for c in commentList:
        browser.execute_async_script('var wrap=document.getElementById("'+c.get_attribute('id')+'").parentNode;console.dir(wrap);for(i=1;i<wrap.childNodes.length;i++){wrap.removeChild(wrap.childNodes[i]);console.dir(wrap.childNodes[i]);}')
except TimeoutException:
    print('超时')
except NoSuchElementException:
    print('查找不到元素')
  