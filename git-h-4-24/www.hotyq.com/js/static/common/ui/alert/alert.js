define(function(require) {
	"use strict";
	var $ = require('jquery');
	
    function alert(){
		return {
			init : function () {
				console.log('alert');
			}
		};
	}
	
	return alert;
});