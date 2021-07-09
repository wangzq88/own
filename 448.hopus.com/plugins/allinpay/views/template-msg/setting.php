<?php
/**
 * link: http://www.zjhejiang.com/
 * copyright: Copyright (c) 2018 浙江禾匠信息科技有限公司
 * author: wxf
 */
?>
<div id="app">
    <app-template url="plugin/allinpay/template-msg/setting" submit-url='plugin/allinpay/template-msg/setting'
                  sign="allinpay"
                  add-url="plugin/allinpay/template-msg/add-template" :one-key="isShow">
        <template slot="after_remind">
            <br/>
            <div style="margin: -10px 20px 20px;background-color: #F4F4F5;padding: 10px 15px;color: #909399;display: inline-block;font-size: 15px">
                注:目前只实现在微信中的通联支付。
            </div>
        </template>
    </app-template>
</div>
<script>
    const app = new Vue({
        el: '#app',
        data() {
            return {
                isShow: false,
            };
        },
    });
</script>
