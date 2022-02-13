import csv,configparser
from handler_mongo import get_all_live_room,get_user


config = configparser.ConfigParser()
# 读取配置文件
config.read('config.ini', encoding='utf-8')
gender_list = ['未知','男','女']
all_rooms = get_all_live_room()
for room in all_rooms:
    if 'ranks' not in room:
        continue
    id_list = [rank['id'] for rank in room['ranks']]
    if 'owner_user_id' not in room: 
        room['owner_user_id'] = ''

    user_list = get_user(id_list)
    with open("csv/{0}.csv".format(room['room_id']), mode='w',encoding='utf8') as csvfile:
        writer = csv.writer(csvfile)
        writer.writerow(['主播ID','火山号', '昵称', '性别', '城市', '等级', '历史总音浪', '当日音浪','粉丝团','粉丝团等级','粉丝数'])   
        for user in user_list:
            #print(user)  
            exactly_score = 0
            for rank in room['ranks']:
                if rank['id'] == user['id']:
                    exactly_score = rank['exactly_score']
                    break
            if 'city' not in user: 
                user['city'] = ''
            if 'club_name' not in user: 
                user['club_name'] = ''    
            if 'club_level' not in user: 
                user['club_level'] = '' 
            if 'follower_count' not in user: 
                user['follower_count'] = ''                                   
            writer.writerow([room['owner_user_id'],user['short_id'],user['nickname'],gender_list[user['gender']],user['city'],user['grade'],user['grade_describe'],exactly_score,user['club_name'],user['club_level'],user['follower_count']])     
