$(document).ready(function() {
	$Functions = new Functions();
});

function Functions() {
	var animateSpeed = 250;

	var __construct = function() {
		initBar();
		windowSettings();
		initSidebar();
		initForms();
	};

	var windowSettings = function() {
		$(window).load(function() {
			barDone();
		}).unload(function() {
		}).scroll(function() {
		}).on('beforeunload', function() {
			barStart();
		}).resize(function() {
			fixFooter();
		}).trigger('resize');
	};

	var barStart = function() {
		if (!NProgress.isStarted()) {
			NProgress.start();
		}
		barIncrease();
	};

	var barIncrease = function() {
		if (typeof timer != 'undefined') {
			clearTimeout(timer);
		}
		NProgress.inc();
		timer = setTimeout(function() {
			barIncrease();
		}, animateSpeed);
	};

	var barDone = function() {
		if (typeof timer != 'undefined') {
			clearTimeout(timer);
		}
		setTimeout(function() {
			NProgress.done();
		}, animateSpeed);
	};

	var initBar = function() {
		NProgress
				.configure({
					template : '<div class="admin"><div class="bar" role="bar"><div class="peg"></div></div></div>'
				});
		barStart();
		$.ajaxSetup({
			beforeSend : function(jqXHR, settings) {
				barStart();
			},
			complete : function(jqXHR, textStatus) {
				barDone();
			}
		});
	};

  var initNicescroll = function() {
    $('#wrapper').niceScroll({
      cursorcolor : 'rgba(17, 17, 17, 0.4)',
      cursoropacitymax : 1,
      cursorwidth : 12.5,
      cursorborder : 'none',
      cursorborderradius : '0',
      background : '#e5e5e5',
      autohidemode : false,
      horizrailenabled : false,
      zindex : 999
    }).resize();
    $('#wrapper').getNiceScroll()[0].rail.addClass('wrapper-scrollbar');
  };

	var fixFooter = function() {
		var height = $(window).height() - $('.xfw_content').offset().top
				- $('.xfw_footline').height() * 2 - 3;
		if (height < $('.xfw_sidebar').outerHeight()) {
			height = $('.xfw_sidebar').outerHeight();
		}
		$('.xfw_content').css('min-height', height + 'px');
		initNicescroll();
	};

	var initSidebar = function() {
		$('.xfw_sidemenu li a').click(function(e) {
			var el = $(this).parent();
			if (el.find('ul').exists()) {
				e.preventDefault();
				el.find('ul').slideToggle({
					duration : animateSpeed,
					queue : false,
					progress : function() {
						fixFooter();
					},
					complete : function() {
						el.toggleClass('opened');
					}
				});
			}
		});
	};

	var initForms = function() {
		$('.only-numbers').onlyNumbers();
		if ($('.js-form').exists()) {
			initCKeditor();
			$('body').on('submit', '.js-form', function(e) {
				e.preventDefault();
				var form = $(this);
				$('.xfw-msg').remove();
				form.ajaxSubmit({
					dataType : 'json',
					success : function(resp) {
						if (resp.process) {
							location.href = resp.redirect;
						} else {
							form.replaceWith(resp.form);
							$(window).trigger('load');
						}
					}
				});
			});
		}
	};

	var initCKeditor = function() {
		$('textarea').each(function() {
			if ($(this).parent()[0].tagName != 'TD') {
				CKEDITOR.replace($(this).attr('name'));
			}
		});
	};

	__construct();
}

$.fn.exists = function() {
	return $(this).length ? true : false;
};

$.fn.onlyNumbers = function() {
	return $(this).each(
			function() {
				$(this).keydown(
						function(e) {
							if (e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 9
									|| e.keyCode == 27 || e.keyCode == 13
									|| (e.keyCode == 65 && e.ctrlKey === true)
									|| (e.keyCode >= 35 && e.keyCode <= 39)) {
								return;
							} else {
								if (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)
										&& (e.keyCode < 96 || e.keyCode > 105)) {
									e.preventDefault();
								}
							}
						});
			});
};
