@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:center;vertical-align:middle;}
.table tbody tr td {text-align:right;}
.table thead tr th:first-child,.table tbody tr td:first-child {text-align:center;}
</style>
@endsection
@section('title', '中国吸纳外资全口径数据演变')

@section('sidebar')
    @parent
	<ul class="nav nav-pills">
		<li class="nav-item">
			<a class="nav-link" href="javascript:void(0);" v-on:click="greet" v-bind:class="{ 'active': flag }">表格</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="javascript:void(0);" v-on:click="greet" v-bind:class="{ 'active': !flag }">图表</a>
		</li>
	</ul>	
@endsection

@section('content')
<div class="col" v-show="flag" style="margin-top:15px">
	<div class="table-responsive">
	 <table class="table table-dark table-hover table-bordered table-striped">
		<thead>
			<tr>
			<th rowspan="2">年份</th>
			<th colspan="6">历年服务及货物贸易进出口数据</th>
			<th rowspan="2">全口径贸易顺差</th>
			<th colspan="2">历年利用外资及对外投资数据</th>
			<th rowspan="2">利用外资净头寸</th>
			<th rowspan="2">吸纳外资净头寸汇总</th>
		  </tr>	
		  <tr>
			<td>服务出口</td>
			<td>服务进口</td>
			<td>服务贸易逆差</td>
			<td>货物出口</td>
			<td>货物进口</td>
			<td>货物贸易顺差</td>
			<td>利用外资</td>
			<td>对外投资</td>
		  </tr>
		</thead>
		<tbody>
			<tr v-if="loading">
				<td colspan="12">
					<div class="spinner-border text-light mx-auto" role="status" style="display:block;">
					  <span class="sr-only">Loading...</span>
					</div>
				</td>
			</tr>
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.service_export}}</td>
				<td>@{{item.service_import}}</td>
				<td>@{{item.all_service}}</td>
				<td>@{{item.goods_export}}</td>
				<td>@{{item.goods_import}}</td>
				<td>@{{item.all_goods}}</td>
				<td>@{{item.all_service_goods}}</td>
				<td>@{{item.utilize_foreign}}</td>
				<td>@{{item.foreign_investment}}</td>
				<td>@{{item.all_foreign}}</td>
				<td>@{{item.income_expend}}</td>
			</tr>
		</tbody>
	  </table>
	</div>  
	<pagination v-bind:url="url" v-bind:total-page="total_page" v-bind:page="page" v-bind:limit="limit" v-on:draw="draw"></pagination>		
</div>
<div class="col" v-show="!flag" style="margin-top:15px">
	<div id="graph" style="min-width:1080px; margin:0">贸易</div>
	<div id="graph2" style="min-width:960px; margin:15px 0 0 0">外资</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
var result = new Array();
var xAxis_cat = new Array();
var service_export_list = new Array();
var service_import_list = new Array();
var goods_export_list = new Array();
var goods_import_list = new Array();
var all_service_list = new Array();
var all_goods_list = new Array();
var all_utilize_list = new Array();
var all_investment_list = new Array();
var all_foreign_list = new Array();
	
function generate_chart(xAxis_cat,service_export_list,service_import_list,goods_export_list,goods_import_list,all_service_list,all_goods_list)
{
	var chart = {
		type: 'column'
	};
	var title = {
		text: '历年服务及货物贸易进出口数据'   
	};
	var subtitle = {
		text: 'Source: www.safe.gov.cn | www.customs.gov.cn'  
	};		
	var xAxis = {
		categories: xAxis_cat
	};
	var yAxis ={
	  allowDecimals: false,
	  title: {
		text: '单位：亿美元'
	  },
      stackLabels: {
        enabled: true,
        style: {
           fontWeight: 'bold',
           color: (Highcharts.theme && Highcharts.theme.textColor) || 'white'
        }
      }	  
	}; 
	var plotOptions = {
      column: {
         pointPadding: 0.2,
         borderWidth: 0,
         dataLabels: {
            enabled: true,
            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
            style: {
               textShadow: '0 0 3px black'
            }
         }			 
      }
	};
    var tooltip = {
        formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
                this.series.name + ': ' + this.y;
        }
    };
	var credits = {
	  enabled: false
	};
	var series= [
		{
			name: '服务出口',
			data: service_export_list
		}, {
			name: '服务进口',
			data: service_import_list
		}, {
			name: '服务贸易逆差',
			data: all_service_list
		}, {
			name: '货物出口',
			data: goods_export_list
		}, {
			name: '货物进口',
			data: goods_import_list		
		}, {
			name: '货物贸易顺差',
			data: all_goods_list
		}	
	];     
	  
	var json = {};   
	json.chart = chart; 
	json.title = title;
	json.subtitle = subtitle; 	
	json.xAxis = xAxis;
	json.yAxis = yAxis;  
	json.plotOptions = plotOptions;
	json.tooltip = tooltip;
	json.credits = credits;
	json.series = series;
	$('#graph').highcharts(json);			
}

