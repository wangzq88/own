@extends('layouts.app')

@section('title', '央行历年资产表')

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
	 <table class="table table-dark table-hover table-striped">
		<thead>
		  <tr>
			<th>年份</th>
			<th>央行总资产（亿）</th>
			<th>其中外汇占款</th>
			<th>其中对商业银行债权</th>
			<th>外汇占款总资产比值</th>
			<th>对商业银行债权占总资产比值</th>
			<th>5年期以上贷款利率（%）</th>
			<th>兑美元汇率（期末）</th>
		  </tr>
		</thead>
		<tbody>
			<tr v-for="item in list">
				<td>@{{item.year}}</td>
				<td>@{{item.all_assets}}</td>
				<td>@{{item.foreign_exchange}}</td>
				<td>@{{item.blank_claims}}</td>
				<td>@{{item.foreign_exchange_rate}}%</td>
				<td>@{{item.blank_claims_rate}}%</td>
				<td>@{{item.lending_rates}}</td>
				<td>@{{item.exchange_rate}}</td>
			</tr>
		</tbody>
	  </table>
	</div>  
	<pagination v-bind:url="url" v-bind:total-page="total_page" v-bind:page="page" v-bind:limit="limit" v-on:draw="draw"></pagination>	
</div>
<div class="col" v-show="!flag" style="margin-top:15px">
	<div id="graph" style="min-width:960px; margin:0">贸易</div>
</div>
@endsection

@section('script')
<script language="JavaScript">
		
const text = '{"channel":"catalog"}';

function generate_chart(xAxis_cat,all_assets_list,foreign_exchange_list,blank_claims_list,foreign_exchange_rate_list,blank_claims_rate_list)
{
	var chart = {
		zoomType: 'xy'
	};
	var title = {
		text: '央行历年资产表'   
	};
	var subtitle = {
		text: 'Source: www.pbc.gov.cn'  
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
		name: '央行总资产',
		type: 'column',
		data: all_assets_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '其中外汇占款',
		type: 'column',
		data: foreign_exchange_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '其中对商业银行债权',
		type: 'column',
		data: blank_claims_list,
		tooltip: {
            valueSuffix: ' 亿'
        }		
	}, {
		name: '外汇占款总资产比值',
		type: 'spline',
		yAxis: 1,
		data: foreign_exchange_rate_list,
		tooltip: {
            valueSuffix: '%'
        }
	}, {
		name: '对商业银行债权占总资产比值',
		type: 'spline',
		yAxis: 1,
		data: blank_claims_rate_list,
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
			url:'http://129.226.134.90:9502/centralbank/assets',
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
			let all_assets_list = new Array();
			let foreign_exchange_list = new Array();
			let blank_claims_list = new Array();
			let foreign_exchange_rate_list = new Array();
			let blank_claims_rate_list = new Array();			
			let result = response.data;
			this.list = result.list;
			this.page = result.page;
			this.total_page = result.total_page;
			for(let i = 0; i < this.list.length; i++)
			{
				xAxis_cat.push(this.list[i].year);
				all_assets_list.push(parseFloat(this.list[i].all_assets));
				foreign_exchange_list.push(parseFloat(this.list[i].foreign_exchange));
				blank_claims_list.push(parseFloat(this.list[i].blank_claims));
				foreign_exchange_rate_list.push(parseFloat(this.list[i].foreign_exchange_rate));
				blank_claims_rate_list.push(parseFloat(this.list[i].blank_claims_rate));
			}
			generate_chart(xAxis_cat.reverse(),all_assets_list.reverse(),foreign_exchange_list.reverse(),blank_claims_list.reverse(),foreign_exchange_rate_list.reverse(),blank_claims_rate_list.reverse());
			
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