/**
 * Created by ahko on 14-10-22.
 */

define(function(require) {

    var info = {};

    info.template = require('text!./index.html');

    info.beforeRender = function() {
        //在页面渲染之前执行，获取数据
        console.log('tool beforeRender');
    }

    info.initBehavior = function() {
        //在页面渲染之后执行，对页面进行操作
        console.log('tool initBehavior');
    }
    return info;
});

