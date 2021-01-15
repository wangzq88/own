<!DOCTYPE html>
<html>
    <head>
		<!-- 新 Bootstrap4 核心 CSS 文件 -->
		<link rel="stylesheet" href="/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
		<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
		<script src="/jquery/3.2.1/jquery.min.js"></script>
		<!-- bootstrap.bundle.min.js 用于弹窗、提示、下拉菜单，包含了 popper.min.js -->
		<script src="/popper.js/1.15.0/umd/popper.min.js"></script>
		<!-- 最新的 Bootstrap4 核心 JavaScript 文件 -->
		<script src="/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<script src="/vue/2.4.2/vue.min.js"></script>
		<script src="/vue-router/2.7.0/vue-router.min.js"></script>
		<script src="/axios/dist/axios.min.js"></script>
		<script src="/highcharts.js"></script>
        <script src="/highcharts/modules/exporting.js"></script>
        <script src="/highcharts-plugins/highcharts-zh_CN.js"></script>
        <script src="/highcharts/themes/dark-unica.js"></script>		
        <title>@yield('title')</title>
		<style>
		body{background-color: #111;}
		.breadcrumb{background-color:#343a40!important;}
		.breadcrumb-item a{color:#fff;}
		.breadcrumb-item.active {color: #adb5bd;}		
		.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
			background-color: #343a40;
			border: 1px solid #454d55;
		}
		a.nav-link {
			color: #fff;
		}		
		.page-link {
			color: #fff;
			background-color: #343a40;
			border: 1px solid #454d55;
		}
		.page-item.active .page-link {
			background-color: #454d55;
			border-color: #454d55;
		}		
		</style>
		@yield('style')
    </head>
    <body>	
		<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		  <!-- Brand -->
		  <a class="navbar-brand" href="/">首页</a>

		  <!-- Links -->
		  <ul class="navbar-nav">
			<li class="nav-item">
			  <a class="nav-link" href="#">Link 1</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="#">Link 2</a>
			</li>

			<!-- Dropdown -->
			<li class="nav-item dropdown">
			  <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
				投资品种
			  </a>
			  <div class="dropdown-menu">
				<a class="dropdown-item" href="/index/glass">玻璃</a>
				<a class="dropdown-item" href="#">Link 2</a>
				<a class="dropdown-item" href="#">Link 3</a>
			  </div>
			</li>
		  </ul>
		</nav>
        <div class="container-fluid" id="container" style="margin-top:30px">
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="/">首页</a></li>
				@foreach ($breadcrumb as $bre)
					@if (empty($bre['href']))
					<li class="breadcrumb-item active">{{$bre['text']}}</li>
					@else
					<li class="breadcrumb-item"><a href="{{$bre['href']}}">{{$bre['text']}}</a></li>
					@endif	
				@endforeach
			</ul>
			
			<div class="row">
				<div class="col">
					@section('sidebar')
					@show
				</div>	
			</div>
			<div class="row">
				@yield('content')	
			</div>					
        </div>
<script>
Vue.component('pagination', {
  props:{
	  url:String,
	  totalPage:Number,
	  page:Number,
	  limit:Number
  },
  methods:{
	jump: function(i) {
		this.page = i;
		axios.get(this.url, {
			params: {
				page:this.page,
				limit:this.limit
			}
		}).then(response => {
			this.$emit('draw',response)
		}).catch(function (error) {
			console.log(error);
		});			
	}		  
  },
  template: `
	<ul class="pagination">
		<li class="page-item"><a class="page-link" href="#">&lt;&lt;</a></li>
		<template v-if="totalPage > 10">
			<li v-bind:class="['page-item',page == 3 ? 'disabled' : '']" v-for="i in totalPage" v-if="page > 6">
				<a class="page-link" href="#" v-on:click="jump(i)" v-if="i <= 2">@{{i}}</a>
				<a class="page-link" href="#" v-on:click="jump(i)" v-else-if="i == 3">…</a>
			</li>		
			<li v-bind:class="['page-item',page == i ? 'active' : '']" v-for="i in totalPage" v-if="page > 6 && i >= page-3 && i <= page+3">
				<a class="page-link" href="#" v-on:click="jump(i)" >@{{i}}</a>
			</li>
			<li v-bind:class="['page-item']" v-for="i in totalPage" v-if="page > 6 && page+4 < totalPage && i >= totalPage-2">
				<a class="page-link" href="#" v-if="i == totalPage-2">…</a>
				<a class="page-link" href="#" v-on:click="jump(i)" v-else>@{{i}}</a>
			</li>	
			<li v-bind:class="['page-item']" v-for="i in totalPage" v-if="page > 6 && page+4 == totalPage && i == totalPage">
				<a class="page-link" href="#" v-on:click="jump(i)" >@{{i}}</a>
			</li>
			<li v-bind:class="['page-item',page == i ? 'active' : '']" v-for="i in totalPage" v-if="page <= 6 && i <= page+3">
				<a class="page-link" href="#" v-on:click="jump(i)" >@{{i}}</a>
			</li>	
			<li v-bind:class="['page-item']" v-for="i in totalPage" v-if="page <= 6 && page+4 < totalPage && i >= totalPage-2 && page+5 != totalPage">
				<a class="page-link" href="#" v-if="i == totalPage-2">…</a>
				<a class="page-link" href="#" v-on:click="jump(i)" v-else>@{{i}}</a>
			</li>
			<li v-bind:class="['page-item']" v-for="i in totalPage" v-if="page <= 6 && page+4 < totalPage && i >= totalPage-2 && page+5 == totalPage">
				<a class="page-link" href="#" v-on:click="jump(i)" >@{{i}}</a>
			</li>				
		</template>
		<template v-else>
			<li v-bind:class="['page-item',page == i ? 'active' : '']" v-for="i in totalPage">
				<a class="page-link" href="#" v-on:click="jump(i)">@{{i}}</a>
			</li>		
		</template>	
		<li class="page-item"><a class="page-link" href="#">&gt;&gt;</a></li>
	</ul>
  `
})		
</script>
		@yield('script')	
    </body>
</html>