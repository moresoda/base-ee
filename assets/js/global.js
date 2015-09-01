/*
$('body').css({'visibility': 'hidden'});
WebFontConfig = {
	custom: {
		families: ['Brandon'], // Font name as used in the stylesheet
		urls: ['/assets/css/font-face.css']
	},
	inactive: function() {
		$('body').css({'visibility': 'visible'});
	},
	active: function() {
		$('body').css({'visibility': 'visible'});
	}
};
(function() {var wf = document.createElement('script');wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';wf.type = 'text/javascript';wf.async = 'true';var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(wf, s);})();
*/

if($('html').hasClass('ie')) {
	$('input, textarea').placeholder();
}

function elementHeights(el) {

	var elements = $(el).map(function() {
		return $(this).height();
	}).get();

	var maxHeight = Math.max.apply(null, elements);

	$(el).height(maxHeight);
}

function navToggle() {
	$('.js-nav-toggle').on('click', function(){

		setTimeout(function(){
			$('.js-nav-toggle-area').stop().slideToggle();
		}, 350);

	});
}

var windowWidth = $(window).width();

navToggle();

function docReady(wW) {

} // docReady()

function winLoad(wW) {

} // winLoad

$(document).ready(function() {

	docReady(windowWidth);

});

// Added window.load for js that requires images to be loaded

$(window).load(function(){

	winLoad(windowWidth);

});

$(window).on('resize orientationchange', function(){
	var windowWidth = $(window).width();

	docReady(windowWidth);
	winLoad(windowWidth);

});
