function initNavbar() {

    var scrollSpeed = 750;
    var scrollOffset = 50;
    var easing = 'swing';

    $('#navbar-top .navbar-default ul.nav').onePageNav({
        currentClass: 'active',
        changeHash: false,
        scrollSpeed: scrollSpeed,
        scrollOffset: scrollOffset,
        scrollThreshold: 0.5,
        filter: ':not(.external)',
        easing: easing
    });

    $('.nav-external').click(function (e) {
        e.preventDefault();
        $('html, body').stop().animate({
            scrollTop: $($(this).attr("href")).offset().top - scrollOffset
        }, scrollSpeed, easing);
    });

    $('#navbar-top .navbar-default').affix({
        offset: {
            top: $('#home').height()
        }
    });
}
function initPortfolio () {
    var portfolio = $('#portfolio');
    var items = $('.items', portfolio); 
    var filters = $('.filters li a', portfolio); 

    items.imagesLoaded(function() {
        items.isotope({
            itemSelector: '.item',
            layoutMode: 'fitRows',
            transitionDuration: '0.7s'
        });
    });
    
    filters.click(function(){
        var el = $(this);
        filters.removeClass('active');
        el.addClass('active');
        var selector = el.attr('data-filter');
        items.isotope({ filter: selector });
        return false;
    });   
}
function initAnimations() {
    $('.animated').appear(function () {
        var el = $(this);
        var animation = el.data('animation');
        var delay = el.data('delay');
        if (delay) {
            setTimeout(function () {
                el.addClass(animation);
                el.addClass('showing');
                el.removeClass('hiding');
            }, delay);
        } else {
            el.addClass(animation);
            el.addClass('showing');
            el.removeClass('hiding');
        }
    }, {
        accY: -60
    });

    // Service hover animation
	$('.service').hover(function(){
		$('i', this).addClass('animated tada');
	},function(){	
        $('i', this).removeClass('animated tada');
	});
}
$(document).keypress(function(e) { 
	// 回车键事件 
	if(e.which == 13) { 
		jQuery("#start").click(); 
	}
});
$(document).ready(function () {
    initNavbar();
    initPortfolio();
    initAnimations();
	$("#start").click(function(){
			var url = $("#url").val();
			if(url == ''){
				alert('推广链接不能为空');
				return;
				}
			$.ajax({
				type:"POST",
				url:"index.php",
				data:{"type":"ok","url":url},
				async:true,
				dataType:"json",
				success: function(ret){
					alert(ret.msg);
					history.go(0);
					}
				
				
				})
			
			})
});
$(window).load(function () {
    $(".loader .fading-line").fadeOut();
    $(".loader").fadeOut("slow");
});
function handleTweets(tweets) {
    var element = document.getElementById('feed');
    if (element) {
        var x = tweets.length;
        var n = 0;
        var html = '<ul class="list-inline">';
        while (n < x) {
            html += '<li>' + tweets[n] + '</li>';
            n++;
        }
        html += '</ul>';
        element.innerHTML = html;
    }
}