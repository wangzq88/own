@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:center;vertical-align:middle;}
</style>
@endsection
@section('title', '财政收支数据演变')

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
			<th colspan="3">财政收入</th>
			<th colspan="3">财政支出</th>
			<th rowspan="2">总财政收入</th>
			<th rowspan="2">年度国债发行规模</th>
			<th rowspan="2">年度地方债发行规模</th>
		  </tr>	
		  <tr>
			<td>总收入</td>
			<td>其中：公共预算收入</td>
			<td>其中：政府性基金收入</td>
			<td>总支出</td>
			<td>其中：公共预算支出</td>
			<td>其中：政府性基金支出</td>
		  </tr>
		</thead>
		<tbody>
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.all_revenue}}</td>
				<td>@{{item.budget_revenue}}</td>
				<td>@{{item.fund_revenue}}</td>
				<td>@{{item.all_expenditure}}</td>
				<td>@{{item.budget_expenditure}}</td>
				<td>@{{item.fund_expenditure}}</td>
				<td>@{{item.income_expend}}</td>
				<td>@{{item.national_debt}}</td>
				<td>@{{item.local_debt}}</td>
			</tr>
		</tbody>
	  </table>
	</div> 
	<pagination v-bind:url="url" v-bind:total-page="total_page" v-bind:page="page" v-bind:limit="limit" v-on:draw="draw"></pagination>		
</div>
<div class="col" v-show="!flag" style="margin-top:15px">
	<div id="graph" style="min-width:960px; margin:0">>这是主体内容。</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
var result = new Array();
var xAxis_cat = new Array();
var budget_revenue_list = new Array();
var fund_revenue_list = new Array();
var budget_expenditure_list = new Array();
var fund_expenditure_list = new Array();
var income_expend_list = new Array();
	
function generate_chart(xAxis_cat,budget_revenue_list,fund_revenue_list,budget_expenditure_list,fund_expenditure_list,income_expend_list)
{
	var chart = {
		type: 'column'
	};
	var title = {
		text: '财政收支数据演变'   
	};
	var subtitle = {
		text: 'Source: www.mof.gov.cn'  
	};	
	var xAxis = {
		categories: xAxis_cat
	};
	var yAxis ={
	  allowDecimals: false,
	  title: {
		text: '单位：亿'
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
		 stacking: 'normal',
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
                this.series.name + ': ' + this.y + '<br/>' +
                '总量: ' + this.point.stackTotal;
        }
    };
	var credits = {
	  enabled: false
	};
	var series= [{
			name: '公共预算收入',
			data: budget_revenue_list,
			stack: 'revenue'
		}, {
			name: '政府性基金收入',
			data: fund_revenue_list,
			stack: 'revenue'
		}, {
			name: '公共预算支出',
			data: budget_expenditure_list,
			stack: 'expenditure'
		}, {
			name: '政府性基金支出',
			data: fund_expenditure_list,
			stack: 'expenditure'
		}, {
			name: '财政收支',
			data: income_expend_list,
			stack: 'income_expend'			
	}];     
	  
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
// 创建根实例
new Vue({
	el: '#container',
	data() {
		return {
			url:'http://129.226.134.90:9502/fiscalrevexp/revexp',
			list: null,
			page:1,
			limit:20,
			total_page:1,	
			flag: true
		}
	},
	methods: {
		greet: function (event) {
			this.flag = !this.flag
		},
		draw: function (response) {
			let result = response.data;
			this.list = result;
			for(let i = 0; i < result.length; i++)
			{
				xAxis_cat.push(result[i].date);
				budget_revenue_list.push(parseFloat(result[i].budget_revenue));	
				fund_revenue_list.push(parseFloat(result[i].fund_revenue));
				budget_expenditure_list.push(parseFloat(result[i].budget_expenditure));
				fund_expenditure_list.push(parseFloat(result[i].fund_expenditure));	
				income_expend_list.push(parseFloat(result[i].income_expend));	
			}
			generate_chart(xAxis_cat,budget_revenue_list,fund_revenue_list,budget_expenditure_list,fund_expenditure_list,income_expend_list);
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