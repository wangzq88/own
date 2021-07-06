<template>
	<app-layout :overflow='false'>
		<view class="app-cart">
            <template v-if="listObj.length > 0">
                
				<app-warehouse-product :theme="getTheme" @change="change" :mch="listObj"  :editStatus="editStatus" @changeSingleRadio="changeSingleRadio" @changeRadioAll="changeRadioAll" ></app-warehouse-product>
			    
            </template>
			<view class="no-cart" v-if="listObj.length === 0">
				<view class="cart-icon">
					<image class="cart-image" src="../../static/image/icon/nav-icon-cart.png"></image>
				</view>
				<view class="cart-text">仓库没有商品~~~</view>
			</view>
			<view>
				<app-empty-bottom backgroundColor="#f7f7f7" :botBool="botBool" :height="Number(100)"></app-empty-bottom>
			</view>
			<view v-if="!tabbarbool" :style="{background: 'white', position: 'fixed', bottom: 0, height: `${getEmpty}rpx`, width: '750rpx'}"></view>
            <view class="safe-area-inset-bottom bd-fixed-bottom ">
                <view class="app-settlement dir-left-nowrap main-between cross-center">
                    <view class="app-radio dir-left-nowrap main-right cross-center">
                        <app-radio type="round" :theme="getTheme" @click="setALl" v-model="all"></app-radio>
                        <text class="app-all-text">全选</text>
                    </view>
					<view class="send-count t-omit">共{{allNum}}件，合计<text :style="{'color': getTheme.color}">￥{{allPrice.toFixed(2)}}</text></view>
                    <button :disabled="submitDis" :style="{'background-color' : !submitDis ? getTheme.background : '#989898'}" :class="submitDis ? 'delete-disabled' : ''" class="app-settlement-button" @click="settlement">
                        确认提货
                    </button>
                </view>
                <view v-if="tabbarbool" class="bd-bottom-height-0"></view>
            </view>
		</view>
	</app-layout>
</template>

