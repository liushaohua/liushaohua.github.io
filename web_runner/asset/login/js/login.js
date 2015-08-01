$(function () {
    var $window = $(window);
    $('[place]').each(function () {
        var $this = $(this);
        $this.click(function () {
            $this.prev('.c_tip').hide();
        });
        $this.blur(function () {
            if ($this.val() == '') {
                $this.prev('.c_tip').show();
            }
        });
    });
    $window.resize(function () {
        var $cImg = $('#imgbg'),
            $cImgH = $cImg.height();
        if ($cImgH >= $window.height()) {
            $cImgH = $window.height();
        }

        $('.login_wrap').css({
            'top': $cImgH * 0.39,
        });
    });
});
