seajs.config({
	// 别名配置
	alias: {
	},
	map: [
		[ /^(.*\.(?:css|js|tpl))(.*)$/i, '$1?v=20150424']
	],
	// Sea.js 的基础路径
	base: 'js/dist',
	// 文件编码
	charset: 'utf-8'
});