define(function(require) {
	
	var tool = {};
	
	tool.template = require('text!./index.html');

	tool.beforeRender = function() {
		//在页面渲染之前执行，获取数据
		console.log('tool beforeRender');
	}

	tool.initBehavior = function() {
		//在页面渲染之后执行，对页面进行操作
		console.log('tool initBehavior');
	}
	return tool;
});