<script>
	import { mapState, mapGetters } from 'vuex';
    import appWarehouseProduct from './components/app-warehouse-product/app-warehouse-product.vue';
    import appRadio from '../../components/basic-component/app-radio/app-radio.vue';
    import appEmptyBottom from '../../components/basic-component/app-empty-bottom/app-empty-bottom.vue';
    
    export default {
        name: 'goods-express',
        components:{
            'app-warehouse-product': appWarehouseProduct,
	        'app-radio': appRadio,
	        'app-empty-bottom': appEmptyBottom,
        },
        data() {
            return {
                editStatus: false,
	            listObj: [],
	            all: false,
                editList: [],
	            edit: false,
                botBool: true,
                // #ifdef MP
                currentRoute: this.$platDiff.route(),
                // #endif
                tabbarbool: false,
                submitDis: false,
				f:0,
				allNum:0,
				allPrice:0
            }
        },
        computed:{
            ...mapState({
                tabBarHeight: (state) => {
                  return state.gConfig.tabBarHeight;
                },
                iphoneHeight: (state) =>{
	                return  state.gConfig.iphoneHeight;
                },
	            iphone: (state) => {
                    return state.gConfig.iphone
	            }
            }),
            ...mapGetters('iPhoneX', {
                BotHeight: 'getBotHeight',
                getEmpty: 'getEmpty',
			}),
			...mapGetters('mallConfig', {
				getTheme: 'getTheme',
			}),
            ...mapState({
                tabBarNavs: state => state.mallConfig.navbar.navs,
				is_edit: state => state.cart.is_edit
            }),
        },
        methods: {
            setALl(data) {
                this.selectAll(data.active);
            },
	        getProductList() {
                uni.showLoading({
	                title: '加载中'
                });			
				const self = this;
				self.$request({
					url: self.$api.warehouse_goods.list
				}).then(response => {
					uni.hideLoading();
					if (response.code == 0) {
						self.listObj = response.list;
						for (let i = 0; i < self.listObj.length; i++) {
							self.listObj[i].is_active = true;
							self.listObj[i].snum = 1;
							self.listObj[i].editStatus = true;		
							self.listObj[i].stock = self.listObj[i].num;	
							self.listObj[i].goods_info = JSON.parse(self.listObj[i].goods_info);
							if (self.listObj[i].num <= 0) {
								self.listObj[i].editStatus = false;	
								self.listObj[i].snum = 0;
								self.listObj[i].num = 0;
								self.listObj[i].is_active = false;
								self.listObj[i].stock = 0;								
							}					
							self.allNum += self.listObj[i].num;
							self.allPrice += self.listObj[i].num * self.listObj[i].goods_info.goods_attr.price;
						}	
					} else {
						uni.showModal({
							title: '',
							content: response.msg,
							showCancel: false,
						});
					}
				}).catch(() => {
					uni.hideLoading();
				});					
	        },
	        
	        // 商城全选
            changeRadioAll(data) {
				let objDetail = this.listObj.detail;
                for (let i = 0; i < objDetail.length; i++) {
					if (!objDetail[i].editStatus) {
						objDetail[i].is_active = false;
					} else {
						objDetail[i].is_active = !objDetail[i].is_active;
					}
                }
            },
	        
	        // 单选
            changeSingleRadio(obj) {
				this.listObj.forEach((item,index,arr) => {
					if(item.goods_id == obj.goods_id) {
						this.$set(this.listObj[index],'is_active',!item.is_active);
						this.$forceUpdate();
					}
				});
            },
	        
            selectAll(bool) {
                this.listObj.forEach((item,index,arr) => {
					if (!item.editStatus) {
						item.is_active = false;
					} else {
						this.all = bool;
						item.is_active = bool;
					}
                });
            },
	        // 结算
            settlement() {
				let pass = false;
				let tip = '请选择要寄送的商品';
				let goods_attr_id = 0;
				let attrs = [];
				const goods_list = [];
				const detailList = this.listObj;
				let form_data = null;

                for (let i = 0; i < detailList.length; i++) {
					if(detailList[i].is_active)
					{
						attrs = [];
						if(detailList[i].snum > 0) {
							pass = true;
							goods_attr_id = detailList[i].goods_info.goods_attr.id
							detailList[i].goods_info.attr_list.forEach(item => {
								attrs.push({attr_id:item.attr_id,attr_group_id:item.attr_group_id});
							});
							goods_list.push({id:detailList[i].goods_id,attrs:attrs,goods_attr_id:goods_attr_id,num:detailList[i].snum,cat_id:0,cart_id:0});							
							
						} else {
							pass = false;
							tip = '寄送的商品数量不能为 0 ';
							break;
						}
						form_data = [{mch_id:0,goods_list:goods_list,distance:0,remark:'',order_form:[],use_integral:0,user_coupon_id:0}];
					}
                }

                if(pass) {
					form_data = JSON.stringify(form_data);
                    //let jump_url = `/pages/order-submit/address-pick?sign=warehouse&form_data=${form_data}`;
					let jump_url = `/pages/order-submit/order-submit?sign=warehouse&mch_list=${form_data}`;
                    this.$jump({
                        open_type: 'navigate',
                        url: jump_url
                    });
                }else {
					uni.showModal({
						title: '',
						content: tip,
						showCancel: false
					});                    
                }
            },
	        
            b() {
                let currentRoute = undefined;
                // #ifdef MP
                currentRoute = this.currentRoute;
                // #endif
                // #ifdef H5
                currentRoute = window.location.hash.split('#')[1].split('?')[0];
                // #endif
                for (let i = 0; i < this.tabBarNavs.length; i++) {
                    if(currentRoute.includes(this.tabBarNavs[i].url.split('?')[0])) {
                        return this.tabbarbool = true;
                    }
                }
                return  this.tabbarbool = false;
            },

            change({number, id}) {				
                for (let i = 0; i < this.listObj.length; i++) {
                    if (this.listObj[i].goods_id === id) {
						this.listObj[i].snum = number;
                    }
                }
            }
        },
		onLoad(options) { 
			this.order_id = options.id;
		},
	    onShow() {
            this.submitDis = false;
			this.listObj = [];
			setTimeout(() => {
				this.getProductList();
			}, 1000);
            this.all = false;
        },
	    watch:{
            tabBarNavs: {
                handler: function() {
                    this.b();
                },
                immediate: true,
            }
	    }
    }
</script>

