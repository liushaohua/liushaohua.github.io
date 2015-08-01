define(function(require) {
	
	var home = {};

    home.template = require('text!./index.html');

    home.beforeRender = function() {
		//在页面渲染之前执行，获取数据
		console.log('beforeRender');
	}

    home.initBehavior = function() {
		//在页面渲染之后执行，对页面进行操作
		console.log('initBehavior','uuu');
		// 使用
		$(document).trigger('Runner/hashChange');

		var Render = {
			init: function () {
				this.render_nav().setDate();
			},
			setDate : function () {
				$('.date_query a').click(function () {
					$(this).addClass('active').siblings().removeClass('active');
					var option = [{
						name:'周一',
						group:'最高气温',
						value:11
					},{
						name:'周二',
						group:'最高气温',
						value:11
					},{
						name:'周三',
						group:'最高气温',
						value:15
					},{
						name:'周四',
						group:'最高气温',
						value:13
					},{
						name:'周五',
						group:'最高气温',
						value:12
					},{
						name:'周六',
						group:'最高气温',
						value:13
					},{
						name:'周日',
						group:'最高气温',
						value:10
					},{
						name:'周一',
						group:'最低气温',
						value:1
					},{
						name:'周二',
						group:'最低气温',
						value:-3
					},{
						name:'周三',
						group:'最低气温',
						value:2
					},{
						name:'周四',
						group:'最低气温',
						value:5
					},{
						name:'周五',
						group:'最低气温',
						value:3
					},{
						name:'周六',
						group:'最低气温',
						value:2
					},{
						name:'周日',
						group:'最低气温',
						value:0
					}];
					var cOption = EchartsCof.ChartOptionTemplates.Lines(option,'hellow-cookie',true).series,
						data = Render.chartsData.myCharts.data.series;

					Render.chartsData.myCharts.data.series = $.extend({}, data, cOption);
					console.log(Render.chartsData.myCharts.data);

					Render.chartsData.myCharts.dom.setOption(Render.chartsData.myCharts.data);
					//Render.chartsData.myCharts.dom.refresh();
					window.onresize = Render.chartsData.myCharts.dom.resize;
				});
			},
			chartsData : {
				'myCharts': {
					'dom': '',
					'data': ''
				}
			},
			render_nav: function () {
				var cHTML = '<li>',
					data = [{
					'name': '日期'
				},{
					'name': '时段'
				},{
					'name': '业务线'
				},{
					'name': '日期'
				}];

				for (var i = 0, len = data.length; i < len; i++) {
					var bBOOL = ((i+1)% 6 == 0 && i+1 != len);
					cHTML += '<a href="javascript:;"'+ (bBOOL ? 'class="c-span-last"' : '')+'>' + data[i]['name'] +'</a>'
					      + ( bBOOL ? '</li><li>'  : '');
				}
					cHTML += '</li>';
				$('.query_wrap ul').html(cHTML);
				return this;
			}
		};

		Render.init();

		require(['echarts/echarts-all','echarts/chart/macarons'],
			function (ec,theme) {;
				var myChart;
				myChart = Render.chartsData.myCharts.dom = echarts.init(document.getElementById('main_wrap'),theme);

				myChart.showLoading({
					text: '正在努力的读取数据中...'
				});

				$.ajax({
					url: '/dashboard/netflow/ajax/mau',
					type: 'post',
					async: true,
					data:{"businessName":'3', 'indexType':''},
					dataType: 'json',
					success: function(data, textStatus) {
						myChart.hideLoading();
					},
					error : function() {
						//console.log(55);
						myChart.hideLoading();
					}
				});

				var option = [{
					name:'周一',
					group:'最高气温',
					value:11
				},{
					name:'周二',
					group:'最高气温',
					value:11
				},{
					name:'周三',
					group:'最高气温',
					value:15
				},{
					name:'周四',
					group:'最高气温',
					value:13
				},{
					name:'周五',
					group:'最高气温',
					value:12
				},{
					name:'周六',
					group:'最高气温',
					value:13
				},{
					name:'周日',
					group:'最高气温',
					value:10
				},{
					name:'周一',
					group:'最低气温',
					value:1
				},{
					name:'周二',
					group:'最低气温',
					value:-2
				},{
					name:'周三',
					group:'最低气温',
					value:2
				},{
					name:'周四',
					group:'最低气温',
					value:5
				},{
					name:'周五',
					group:'最低气温',
					value:3
				},{
					name:'周六',
					group:'最低气温',
					value:2
				},{
					name:'周日',
					group:'最低气温',
					value:0
				}];
				var cOption = EchartsCof.ChartOptionTemplates.Lines(option,'hellow-cookie',true);
				console.log(cOption,'wee');
				cOption = $.extend({}, cOption, {
					title : {
						text: '未来一周气温变化',
						subtext: '纯属虚构'
					},
					tooltip : {
						trigger: 'axis'
					},
					legend: {
						data:['最高气温','最低气温'],
						textStyle:{color: '#fff'}
					},
					xAxis : [
						{
							type : 'category',
							boundaryGap : false,
							data : ['周一','周二','周三','周四','周五','周六','周日'],
							axisLabel : {
								textStyle:{
									color:"#fff"
								}
							}
						}
					],
					yAxis : [
						{
							type : 'value',
							axisLabel : {
								formatter: '{value} °C',
								textStyle:{
									color:"#fff"
								}
							}
						}
					]
				});

				cOption['series'][0] = $.extend({}, cOption['series'][0], {
					markPoint : {
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					},
					markLine : {
						data : [
							{type : 'average', name: '平均值'}
						]
					}
				});

				cOption['series'][1] = $.extend({}, cOption['series'][1], {
					markPoint : {
						data : [
							{type : 'max', name: '最大值'},
							{type : 'min', name: '最小值'}
						]
					},
					markLine : {
						data : [
							{type : 'average', name : '平均值'}
						]
					}
				});
				// 为echarts对象加载数据
				Render.chartsData.myCharts.dom.setOption(cOption);
				Render.chartsData.myCharts.data = cOption;
			}
		);
	}

	return home;
});