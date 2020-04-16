from selenium import webdriver
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import *
import pymongo
import re
import pandas as pd

myclient = pymongo.MongoClient('mongodb://localhost:27017/')
mydb = myclient["test"]
mycol = mydb["news"]


driver = webdriver.Chrome()
df = pd.read_excel("pengbo.xlsx")
for row in df.values :
    if row[2] == 1:
        continue
    url = row[1]
    driver.get(url)
    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.CLASS_NAME, 'rich_media_content')))
    try:
        title = driver.find_element_by_id('activity-name')
        searchObj = re.search( r'彭博盘前简报：(.*)月(.*)日', title.text, re.M|re.I)
        if searchObj:
           date = '2020-'+searchObj.group(1).rjust(2,'0')+'-'+searchObj.group(2).rjust(2,'0')+' 08:00:00'
        else:
           exit("Nothing found!!")	
        list = driver.find_elements_by_css_selector('.list-paddingleft-2')
        for i in range(len(list)):
            childList = list[i].find_elements_by_tag_name('li')
            if(i in (0, 1)):
                type = 1
            else:
                type = 2
            for child in childList:
                myquery = {"content": child.text,"date":date}
                x = mycol.find_one(myquery)
                if(x == None):
                    mydict = { "type": type,"content": child.text, "date": date, "url": url,"ext": 0,"source": "pengbo" }
                    x = mycol.insert_one(mydict)
    except NoSuchElementException:
        print("Oops!  That was no valid number.  Try again   ")