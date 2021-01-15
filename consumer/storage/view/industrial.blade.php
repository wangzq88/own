@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:center;vertical-align:middle;}
.table tbody tr td {text-align:right;}
.table tbody tr td:first-child {text-align:center;}
</style>
@endsection
@section('title', '三类规模以上工业企业利润增幅演变')

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
			<th colspan="2">规模以上工业企业利润</th>
			<th colspan="2">其中民企</th>
			<th colspan="2">其中外企</th>
			<th colspan="2">其中国企</th>
		  </tr>
		  <tr>
			<td>数值</td>
			<td>同比增幅</td>
			<td>数值</td>
			<td>同比增幅</td>
			<td>数值</td>
			<td>同比增幅</td>
			<td>数值</td>
			<td>同比增幅</td>			
		  </tr>		  
		</thead>
		<tbody>
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.industrial_profit}}</td>
				<td>@{{item.industrial_profit_increase}}%</td>
				<td>@{{item.private_profit}}</td>
				<td>@{{item.private_profit_increase}}%</td>
				<td>@{{item.foreign_profit}}</td>
				<td>@{{item.foreign_profit_increase}}%</td>
				<td>@{{item.stateowned_profit}}</td>
				<td>@{{item.stateowned_profit_increase}}%</td>				
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

function generate_chart(xAxis_cat,industrial_profit_list,stateowned_profit_list,private_profit_list,foreign_profit_list,industrial_profit_increase_list,stateowned_profit_increase_list,private_profit_increase_list,foreign_profit_increase_list)
{
	var chart = {
		zoomType: 'xy'
	};
	var title = {
		text: '三类规模以上工业企业利润增幅演变'   
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
				color: Highcharts.getOptions().colors[9]
			}
		},		
		title: {
			text: '单位（亿）',
			style: {
				color: Highcharts.getOptions().colors[9]
			}			
		}      
	},{ // 第二条Y轴
      title: {
         text: '百分比',
         style: {
            color: Highcharts.getOptions().colors[10]
         }
      },
      labels: {
         format: '{value} %',
         style: {
            color: Highcharts.getOptions().colors[10]
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
		name: '规模以上工业企业利润',
		type: 'column',
		data: industrial_profit_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '其中国企',
		type: 'column',
		data: stateowned_profit_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '其中民企',
		type: 'column',
		data: private_profit_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '其中外企',
		type: 'column',
		data: foreign_profit_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '同比增幅',
		type: 'spline',
		yAxis: 1,
		data: industrial_profit_increase_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '国企增幅',
		type: 'spline',
		yAxis: 1,
		data: stateowned_profit_increase_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '民企增幅',
		type: 'spline',
		yAxis: 1,
		data: private_profit_increase_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '外企增幅',
		type: 'spline',
		yAxis: 1,
		data: foreign_profit_increase_list,
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
			url:'http://129.226.134.90:9502/industrialprofit/list',
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
			let industrial_profit_list = new Array();
			let stateowned_profit_list = new Array();
			let private_profit_list = new Array();
			let foreign_profit_list = new Array();
			let industrial_profit_increase_list = new Array();
			let stateowned_profit_increase_list = new Array();
			let private_profit_increase_list = new Array();
			let foreign_profit_increase_list = new Array();	
			let result = response.data;
			this.list = result.list;
			this.page = result.page;
			this.total_page = result.total_page;
			for(let i = 0; i < this.list.length; i++)
			{
				xAxis_cat.push(this.list[i].date);
				industrial_profit_list.push(parseFloat(this.list[i].industrial_profit));
				stateowned_profit_list.push(parseFloat(this.list[i].stateowned_profit));
				private_profit_list.push(parseFloat(this.list[i].private_profit));
				foreign_profit_list.push(parseFloat(this.list[i].foreign_profit));
				industrial_profit_increase_list.push(parseFloat(this.list[i].industrial_profit_increase));
				stateowned_profit_increase_list.push(parseFloat(this.list[i].stateowned_profit_increase));
				private_profit_increase_list.push(parseFloat(this.list[i].private_profit_increase));
				foreign_profit_increase_list.push(parseFloat(this.list[i].foreign_profit_increase));
			}
			generate_chart(xAxis_cat.reverse(),industrial_profit_list.reverse(),stateowned_profit_list.reverse(),private_profit_list.reverse(),foreign_profit_list.reverse(),industrial_profit_increase_list.reverse(),stateowned_profit_increase_list.reverse(),private_profit_increase_list.reverse(),foreign_profit_increase_list.reverse());
			
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