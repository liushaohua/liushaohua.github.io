;(function($) {
	$.extend({
		hyqZindex : function() {
			var s = (arguments.length == 1) ? ($(arguments[0]).find('*') ? $(arguments[0]).find('*') : $('body > *')) : $('body > *');
			//console.log($('body > *'))
			return Math.max.apply(null, $.map($('body > *'), function(e, n) {
				if ($(e).css('position') == 'absolute')
					return parseInt($(e).css('z-index')) || 1;
				else
					return 0;
			}));

		}
	});

	$.fn.center = function() {
		this.css("position", "absolute");
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
		return this;
	}

	$.fn.HYQModal = function(params) {
		var $s = $(this);
		var sel = this;
		sel.zindex = 50;
		sel._overlay = null;
		sel.E_SHOW = "hyq/modal/show";
		sel.E_SHOWN = "hyq/modal/shown";
		sel.E_HIDDEN = "hyq/modal/hidden";
		sel.E_SHOW_WITH_MESSAGE = "hyq/modal/show_with_message";
		sel.E_HIDE = "hyq/modal/hide";
		sel.E_DO_CLOSE = "hyq/modal/close";
		sel.E_SIZE_CHANGED = "hyq/modal/sizechanged";

		sel._S = function() {

			return {
				w : $(sel).width(),
				h : $(sel).height()
			}
		}
		sel._centerModal = function() {
			 
			$(sel).css({
				"left" : "50%",
				"top" : "50%",
				"opacity" : 1,
				"margin-top" : "-" + $(sel).height() / 2 + 'px',
				"margin-left" : "-" + $(sel).width() / 2 + 'px'
			});
		}
		sel._slideOut = function() {
			$(sel).animate({
				"top" : "-" + sel._S().h + "px",
				"opacity" : 0
			}, 300,function() {
				sel._removeOverlay();
				$(sel).trigger(sel.E_HIDDEN);
				$(sel).hide();
			});

			
		}
		sel._fadeIn = function() {
			$(sel).css({
				"position" : "fixed",
				"left" : "50%",
				"z-index" : s.zindex + 10,
				"top" : "30%",
				"opacity" : 1,
				//"margin-top":"-"+sel._S().h+'px',
				"margin-left" : "-" + sel._S().w / 2 + 'px'
			});
			$(sel).fadeIn();
			
		}

		sel.on(sel.E_SHOW, function(event) {
			
			sel.pop();
			
		});

		sel.on(sel.E_SHOW_WITH_MESSAGE, function(event) {
			sel.popWithMessage(event.hyqMessage);
		});
		$(sel).find('.hyq-modal-close-btn,.hyq-modal-close-btn-sm,.hyq-modal-close-trigger').click(function() {
			sel._slideOut();
		});

		$(sel).find('[data-dismiss=modal]').click(function() {
			sel._slideOut();
		});

		//prepare
		sel._makeOverlay = function() {

			var mz = $.hyqZindex();
			sel.zindex = mz + 100;
			if (sel._overlay)
				sel._overlay.remove();
			$(".hyq-model-overlay").remove();
			sel._overlay = $("<div class='hyq-model-overlay'></div>");
			sel._overlay.attr('holder-modal', "#" + $s.attr('id'));
			sel._overlay.css('z-index', sel.zindex);
			$(document.body).prepend(sel._overlay);
		}
		// remove overlay
		sel._removeOverlay = function(way) {
			if (sel._overlay)
				sel._overlay.remove();

		}

		$(sel).resize(function() {

			$(sel).css({
				"left" : "50%",
				"top" : "50%",
				"opacity" : 1,
				"margin-top" : "-" + sel._S().h / 2 + 'px',
				"margin-left" : "-" + sel._S().w / 2 + 'px'
			});

		});

		//调整窗口位置以居中
		sel._display = function(way) {/* fade slidedown*/

			try {

				if (way.toLowerCase(way) == "fade") {
					sel._fadeIn();

				}
				$(sel).show();
				if (way.toLowerCase(way) == "slidedown") {
					$(sel).css({
						"position" : "fixed",
						"top" : "-" + sel._S().h + "px",
						"z-index" : $.hyqZindex() + 10,
						"opacity" : 0,
						"left" : "50%",
						"margin-left" : "-" + sel._S().w / 2 + 'px'
					});
					$(sel).animate({
						"left" : "50%",
						"top" : "45%",
						"opacity" : 1,
						"margin-top" : "-" + sel._S().h / 2 + 'px',
						"margin-left" : "-" + sel._S().w / 2 + 'px'
					},function(){
						$(sel.trigger(sel.E_SHOWN));
					})

				}
				
			} catch(e) {
				throw e;
			}

			return this;
		};

		//
		sel.pop = function() {
			sel._makeOverlay();
			sel._display("slidedown");

			return sel;
		}

		sel.popWithMessage = function(message) {
			sel._makeOverlay();
			sel._display("slidedown");
			sel.find('[hyq-modal-meesage-dock]').html(message);
			return this;
		}
		sel.close = function(){
			sel._slideOut();	
		}
		sel.on(sel.E_DO_CLOSE, function() {
			sel._slideOut();
		});

		// Hide model
		sel.fade = function() {

			return this;
		}

		$s.draggable();
		$s.hide();
		return  $.extend($(this),params);
	};

})(jQuery);
