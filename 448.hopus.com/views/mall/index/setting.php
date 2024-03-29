<?php
/**
 * @copyright ©2018 浙江禾匠信息科技
 * @author Lu Wei
 * @link http://www.zjhejiang.com/
 * Created by IntelliJ IDEA
 * Date Time: 2018/11/8 18:12
 */
?>
<style>
    .el-tabs__header {
        padding: 0 20px;
        height: 56px;
        line-height: 56px;
        background-color: #fff;
        margin-bottom: 0;
    }

    .title {
        margin-top: 10px;
        padding: 18px 20px;
        border-top: 1px solid #F3F3F3;
        border-bottom: 1px solid #F3F3F3;
        background-color: #fff;
    }

    .form-body {
        background-color: #fff;
        padding: 20px 50% 20px 0;
    }

    .button-item {
        margin-top: 12px;
        padding: 9px 25px;
    }

    .form-body .item {
        width: 300px;
        margin-bottom: 50px;
        margin-right: 25px;
    }

    .item-img {
        height: 550px;
        padding: 25px 10px;
        border-radius: 30px;
        border: 1px solid #CCCCCC;
        background-color: #fff;
        position: relative;
    }

    .item-img .btn-left,.btn-right {
        width: 40px;
        height: 40px;
        background: rgba(204,204,204,.5);
        position: absolute;
        border-radius: 50%;
        top: 50%;
        cursor: pointer;
        background-repeat: no-repeat;
        background-size: 70% 30%;
        background-position: 50% 50%;
        background-image: url("statics/img/mall/line_price_arrow.png");
    }
    .item-img .btn-left {
        left: -20px;
    }
    .item-img .btn-right {
        right: -20px;
        transform: rotate(180deg);
    }
    .item .el-form-item {
        width: 300px;
        margin: 20px auto;
    }

    .left-setting-menu {
        width: 260px;
    }

    .left-setting-menu .el-form-item {
        height: 60px;
        padding-left: 20px;
        display: flex;
        align-items: center;
        margin-bottom: 0;
        cursor: pointer;
    }

    .left-setting-menu .el-form-item .el-form-item__label {
        cursor: pointer;
    }

    .left-setting-menu .el-form-item.active {
        background-color: #F3F5F6;
        border-top-left-radius: 10px;
        width: 105%;
        border-bottom-left-radius: 10px;
    }

    .left-setting-menu .el-form-item .el-form-item__content {
        margin-left: 0 !important
    }

    .no-radius {
        border-top-left-radius: 0 !important;
    }

    .del-btn {
        position: absolute;
        right: -8px;
        top: -8px;
        padding: 4px 4px;
    }

    .reset {
        position: absolute;
        top: 3px;
        left: 90px;
    }

    .app-tip {
        position: absolute;
        right: 24px;
        top: 16px;
        height: 72px;
        line-height: 72px;
        max-width: calc(100% - 78px);
    }

    .app-tip:before {
        content: ' ';
        width: 0;
        height: 0;
        border-color: inherit;
        position: absolute;
        top: -32px;
        right: 100px;
        border-width: 16px;
        border-style: solid;
    }

    .tip-content {
        display: block;
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0 28px;
        font-size: 28px;
    }

    .input-item {
        display: inline-block;
        width: 250px;
        margin: 0 0 20px;
    }

    .input-item .el-input__inner {
        border-right: 0;
    }

    .input-item .el-input__inner:hover{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input__inner:focus{
        border: 1px solid #dcdfe6;
        border-right: 0;
        outline: 0;
    }

    .input-item .el-input-group__append {
        background-color: #fff;
        border-left: 0;
        width: 10%;
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        padding: 0;
    }

    .input-item .el-input-group__append .el-button {
        margin: 0;
    }

    .template-box {
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .template-item {
        width: 208px;
        margin: 0px 10px;
        border: 1px solid #e2e2e2;
        position: relative;
    }

    .template-img {
        width: 187.5px;
    }

    .action-box {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .action-box:hover {
        background: rgba(0,0,0,50%);
        cursor: pointer;
    }

    .action-button {
        height: 30px;
        border: 1px solid #ffffff;
        color: #ffffff;
        border-radius: 5px;
        margin: 5px 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .action-button:hover {
        background: #409eff;
        border: 1px solid #409eff;
    }
    .wechat-image {
        height: 232px;
        width: 200px;
        cursor: pointer;
        position: relative;
    }

    .wechat-end-box {
        height: 32px;
        line-height: 32px;
        width: 200px;
        padding: 0 12px;
        color: #606266;
        border-left: 1px solid #e2e2e2;
        border-right: 1px solid #e2e2e2;
        border-bottom: 1px solid #e2e2e2;
        word-break: break-all;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
</style>
<div id="app" v-cloak>
    <el-card v-loading="loading" style="border:0" shadow="never" body-style="background-color: #f3f3f3;padding: 0 0;">
        <el-form :model="ruleForm"
                 :rules="rules"
                 ref="ruleForm"
                 label-width="172px"
                 size="small">
            <el-tabs v-model="activeName" @tab-click="handleClick">
                <el-tab-pane label="基本信息" name="first">
                    <el-row>
                        <el-col :span="24">
                            <div class="title">
                                <span>基本设置</span>
                            </div>
                            <div class="form-body">
                                <el-form-item label="商城名称" prop="name">
                                    <el-input v-model="ruleForm.name" maxlength="15" placeholder="最多输入15个字符"></el-input>
                                </el-form-item>

                                <el-form-item label="店铺logo" prop="mall_logo_pic">
                                    <app-attachment style="margin-bottom:10px" :multiple="false" :max="1"
                                                    @selected="mallLogoPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:40 * 40"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <div style="margin-right: 20px;display:inline-block;position: relative;cursor: move;">
                                        <app-attachment :multiple="false" :max="1"
                                                        @selected="mallLogoPic">
                                            <app-image mode="aspectFill"
                                                       width="80px"
                                                       height='80px'
                                                       :src="ruleForm.setting.mall_logo_pic">
                                            </app-image>
                                        </app-attachment>
                                        <el-button v-if="ruleForm.setting.mall_logo_pic" class="del-btn"
                                                   size="mini" type="danger" icon="el-icon-close"
                                                   circle
                                                   @click="removeMallLoGoPic"></el-button>
                                    </div>
                                    <el-button size="mini" @click="resetImg('mall_logo_pic')" class="reset" type="primary">恢复默认</el-button>
                                </el-form-item>

                                <el-form-item label="营业状态" prop="is_open">
                                    <el-radio v-model="ruleForm.setting.is_open" :label="1">营业
                                    </el-radio>
                                    <el-radio v-model="ruleForm.setting.is_open" :label="2">打烊
                                    </el-radio>
                                </el-form-item>
                                <el-form-item v-if="ruleForm.setting.is_open == 1" label="营业时间" prop="open_type">
                                    <div>
                                        <el-radio v-model="ruleForm.setting.open_type" :label="1">全天24小时</el-radio>
                                        <el-radio v-model="ruleForm.setting.open_type" :label="2">自定义时间</el-radio>
                                    </div>
                                    <div v-if="ruleForm.setting.open_type == 2" style="margin-top: 10px">
                                        <el-checkbox-group v-model="ruleForm.setting.week_list" size="mini">
                                            <el-checkbox label="1" size="mini">周一</el-checkbox>
                                            <el-checkbox label="2" size="mini">周二</el-checkbox>
                                            <el-checkbox label="3" size="mini">周三</el-checkbox>
                                            <el-checkbox label="4" size="mini">周四</el-checkbox>
                                            <el-checkbox label="5" size="mini">周五</el-checkbox>
                                            <el-checkbox label="6" size="mini">周六</el-checkbox>
                                            <el-checkbox label="0" size="mini">周日</el-checkbox>
                                        </el-checkbox-group>
                                    </div>
                                    <div v-if="ruleForm.setting.open_type == 2" style="margin-top: 10px">
                                        <div v-for="(item,index) in ruleForm.setting.time_list" :key="index" style="margin-bttom: 6px" flex="dir:left cross:center">
                                            <el-time-picker @focus="getTime(index)" v-if="ruleForm.setting.open_type == 2" is-range v-model="item.value" range-separator="至" start-placeholder="开始时间" value-format="HH:mm:ss" end-placeholder="结束时间" placeholder="选择时间范围">
                                            </el-time-picker>
                                            <el-button type="text" size="mini" circle style="margin: 0 20px;" @click.native="deleteTime(index)">
                                                <el-tooltip effect="dark" content="删除" placement="top">
                                                    <img src="statics/img/mall/del.png" alt="">
                                                </el-tooltip>
                                            </el-button>
                                        </div>
                                        <el-button v-if="ruleForm.setting.time_list.length < 3" @click="addTime" style="border-color: #3a8ee6;color: #3a8ee6" plain>+增加时间段</el-button>
                                    </div>
                                </el-form-item>
                                <el-form-item v-if="ruleForm.setting.is_open == 2" label="设置自动开业" prop="is_auto_open">
                                    <div>
                                        <el-radio v-model="ruleForm.setting.is_auto_open" :label="1">不设置自动开业
                                        </el-radio>
                                        <el-radio v-model="ruleForm.setting.is_auto_open" :label="2">设置自动开业时间
                                        </el-radio>
                                        <el-date-picker v-model="ruleForm.setting.auto_open_time" type="datetime" placeholder="选择自动开业时间" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
                                    </div>
                                </el-form-item>
                                <el-form-item label="联系号码" prop="contact_tel">
                                    <el-input v-model="ruleForm.setting.contact_tel"></el-input>
                                </el-form-item>

                                <el-form-item label="外链客服链接"
                                              prop="web_service_url">
                                    <el-input v-model="ruleForm.setting.web_service_url"></el-input>
                                </el-form-item>

                                <el-form-item label="一键导航">
                                    <el-form-item label="详细地址" label-width="80px">
                                        <el-input v-model="ruleForm.setting.quick_map_address"
                                                  placeholder="请输入详细地址">
                                        </el-input>
                                    </el-form-item>
                                    <el-form-item label-width="80px">
                                        <template slot='label'>
                                            <span>经纬度</span>
                                            <el-tooltip effect="dark" content="点击地图,可获取经纬度"
                                                        placement="top">
                                                <i class="el-icon-info"></i>
                                            </el-tooltip>
                                        </template>
                                        <div flex="dir:left">
                                            <el-input v-model="ruleForm.setting.latitude_longitude"
                                                      placeholder="请输入经纬度,用英文逗号分离">
                                            </el-input>
                                        </div>
                                    </el-form-item>
                                    <el-form-item label="地图"  label-width="80px">
                                        <div flex="dir:left">
                                            <app-map @map-submit="mapEvent"
                                                     :address="ruleForm.setting.quick_map_address"
                                                     :lat="ruleForm.setting.latitude"
                                                     :long="ruleForm.setting.longitude">
                                                <el-button size="small">展开地图</el-button>
                                            </app-map>
                                        </div>
                                    </el-form-item>
                                </el-form-item>

                                <el-form-item>
                                    <template slot='label'>
                                        <span>跳转小程序</span>
                                        <el-tooltip effect="dark" content="悬浮按钮跳转小程序"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-form-item label="小程序appId" label-width="100px">
                                        <el-input v-model="ruleForm.setting.small_app_id"
                                                  placeholder="请输入跳转小程序AppId">
                                        </el-input>
                                    </el-form-item>
                                    <el-form-item label="小程序路径" label-width="100px">
                                        <el-input v-model="ruleForm.setting.small_app_url"
                                                  placeholder="请输入跳转小程序路径">
                                        </el-input>
                                    </el-form-item>
                                </el-form-item>
                            </div>
                            <div class="title">
                                <span>交易设置</span>
                            </div>
                            <div class="form-body">
                                <el-form-item prop="over_time">
                                    <template slot='label'>
                                        <span>未支付订单超时时间</span>
                                        <el-tooltip effect="dark" content="注意：如设置为0分，则未支付订单将不会被取消，不能超过100"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-input v-model="ruleForm.setting.over_time" type="number">
                                        <template slot="append">分</template>
                                    </el-input>
                                </el-form-item>
                                <el-form-item prop="delivery_time">
                                    <template slot='label'>
                                        <span>自动确认收货时间</span>
                                        <el-tooltip effect="dark" content="从发货到自动确认收货的时间，不能超过30"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-input v-model="ruleForm.setting.delivery_time"
                                              type="number">
                                        <template slot="append">天</template>
                                    </el-input>
                                </el-form-item>
                                <el-form-item prop="after_sale_time">
                                    <template slot='label'>
                                        <span>售后时间</span>
                                        <el-tooltip effect="dark" placement="top">
                                            <div slot="content">可以申请售后的时间<br/>
                                                注意：分销订单中的已完成订单，只有订单已确认收货，并且时间超过设置的售后天数之后才计入其中！不能超过30
                                            </div>
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-input v-model="ruleForm.setting.after_sale_time"
                                              type="number">
                                        <template slot="append">天</template>
                                    </el-input>
                                </el-form-item>
                                <el-form-item prop="has_order_evaluate" label="默认好评功能">
                                    <el-switch v-model="ruleForm.setting.has_order_evaluate" active-value="1"
                                               inactive-value="0"></el-switch>
                                    <div v-if="ruleForm.setting.has_order_evaluate == 1" style="color:#606266">
                                        <span style="flex-grow: 1">买家购买商品过售后</span>
                                        <el-input size="small" style="width: 150px;margin: 0 5px"
                                                  oninput="this.value = this.value.match(/^\d{0,3}/g)"
                                                  v-model="ruleForm.setting.order_evaluate_day">
                                            <template slot="append">天</template>
                                        </el-input>
                                        <span style="flex-grow: 1">后，若没有主动评论，系统将自动默认好评</span>
                                    </div>
                                </el-form-item>
                                <el-form-item prop="payment_type">
                                    <template slot='label'>
                                        <span>支付方式</span>
                                        <el-tooltip effect="dark" content="若都不勾选，默认选中线上支付"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-checkbox-group v-model="ruleForm.setting.payment_type" size="mini">
                                        <el-checkbox label="online_pay" size="mini">线上支付</el-checkbox>
                                        <el-checkbox label="huodao" size="mini">货到付款</el-checkbox>
                                        <el-checkbox label="balance" size="mini">余额支付</el-checkbox>
                                    </el-checkbox-group>
                                </el-form-item>
                                <el-form-item prop="send_type">
                                    <template slot='label'>
                                        <span>发货方式</span>
                                        <el-tooltip effect="dark" content="需添加门店，到店自提方可生效"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <div>
                                        <el-checkbox-group v-model="ruleForm.setting.send_type">
                                            <el-checkbox v-for="(item, index) in ruleForm.setting.send_type_desc" :label="item.key" :key="item.key">
                                                <template v-if="item.origin !== item.modify && item.modify">
                                                    {{item.modify}}({{item.origin}})
                                                </template>
                                                <template v-else>
                                                    {{item.origin}}
                                                </template>
                                                <el-button style="padding: 0;" type="text" @click="set_send_type(item, index)">
                                                    <img src="statics/img/mall/order/edit.png" alt="">
                                                </el-button>
                                            </el-checkbox>
                                        </el-checkbox-group>
                                        <div style="color: #CCCCCC;">注：手机端显示排序（<span v-for="(item, index) in send_type_list" :key="index">{{index + 1}}.{{item}} </span>）</div>
                                        <el-dialog
                                                :visible.sync="send_type_dialogVisible"
                                                width="30%"
                                                >
                                            <el-row>
                                                <el-col :span="3">
                                                    <span>{{send_type_item.item.origin}}</span>
                                                </el-col>
                                                <el-col :span="20" :offset="1">
                                                    <el-input type="text" v-model="send_type_item.item.modify" maxLength="4">
                                                    </el-input>
                                                </el-col>
                                            </el-row>

                                            <span slot="footer" class="dialog-footer">
                                                <el-button @click="send_type_dialogVisible = false">取 消</el-button>
                                                <el-button type="primary" @click="sureSendType()">确 定</el-button>
                                              </span>
                                        </el-dialog>
                                    </div>
                                </el-form-item>
                                <el-form-item label="余额功能" prop="status">
                                    <el-switch v-model="ruleForm.recharge.status"
                                               active-value="1" inactive-value="0"></el-switch>
                                </el-form-item>
                                <el-form-item label="余额支付密码开关" prop="is_pay_password">
                                    <el-switch v-model="ruleForm.recharge.is_pay_password"
                                               active-value="1" inactive-value="0"></el-switch>
                                </el-form-item>

                                <el-form-item prop="good_negotiable">
                                    <template slot='label'>
                                        <span>商品面议联系方式</span>
                                        <el-tooltip effect="dark" placement="top">
                                            <div slot="content">若客服和外链客服两者都不勾选，默认勾选在线客服；客服和外链客服前端统一显示为客服
                                            </div>
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-checkbox-group v-model="ruleForm.setting.good_negotiable" size="mini">
                                        <el-checkbox label="contact" size="mini">在线客服</el-checkbox>
                                        <el-checkbox label="contact_tel" size="mini">联系电话</el-checkbox>
                                        <el-checkbox label="contact_web" size="mini">外链客服</el-checkbox>
                                    </el-checkbox-group>
                                </el-form-item>
                            </div>

                            <div class="title">
                                <span>客服设置</span>
                            </div>
                            <div class="form-body">
                                <el-form-item label="客服微信" prop="list">
                                    <el-button size="mini" @click="addWechat">选择</el-button>
                                    <div flex="dir:left" style="flex-wrap:wrap">
                                        <div v-for="(value,index) in ruleForm.setting.customer_service_list" style="margin-right: 24px;margin-top: 12px">
                                            <div class="wechat-image" flex="dir:top"
                                                 @click="editWechat(value,index)">
                                                <el-image :src="value.qrcode_url" style="height: 200px;width:100%"></el-image>
                                                <el-tooltip class="v" effect="dark" :content="'微信号'+ value.name" placement="top">
                                                    <div class="wechat-end-box">微信号：{{value.name}}</div>
                                                </el-tooltip>
                                                <el-button class="del-btn" size="mini" type="danger"
                                                           icon="el-icon-close" circle @click.stop="picClose(index)"></el-button>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="color:#909399">注意：最多允许上传10张，前端随机展示一张</div>
                                </el-form-item>
                            </div>
                            <div class="title">
                                <span>自定义设置</span>
                            </div>
                            <div flex style="background-color: #ffffff;padding: 20px 0;">
                                <div class="" style="width: 50%;">
                                    <el-form-item label="添加到我的小程序" prop="is_add_app">
                                    </el-form-item>
                                    <el-form-item label="开关" prop="is_add_app">
                                        <el-switch
                                                v-model="ruleForm.setting.is_add_app"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <template v-if="ruleForm.setting.is_add_app == 1">
                                        <el-form-item label="背影颜色" prop="add_app_bg_color">
                                            <el-color-picker
                                                    color-format="hex"
                                                    v-model="ruleForm.setting.add_app_bg_color"
                                                    :predefine="predefineColors">
                                            </el-color-picker>
                                        </el-form-item>
                                        <el-form-item label="背影透明度" prop="add_app_bg_transparency">
                                            <el-row>
                                                <el-col :span="16">
                                                    <el-slider v-model="ruleForm.setting.add_app_bg_transparency">
                                                    </el-slider>
                                                </el-col>
                                                <el-col :span="7" :offset="1">
                                                    <el-input type="number" v-model.number="ruleForm.setting.add_app_bg_transparency">
                                                        <template slot="append">%</template>
                                                    </el-input>
                                                </el-col>
                                            </el-row>
                                        </el-form-item>
                                        <el-form-item label="背景框圆角" prop="add_app_bg_radius">
                                            <el-row>
                                                <el-col :span="16">
                                                    <el-slider :max="36" v-model="ruleForm.setting.add_app_bg_radius">
                                                    </el-slider>
                                                </el-col>
                                                <el-col :span="7" :offset="1">
                                                    <el-input :max="36" type="number" v-model.number="ruleForm.setting.add_app_bg_radius">
                                                        <template slot="append">px</template>
                                                    </el-input>
                                                </el-col>
                                            </el-row>
                                        </el-form-item>
                                        <el-form-item prop="add_app_text">
                                            <template slot='label'>
                                                <span>提示文本内容</span>
                                                <el-tooltip effect="dark" placement="top">
                                                    <div slot="content">最多允许添加20个字</div>
                                                    <i class="el-icon-info"></i>
                                                </el-tooltip>
                                            </template>
                                            <el-input v-model="ruleForm.setting.add_app_text"></el-input>
                                        </el-form-item>
                                        <el-form-item label="文本颜色" prop="add_app_text_color">
                                            <el-color-picker
                                                    color-format="hex"
                                                    v-model="ruleForm.setting.add_app_text_color"
                                                    :predefine="predefineColors">
                                            </el-color-picker>
                                        </el-form-item>
                                        <el-form-item label="图标颜色" prop="add_app_icon_color_type">
                                            <el-radio v-model="ruleForm.setting.add_app_icon_color_type" label="1">白色
                                            </el-radio>
                                            <el-radio v-model="ruleForm.setting.add_app_icon_color_type" label="2">黑色
                                            </el-radio>
                                        </el-form-item>
                                    </template>
                                </div>
                                <div style="width: 50%;padding-left: 100px;zoom: 0.5;" v-if="ruleForm.setting.is_add_app == 1">
                                    <div style="width: 750px;border: 1px solid #eeeeee;">
                                        <img src="statics/img/mall/heads.png" style="width: 750px;" alt="">
                                        <div style="position: relative;top: -325px;left: -23px;">
                                            <div class="app-tip" :style="appTip" flex="dir:left cross:center">
                                                <img :src="ruleForm.setting.add_app_icon_color_type == 1 ? 'statics/img/mall/fork_white.png' : 'statics/img/mall/fork_black.png'" alt="">
                                                <div class="tip-content">{{ruleForm.setting.add_app_text}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="background-color: #ffffff;padding: 0 0 20px 0;">
                                <el-form-item prop="share_title" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <template slot='label'>
                                        <span>转发小程序</span>
                                    </template>
                                </el-form-item>

                                <el-form-item prop="share_title">
                                    <template slot='label'>
                                        <span>转发标题</span>
                                    </template>
                                    <el-input style="width: 50%" v-model="ruleForm.setting.share_title"></el-input>
                                </el-form-item>

                                <el-form-item prop="share_pic">
                                    <app-attachment style="margin-bottom:10px" :multiple="false" :max="1"
                                                    @selected="sharePic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:420 * 336"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <div style="margin-right: 20px;display:inline-block;position: relative;cursor: move;">
                                        <app-attachment :multiple="false" :max="1"
                                                        @selected="sharePic">
                                            <app-image mode="aspectFill"
                                                       width="80px"
                                                       height='80px'
                                                       :src="ruleForm.setting.share_pic">
                                            </app-image>
                                        </app-attachment>
                                        <el-button v-if="ruleForm.setting.share_pic" class="del-btn"
                                                   size="mini" type="danger" icon="el-icon-close"
                                                   circle
                                                   @click="removeSharePic"></el-button>
                                    </div>
                                </el-form-item>

                                <el-form-item label="首页购买记录框" prop="is_purchase_frame" style="margin-top: 30px;padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                </el-form-item>
                                <el-form-item label="开关" prop="is_purchase_frame">
                                    <el-switch v-model="ruleForm.setting.is_purchase_frame"
                                               active-value="1"
                                               inactive-value="0">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item prop="purchase_num">
                                    <template slot='label'>
                                        <span>轮播订单数</span>
                                        <el-tooltip effect="dark" content="轮播最新的N条订单"
                                                    placement="top">
                                            <i class="el-icon-info"></i>
                                        </el-tooltip>
                                    </template>
                                    <el-input style="width: 50%" type="number" min="1" step="1" max="50"
                                              v-model.number="ruleForm.setting.purchase_num"></el-input>
                                </el-form-item>

                                <div class="form-body" v-if="ruleForm.permission.mpvideo">
                                    <el-form-item label="视频号设置" style="margin-top: 30px;padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    </el-form-item>
                                    <el-form-item label="开关" prop="is_video_number">
                                        <el-switch v-model="ruleForm.setting.is_video_number"
                                                   :active-value="1"
                                                   :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item label="公众号AppID" prop="video_number_app_id">
                                        <el-input v-model="ruleForm.setting.video_number_app_id"></el-input>
                                    </el-form-item>
                                    <el-form-item label="公众号AppSecret" prop="video_number_app_secret">
                                        <el-input v-model="ruleForm.setting.video_number_app_secret"></el-input>
                                    </el-form-item>
                                    <el-form-item>
                                    <template slot='label'>
                                            <span>接受消息用户</span>
                                            <el-tooltip effect="dark" content="接受消息用户公众号openId"
                                                        placement="top">
                                                <i class="el-icon-info"></i>
                                            </el-tooltip>
                                        </template>
                                        <el-form-item label="openId1" label-width="100px">
                                            <el-input v-model="ruleForm.setting.video_number_user_1"
                                                      placeholder="请输入用户openId1">
                                            </el-input>
                                        </el-form-item>
                                        <el-form-item label="openId2" label-width="100px">
                                            <el-input v-model="ruleForm.setting.video_number_user_2"
                                                      placeholder="请输入用户openId2">
                                            </el-input>
                                            <div style="color: #409EFF;cursor: pointer;" @click="openDialog2">查看获取管理员openid示例</div>
                                        </el-form-item>
                                    </el-form-item>
                                    <el-form-item label="分享标题" prop="video_number_share_title">
                                        <el-input v-model="ruleForm.setting.video_number_share_title"></el-input>
                                    </el-form-item>
                                    <el-form-item label="会员等级可用权限" prop="is_video_number_member">
                                        <el-switch v-model="ruleForm.setting.is_video_number_member"
                                                   :active-value="1"
                                                   :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item v-if="ruleForm.setting.is_video_number_member" label="会员等级" prop="video_number_member_list">
                                        <el-tag
                                          style="margin-right: 5px;margin-bottom: 5px;"
                                          v-for="(tag, index) in ruleForm.setting.video_number_member_list"
                                          :key="tag.name"
                                          @close="memberHandleClose(index)"
                                          closable>
                                          {{tag.name}}
                                        </el-tag>
                                        <el-button @click="getMembers" size="small">选择会员等级</el-button>
                                    </el-form-item>
                                    <el-form-item label="公众号模板" prop="video_number_template_list">
                                        <el-tag
                                          style="margin-right: 5px;margin-bottom: 5px;"
                                          v-for="(tag, index) in ruleForm.setting.video_number_template_list"
                                          :key="tag.name"
                                          @close="templateHandleClose(index)"
                                          closable>
                                          {{tag.name}}
                                        </el-tag>
                                        <el-button v-if="ruleForm.setting.video_number_template_list.length == 0" @click="getTemplates" size="small">选择模板</el-button>
                                    </el-form-item>

                                    <el-dialog title="选择会员等级" :visible.sync="memberDialogVisible">
                                        <div class="input-item">
                                            <el-input @keyup.enter.native="getMembers" size="small" placeholder="请输入会员等级名称" v-model="memberKeyword" clearable @clear="getMembers">
                                                <el-button slot="append" icon="el-icon-search" @click="getMembers"></el-button>
                                            </el-input>
                                        </div>
                                      <el-table v-loading="memberListLoading" :data="memberList" @selection-change="handleSelectionChange">
                                        <el-table-column type="selection" width="55"></el-table-column>
                                        <el-table-column property="name" label="会员等级名称"></el-table-column>
                                      </el-table>
                                      <div style="margin-top: 10px; text-align: right;">
                                          <el-pagination @current-change="memberPagination" hide-on-single-page background layout="prev, pager, next"
                               :page-count="memberPageCount" :current-page="memberCurrentPage"></el-pagination>
                                      </div>
                                      <div slot="footer" class="dialog-footer">
                                        <el-button size="small" @click="memberDialogVisible = false">取 消</el-button>
                                        <el-button size="small" type="primary" @click="memberSubmit">确 定</el-button>
                                      </div>
                                    </el-dialog>

                                    <el-dialog
                                      title="选择模板"
                                      :visible.sync="templateDialogVisible"
                                      width="60%">
                                      <div class="template-box">
                                        <div @mouseover="mouseover(item)" @mouseleave="mouseleave" class="template-item" v-for="item in templateList">
                                          <img class="template-img" :src="item.pic_url">
                                          <div class="action-box" v-if="currentTemplateId == item.id">
                                              <div @click="templateSubmit(item)" class="action-button">选择模板</div>
                                              <div @click="templatePreview(item)" class="action-button">预览模板</div>
                                          </div>
                                        </div>
                                      </div>
                                    </el-dialog>

                                    <el-dialog
                                      title="手机端预览"
                                      :visible.sync="previewDialogVisible"
                                      width="30%">
                                      <div style="height: 500px;overflow-y: scroll;">
                                          <img style="width: 100%;" :src="currentTemplate.preview_pic_url">
                                      </div>
                                      <span slot="footer" class="dialog-footer">
                                        <el-button size="small" @click="previewDialogVisible = false">取 消</el-button>
                                        <el-button size="small" type="primary" @click="templateSubmit(currentTemplate)">选择模板</el-button>
                                      </span>
                                    </el-dialog>

                                    <el-dialog title="如何获取OpenId" :visible.sync="dialogVisible2">
                                        <div class="dialog">
                                            <div class="dialog-text">1.关注公众号，在公众号中回复任意消息</div>
                                            <div class="dialog-text">2.登录微信公众号平台，进入消息管理，点击刚刚回复的消息</div>
                                            <img style="width: 100%;" :src="openIdPicUrl_1">
                                            <div class="dialog-text">3.复制OpenID</div>
                                            <img style="width: 100%;" :src="openIdPicUrl_2">
                                        </div>
                                    </el-dialog>
                                </div>

                            </div>
                        </el-col>
                    </el-row>
                </el-tab-pane>
                <el-tab-pane label="显示设置" name="second">
                    <el-tabs v-model="secondActiveName" @tab-click="secondHandleClick" style="margin-top: 10px;">
                        <el-tab-pane label="商品列表显示" name="first">
                            <div class="form-body" style="padding: 40px;display: flex;">
                                <div class='left-setting-menu'>
                                    <el-form-item :class='active_setting == "is_show_cart" ? "active":""'
                                                  @click.native='chooseSetting("is_show_cart")' label="购物车" prop="is_show_cart">
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_cart"
                                                :active-value="1"
                                                :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_show_sales_num" ? "active":""'
                                                  @click.native='chooseSetting("is_show_sales_num")' label="已售量" prop="is_show_sales_num">
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_sales_num"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_show_goods_name" ? "active":""'
                                                  @click.native='chooseSetting("is_show_goods_name")' label="商品名称" prop="is_show_goods_name">
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_goods_name"
                                                :active-value="1"
                                                :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_list_underline_price" ? "active":""'
                                                  @click.native='chooseSetting("is_list_underline_price")' label="划线价" prop="is_list_underline_price">
                                        <el-switch
                                                v-model="ruleForm.setting.is_list_underline_price"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                </div>
                                <div style='background-color: #F3F5F6;padding: 30px;border-radius: 10px;'
                                     :class='active_setting == "is_purchase_frame" ? "no-radius":""'>
                                    <div v-if='active_setting == "is_list_underline_price"' class="item-img">
                                        <div class="btn-left" @click="btnClick(-1)" v-if="goods_line_price !== 0"></div>
                                        <app-image  v-if="goods_line_price === 0" mode="aspectFill"
                                                   src="statics/img/mall/is_show_goods_line_price-0.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if="goods_line_price === 1" mode="aspectFill"
                                                    src="statics/img/mall/is_show_goods_line_price-1.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if="goods_line_price === 2" mode="aspectFill"
                                                    src="statics/img/mall/is_show_goods_line_price-2.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if="goods_line_price === 3" mode="aspectFill"
                                                    src="statics/img/mall/is_show_goods_line_price-3.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if="goods_line_price === 4" mode="aspectFill"
                                                    src="statics/img/mall/is_show_goods_line_price-4.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <div class="btn-right" v-if="goods_line_price !== 4" @click="btnClick(+1)"></div>
                                        <div style="text-align: center;margin-top: 30px;color: #888888;font-size: 10px;">{{goods_line_price + 1}}/5</div>
                                    </div>
                                    <div v-else class="item-img">
                                        <app-image v-if='active_setting == "is_show_cart"' mode="aspectFill"
                                                   src="statics/img/mall/is_show_cart.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_sales_num"' mode="aspectFill"
                                                   src="statics/img/mall/is_show_sales_num.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_goods_name"' mode="aspectFill"
                                                   src="statics/img/mall/is_show_goods_name.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                    </div>
                                </div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="商品详情显示" name="second">
                            <div class="form-body" style="padding: 40px;display: flex;">
                                <div class='left-setting-menu'>
                                    <el-form-item :class='active_setting == "is_underline_price" ? "active":""'
                                                  @click.native='chooseSetting("is_underline_price")' label="划线价" prop="is_underline_price">
                                        <el-switch
                                                v-model="ruleForm.setting.is_underline_price"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_common_user_member_price" ? "active":""'
                                                  @click.native='chooseSetting("is_common_user_member_price")' label="普通用户会员价"
                                                  prop="is_common_user_member_price">
                                        <el-switch
                                                v-model="ruleForm.setting.is_common_user_member_price"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_member_user_member_price" ? "active":""'
                                                  @click.native='chooseSetting("is_member_user_member_price")' label="会员用户会员价"
                                                  prop="is_member_user_member_price">
                                        <el-switch
                                                v-model="ruleForm.setting.is_member_user_member_price"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item v-if="is_svip" :class='active_setting == "is_show_normal_vip" ? "active":""' @click.native='chooseSetting("is_show_normal_vip")' prop="is_show_normal_vip">
                                        <template slot='label'>
                                            <span>非SVIP用户超级会员价</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_normal_vip"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item v-if="is_svip" :class='active_setting == "is_show_super_vip" ? "active":""' @click.native='chooseSetting("is_show_super_vip")' prop="is_show_super_vip">
                                        <template slot='label'>
                                            <span>SVIP用户超级会员价</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_super_vip"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_express" ? "active":""'
                                                  @click.native='chooseSetting("is_express")' label="快递" prop="is_express">
                                        <el-switch
                                                v-model="ruleForm.setting.is_express"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_sales" ? "active":""'
                                                  @click.native='chooseSetting("is_sales")' label="已售量" prop="is_sales">
                                        <el-switch
                                                v-model="ruleForm.setting.is_sales"
                                                :active-value="1"
                                                :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_share_price" ? "active":""'
                                                  @click.native='chooseSetting("is_share_price")' label="分销价"
                                                  prop="is_share_price">
                                        <el-switch
                                                v-model="ruleForm.setting.is_share_price"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_goods_video" ? "active":""'
                                                  @click.native='chooseSetting("is_goods_video")' label="商品视频特色样式开关"
                                                  prop="is_goods_video">
                                        <el-switch
                                                v-model="ruleForm.setting.is_goods_video"
                                                :active-value="1"
                                                :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                </div>
                                <div style='background-color: #F3F5F6;padding: 30px;border-radius: 10px;'
                                     :class='active_setting == "is_purchase_frame" ? "no-radius":""'>
                                    <div class="item-img">
                                        <app-image v-if='active_setting == "is_purchase_frame"' mode="aspectFill"
                                                   src="statics/img/mall/buy_log.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_underline_price"' mode="aspectFill"
                                                   src="statics/img/mall/is_underline_price.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_express"' mode="aspectFill"
                                                   src="statics/img/mall/is_express.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_sales"' mode="aspectFill"
                                                   src="statics/img/mall/sales_show.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_common_user_member_price"' mode="aspectFill"
                                                   src="statics/img/mall/price_show_2.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_member_user_member_price"' mode="aspectFill"
                                                   src="statics/img/mall/price_show.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_share_price"' mode="aspectFill"
                                                   src="statics/img/mall/share_show.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_mobile_auth"' mode="aspectFill"
                                                   src="statics/img/mall/auth_mobile.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_manual_mobile_auth"' mode="aspectFill"
                                                   src="statics/img/mall/manual_mobile_auth.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <!--                                <app-image  v-if='active_setting == "is_recommend"' mode="aspectFill" src="statics/img/mall/recommend.png"-->
                                        <!--                                           style="margin-bottom: 20px" height="500" width="280"></app-image>-->
                                        <app-image v-if='active_setting == "is_official_account"' mode="aspectFill"
                                                   src="statics/img/mall/official_account.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if='active_setting == "is_icon_members_grade"' mode="aspectFill" src="statics/img/mall/icon_members_grade.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <!-- 超级会员卡图 -->
                                        <app-image v-if='active_setting == "is_icon_super_vip" && is_svip' mode="aspectFill" src="statics/img/mall/is_icon_super_vip.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_normal_vip" && is_svip' mode="aspectFill" src="statics/img/mall/member-price.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_super_vip" && is_svip' mode="aspectFill" src="statics/img/mall/member-price.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_goods_video"' mode="aspectFill" src="statics/img/mall/goods-video.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                    </div>
                                </div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="其他显示" name="third">
                            <div class="form-body" style="padding: 40px;display: flex;">
                                <div class='left-setting-menu'>
                                    <el-form-item :class='active_setting == "is_must_login" ? "active":""'
                                                  @click.native='chooseSetting("is_must_login")' label="强制授权开关"
                                                  prop="is_must_login">
                                        <el-switch
                                                v-model="ruleForm.setting.is_must_login"
                                                :active-value="1"
                                                :inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_not_share_show" ? "active":""'
                                                  @click.native='chooseSetting("is_not_share_show")'
                                                  prop="is_not_share_show">
                                        <template slot='label'>
                                            <span>非分销商分销中心显示</span>
                                            <el-tooltip effect="dark" content="仅控制用户中心显示"
                                                        placement="top">
                                                <i class="el-icon-info"></i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_not_share_show"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_mobile_auth" ? "active":""'
                                                  @click.native='chooseSetting("is_mobile_auth")' label="登陆授权手机号"
                                                  prop="is_mobile_auth">
                                        <el-switch
                                                v-model="ruleForm.setting.is_mobile_auth"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_manual_mobile_auth" ? "active":""'
                                                  @click.native='chooseSetting("is_manual_mobile_auth")'
                                                  prop="is_manual_mobile_auth">
                                        <template slot='label'>
                                            <span>手动授权手机号</span>
                                            <el-tooltip effect="dark" content="开启后,绑定手机号页面会显示手机号手动授权"
                                                        placement="top">
                                                <i class="el-icon-info"></i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_manual_mobile_auth"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <!--                            <el-form-item :class='active_setting == "is_recommend" ? "active":""' @click.native='chooseSetting("is_recommend")' label="推荐商品状态" prop="is_recommend">-->
                                    <!--                                <el-switch-->
                                    <!--                                        v-model="ruleForm.setting.is_recommend"-->
                                    <!--                                        active-value="1"-->
                                    <!--                                        inactive-value="0">-->
                                    <!--                                </el-switch>-->
                                    <!--                            </el-form-item>-->
                                    <el-form-item :class='active_setting == "is_official_account" ? "active":""'
                                                  @click.native='chooseSetting("is_official_account")'
                                                  prop="is_official_account">
                                        <template slot='label'>
                                            <span>关联公众号组件</span>
                                            <el-tooltip effect="dark" content="注意：该功能需要 ->微信小程序后台->设置->接口设置 开启并设置关联(同一主体下)的公众号"
                                                        placement="top">
                                                <i class="el-icon-info"></i>
                                            </el-tooltip>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_official_account"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_icon_members_grade" ? "active":""' @click.native='chooseSetting("is_icon_members_grade")' prop="is_icon_members_grade">
                                        <template slot='label'>
                                            <span>会员等级标识</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_icon_members_grade"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <!-- 超级会员卡插件 -->
                                    <el-form-item v-if="is_svip" :class='active_setting == "is_icon_super_vip" ? "active":""' @click.native='chooseSetting("is_icon_super_vip")' prop="is_icon_super_vip">
                                        <template slot='label'>
                                            <span>超级会员标识</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_icon_super_vip"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_required_position" ? "active":""' @click.native='chooseSetting("is_required_position")' prop="is_required_position">
                                        <template slot='label'>
                                            <span>定位地址是否必填</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_required_position"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_share_tip" ? "active":""' @click.native='chooseSetting("is_share_tip")' prop="is_share_tip">
                                        <template slot='label'>
                                            <span>申请分销商提示弹窗</span>
                                        </template>
                                        <el-switch
                                                v-model="ruleForm.setting.is_share_tip"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_comment" ? "active":""'
                                                  @click.native='chooseSetting("is_comment")' label="商城评论" prop="is_comment">
                                        <el-switch
                                                v-model="ruleForm.setting.is_comment"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                    <el-form-item :class='active_setting == "is_show_hot_goods" ? "active":""'
                                                  @click.native='chooseSetting("is_show_hot_goods")' label="热搜显示开关" prop="is_show_hot_goods">
                                        <el-switch
                                                v-model="ruleForm.setting.is_show_hot_goods"
                                                active-value="1"
                                                inactive-value="0">
                                        </el-switch>
                                    </el-form-item>
                                </div>

                                <div style='background-color: #F3F5F6;padding: 30px;border-radius: 10px;'
                                     :class='active_setting == "is_purchase_frame" ? "no-radius":""'>
                                    <div class="item-img">
                                        <app-image v-if='active_setting == "is_not_share_show"' mode="aspectFill"
                                                   src="statics/img/mall/is_not_share_show.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_mobile_auth"' mode="aspectFill"
                                                   src="statics/img/mall/auth_mobile.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_manual_mobile_auth"' mode="aspectFill"
                                                   src="statics/img/mall/manual_mobile_auth.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <!--                                <app-image  v-if='active_setting == "is_recommend"' mode="aspectFill" src="statics/img/mall/recommend.png"-->
                                        <!--                                           style="margin-bottom: 20px" height="500" width="280"></app-image>-->
                                        <app-image v-if='active_setting == "is_official_account"' mode="aspectFill"
                                                   src="statics/img/mall/official_account.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image  v-if='active_setting == "is_icon_members_grade"' mode="aspectFill" src="statics/img/mall/icon_members_grade.png"
                                                    style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <!-- 超级会员卡图 -->
                                        <app-image v-if='active_setting == "is_icon_super_vip" && is_svip' mode="aspectFill" src="statics/img/mall/is_icon_super_vip.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_normal_vip" && is_svip' mode="aspectFill" src="statics/img/mall/member-price.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_show_super_vip" && is_svip' mode="aspectFill" src="statics/img/mall/member-price.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_required_position"' mode="aspectFill" src="statics/img/mall/is_required_position.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image v-if='active_setting == "is_share_tip"' mode="aspectFill" src="statics/img/mall/is_share_tip.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                       <app-image mode="aspectFill" v-if='active_setting == "is_comment"' src="statics/img/mall/comment_show.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                       <app-image mode="aspectFill" v-if='active_setting == "is_must_login"' src="statics/img/mall/is_must_login.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                        <app-image mode="aspectFill" v-if='active_setting == "is_show_hot_goods"' src="statics/img/mall/is_show_hot_goods.png"
                                                   style="margin-bottom: 20px" height="500" width="280"></app-image>
                                    </div>
                                </div>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </el-tab-pane>

                <el-tab-pane label="悬浮按钮设置" name="third">
                    <el-row>
                        <el-col :span="24">
                            <div class="title">
                                <span>悬浮窗设置</span>
                            </div>
                            <div class="form-body">
                                <el-form-item label="启用悬浮按钮">
                                    <el-switch
                                            v-model="ruleForm.setting.is_quick_navigation"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item v-if="ruleForm.setting.is_quick_navigation == '1'" label="悬浮按钮样式"
                                              prop="quick_navigation_style">
                                    <el-radio v-model="ruleForm.setting.quick_navigation_style" label="1">展开收起
                                    </el-radio>
                                    <el-radio v-model="ruleForm.setting.quick_navigation_style" label="2">固定展开
                                    </el-radio>
                                </el-form-item>
                                <el-form-item v-if="ruleForm.setting.is_quick_navigation == '1'" label="展开图标">
                                    <app-attachment :multiple="false" :max="1" @selected="quickNavigationOpenedPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('quick_navigation_opened_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.quick_navigation_opened_pic">
                                    </app-image>
                                </el-form-item>
                                <el-form-item v-if="ruleForm.setting.is_quick_navigation == '1'" label="收起图标">
                                    <app-attachment :multiple="false" :max="1" @selected="quickNavigationClosedPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('quick_navigation_closed_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.quick_navigation_closed_pic">
                                    </app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'" style="background-color: #ffffff;padding: 0 0 20px 0;">
                                <el-form-item label="在线客服开关" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_customer_services"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <app-attachment :multiple="false" :max="1" @selected="customerServicesPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('customer_services_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.customer_services_pic">
                                    </app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="返回首页导航开关" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_quick_home"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <app-attachment :multiple="false" :max="1" @selected="quickHomePic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('quick_home_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.quick_home_pic">
                                    </app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="一键拨号开关" prop="is_dial" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_dial"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>

                                <el-form-item>
                                    <app-attachment :multiple="false" :max="1" @selected="dialPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('dial_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.dial_pic"></app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="客服外链开关" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_web_service"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <app-attachment :multiple="false" :max="1" @selected="webServicePic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('web_service_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.web_service_pic"></app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="快捷导航开关" prop="is_quick_map" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_quick_map"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <app-attachment :multiple="false" :max="1" @selected="quickMapPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('quick_map_pic')" class="reset" type="primary">恢复默认</el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.quick_map_pic"></app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="跳转小程序开关" prop="is_small_app" style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_small_app"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item>
                                    <app-attachment :multiple="false"
                                                    :max="1"
                                                    @selected="smallAppPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('small_app_pic')" class="reset"
                                               type="primary">恢复默认
                                    </el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.small_app_pic"></app-image>
                                </el-form-item>
                            </div>

                            <div class="form-body" v-if="ruleForm.setting.is_quick_navigation == '1'">
                                <el-form-item label="自定义按钮" prop="is_quick_customize"
                                              style="padding-top: 20px;border-top: 1px solid #F3F3F3;">
                                    <el-switch
                                            v-model="ruleForm.setting.is_quick_customize"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>


                                <el-form-item label="跳转链接" prop="quick_customize_link_url">
                                    <el-input :disabled="true" size="small"
                                              v-model="ruleForm.setting.quick_customize_link_url" autocomplete="off">
                                        <app-pick-link slot="append" @selected="selectQuickCustomize">
                                            <el-button size="mini">选择链接</el-button>
                                        </app-pick-link>
                                    </el-input>
                                </el-form-item>

                                <el-form-item>
                                    <app-attachment :multiple="false"
                                                    :max="1"
                                                    @selected="quickCustomizePic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:100 * 100"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <el-button size="mini" @click="resetImg('quick_customize')" class="reset"
                                               type="primary">恢复默认
                                    </el-button>
                                    <app-image style="width: 80px; height: 80px;"
                                               :src="ruleForm.setting.quick_customize_pic"></app-image>
                                </el-form-item>
                            </div>

                            <div class="title">
                                <span>悬浮按钮设置</span>
                                <el-tooltip effect="dark" content="用于商品列表页"
                                            placement="top">
                                    <i class="el-icon-info"></i>
                                </el-tooltip>
                            </div>
                            <div class="form-body">
                                <el-form-item label="启用回到顶部悬浮按钮" prop="is_show_score_top">
                                    <el-switch
                                            v-model="ruleForm.setting.is_show_score_top"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item label="启用购物车悬浮按钮" prop="is_show_cart_fly">
                                    <el-switch
                                            v-model="ruleForm.setting.is_show_cart_fly"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                            </div>
                            <div class="title">
                                <span>商品售罄图标设置</span>
                            </div>
                            <div class="form-body">
                                <el-form-item label="售罄图标显示开关">
                                    <el-switch
                                            v-model="ruleForm.setting.is_show_stock"
                                            :active-value="1"
                                            :inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item label="是否使用默认图标" v-if="ruleForm.setting.is_show_stock == '1'">
                                    <el-switch
                                            v-model="ruleForm.setting.is_use_stock"
                                            active-value="1"
                                            inactive-value="0"
                                            active-color="#409EFF">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item label="商品图正常尺寸" v-if="ruleForm.setting.is_show_stock == '1'">
                                    <app-attachment :multiple="false" :max="1" @selected="sellOutPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:702 * 702"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <app-image style="width: 80px; height: 80px;background-color: rgba(0,0,0,.5)"
                                               :src="ruleForm.setting.is_use_stock == '1' ? 'statics/img/app/mall/plugins-out.png' : ruleForm.setting.sell_out_pic">
                                    </app-image>
                                </el-form-item>
                                <el-form-item label="商品图4:3尺寸" v-if="ruleForm.setting.is_show_stock == '1'">
                                    <app-attachment :multiple="false" :max="1" @selected="sellOutOtherPic">
                                        <el-tooltip effect="dark"
                                                    content="建议尺寸:702 * 468"
                                                    placement="top">
                                            <el-button size="mini">选择图标</el-button>
                                        </el-tooltip>
                                    </app-attachment>
                                    <app-image style="width: 80px; height: 80px;background-color: rgba(0,0,0,.5)"
                                               :src="ruleForm.setting.is_use_stock == '1' ? 'statics/img/app/mall/rate-out.png' : ruleForm.setting.sell_out_other_pic">
                                    </app-image>
                                </el-form-item>
                            </div>
                        </el-col>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
            <el-button :loading="submitLoading" class="button-item" size="small" type="primary"
                       @click="submit('ruleForm')">保存
            </el-button>
        </el-form>        
        <!--客服微信-->
        <el-dialog title="客服微信" :visible.sync="wechatVisible" width="30%" :close-on-click-modal="false">
            <el-form :model="wechatForm" label-width="150px" :rules="wechatRules" ref="wechatForm"
                     @submit.native.prevent>
                <el-form-item label="客服微信二维码" prop="qrcode_url">
                    <div style="margin-bottom:10px;">
                        <app-attachment style="display:inline-block;margin-right: 10px" :multiple="false" :max="1"
                                        @selected="wechatSelect">
                            <el-tooltip effect="dark" content="建议尺寸:360 * 360" placement="top">
                                <el-button size="mini">选择文件</el-button>
                            </el-tooltip>
                        </app-attachment>
                    </div>
                    <div style="margin-right: 20px;display:inline-block;position: relative;cursor: move;">
                        <app-attachment :multiple="false" :max="1" @selected="wechatSelect">
                            <app-image mode="aspectFill" width="80px" height='80px'
                                       :src="wechatForm.qrcode_url"></app-image>
                        </app-attachment>
                        <el-button v-if="wechatForm.qrcode_url" class="del-btn" size="mini" type="danger"
                                   icon="el-icon-close" circle @click="wechatClose"></el-button>
                    </div>
                </el-form-item>
                <el-form-item label="客服微信号" prop="name">
                    <el-input size="small" v-model="wechatForm.name" maxlength="20" auto-complete="off"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button size="small" @click="wechatVisible = false">取消</el-button>
                <el-button size="small" type="primary" @click.native="wechatSubmit">提交</el-button>
            </div>
        </el-dialog>
    </el-card>
</div>

<script>
    const app = new Vue({
        el: '#app',
        data() {
            let noPay = (rule, value, callback) => {
                let reg = /^[1-9]\d*$/;
                if (!reg.test(this.ruleForm.setting.over_time) && this.ruleForm.setting.over_time != 0) {
                    callback(new Error('未支付订单超时时间必须为整数'))
                } else if (this.ruleForm.setting.over_time > 100) {
                    callback(new Error('未支付订单超时时间不能大于100'))
                } else {
                    callback()
                }
            };
            let after_sale = (rule, value, callback) => {
                let reg = /^[1-9]\d*$/;
                if (!reg.test(this.ruleForm.setting.after_sale_time) && this.ruleForm.setting.after_sale_time != 0) {
                    callback(new Error('售后时间时间必须为整数'))
                } else if (this.ruleForm.setting.after_sale_time > 30) {
                    callback(new Error('售后时间不能大于30天'))
                } else {
                    callback()
                }
            };
            let delivery = (rule, value, callback) => {
                let reg = /^[1-9]\d*$/;
                if (!reg.test(this.ruleForm.setting.delivery_time) && this.ruleForm.setting.delivery_time != 0) {
                    callback(new Error('收货时间必须为整数'))
                } else if (this.ruleForm.setting.delivery_time > 30) {
                    callback(new Error('收货时间不能大于30天'))
                } else {
                    callback()
                }
            };
            let integral = (rule, value, callback) => {
                let reg = /^[1-9]\d*$/;
                if (!reg.test(this.ruleForm.setting.member_integral) && this.ruleForm.setting.member_integral != 0) {
                    callback(new Error('用户积分必须为整数'))
                } else {
                    callback()
                }
            };
            let purchase = (rule, value, callback) => {
                if (this.ruleForm.setting.purchase_num > 50 || this.ruleForm.setting.purchase_num < 0) {
                    callback(new Error('轮播订单数范围为0-50'))
                } else {
                    callback()
                }
            };
            let contactTel = (rule, value, callback) => {
                let reg = /(^1\d{10}$)|(^$)|(^([0-9]{3,4}-)?\d{7,8}$)|(^400[0-9]{7}$)|(^800[0-9]{7}$)|(^(400)-(\d{3})-(\d{4})(.)(\d{1,4})$)|(^(400)-(\d{3})-(\d{4}$))/;
                if (!reg.test(this.ruleForm.setting.contact_tel)) {
                    callback(new Error('请填写有效的联系电话或手机'))
                } else {
                    callback()
                }
            };
            return {
                wechatVisible: false,
                wechatForm: {
                    qrcode_url: '',
                    name: '',
                },
                wechatRules: {
                    qrcode_url: [
                        {required: true, message: '图片不能为空', trigger: 'blur'},
                    ]
                },
                index: -1,
                loading: false,
                submitLoading: false,
                mall: null,
                is_svip: false,
                active_setting: 'is_show_cart',
                activeName: 'first',
                secondActiveName: 'first',
                checkList: [],
                ruleForm: {
                    name: '',
                    setting: {
                        payment_type: [],
                        send_type: [],
                        video_number_template_list: []
                    },
                    recharge: {},
                    permission: []
                },
                rules: {
                    name: [
                        {required: true, message: '请填写商城名称。'},
                        {max: 64, message: '最多64个字。'},
                    ],
                    contact_tel: [
                        {validator: contactTel, trigger: 'change'},
                    ],
                    over_time: [
                        {validator: noPay, trigger: 'blur'}
                    ],
                    delivery_time: [
                        {validator: delivery, trigger: 'blur'}
                    ],
                    after_sale_time: [
                        {validator: after_sale, trigger: 'blur'}
                    ],
                    member_integral: [
                        {validator: integral, trigger: 'blur'}
                    ],
                    purchase_num: [
                        {validator: purchase, trigger: 'blur'}
                    ]
                },
                catGoodsCols: [
                    {
                        label: '1',
                        value: 1
                    },
                    {
                        label: '2',
                        value: 2
                    },
                    {
                        label: '3',
                        value: 3
                    },
                ],
                predefineColors: [
                    '#000',
                    '#fff',
                    '#888',
                    '#ff4544'
                ],
                send_type_dialogVisible: false,
                send_type_item: {
                    item: {},
                    index: 0
                },
                goods_line_price: 0,

                // 视频号参数
                memberDialogVisible: false,
                memberListLoading: false,
                memberPage: 1,
                memberPageCount: 0,
                memberCurrentPage: null,
                memberList: [],
                multipleSelection: [],
                memberKeyword: '',
                templateDialogVisible: false,
                currentTemplateId: null,
                templateList: [
                    {
                        id: 1,
                        name: '模板一',
                        pic_url: 'statics/img/mall/sph/template-1-1.png',
                        preview_pic_url: 'statics/img/mall/sph/template-2-1.png'
                    },
                    {
                        id: 2,
                        name: '模板二',
                        pic_url: 'statics/img/mall/sph/template-1-2.png',
                        preview_pic_url: 'statics/img/mall/sph/template-2-2.png'
                    },
                    {
                        id: 3,
                        name: '模板三',
                        pic_url: 'statics/img/mall/sph/template-1-3.png',
                        preview_pic_url: 'statics/img/mall/sph/template-2-3.png'
                    },
                    {
                        id: 4,
                        name: '模板四',
                        pic_url: 'statics/img/mall/sph/template-1-4.png',
                        preview_pic_url: 'statics/img/mall/sph/template-2-4.png'
                    },
                ],
                previewDialogVisible: false,
                currentTemplate: {
                    preview_pic_url: ''
                },
                dialogVisible2: false,
                openIdPicUrl_1: _baseUrl + '/statics/img/mall/wechatplatform/open_id_1.png',
                openIdPicUrl_2: _baseUrl + '/statics/img/mall/wechatplatform/open_id_2.png',
            };
        },
        created() {
            this.loadData();
            this.getSvip();
        },
        methods: {
            editWechat(item, index) {
                this.index = index;
                this.wechatForm = Object.assign({}, item);
                this.wechatVisible = true;
            },
            picClose(index) {
                this.ruleForm.setting.customer_service_list.splice(index, 1);
            },

            wechatSelect(e) {
                if (e.length) {
                    this.wechatForm.qrcode_url = e[0].url;
                }
            },

            wechatClose() {
                this.wechatForm.qrcode_url = '';
            },
            wechatSubmit() {
                this.$refs.wechatForm.validate((valid) => {
                    if (valid) {
                        if (this.index === -1) {
                            this.ruleForm.setting.customer_service_list.push(Object.assign({}, this.wechatForm));
                        } else {
                            this.ruleForm.setting.customer_service_list.splice(this.index, 1, this.wechatForm);
                        }
                        this.wechatVisible = false;
                    }
                });
            },

            addWechat() {
                this.index = -1;
                this.wechatForm = {
                    qrcode_url: '',
                    name: '',
                };
                this.wechatVisible = true
            },
            addTime() {
                let para = {
                    value: ['','']
                }
                this.ruleForm.setting.time_list.push(para)
            },

            getTime(i) {
                if(this.ruleForm.setting.time_list[i].value[0] == '' && this.ruleForm.setting.time_list[i].value[1] == '') {
                    this.ruleForm.setting.time_list[i].value = [new Date(2020, 10, 10, 8, 0), new Date(2020, 10, 10, 23, 0)];
                }
            },

            deleteTime(index) {
                this.ruleForm.setting.time_list.splice(index,1)
            },

            chooseSetting(setting) {
                this.active_setting = setting;
            },

            loadData() {
                this.loading = true;
                request({
                    params: {
                        r: 'mall/index/setting',
                    },
                }).then(e => {
                    this.loading = false;
                    if (e.data.code === 0) {
                        this.ruleForm = e.data.data.detail;
                        let setting = this.ruleForm.setting;
                        this.ruleForm.setting.latitude_longitude = setting.latitude + ',' + setting.longitude;
                        this.initMap();
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            getSvip() {
                request({
                    params: {
                        r: 'mall/mall-member/vip-card-permission',
                    },
                }).then(e => {
                    if (e.data.code === 0) {
                        this.is_svip = true;
                    }else {
                        this.is_svip = false;
                    }
                })
            },
            submit(formName) {
                if(this.ruleForm.setting.open_type == 2 && this.ruleForm.setting.week_list.length == 0) {
                    this.$message.error('请选择营业时间');
                    return false
                }
                this.$refs[formName].validate((valid,mes) => {
                    if (valid) {
                        this.submitLoading = true;
                        request({
                            params: {
                                r: 'mall/index/setting',
                            },
                            method: 'post',
                            data: {
                                ruleForm: JSON.stringify(this.ruleForm)
                            },
                        }).then(e => {
                            this.submitLoading = false;
                            if (e.data.code === 0) {
                                this.$message.success(e.data.msg);
                            } else {
                                this.$message.error(e.data.msg);
                            }
                        }).catch(e => {
                        });
                    } else {
                        //test
                        this.$message.error(Object.values(mes).shift().shift().message);
                    }
                });
            },
            handleClick(tab, event) {
                console.log(tab, event);
            },
            secondHandleClick(tab, event) {
                if (tab.name == 'first') {
                    this.active_setting = 'is_show_cart';
                } else if (tab.name == 'second') {
                    this.active_setting = 'is_underline_price';
                } else if (tab.name == 'third') {
                    this.active_setting = 'is_must_login';
                }
            },
            //
            mallLogoPic(e) {
                if (e.length) {
                    this.ruleForm.setting.mall_logo_pic = e[0].url;
                }
            },
            sharePic(e) {
                if (e.length) {
                    this.ruleForm.setting.share_pic = e[0].url;
                }
            },
            removeMallLoGoPic() {
                this.ruleForm.setting.mall_logo_pic = '';
            },
            removeSharePic() {
                this.ruleForm.setting.share_pic = '';
            },
            //客服图标
            customerServicesPic(e) {
                if (e.length) {
                    this.ruleForm.setting.customer_services_pic = e[0].url;
                }
            },
            //拨号图标
            dialPic(e) {
                if (e.length) {
                    this.ruleForm.setting.dial_pic = e[0].url;
                }
            },
            //客服外链图标
            webServicePic(e) {
                if (e.length) {
                    this.ruleForm.setting.web_service_pic = e[0].url;
                }
            },
            //快捷导航图标(展开)
            quickNavigationOpenedPic(e) {
                if (e.length) {
                    this.ruleForm.setting.quick_navigation_opened_pic = e[0].url;
                }
            },
            //快捷导航图标(收起)
            quickNavigationClosedPic(e) {
                if (e.length) {
                    this.ruleForm.setting.quick_navigation_closed_pic = e[0].url;
                }
            },
            //客服外链图标
            smallAppPic(e) {
                if (e.length) {
                    this.ruleForm.setting.small_app_pic = e[0].url;
                }
            },
            //一键导航图标
            quickMapPic(e) {
                if (e.length) {
                    this.ruleForm.setting.quick_map_pic = e[0].url;
                }
            },
            quickHomePic(e) {
                if (e.length) {
                    this.ruleForm.setting.quick_home_pic = e[0].url;
                }
            },
            sellOutPic(e) {
                if (e.length) {
                    this.ruleForm.setting.sell_out_pic = e[0].url;
                    this.ruleForm.setting.is_use_stock = '0'
                }
            },
            sellOutOtherPic(e) {
                if (e.length) {
                    this.ruleForm.setting.sell_out_other_pic = e[0].url;
                    this.ruleForm.setting.is_use_stock = '0'
                }
            },
            //地图确定事件
            mapEvent(e) {
                let self = this;
                self.ruleForm.setting.latitude_longitude = e.lat + ',' + e.long;
                self.ruleForm.setting.longitude = e.long;
                self.ruleForm.setting.latitude = e.lat;
                self.ruleForm.setting.quick_map_address = e.address;
            },

            selectQuickCustomize(e) {
                e.map((item, index) => {
                    this.ruleForm.setting.quick_customize_link_url = item.new_link_url;
                    this.ruleForm.setting.quick_customize_open_type = item.open_type;
                    this.ruleForm.setting.quick_customize_new_params = item.params;
                });
            },
            quickCustomizePic(e) {
                if (e.length) {
                    this.ruleForm.setting.quick_customize_pic = e[0].url;
                    this.ruleForm.setting.is_quick_customize = '0';
                }
            },
            resetImg(type) {
                const host = "<?php echo \Yii::$app->request->hostInfo . \Yii::$app->request->baseUrl."/" ?>";
                if (type == 'quick_navigation_opened_pic') {
                    this.ruleForm.setting.quick_navigation_opened_pic = host + 'statics/img/mall/quick_navigation_opened_pic.png';
                } else if (type == 'quick_navigation_closed_pic') {
                    this.ruleForm.setting.quick_navigation_closed_pic = host + 'statics/img/mall/quick_navigation_closed_pic.png';
                } else if (type == 'small_app_pic') {
                    this.ruleForm.setting.small_app_pic = host + 'statics/img/mall/small_app_pic.png';
                } else if (type == 'quick_map_pic') {
                    this.ruleForm.setting.quick_map_pic = host + 'statics/img/mall/quick_map_pic.png';
                } else if (type == 'web_service_pic') {
                    this.ruleForm.setting.web_service_pic = host + 'statics/img/mall/web_service_pic.png';
                } else if (type == 'dial_pic') {
                    this.ruleForm.setting.dial_pic = host + 'statics/img/mall/dial_pic.png';
                } else if (type == 'quick_home_pic') {
                    this.ruleForm.setting.quick_home_pic = host + 'statics/img/mall/quick_home_pic.png';
                } else if (type == 'customer_services_pic') {
                    this.ruleForm.setting.customer_services_pic = host + 'statics/img/mall/customer_services_pic.png';
                } else if (type == 'quick_customize') {
                    this.ruleForm.setting.quick_customize_pic = '';
                } else if (type == 'mall_logo_pic') {
                    this.ruleForm.setting.mall_logo_pic = host + 'statics/img/mall/poster-big-shop.png';
                }
            },

            set_send_type(data, index) {
                this.send_type_dialogVisible = true;
                this.send_type_item = {
                    item : JSON.parse(JSON.stringify(data)),
                    index: index
                };
            },

            sureSendType() {
                this.send_type_dialogVisible = false;
                this.ruleForm.setting.send_type_desc[this.send_type_item.index].modify = this.send_type_item.item.modify;
            },

            btnClick(data) {
                this.goods_line_price += data;
            },
            getMembers() {
                this.memberDialogVisible = true;
                this.memberListLoading = true;
                request({
                    params: {
                        r: 'mall/mall-member/index',
                        page: this.memberPage,
                        keyword: this.memberKeyword
                    },
                    method: 'get',
                }).then(e => {
                    this.memberListLoading = false;
                    if (e.data.code === 0) {
                        this.memberList = e.data.data.list;
                        this.memberPageCount = e.data.data.pagination.page_count;
                        this.memberCurrentPage = e.data.data.pagination.current_page;
                    } else {
                        this.$message.error(e.data.msg);
                    }
                }).catch(e => {
                });
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
            },
            memberPagination(currentPage) {
                this.memberPage = currentPage;
                this.getMembers();
            },
            memberSubmit() {
                let self = this;
                let memberList = self.ruleForm.setting.video_number_member_list;
                self.multipleSelection.forEach(function(item) {
                    let sign = true;
                    memberList.forEach(function(item2) {
                        if (item.id == item2.id) {
                            sign = false;
                        }
                    })

                    if (sign) {
                        memberList.push({
                            id: item.id,
                            level: item.level,
                            name: item.name
                        })
                    }
                });

                self.memberDialogVisible = false;
            },
            memberHandleClose(index) {
                let self = this;
                let memberList = self.ruleForm.setting.video_number_member_list;
                memberList.splice(index, 1);
            },
            getTemplates() {
                this.templateDialogVisible = true;
            },
            mouseover(item) {
                this.currentTemplateId = item.id;
            },
            mouseleave() {
                this.currentTemplateId = null;
            },
            templateSubmit(item) {
                this.ruleForm.setting.video_number_template_list = [];
                this.ruleForm.setting.video_number_template_list.push({
                    id: item.id,
                    name: item.name
                });
                this.templateDialogVisible = false;
                this.previewDialogVisible = false;
            },
            templateHandleClose(index) {
                let templateList = this.ruleForm.setting.video_number_template_list;
                templateList.splice(index, 1);
            },
            templatePreview(item) {
                this.previewDialogVisible = true;
                this.currentTemplate = item;
            },
            openDialog2() {
                this.dialogVisible2 = true;
            },
        },
        computed: {
            appTip() {
                let setting = this.ruleForm.setting;
                return `background-color:${setting.add_app_bg_color};` +
                    `opacity:${setting.add_app_bg_transparency / 100};` +
                    `border-radius:${setting.add_app_bg_radius}px;` +
                    `border-color: transparent transparent ${setting.add_app_bg_color} transparent;` +
                    `color:${setting.add_app_text_color}`;
            },
            send_type_list() {
                let list = [];
                for (let i in this.ruleForm.setting.send_type) {
                    if (this.ruleForm.setting.send_type[i] == 'express') {
                        list.push('快递配送');
                    }
                    if (this.ruleForm.setting.send_type[i] == 'offline') {
                        list.push('到店自提');
                    }
                    if (this.ruleForm.setting.send_type[i] == 'city') {
                        list.push('同城配送');
                    }
                }
                return list;
            }
        },
    });
</script>
