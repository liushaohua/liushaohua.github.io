;(function($) {

	//icon class
	//.hyq-ic .ic-alert-lg  警告
	//.hyq-ic .ic-success-lg 成功
	//.hyq-ic .ic-error-lg 错误

	$.fn.HYQTip = function() {
		var s = this;
		var $s = $(this);
		s.w = 0;
		s.h = 0;

		var _iconNode = $s.find('[hyq-tip-icon-node]');
		var _messageNode = $s.find('.hyq-tip-msg-node');

		var __LOOK_CSS = {
			"padding" : "15px",
			"border-radius" : "2px",
			"position" : "fixed",
			"display" : "block",
			"width" : "250px",
			"min-height" : "24px",
			"box-shadow" : "0px 5px 10px #999",
			"z-index" : $.hyqZindex()+10,
			"background-color" : "#f7f7f7",


		};

		var __MSG_CSS = {
			"font-size" : '18px',
			"backgorund-color" : "#a4a4a4",
		}

		var _E = {
			shown : "hqy/tip/shwon",
			show : "hqy/tip/show",
			hidden : "hqy/tip/hidden",
			hide : "hyq/tip/hide",
			highlighten : "hyq/tip/highlighten"
		}

		var __READY_POS = {
			"left" : "50%",
			"position" : "fixed",
			"margin-left" : "-" + $s.width() / 2 + "px",
			"top" : "-999px",
			"opacity" : "0.0",
			"z-index":$.hyqZindex()+10

		}

		var __APPEAR_POS = {
			"top" : "10px",
			"opacity" : "1",
			"left" : "50%",
			"position" : "fixed",
			"display" : "block",
			"z-index":$.hyqZindex()+10,
			"margin-left" : "-" + $s.width() / 2 + "px"

		}
		_messageNode.css(__MSG_CSS);
		$s.css(__LOOK_CSS).each(function() {
			if (this.complete) {
				s.w = $s.width();
				s.h = $s.height();
			}
		});
		$s.css(__READY_POS);

		s.showError = function(message) {
			_iconNode.removeClass('*').addClass('hyq-ic ic-error-lg');
			_messageNode.html(message);
			$s.css(__READY_POS);
			$s.animate(__APPEAR_POS).delay(1500).animate(__READY_POS);
		};
		s.showSuccess = function(message) {
			_iconNode.removeClass('*').addClass('hyq-ic ic-success-lg');
			_messageNode.html(message);
			$s.css(__READY_POS);
			$s.animate(__APPEAR_POS).delay(1500).animate(__READY_POS);
		};
		s.showAlert = function(message) {
			_messageNode.html(message);
			$s.css(__READY_POS);
			_iconNode.removeClass('*').addClass('hyq-ic ic-alert-lg');
			$s.animate(__APPEAR_POS).delay(1500).animate(__READY_POS);
		}

		$s.on('hyq/tip/show/err', function(event) {
			s.showError(event.message);
		});
		$s.on('hyq/tip/show/success', function(event) {
			s.showSuccess(event.message);
		});
		$s.on('hyq/tip/show/alert', function(event) {
			s.showAlert(event.message);
		});

		return this;

	};

	$(function() {
		var tips = $(".hyq-tip");
		if (tips) {
			tips.each(function(i, e) {
				$(e).HYQTip();
			});
		}

		var tipsToggle = $("[data-toggle=hyq-tip]");
		$.each(tipsToggle, function(i, item) {

			var id = $(item).attr("data-target");

			if ($(id)) {
				$(item).click(function() {
					$(id).trigger({
						type : 'hyq/modal/show',
						id : id
					});
				});
			}
		});

	});

})(jQuery);

