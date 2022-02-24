from splider import Splider
import configparser,pandas,csv,time,os

if __name__ == '__main__':
    config = configparser.ConfigParser()
    # 读取配置文件
    config.read('config.ini', encoding='utf-8')
    ftime = time.strftime("%Y-%m-%d", time.localtime()) 
    path = 'csv/{0}'.format(ftime)
    if not os.path.exists(path):
        os.makedirs(path)    
    spd = Splider()
    '''
    filename = 'brand-{0}.csv'.format(ftime)
    with open(filename, 'a', newline='') as csvfile:
        writer = csv.writer(csvfile)
        for item in brandList:
            writer.writerow((item['title'],item['href'],0))       
    df = pandas.read_csv(filename)        
    for i,row in enumerate(df.itertuples(name=None)):
        print(row)    
    '''      
    timeText = time.strftime("%H-%M-%S", time.localtime())    
    brand = spd.getBrandGoods()
    #判断文件是否已经存在，必须在文件创建前判断，不能移动位置
    f_exist = os.path.exists(f'{path}/jdsz.csv')
    with open(f'{path}/jdsz.csv', 'a', newline='',encoding='utf-8') as csvfile:
        writer = csv.writer(csvfile)   
        #第一次不存在，写入标题
        if not f_exist:
            writer.writerow(('品牌','序号','名称','成交金额指数','成交单量指数','关注人数','访客指数','链接')) 
        j = 1
        while True:
            try:
                i = 1
                iter = next(brand)
                print(iter)    
                goodsList = iter['list']
                for goods in goodsList:
                    writer.writerow((iter['title'],i,goods['title'],goods['deal_amount'],goods['deal_number'],goods['follow_number'],goods['visitor_number'],goods['href']))   
                    i += 1
                with open(path + '/{0}-{1}.txt'.format(j,iter['title']), 'w') as backup:
                    backup.write(iter['title'])     
                j += 1      
            except StopIteration:
                print(f'抓取完毕,请前往 {path}/jdsz.csv 文件查看')
                break                                