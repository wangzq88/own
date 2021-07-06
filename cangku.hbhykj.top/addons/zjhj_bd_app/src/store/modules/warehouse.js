import Vue from 'vue';
import request from '../../core/request.js';
import Api from '../../core/appOnLaunch.js';

const state = {
    address_id: ''
};

const mutations = {
    addressId(state, data) {
        state.address_id = data;
    }	
};

const actions = {
    // 我的仓库，设置收货地址
    setAddress(context, data) {
        // uni.showLoading({
        //     title: '加载中',
        // });
        request({
           url:Api.order.submit,
		   data:{form_data:JSON.stringify(data.form_data)},
           method:'post',
        }).then(res => {
			uni.hideLoading();
            if (res.code === 0) {
				uni.showToast({
					title: '收货地址已提交成功，我们将尽快为你发货。实时物流可以在我的订单里查看。',
					duration: 3000,
					complete:function () {
						setTimeout(function(){
							uni.redirectTo({
								url: '/pages/cart/goods-express'
							}
						)},3000);						
					}
				});				
            } else {
				uni.showToast({
					title: res.msg,
					duration: 3000,
					complete:function () {
						setTimeout(function(){
							uni.redirectTo({
								url: '/pages/cart/goods-express'
							}
						)},3000);						
					}
				});					
			}
        }).catch(() => {
           uni.hideLoading();
        });
    }
};

export default {
    namespaced: true,
    state,
    mutations,
	actions
}