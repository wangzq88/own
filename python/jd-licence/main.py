#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from splider import Splider,insertLogo
import tkinter,threading,configparser,sqlite3,time,traceback,csv,os
from tkinter import *
from tkinter import messagebox
from tkinter import ttk

class SpliderThread (threading.Thread):
    
    stop = False
    done = False
    index = 0

    def __init__(self,config):
        threading.Thread.__init__(self)
        self.__config = config  

    def run(self):
        spd = Splider()
        conn = sqlite3.connect(self.__config.get('sqlite', 'db'))
        lastIndex = 0
        try:
            while not self.stop:
                print('上次抓取ID',lastIndex)
                if self.index > lastIndex:
                    print('抓取ID',self.index)
                    result = spd.showLicence(f'https://mall.jd.com/showLicence-{self.index}.html')
                    if len(result) == 0:
                        result['shop'] = ''
                        result['company'] = ''
                    result['shop_id'] = self.index
                    now = int(time.time())
                    #转换为其他日期格式,如:"%Y-%m-%d %H:%M:%S"
                    timeArray = time.localtime(now)
                    result['time'] = time.strftime("%Y-%m-%d %H:%M:%S", timeArray)          
 
                    threadLock.acquire()    
                    try:
                        self.__insertSql(conn,result)
                    except Exception as e:
                        traceback.print_exc()  
                        insertLogo(traceback.format_exc()) 
                    cursor = conn.execute("SELECT * from shop where shop_id=%d limit 1" % self.index)
                    data = cursor.fetchone()                   
                    # 获取锁，用于线程同步
                    threadLock.release()                  
                    self.done = True 
                    lastIndex = self.index         
                    if data != None:
                        table.insert('', END, values=data)     
                    print('上次抓取ID2',lastIndex)       
                time.sleep(0.8)    
        except Exception as e:
            traceback.print_exc()  
            insertLogo(traceback.format_exc())              
        finally:
            spd.quit() 
            conn.close()  

    def __insertSql(self,conn,result):
        sql = "INSERT INTO shop (shop_id,shop_name,shop_company,`time`) VALUES "
        subSQL = []            
        cursor = conn.execute("SELECT * from shop where shop_id=%d limit 1" % result['shop_id'])
        if cursor.fetchone() != None:
            return
        subSQL.append("(%d,'%s','%s','%s')" % \
            (int(result['shop_id']),result['shop'],result['company'],result['time']))
        sql = sql + ",".join(subSQL)    
        conn.execute(sql)
        conn.commit()      

class ThreadPool (threading.Thread):

    stop = False
    def __init__(self,config):
        threading.Thread.__init__(self)
        self.__config = config

    def run(self):    
        global threads
        startID = input1.get()
        endID = input2.get()        
        thrNum = int(cmb.get())
        if startID.strip() == '' or endID.strip() == '':
            messagebox.showinfo('提示','请输入起始ID和终止ID') 
            return    
        if int(startID) >= int(endID):
            messagebox.showinfo('提示', '起始ID不能大于终止ID')
            return    
        if not startID.isdigit() or not endID.isdigit():
            messagebox.showinfo('提示','起始ID和终止ID只能是数字') 
            return    
        if firmBtn['state'] != 'disabled':      
            firmBtn['text'] = '终止'
            firmBtn['state'] = 'normal'   
        if refBtn['state'] != 'disabled':      
            refBtn['text'] = '终止'
            refBtn['state'] = 'normal'                     
        obj = table.get_children()  # 获取所有对象
        for o in obj:
            table.delete(o)  # 删除对象            
        conn = sqlite3.connect(self.__config.get('sqlite', 'db'))            
        index = int(startID)                      
    
        while index >= int(startID) and index <= int(endID):
            cursor = conn.execute("SELECT * from shop where shop_id=%d limit 1" % index)
            data = cursor.fetchone()
            if data != None:
                table.insert('', END, values=data)  
                index += 1
                continue     
            print('准备抓取ID',index)   
            goon = True
            #分配线程
            while goon:
                if self.stop:
                    return
                for i in range(thrNum):                  
                    try:
                        print('当前线程',i)     
                        if threads[i] != None:
                            if not threads[i].is_alive():
                                print('线程已经死亡，清除线程',i)  
                                del threads[i]     
                                raise IndexError                            
                            if threads[i].done:
                                print('线程空闲，运用这个线程',i)   
                                threads[i].done = False
                                threads[i].index = index
                                goon = False
                                break                                                                                        
                    except IndexError:                
                        threadLock.acquire()  
                        print('创建线程---------------') 
                        tmpThread = SpliderThread(config)
                        tmpThread.index = index
                        tmpThread.start()      
                        threads.append(tmpThread)  
                        threadLock.release()   
                        goon = False
                        break                            
                    time.sleep(0.8)       
            index += 1
        if index > int(endID): 
            firmBtn['text'] = '启动' 
            firmBtn['state'] = 'normal'
            refBtn['text'] = '重新抓取' 
            refBtn['state'] = 'normal'                              
            messagebox.showinfo('提示','抓取完毕')      
        conn.close()    