<style lang="scss" scoped>
	.app-cart {
		background-color: #f7f7f7;
		position: absolute;
		top: 0;
		left: 0;
		width: #{750rpx};
		.app-announcement {
			width: 100%;
			height: #{72rpx};
			background-color: #ffffff;
			.app-announcement-text {
				font-size: #{26rpx};
				color: #999999;
				margin-left: #{24rpx};
			}
			.app-edit-text {
				font-size: #{26rpx};
				color: #353535;
				margin-right: #{32rpx};
			}
		}
		.app-settlement {
			width: 100%;
			height: #{110rpx};
			border-top: #{1rpx} solid #e2e2e2;
			background-color: white;
            padding: 15upx 24upx;
			.app-radio {
				padding-left: #{23rpx};
				.app-price {
					margin-left: #{24rpx};
					font-size: #{28rpx};
				}
			}
			.app-all-text {
				margin-left: #{9rpx};
				font-size: #{25rpx};
				color: #3f3f3f;
			}
			.app-delete {
				width: #{140rpx};
				height: #{64rpx};
				line-height: #{64rpx};
				text-align: center;
				background-color: white;
				border-radius: #{32rpx};
				border: #{1rpx} solid ;
				font-size: #{28rpx};
				margin: #{0 24rpx 0 0};
				padding: 0;
			}
			.delete-disabled {
				color: #989898;
				border: #{1rpx} solid #989898;
			}
			.app-settlement-button {
				height: #{82rpx};
				width: #{250rpx};
				color: #ffffff;
				font-size: #{30rpx};
				line-height: #{82rpx};
				text-align: center;
				margin: 0;
				padding: 0;
                border-radius: 41upx;
				border: none;
			}
		}
		.no-cart {
			width: 100%;
			.cart-icon {
				width: #{160rpx};
				height: #{160rpx};
				border-radius: 50%;
				background-color: rgba(0, 0, 0, 0.1);
				margin: #{150rpx auto 40rpx};
				.cart-image {
					height: #{80rpx};
					width: #{80rpx};
					margin: #{40rpx};
				}
			}
			.cart-text {
				font-size: #{30rpx};
				color: #888;
				text-align: center;
				
			}
		}
	}
    .send-dialog {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,.3);
        z-index: 1700;
        .send-content {
            position: absolute;
            bottom: 0;
            left: 0;
            background-color: #fff;
            border-top-left-radius: 16rpx;
            border-top-right-radius: 16rpx;
            padding: 24rpx;
            .send-close {
                position: absolute;
                top: 24rpx;
                right: 24rpx;
                width: 30rpx;
                height: 30rpx;
            }
            .send-title {
                font-size: 32rpx;
                margin: 16rpx 0 36rpx;
                text-align: center;
                color: #000000;
            }
            .send-tip {
                font-size: 24rpx;
                color: #999999;
            }
            .send-type {
                margin-top: 32rpx;
                .send-type-name {
                    height: 42rpx;
                    font-size: 28rpx;
                    color: #353535;
                    image {
                        width: 42rpx;
                        height: 42rpx;
                        margin-right: 18rpx;
                    }
                }
                .send-type-item {
                    margin-top: 15rpx;
                    height: 226rpx;
                    border-radius: 16rpx;
                    background-color: #f2f2f2;
                    padding: 20rpx;
                    padding-left: 0;
                    padding-bottom: 0;
                    width: 702rpx;
                    position: relative;
                    .send-goods {
                        white-space: nowrap;
                        .send-goods-list {
                            white-space: nowrap;
                            margin-left: 0;
                        }
                        view {
                            height: 110rpx;
                            margin-left: 20rpx;
                            display: inline-block;
                            image {
                                width: 110rpx;
                                height: 110rpx;
                                border-radius: 16rpx;
                            }
                        }
                    }
                    .send-count {
                        height: 96rpx;
                        line-height: 96rpx;
                        width: 500rpx;
                        font-size: 22rpx;
                        color: #353535;
                        position: absolute;
                        bottom: 0;
                        left: 30rpx;
                        text {
                            color: #ff4544;
                        }
                    }
                    .send-count-btn {
                        position: absolute;
                        bottom: 22rpx;
                        right: 28rpx;
                        height: 54rpx;
                        border-radius: 27rpx;
                        width: 128rpx;
                        text-align: center;
                        line-height: 54rpx;
                        background-color: #ff4544;
                        color: #fff;
                        font-size: 28rpx;
                    }
                }
            }
        }
    }
    .bd-fixed-bottom {
        width: 100%;
        z-index: 1500;
        position: fixed;
        left: 0;
        bottom: 0;
    }
    .bd-bottom-height-0 {
        width:100%;
        height: 110upx;
    }
</style>
