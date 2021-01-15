@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:center;vertical-align:middle;}
.table tbody tr td {text-align:right;}
.table tbody tr td:first-child {text-align:center;}
</style>
@endsection
@section('title', '历年固定资产资产')

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
			<th colspan="2">固定资产投资</th>
			<th colspan="2">其中第二产业投资</th>
			<th colspan="2">其中地产投资</th>
		  </tr>
		  <tr>
			<td>数值</td>
			<td>绝对值同比增幅</td>
			<td>数值</td>
			<td>绝对值同比增幅</td>
			<td>数值</td>
			<td>绝对值同比增幅</td>
		  </tr>		  
		</thead>
		<tbody>
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.not_farmers}}</td>
				<td>@{{item.not_farmers_increase}}%</td>
				<td>@{{item.secondary_industry}}</td>
				<td>@{{item.secondary_industry_increase}}%</td>
				<td>@{{item.real_estate}}</td>
				<td>@{{item.real_estate_increase}}%</td>
			</tr>
		</tbody>
	  </table>
	</div>  
	<pagination v-bind:url="url" v-bind:total-page="total_page" v-bind:page="page" v-bind:limit="limit" v-on:draw="draw"></pagination>	
</div>
<div class="col" v-show="!flag" style="margin-top:15px">
	<div id="graph" style="min-width:1080px; margin:0"></div>
</div>
@endsection

@section('script')
<script language="JavaScript">
		
const text = '{"channel":"catalog"}';

function generate_chart(xAxis_cat,not_farmers_list,secondary_industry_list,real_estate_list,not_farmers_rate_list,secondary_industry_rate_list,real_estate_rate_list)
{
	var chart = {
		zoomType: 'xy'
	};
	var title = {
		text: '历年固定资产资产'   
	};
	var subtitle = {
		text: 'Source: www.stats.gov.cn'  
	};
	var xAxis = {
		categories: xAxis_cat,
		crosshair: true
	};
	var yAxis = [{
		allowDecimals: false,
		labels: {
			format: '{value}亿',
			style: {
				color: Highcharts.getOptions().colors[5]
			}
		},		
		title: {
			text: '单位（亿）',
			style: {
				color: Highcharts.getOptions().colors[5]
			}			
		}      
	},{ // 第二条Y轴
      title: {
         text: '百分比',
         style: {
            color: Highcharts.getOptions().colors[6]
         }
      },
      labels: {
         format: '{value} %',
         style: {
            color: Highcharts.getOptions().colors[6]
         }
      },
      opposite: true
   }];
	var tooltip = {
		shared: true
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
	var credits = {
	  enabled: false
	};

	var series= [{
		name: '固定资产投资',
		type: 'column',
		data: not_farmers_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '第二产业投资',
		type: 'column',
		data: secondary_industry_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '地产投资',
		type: 'column',
		data: real_estate_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '固定资产投资同比增幅',
		type: 'spline',
		yAxis: 1,
		data: not_farmers_rate_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '第二产业投资同比增幅',
		type: 'spline',
		yAxis: 1,
		data: secondary_industry_rate_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '地产投资同比增幅',
		type: 'spline',
		yAxis: 1,
		data: real_estate_rate_list,
		tooltip: {
            valueSuffix: '%'
        }
	}];     
  
	var json = {};   
	json.chart = chart; 
	json.title = title;   
	json.subtitle = subtitle; 
	json.tooltip = tooltip;
	json.xAxis = xAxis;
	json.yAxis = yAxis;  
	json.series = series;
	json.plotOptions = plotOptions;  
	json.credits = credits;

	$('#graph').highcharts(json);
}

// 创建根实例
new Vue({
	el: '#container',
	data() {
		return {
			url:'http://129.226.134.90:9502/assetinvestment/list',
			list: null,
			flag: true,
			total_page:1,
			page:1,
			limit:10
		}
	},
	methods: {
		greet: function (event) {
			this.flag = !this.flag
		},
		draw: function(response) {
			let xAxis_cat = new Array();
			let not_farmers_list = new Array();
			let secondary_industry_list = new Array();
			let real_estate_list = new Array();
			let not_farmers_rate_list = new Array();
			let secondary_industry_rate_list = new Array();
			let real_estate_rate_list = new Array();					
			let result = response.data;
			this.list = result.list;
			this.page = result.page;
			this.total_page = result.total_page;
			for(let i = 0; i < this.list.length; i++)
			{
				xAxis_cat.push(this.list[i].date);
				not_farmers_list.push(parseFloat(this.list[i].not_farmers));
				secondary_industry_list.push(parseFloat(this.list[i].secondary_industry));
				real_estate_list.push(parseFloat(this.list[i].real_estate));
				not_farmers_rate_list.push(parseFloat(this.list[i].not_farmers_increase));
				secondary_industry_rate_list.push(parseFloat(this.list[i].secondary_industry_increase));
				real_estate_rate_list.push(parseFloat(this.list[i].real_estate_increase));
			}
			generate_chart(xAxis_cat.reverse(),not_farmers_list.reverse(),secondary_industry_list.reverse(),real_estate_list.reverse(),not_farmers_rate_list.reverse(),secondary_industry_rate_list.reverse(),real_estate_rate_list.reverse());
			
		}	
    },	
	mounted () {
		axios.get(this.url, {
			params: {
				page:this.page,
				limit:this.limit
			}
		}).then(response => {
			this.draw(response);
		}).catch(function (error) {
			console.log(error);
		});
	}
})

</script>
@endsection	