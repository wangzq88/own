<template>
	<app-layout>
		<view class="order-box">
			<view v-if="is_show" class='content-box'>
				<template v-if="orders.length">
					<view hover-class="u-hover-class" v-for='(item, index) in orders' :key='item.id' :style="{'margin-top': index == 0 ? '24rpx' : '0'}" class='order-item-box dir-top-wrap'>
                        <app-jump-button :url="getPageUrl(item.id,'warehouse')">
                            <view class="dir-top-nowrap" style="width: 100%">
                                <view class='dir-left-nowrap view-1'>
                                    <view class='box-grow-1'>订单号：{{item.order_no}}</view>
                                    <template>
                                        <view v-if="item.detailExpress.length != 0">部分发货</view>
										<view v-else>未发货</view>
                                    </template>
                                </view>
                                <view v-for='(dItem, dIndex) in item.detail' :key='dIndex' class='view-2'>
                                    <app-order-goods-info :goods='dItem.goods_info' :plugin-data="item.plugin_data" :plugin-index="dIndex"></app-order-goods-info>
                                </view>
                            </view>
                        </app-jump-button>
                        <view class='dir-top-nowrap view-3'>
                            <template>
                                <view class='box-grow-1 main-right cross-center price-count-box'>
                                    <text class="price-label">合计 </text>
                                    <span class='price-color'>
                                        <text v-if="item.plugin_data.exchange_count">{{item.plugin_data.exchange_count}}{{item.plugin_data.price_name}}+</text>
                                        <text>￥{{item.total_pay_price}}</text>
                                    </span>
                                </view>
                            </template>
						</view>										
					</view>
				</template>
                <view class="no-list" v-else>
                    <app-no-goods background="#f7f7f7" :title="search.keyword || search.dateArr[0] || search.dateArr[1] ? '暂无您搜索的订单':'暂无相关订单'" color="#999999" :is_image="1"></app-no-goods>
                </view>					
			</view>	
			<app-load-text v-if="is_load_show"></app-load-text>
		</view>	
	</app-layout>		
</template>

<script>
import appTabNav from '../../components/basic-component/app-tab-nav/app-tab-nav.vue';
import appNoGoods from '../../components/page-component/app-no-goods/app-no-goods.vue';
import appRelatedSuggestionProduct from '../../components/page-component/app-related-suggestion-product/app-related-suggestion-product.vue';
import appOrderGoodsInfo from '../../components/page-component/app-order-goods-info/app-order-goods-info.vue';
// #ifdef H5
import bdH5Back from "@/components/page-component/goods/bd-h5-back.vue";
// #endif
import { mapGetters } from 'vuex';
	
export default {
    data() {
        return {
            list: [{ name: '全部', id: 0 }, { name: '待付款', id: 1 }, { name: '待发货', id: 2 }, { name: '待收货', id: 3 }, { name: '已完成', id: 4 }],
            page: 1,
            currentIndex: 10,
            orders: [],
            search: {
                keyword: '',
                dateArr: []
            },
            pagination: null,
            qr_code_url: '',
            is_qrcode: false,
            is_show: false,
            is_load_show: false,
            bgColor: '#f7f7f7',
            isRequest: true, //防止数据重复加载
            template_message: [],
            recommend_list: []
        }
    },
    methods: {
        getList() {
            this.isRequest = false;
            this.$request({
                url: this.$api.order.list,
                data: {
                    status: this.currentIndex,
                    keyword: this.search ? this.search.keyword : '',
                    dateArr: this.search ? JSON.stringify(this.search.dateArr) : JSON.stringify([]),
                    page: this.page,
                }
            }).then(response => {
                let { code, data, msg } = response;
                this.$hideLoading();
                this.is_load_show = false;
                this.is_show = true;
                if (code === 0) {
                    let { list, pagination } = data;
                    if (this.page !== 1) {
                        this.orders = this.orders.concat(list);
                    } else {
                        this.orders = list;
                    }
                    this.page = list.length ? this.page + 1 : this.page;
                    this.pagination = pagination;
                    this.template_message = data.template_message;
                } else {
                    uni.showModal({
                        title: '',
                        content: msg,
                        showCancel: false,
                    });
                }
                this.isRequest = true;
            }).catch(() => {
                this.is_load_show = false;
                this.$hideLoading();
            });
        },
        loadRecommend() {
            this.$request({
                url: this.$api.goods.new_recommend,
                data: {
                    type: 'order_pay'
                }
            }).then(response => {
                if (response.code === 0) {
                    this.recommend_list = response.data.list;
                }
            });
        },
        getPageUrl: function(id,sign) {
            return `/pages/cart/goods-express?id=${id}`
			//return `/pages/order-submit/address-pick?id=${id}&sign=${sign}`
        }
    },
    onLoad(options) { this.$commonLoad.onload(options);
        this.loadRecommend();
        this.$storage.removeStorage({key: 'search'});
    },

    onReachBottom() {
        this.is_load_show = true;
        this.getList();
    },
    onShow() {
        let self = this;
        setTimeout(function() {
            self.$storage.getStorage({
                key: 'search',
                success(res) {
                    self.search = res.data;
                    let interval = setInterval(function() {
                        if (self.isRequest) {
                            self.page = 1;
                            self.$showLoading();
                            self.getList();
                            self.$store.dispatch('gConfig/setTabBarBoolean', self.tabBarNavs.navs);
                        }
                        clearInterval(interval)
                    }, 300);
                },
                fail() {
                    let interval = setInterval(function() {
                        if (self.isRequest) {
                            self.page = 1;
                            self.$showLoading();
                            self.getList();
                            self.$store.dispatch('gConfig/setTabBarBoolean', self.tabBarNavs.navs);
                        }
                        clearInterval(interval)
                    }, 300);
                }
            });
        }, 0)
    },
    computed: {
        ...mapGetters('mallConfig', {
            tabBarNavs: 'getNavBar',
            getTheme: 'getTheme',
        })
    },
    components: {
        // #ifdef H5
        bdH5Back,
        // #endif
        'app-tab-nav': appTabNav,
        'app-order-goods-info': appOrderGoodsInfo,
        'app-related-suggestion-product': appRelatedSuggestionProduct,
        'app-no-goods': appNoGoods,
    },
}	
</script>

