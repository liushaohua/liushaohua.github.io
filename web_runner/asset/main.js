/**
 * 主main
 * @copyright 银翼流量可视化平台
 * @author liushaohua
 * @email fe.liushaohua#gmail.com
 */
define(function(require) {
	'use strict';
	require('jQuery');
    var router = require('router'),
		$ = jQuery,cPage = {};
		window.EchartsCof = require('common/MyEcharts');

	router.registerRouter({
		path: '/home/',
		type: 'home/index'
	});

	router.registerRouter({
		path: '/tool/',
		type: 'home/index'
	});

    router.registerRouter({
        path: '/info/',
        type: 'info/index'
    })
	router.start('/home/');

	cPage = {
		init : function () {

		}
	};
	cPage.init();
	//hashChange
	$(document).on("Runner/hashChange",function(){
		var hash = location.hash;
		$('.submenu li').removeClass('active');
		$('a[href="'+ hash +'"]').parent().addClass('active');
	});

	$('.dropdown-toggle').click(function () {
		var $li = $(this).parent();
		$li.addClass('active').siblings().removeClass('active');
		$('.menu_wrap').slideUp();
		$(this).next().stop().slideDown();
	});

	var $show_bar = $('.side_show'),
		$hide_bar = $('.side_hide'),
		$main = $('#main');

	$hide_bar.click(function () {
		$('.left_bar').animate({'left': -223});
		$show_bar.css('left',210);
		$main.animate({'margin-left': 0});
	});

	$show_bar.click(function () {
		$('.left_bar').animate({'left': 0});
		$show_bar.css('left',0);
		$main.animate({'margin-left': 223});
	});
});