def canuse():
    now = int(time.time())
    return 1631507720 + 259200 > now

def refesh():
    global pThread,threads
    if not canuse():
        return
    if refBtn['text'] == '重新抓取':
        startID = input1.get()
        endID = input2.get()         
        if startID.strip() == '' or endID.strip() == '':
            messagebox.showinfo('提示','请输入起始ID和终止ID') 
            return    
        if int(startID) >= int(endID):
            messagebox.showinfo('提示', '起始ID不能大于终止ID')
            return    
        if not startID.isdigit() or not endID.isdigit():
            messagebox.showinfo('提示','起始ID和终止ID只能是数字') 
            return             
        firmBtn['state'] = 'disabled'    
        sql = "DELETE FROM shop WHERE shop_id >= %d and shop_id <= %d and shop_name=''" % (int(startID),int(endID))
        conn.execute(sql)
        conn.commit()                  
        pThread = ThreadPool(config)
        pThread.start()          
    else:
        refBtn['text'] = '终止中…'
        refBtn['state'] = 'disabled'        
        pThread.stop = True 
        threadLock.acquire()
        for thr in threads:
            thr.stop = True       
        threads.clear() 
        threadLock.release()  
        refBtn['text'] = '重新抓取' 
        refBtn['state'] = 'normal'  
        firmBtn['state'] = 'normal'            

def go():
    global pThread,threads
    if not canuse():
        return    
    if firmBtn['text'] == '启动':
        refBtn['state'] = 'disabled'
        pThread = ThreadPool(config)
        pThread.start()  
    else:
        firmBtn['text'] = '终止中…'
        firmBtn['state'] = 'disabled'
        pThread.stop = True 
        threadLock.acquire()
        for thr in threads:
            thr.stop = True       
        threads.clear() 
        threadLock.release()  
        firmBtn['text'] = '启动' 
        firmBtn['state'] = 'normal'
        refBtn['state'] = 'normal'            

def export():
    if not canuse():
        return    
    exportBtn['text'] = '导出中…'
    exportBtn['state'] = 'disabled'    
    cursor = conn.execute("SELECT shop_id,shop_name,shop_company from shop where shop_name != '' order by shop_id")
    resultList = cursor.fetchall()  
    csvfile = open('shop.csv', 'w')  #打开方式还可以使用file对象
    writer = csv.writer(csvfile)
    writer.writerow(['店铺ID', '店铺名称', '企业名称'])
    writer.writerows(resultList)
    csvfile.close()  
    exportBtn['text'] = '导出'
    exportBtn['state'] = 'normal' 
    messagebox.showinfo('提示','已成功导出，请在该目录下查看 shop.csv 文件')      

