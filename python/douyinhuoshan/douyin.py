import json,re,time,os
from mitmproxy import ctx
from handler_mongo import save_live_room,save_user,get_live_room

def insertLogo(content):
    now = int(time.time())
    timeArray = time.localtime(now)   
    logName = time.strftime("%Y-%m-%d", timeArray)      
    path = 'logs/'
    if os.path.isdir(path) == False:
        os.makedirs(path,0o777)      
    with open(f"{path}{logName}_except.log", mode='a',encoding='utf8') as f:
        timeText = time.strftime("%Y-%m-%d %H:%M:%S", timeArray)      
        f.write('\n'+timeText+'\n'+content)

def insertBinLogo(content):
    now = int(time.time())
    timeArray = time.localtime(now)   
    logName = time.strftime("%Y-%m-%d-%H-%M-%S", timeArray)      
    path = 'logs/'
    if os.path.isdir(path) == False:
        os.makedirs(path,0o777)      
    with open(f"{path}{logName}.log", mode='wb') as f:      
        f.write(content.encode())

def response(flow):
    if '/webcast/feed/' in flow.request.url and flow.request.host == 'webcast.huoshan.com':
        insertBinLogo(flow.request.url+'\n'+flow.response.text)       
    if '/webcast/feed/' in flow.request.url and flow.request.host == 'hotsoon.snssdk.com':
        insertLogo(flow.request.url+'\n'+flow.response.text)   
        feed_set = set()
        feed_list = json.loads(flow.response.text)['data']
        for feed in feed_list:
            id_str = feed['data']['id_str']
            if id_str not in feed_set:
                feed_set.add(id_str)
                room = {'owner_user_id':feed['data']['owner_user_id'],'short_id':feed['data']['owner']['short_id'],'nickname':feed['data']['owner']['nickname']}
                save_live_room(room)   

    elif '/webcast/user/' in flow.request.url:
        #insertLogo(flow.request.url+'\n'+flow.response.text)  
        result = json.loads(flow.response.text)['data']
        if 'id' in result:
            user_info = {}
            user_info['id'] = result['id']
            user_info['city'] = result['city']
            user_info['follower_count'] = result['follow_info']['follower_count']
            user_info['club_name'] = result['fans_club']['data']['club_name']
            user_info['club_level'] = result['fans_club']['data']['level']
            save_user(user_info)
        if 'own_room' in result:
            params = flow.request.query
            room = {'owner_user_id':result['id'],'short_id':result['short_id'],'nickname':result['nickname']}
            insertLogo(flow.request.url)
            insertLogo(str(room))  
            save_live_room(room)                 

    searchObj = re.search(r'/webcast/ranklist/room/(\d+)/contributor/', flow.request.url)
    if searchObj:
        ranks = []
        response = json.loads(flow.response.text)
        if 'ranks' in response['data']:
            for rank in response['data']['ranks']:
                rank_info = {}
                rank_info['id'] = rank['user']['id']
                rank_info['short_id'] = rank['user']['short_id']
                rank_info['nickname'] = rank['user']['nickname']
                rank_info['gender'] = rank['user']['gender']
                rank_info['city'] = rank['user']['city']
                rank_info['grade'] = rank['user']['pay_grade']['name']
                rank_info['grade_describe'] = rank['user']['pay_grade']['grade_describe']
                rank_info['exactly_score'] = rank['exactly_score']
                rank_info['club_name'] = rank['user']['fans_club']['data']['club_name']
                rank_info['club_level'] = rank['user']['fans_club']['data']['level']
                rank_info['follower_count'] = rank['user']['follow_info']['follower_count']
                save_user(rank_info)
                obj = {'id':rank['user']['id'],'exactly_score':rank['exactly_score']}
                ranks.append(obj) 
            #rank_info = {'room_id':searchObj.group(1),'ranks':ranks}
            #save_live_room(rank_info) 
                          