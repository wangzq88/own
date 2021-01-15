@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:center;vertical-align:middle;}
.table tbody tr td {text-align:right;}
.table tbody tr td:first-child {text-align:center;}
</style>
@endsection
@section('title', $title)

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
			<th>年份</th>
			<th>人均可支配收入</th>
			<th>收入增幅</th>
			<th>人均消费支付出</th>
			<th>支出增幅</th>
			<th>人均收支结余</th>
			<th>城镇常住人口</th>
			<th>城镇居民总收支结余</th>
		  </tr>	  
		</thead>
		<tbody>
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.city_income}}</td>
				<td>@{{item.city_income_increase}}%</td>
				<td>@{{item.city_expenses}}</td>
				<td>@{{item.city_expenses_increase}}%</td>
				<td>@{{item.city_left}}</td>
				<td>@{{item.city_population}}</td>
				<td>@{{item.all_city_left}}</td>
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

function generate_chart(xAxis_cat,city_income_list,city_expenses_list,city_left_list,city_income_increase_list,city_expenses_increase_list,all_city_left_list)
{
	var chart = {
		zoomType: 'xy'
	};
	var title = {
		text: '{{$title}}'   
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
			format: '{value}元',
			style: {
				color: Highcharts.getOptions().colors[6]
			}
		},		
		title: {
			text: '单位（元）',
			style: {
				color: Highcharts.getOptions().colors[6]
			}			
		}      
	},{ // 第二条Y轴
      title: {
         text: '百分比',
         style: {
            color: Highcharts.getOptions().colors[8]
         }
      },
      labels: {
         format: '{value} %',
         style: {
            color: Highcharts.getOptions().colors[8]
         }
      },
	  opposite: true
   },{ // Tertiary yAxis
		gridLineWidth: 0,
		title: {
			text: '单位（亿）',
			style: {
				color: Highcharts.getOptions().colors[5]
			}
		},
		labels: {
			format: '{value} 亿',
			style: {
				color: Highcharts.getOptions().colors[5]
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
		name: '人均可支配收入',
		type: 'column',
		data: city_income_list,
		tooltip: {
            valueSuffix: ' 元'
        }		
	}, {
		name: '人均消费支出',
		type: 'column',
		data: city_expenses_list,
		tooltip: {
            valueSuffix: ' 元'
        }		
	}, {
		name: '人均收支结余',
		type: 'column',
		data: city_left_list,
		tooltip: {
            valueSuffix: ' 元'
        }		
	}, {
		name: '收入增幅',
		type: 'spline',
		yAxis: 1,
		data: city_income_increase_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '支出增幅',
		type: 'spline',
		yAxis: 1,
		data: city_expenses_increase_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '总收支结余',
		type: 'column',
		yAxis: 2,
		data: all_city_left_list,
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
			url:'http://129.226.134.90:9502/incomeexpenditure/list',
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
			let city_income_list = new Array();
			let city_expenses_list = new Array();
			let city_left_list = new Array();
			let city_income_increase_list = new Array();
			let city_expenses_increase_list = new Array();
			let all_city_left_list = new Array();					
			let result = response.data;
			this.list = result.list;
			this.page = result.page;
			this.total_page = result.total_page;
			for(let i = 0; i < this.list.length; i++)
			{
				xAxis_cat.push(this.list[i].date);
				city_income_list.push(parseFloat(this.list[i].city_income));
				city_expenses_list.push(parseFloat(this.list[i].city_expenses));
				city_left_list.push(parseFloat(this.list[i].city_left));
				city_income_increase_list.push(parseFloat(this.list[i].city_income_increase));
				city_expenses_increase_list.push(parseFloat(this.list[i].city_expenses_increase));
				all_city_left_list.push(parseFloat(this.list[i].all_city_left));
			}
			generate_chart(xAxis_cat.reverse(),city_income_list.reverse(),city_expenses_list.reverse(),city_left_list.reverse(),city_income_increase_list.reverse(),city_expenses_increase_list.reverse(),all_city_left_list.reverse());
			
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