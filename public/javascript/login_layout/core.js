$(document).ready(function() {
	$Functions = new Functions();
});

function Functions() {
  var animateSpeed = 250;
  
	var __construct = function() {
		initBar();
		windowSettings();
		initForm();
	};

	var windowSettings = function() {
		$(window).load(function() {
			barDone();
		}).on('beforeunload', function() {
			barStart();
		}).unload(function() {
		}).scroll(function() {
		}).resize(function() {
		});
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
					template : '<div class="admin"><div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div></div>'
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

	var initForm = function() {
		$('body').on('submit', '#xfw-login', function(e) {
			e.preventDefault();
			var form = $(this);
			$('.xfw_login_msg').slideUp(animateSpeed, function() {
				$(this).remove();
			});
			form.ajaxSubmit({
				dataType : 'json',
				success : function(resp) {
					if (resp.process) {
						location.href = resp.redirect;
					} else {
						form.replaceWith(resp.form);
					}
				}
			});
		});
	};

	__construct();
};