<style scoped lang="scss">
.search-area {
    position: fixed;
    z-index: 3;
    top: 0;
    left: 0;
    right: 0;
    height: #{88rpx};
    line-height: #{88rpx};
    width: 100%;
    background-color: #efeff4;
    padding: #{12rpx} #{24rpx};
    // #ifdef H5
    padding-left: #{100rpx};
    // #endif
    .search {
        height: #{64rpx};
        line-height: #{64rpx};
        border-radius: #{32rpx};
        background-color: #fff;
        color: #b2b2b2;
        font-size:#{26rpx};

        &.be-search {
            color: #353535;
            padding-left: 32rpx;

            .icon-search {
                margin-right: 10rpx;
            }
        }

        .icon-search {
            height: #{24rpx};
            width: #{24rpx};
            margin-top: #{20rpx};

            &+text {
                color: #b2b2b2;
                margin:0 #{8rpx};
            }
        }
    }
}

.search-placeholder {
    width: 100%;
    height: #{88rpx};
}

.order-box {
    width: 100%;
    height: 100%;
}

.title-box {
    height: 80#{rpx};
    width: 100%;
    position: fixed;
    border-bottom: 1#{rpx} solid $uni-weak-color-one;
    top: 0;
    background: #fff;
    z-index: 1;
}

.no-list {
    margin-top: #{120upx};
}

.not-order-box {
    height: calc(100vh - 80#{rpx});
    color: $uni-general-color-two;
}

.order-item-box {
    background: #fff;
    padding: 0 24#{rpx};
    margin: 0 24#{rpx};
    margin-bottom: 24#{rpx};
    border-radius: 16#{rpx};
    font-size: $uni-font-size-general-two;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.06);
}

.order-item-box .view-1 {
    width: 100%;
    font-size: $uni-font-size-weak-one;
    color: $uni-important-color-black;
    margin: 32#{rpx} 0;
}

.order-item-box .view-2 {}

.order-item-box .view-3 {
    font-size: $uni-font-size-import-two;
}

.order-item-box .view-3 .btn {
    margin-left: 15#{rpx};
}

.price-count-box {
    margin: 28#{rpx} 0;
}

.price-label {
    margin-right: 5#{rpx};
    font-size: $uni-font-size-weak-one;
    color: $uni-general-color-two;
}

.price-color {
    color: $uni-important-color-black;
}

.price-express {
    font-size: $uni-font-size-weak-one;
    color: $uni-general-color-two;
}

.success-color {
    color: $uni-important-color-black;
}

.error-color {
    color: $uni-important-color-red;
}

.qrcode-box {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.5);
}

.qrcode-box .item {
    position: absolute;
    top: 25%;
    width: 562#{rpx};
    height: 562#{rpx};
    background: #fff;
    left: 94#{rpx};
    z-index: 999;
    border-radius: 15#{rpx};
}

.qrcode-box .img {
    margin-top: 30#{rpx};
    width: 375#{rpx};
    height: 375#{rpx};
}

.qrcode-box .item .remove {
    position: absolute;
    top: 0;
    right: 0;
    width: 50#{rpx};
    height: 50#{rpx};
    margin: 15#{rpx};
    padding: #{7rpx}
}

.order-btn {
    display: inline-block;
    line-height: 2.3;
    font-size: 26#{rpx};
    padding: 0 30#{rpx};
    border: 1#{rpx} solid $uni-weak-color-one;
    border-radius: 30#{rpx};
    margin-left: 16#{rpx};
}

.action-button-box {
    margin-bottom: 24#{rpx};
}


.action-box-view {
    width: 100%;
}
</style>
