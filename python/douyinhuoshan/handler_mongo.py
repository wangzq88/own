import pymongo,configparser,sys
from mitmproxy import ctx

config = configparser.ConfigParser()
# 读取配置文件
config.read('config.ini', encoding='utf-8')

client = pymongo.MongoClient(host=config.get('mongodb', 'ip'),port=int(config.get('mongodb', 'port')))
db = client['douyin']

def save_live_room(task):
    db['live_room'].update({'owner_user_id':task['owner_user_id']},{"$set": task },True)

def get_live_room(room_id):
    return db['live_room'].find_one({},{ "room_id": room_id})

def save_user(task):
    db['user'].update({'id':task['id']},{"$set": task },True)    

def get_all_live_room():
    return db['live_room'].find()

def get_user(id_list):
    return db['user'].find({"id":{"$in":id_list}})    
    
