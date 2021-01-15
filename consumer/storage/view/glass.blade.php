@extends('layouts.app')
@section('style')
<style>
.table thead tr th,.table thead tr td {text-align:right;vertical-align:middle;}
.table tbody tr td {text-align:right;}
.table thead tr th:first-child,.table tbody tr td:first-child {text-align:center;}
</style>
@endsection
@section('title', '玻璃库存')

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
			<th>日期</th>
			<th>库存（万重箱）</th>
			<th>库存增长（万重箱）</th>
			<th>在产产能（万重箱）</th>
			<th>同比去年增加产量（万重箱）</th>
			<th>主力合约收盘价（元 / 吨）</th>
		  </tr>	  
		</thead>
		<tbody>
			<tr v-if="loading">
				<td colspan="6">
					<div class="spinner-border text-light mx-auto" role="status" style="display:block;">
					  <span class="sr-only">Loading...</span>
					</div>
				</td>
			</tr>		
			<tr v-for="item in list">
				<td>@{{item.date}}</td>
				<td>@{{item.inventory}}</td>
				<td>@{{item.inventory_increase}}</td>
				<td>@{{item.production}}</td>
				<td>@{{item.yoy_production}}</td>
				<td>@{{item.contract_price}}</td>
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
function generate_chart(xAxis_cat,glass_stock_list,price_list,inventory_increase_list)
{
	var chart = {
		zoomType: 'xy'
	};	
	var title = {
	  text: '玻璃库存'   
	};
	var subtitle = {
	  text: 'Source: www.glassqihuo.com'
	};
	var xAxis = {
	  categories: xAxis_cat
	};
	var yAxis = [{
	  title: {
		 text: '单位：万重箱',
         style: {
            color: Highcharts.getOptions().colors[0]
         }		 
	  },
      labels: {
         format: '{value}',
         style: {
            color: Highcharts.getOptions().colors[0]
         }
      }
	},{ // 第二条Y轴
      title: {
         text: '元 / 吨',
         style: {
            color: Highcharts.getOptions().colors[1]
         }
      },
      labels: {
         format: '{value}',
         style: {
            color: Highcharts.getOptions().colors[1]
         }
      },
	  opposite: true
   },{ // 第三条Y轴
      title: {
         text: '单位：万重箱',
         style: {
            color: Highcharts.getOptions().colors[2]
         }
      },
      labels: {
         format: '{value}',
         style: {
            color: Highcharts.getOptions().colors[2]
         }
      },
	  opposite: true
   }];   

	var tooltip = {
		shared: true
	}

	var legend = {
		layout: 'vertical',
		align: 'right',
		verticalAlign: 'middle',
		borderWidth: 0
	};

	var series =  [
		{
			 name: '库存',
			 type: 'spline',
			 data: glass_stock_list
		},
		{
			name: '主力合约价格',
			type: 'spline',
			yAxis: 1,
			data: price_list
		},
		{
			name: '库存增长',
			type: 'spline',
			yAxis: 2,
			data: inventory_increase_list
		}		
	];

	var json = {};
	json.chart = chart; 
	json.title = title;
	json.subtitle = subtitle;
	json.xAxis = xAxis;
	json.yAxis = yAxis;
	json.tooltip = tooltip;
	json.legend = legend;
	json.series = series;

	$('#graph').highcharts(json);	
}
// 创建根实例
new Vue({
	el: '#container',
	data() {
		return {
			url:'http://129.226.134.90:9502/glassactivities/inventory',
			list: null,
			flag: true,
			loading:true,
			total_page:1,
			page:1,
			limit:20
		}
	},
	methods: {
		greet: function (event) {
			this.flag = !this.flag
		},
		draw: function(response) {
			let xAxis_cat = new Array();
			let glass_stock_list = new Array();	
			let price_list = new Array();
			let inventory_increase_list = new Array();	
			let result = response.data;
			this.list = result.list;
			this.page = result.page;
			this.total_page = result.total_page;
			this.loading = false;
			for(let i = 0; i < this.list.length; i++)
			{
				xAxis_cat.push(this.list[i].date);
				glass_stock_list.push(parseInt(this.list[i].inventory));
				price_list.push(parseInt(this.list[i].contract_price));
				inventory_increase_list.push(parseFloat(this.list[i].inventory_increase));				
			}
			generate_chart(xAxis_cat.reverse(),glass_stock_list.reverse(),price_list.reverse(),inventory_increase_list.reverse());
			
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