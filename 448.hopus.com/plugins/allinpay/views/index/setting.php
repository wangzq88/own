<?php

/**
 * Created by IntelliJ IDEA.
 * User: luwei
 * Date: 2019/4/18
 * Time: 16:09
 */

/* @var $this \yii\web\View */
?>
<style>
    .key-textarea textarea{
        font-family: SFMono-Regular, Consolas !important;
    }

    .form-body {
        background-color: #fff;
        padding: 20px 30% 20px 20px;
    }

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
    }
</style>
<div id="app" v-cloak>
    <el-card style="border:0" shadow="never" body-style="background-color: #f3f3f3;padding: 10px 0 0;" v-loading="loading">
        <div slot="header">通联支付配置</div>
        <div class="form-body">
           <span>请在【设置】->【支付管理】->【支付方式】中查看详情</span>
		   <el-divider></el-divider>
		   <span>该通联支付的版本为 11 </span>
        </div>
    </el-card>
</div>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                loading: false,
                submitLoading: false,
            };
        },
        created() {
            this.loadData();
        },
        methods: {
          
        },
    });
</script>
