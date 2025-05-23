jQuery(document).ready(function($) {
    var placeholder = $('#video_placeholder');
    var videoContainer = $('#video_container');
    var resizeTimeout;
    var viewportWidth = $(window).width();

    placeholder.show();

    function canLoadVideo() {
        var userAgent = navigator.userAgent.toLowerCase();
        var isOldBrowser = (
            /safari/.test(userAgent) && !/chrome/.test(userAgent) && parseInt(userAgent.match(/version\/(\d+)/i)[1], 10) < 11 ||
            /chrome/.test(userAgent) && parseInt(userAgent.match(/chrome\/(\d+)/i)[1], 10) < 100 ||
            /firefox/.test(userAgent) && parseInt(userAgent.match(/firefox\/(\d+)/i)[1], 10) < 100
        );
        var isSlowConnection = navigator.connection ? (
            navigator.connection.downlink < 1.5 ||
            navigator.connection.effectiveType.includes('2g') ||
            navigator.connection.effectiveType.includes('3g')
        ) : false;

        return !isOldBrowser && !isSlowConnection;
    }

    function setVideoCookie() {
        var expires = new Date();
        expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000)); // 1 day expiration
        document.cookie = "canLoadVideo=true; expires=" + expires.toUTCString() + "; path=/; SameSite=Lax";
    }

    function getVideoCookie() {
        var name = "canLoadVideo=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length) === "true";
            }
        }
        return false;
    }

    function loadVideo() {
        if (heroVideoUrl && canLoadVideo() && !getVideoCookie()) {
            setVideoCookie();
            addVideo();
        }

        if (heroVideoUrl && canLoadVideo() && document.cookie.includes('canLoadVideo=true')) {
            if ($('#hero_video').length === 0) {
                addVideo();
            }
        }

        checkVideoDisplay();
    }

    function addVideo() {
        if (!heroVideoUrl || heroVideoUrl === "null") {
            return;
        }

        var videoURL = heroVideoUrl;
        if (!videoURL.includes('?')) {
            videoURL += '?';
        }
        videoURL += 'autoplay=1&loop=1&title=0&byline=0&portrait=0&background=1';

        var videoHTML = '<div class="hero-video-wrapper" style="padding:56.25% 0 0 0; position:relative;">' +
            '<iframe id="hero_video" src="' + videoURL + '" style="position:absolute; top:0; left:0; width:100%; height:100%; display:none;" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>' +
            '</div>' +
            '<script src="https://player.vimeo.com/api/player.js"></script>';
        $(videoContainer).html(videoHTML);
    }

    function checkVideoDisplay() {
        var video = $('#hero_video');
        if (video.length) {
            if ($(window).width() < 540) {
                $(video).hide();
                placeholder.show();
            } else {
                $(video).show();
                setTimeout(function() {
                    placeholder.hide();
                }, 1000);
            }
        } else {
            placeholder.show();
        }
    }

    function debounceResize(fn) {
        $(window).resize(function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                var viewportWidthResized = $(window).width();

                if (viewportWidth !== viewportWidthResized) {
                    fn();
                }
                viewportWidth = viewportWidthResized;
            }, 50);
        });
    }

    loadVideo();
    debounceResize(checkVideoDisplay);
});