def on_closing():
    global threads
    if messagebox.askokcancel("退出","确定要退出应用程序吗?"):
        conn.close()     
        win.destroy()        
        for thr in threads:
            thr.stop = True
            thr.join()        
        #os.system("taskkill /F /IM java.exe")            

if __name__ == '__main__':

    threadLock = threading.Lock()
    pThread = None
    threads = []
    config = configparser.ConfigParser()
    # 读取配置文件
    config.read('config.ini', encoding='utf-8')     
    conn = sqlite3.connect(config.get('sqlite', 'db'))
    cur = conn.cursor()
    create_tb_cmd = '''
        CREATE TABLE IF NOT EXISTS shop(
        id INTEGER PRIMARY KEY AUTOINCREMENT,    
        shop_id INTEGER UNSIGNED NOT NULL UNIQUE,
        shop_name NVARCHAR(50) NOT NULL,
        shop_company NVARCHAR(50) NOT NULL,
        time timestamp NOT NULL);
    '''
    cur.execute(create_tb_cmd)
    conn.commit()  

    win = Tk()  # 窗口
    win.title('京东店铺抓取')  # 标题
    win.resizable(0, 0) 
    screenwidth = win.winfo_screenwidth()  # 屏幕宽度
    screenheight = win.winfo_screenheight()  # 屏幕高度
    width = 700
    height = 500
    x = int((screenwidth - width) / 2)
    y = int((screenheight - height) / 2)
    win.geometry('{}x{}+{}+{}'.format(width, height, x, y))  # 大小以及位置    
    
    formFrame = Frame(win)
    formFrame.pack()
    label= Label(formFrame,text="店铺起始ID",padx=10,pady=20)
    label.pack(side=LEFT)
    input1 = Entry(formFrame)
    input1.pack(side=LEFT)
    label= Label(formFrame,text="店铺终止ID",padx=10,pady=20)
    label.pack(side=LEFT)    
    input2 = Entry(formFrame)
    input2.pack(side=LEFT)    
    label= Label(formFrame,text="线程数目",padx=10,pady=20)
    label.pack(side=LEFT)
    cmb = ttk.Combobox(formFrame,width=5,value=(1,2,3,4,5,6,7,8,9,10))
    cmb.pack(side=LEFT)      
    cmb.current(1)    

    tabel_frame = tkinter.Frame(win)
    tabel_frame.pack()

    yscroll = Scrollbar(tabel_frame, orient=VERTICAL)

    columns = ['ID','店铺ID', '店铺名称', '企业名称']
    table = ttk.Treeview(
            master=tabel_frame,  # 父容器
            height=15,  # 表格显示的行数,height行
            columns=columns,  # 显示的列
            show='headings',  # 隐藏首列
            yscrollcommand=yscroll.set,  # y轴滚动条
            )
    for key,column in enumerate(columns):
        table.heading(column=column, text=column, anchor=CENTER,
                      command=lambda name=column:
                      print(''))  # 定义表头
        width = 200 if key >= 2 else 100               
        table.column(column=column, width=width, minwidth=100, anchor=CENTER, )  # 定义列
    yscroll.config(command=table.yview)
    yscroll.pack(side=RIGHT, fill=Y)
    table.pack(fill=BOTH, expand=True)

    btn_frame = Frame(win)
    btn_frame.pack()
    exportBtn = Button(btn_frame, text='导出', padx=10,  command=export)
    exportBtn.pack(side=LEFT,pady=20,padx=20)    
    firmBtn = Button(btn_frame, text='启动', padx=10, command=go)
    firmBtn.pack(side=LEFT,pady=20,padx=20)
    refBtn = Button(btn_frame, text='重新抓取', padx=10, command=refesh)
    refBtn.pack(side=LEFT,pady=20,padx=20)    
    win.protocol("WM_DELETE_WINDOW", on_closing)    
    win.mainloop() 