function generate_chart2(xAxis_cat,all_utilize_list,all_investment_list,all_foreign_list)
{
	var chart = {
		type: 'column'
	};
	var title = {
		text: '历年利用外资及对外投资数据'   
	};
	var subtitle = {
		text: 'Source: www.mofcom.gov.cn'  
	};		
	var xAxis = {
		categories: xAxis_cat
	};
	var yAxis ={
	  allowDecimals: false,
	  title: {
		text: '单位：亿美元'
	  },
      stackLabels: {
        enabled: true,
        style: {
           fontWeight: 'bold',
           color: (Highcharts.theme && Highcharts.theme.textColor) || 'white'
        }
      }	  
	}; 
	var plotOptions = {
      column: {
         pointPadding: 0.2,
         borderWidth: 0,
         dataLabels: {
            enabled: true,
            color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
            style: {
               textShadow: '0 0 3px black'
            }
         }			 
      }
	};
    var tooltip = {
        formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
                this.series.name + ': ' + this.y;
        }
    };
	var credits = {
	  enabled: false
	};
	var series= [
		{
			name: '利用外资',
			data: all_utilize_list
		}, {
			name: '对外投资',
			data: all_investment_list
		}, {
			name: '利用外资净头寸',
			data: all_foreign_list
		}	
	];     
	  
	var json = {};   
	json.chart = chart; 
	json.title = title;
	json.subtitle = subtitle; 
	json.xAxis = xAxis;
	json.yAxis = yAxis;  
	json.plotOptions = plotOptions;
	json.tooltip = tooltip;
	json.credits = credits;
	json.series = series;
	$('#graph2').highcharts(json);			
}
// 创建根实例
new Vue({
	el: '#container',
	data() {
		return {
			list: null,
			flag: true,
			loading:true,
			url:'http://129.226.134.90:9502/importexport/index',
			page:1,
			limit:10,
			total_page:1			
		}
	},
	methods: {
		greet: function (event) {
			this.flag = !this.flag
		},
		draw: function (response) {
			let result = response.data;
			this.list = result;
			this.loading = false;
			for(let i = 0; i < result.length; i++)
			{
				xAxis_cat.push(result[i].date);
				service_export_list.push(parseFloat(result[i].service_export));	
				service_import_list.push(parseFloat(result[i].service_import));
				goods_export_list.push(parseFloat(result[i].goods_export));
				goods_import_list.push(parseFloat(result[i].goods_import));	
				all_service_list.push(parseFloat(result[i].all_service));
				all_goods_list.push(parseFloat(result[i].all_goods));	
				all_utilize_list.push(parseFloat(result[i].utilize_foreign));
				all_investment_list.push(parseFloat(result[i].foreign_investment));
				all_foreign_list.push(parseFloat(result[i].all_foreign));
			}
			generate_chart(xAxis_cat,service_export_list,service_import_list,goods_export_list,goods_import_list,all_service_list,all_goods_list);
			generate_chart2(xAxis_cat,all_utilize_list,all_investment_list,all_foreign_list);
		} 		
    },	
	mounted () {
		axios.get(this.url, {
			params: {
				page:this.page,
				limit:this.limit
			}
		})
		.then(response => {
			this.draw(response);
		})
		.catch(function (error) {
			console.log(error);
		});
	}
})
</script>
@endsection		