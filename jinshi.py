from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.wait import WebDriverWait
from selenium.common.exceptions import NoSuchElementException,TimeoutException,StaleElementReferenceException,ElementClickInterceptedException
import pymongo
import json
import time
import sys       

def splider(browser,col):
    fo = open("jshistoty.txt", "r")
    content = fo.read()
    fo.close()
    if content.strip() != '':
        content = json.loads(content)
        first = content['first_news_id']
        last = content['last_news_id']
    commentList = browser.find_elements_by_css_selector('.J_flash_wrap > .J_flash_item')
    for i in range(len(commentList)):   
        try:
            c = commentList[i]
            try:
                id = c.get_attribute('id')
            except StaleElementReferenceException:
                break
            if i == 0:
                begin = id
            if len(commentList) - 1 == i :
                end = id               
            if id == None :
                continue  
            if 'first' in locals().keys() and 'last' in locals().keys() and id <= first and id >= last :
                browser.execute_script('$("#'+id+'").remove()');
                continue
      
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
                dict['num_json'] = num
                dict['data_tag'] = c.find_element_by_css_selector('.jin-flash_b > .jin-flash_data > .jin-flash_data-wrap > .jin-flash_data-tag').text.strip()
            try:
                remarkList = c.find_elements_by_css_selector('.J_flash_remark_item')
                dict['ext'] = 1 if len(remarkList) > 0 else 0
                for remark in remarkList:
                    remark.find_element_by_css_selector('.jin-icon_remark-link').click() 
                    try:
                        browser.implicitly_wait(3) 
                    except TimeoutException:
                        raise
                    finally:    
                        for handle in browser.window_handles:
                            browser.switch_to.window(handle)		
                            if browser.current_url != 'https://www.jin10.com/':
                                #添加链接
                                dict['new_content'] = dict['content'] + "\n" + browser.current_url
                                browser.close()
                                browser.switch_to.window(browser.window_handles[0])	
            except TimeoutException:
                break
            except (NoSuchElementException,ElementClickInterceptedException,StaleElementReferenceException):
                pass             
            try:
                dict['img'] = c.find_element_by_css_selector('div.jin-flash_b > div.jin-flash_attach > a').get_attribute('href')
            except (NoSuchElementException,StaleElementReferenceException):
                pass          
            isNew = True
            for x in col.find({"date":dict['date']}):
                if ((x['type'] == 1 and 'new_content' not in dict and dict['type'] == 1 and x['content'] == dict['content']) 
                    or (x['type'] == 1 and 'new_content' in dict and dict['type'] == 1 and (x['content'] == dict['new_content'] or x['content'] == dict['content']))
                    or (x['type'] == 2 and 'title' in dict and x['title'] == dict['title'])):
                    print(x) 
                    if ('new_content' in dict and x['content'] != dict['new_content']):
                        newvalues = {'$set':{'content':dict['new_content']}}
                        col.update_one({"_id":x['_id']}, newvalues)                  
    #                dict['content'] = dict['new_content'] if 'new_content' in dict else dict['content']
                    if('img' in dict and ('img' not in x or x['img'] != dict['img'])):
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
            if (isNew):
                x = col.insert_one(dict)
        finally:        
            if 'begin' in locals().keys() and 'end' in locals().keys() :
                if 'first' in locals().keys() and 'last' in locals().keys():
                    begin = first if begin < first else begin
                    end = last if end > last else end
                new = {'first_news_id':begin,'last_news_id':end}
                print(new)
                fo = open("jshistoty.txt", "w")
                fo.write(json.dumps(new))
                fo.close()
            
client = pymongo.MongoClient('mongodb://localhost:27017/')
db = client["test"]
col = db["news"]  
browser = webdriver.Chrome()
try:
    browser.get('https://www.jin10.com/')
    WebDriverWait(browser, 10).until(EC.presence_of_element_located((By.CLASS_NAME, 'jin-flash_wrap')))
    j = 0
    while True:
        try:
            browser.find_element_by_id('J_flashMoreBtn').click()
        except ElementClickInterceptedException:
            print('不可点击')           
        except NoSuchElementException:  
            for handle in browser.window_handles:
                browser.switch_to.window(handle)		
                if browser.current_url != 'https://www.jin10.com/':
                    browser.close()
                    browser.switch_to.window(browser.window_handles[0])	             
        time.sleep(3)
        j += 1
        if j <= 1:
            continue
        splider(browser,col) 
        browser.execute_script('$(function(){var wrap_list = $("#J_flashList .jin-flash_wrap");var wrap_length = wrap_list.length;console.log(wrap_length);if(wrap_length > 2){wrap_list.each(function(i,e){if(i == 0 || i == wrap_length - 1)return true;$(e).remove();});}});')        
except TimeoutException:
    print('超时')   