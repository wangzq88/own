import time,math,traceback
from selenium.webdriver.support.ui import WebDriverWait
from appium import webdriver



desired_caps = {}
desired_caps['platformName'] = 'Android'
desired_caps['deviceName'] = '127.0.0.1:62025'
desired_caps['platformVersion'] = '5.1.1'
desired_caps['appPackage'] = 'com.ss.android.ugc.live'
desired_caps['appActivity'] = '.main.MainActivity'
desired_caps['noReset'] = True
desired_caps['unicodeKeyboard'] = True
desired_caps['resetKeyboard'] = True

driver = webdriver.Remote('http://127.0.0.1:4723/wd/hub', desired_caps)

def get_size(driver):
    x = driver.get_window_size()['width']
    y = driver.get_window_size()['height']
    return (x,y)

def is_number(s):
    try:
        float(s)
        return True
    except ValueError:
        pass
 
    try:
        import unicodedata
        unicodedata.numeric(s)
        return True
    except (TypeError, ValueError):
        pass
 
    return False

def handle_douyin(driver):
    #青少年模式
    try:
        if WebDriverWait(driver,5).until(lambda x:x.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/aoj' and @text='我知道了']")):
            driver.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/aoj' and @text='我知道了']").click()
    except:
        pass
    #点击直播    
    try:
        if WebDriverWait(driver,3).until(lambda x:x.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/title' and @text='直播' and @content-desc='直播']")):
            driver.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/title' and @text='直播' and @content-desc='直播']").click()
    except:
        pass
    #弹出框（关注、不敢兴趣），点击取消
    try:
        if WebDriverWait(driver,7).until(lambda x:x.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/gea' and @text='取消']")):
            driver.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/gea' and @text='取消']").click()
    except:
        pass    	 
    #点击屏幕，进入直播间
    try:
        if WebDriverWait(driver,1).until(lambda x:x.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/f6s' and @text='点击进入直播间']")):
            driver.find_element_by_xpath("//android.widget.TextView[@resource-id='com.ss.android.ugc.live:id/f6s' and @text='点击进入直播间']").click()  
    except:
        print('hahhahha')
        pass        
    l = get_size(driver)
    x1 = int(l[0]*0.5)
    y1 = int(l[1]*0.6)       
    #如果是购物直播，直接跳过
    #if WebDriverWait(driver,3).until(lambda x:x.find_element_by_id("com.ss.android.ugc.live:id/cyk")):    
        #return
    wait_sec = 30    
    while True:    
        fans_count = 0
        #定位本场榜
        if WebDriverWait(driver,wait_sec).until(lambda x:x.find_element_by_id("com.ss.android.ugc.live:id/e__")):
            #点击本场榜
            fans_count = driver.find_element_by_id("com.ss.android.ugc.live:id/e__").text
            driver.find_element_by_id("com.ss.android.ugc.live:id/e__").click()
        x1 = int(l[0]*0.5)
        y1 = int(l[1]*0.8)
        y2 = int(l[1]*0.5)
        i = 0
        print('fans_count',fans_count)
        while True:
            if '最多只展示前100名用户' in driver.page_source:
                break
            #滑动一次9条记录
            if is_number(fans_count) and int(fans_count) <= 100 and i > math.ceil(int(fans_count)/7):
                break
            driver.swipe(x1,y1,x1,y2)
            time.sleep(0.3)
            i += 1
        #y2 = int(l[1]*0.5)
        #往回滑动，滑动一次7条记录
        y1 = int(l[1]*0.9)
        drag_count = 25
        if is_number(fans_count) and int(fans_count) <= 100:
            drag_count = math.ceil(int(fans_count)/6)
        for i in range(drag_count):
            #逐个点击top100粉丝     
            ele_list = driver.find_elements_by_id("com.ss.android.ugc.live:id/f81") 
            for ele in ele_list:
                #if i+1 < len(ele_list):
                    #driver.scroll(ele, ele_list[i+1])
                try:    
                    ele.click()
                    #关闭弹出框（粉丝信息）
                    if WebDriverWait(driver,3).until(lambda x:x.find_element_by_id("com.ss.android.ugc.live:id/enh")):
                        driver.tap([(x1,800)],500)
                except:
                    driver.tap([(x1,800)],500)
                    traceback.print_exc()
                    pass       
            try:
                #如果存在本场榜界面才滑动
                if WebDriverWait(driver,0.1).until(lambda x:x.find_element_by_id("com.ss.android.ugc.live:id/bo5")):          
                    driver.swipe(x1,y2,x1,y1)
            except:
                traceback.print_exc()
                pass                         
            #time.sleep(0.1)          
        try:
            x1 = int(l[0]*0.5)
            y1 = int(l[1]*0.15)            
            driver.tap([(x1,y1)],1000)      
        except:
            pass              
        #下一个视频            
        x1 = int(l[0]*0.5)
        y1 = int(l[1]*0.7)
        y2 = int(l[1]*0.2)
        driver.swipe(x1,y1,x1,y2)        
        wait_sec = 15
if __name__ == '__main__':
    handle_douyin(driver)

