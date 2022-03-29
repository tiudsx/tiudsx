var $allVideos = jQuery("iframe[src^='http://player.vimeo.com'], iframe[src^='http://www.youtube.com'], iframe[src^='http://www.youtube-nocookie.com'], iframe[src^='http://serviceapi.nmv.naver.com'], embed[src^='http://www.youtube.com'], embed[src^='http://serviceapi.nmv.naver.com'], iframe[src^='https://www.youtube.com'], iframe[src^='https://www.youtube-nocookie.com'], iframe[src^='http://flvs.daum.net'], iframe[src^='http://play.pullbbang.com'], iframe[src^='http://www.gamespot.com'], iframe[src^='http://sbsplayer.sbs.co.kr/'], iframe[src^='http://www.microsoft.com'], iframe[src^='http://dotsub.com'], iframe[src^='http://www.travelro.co.kr'], embed[src^='http://v.egloos.com/v.sk/'], embed[src^='http://api.v.daum.net/'], embed[src^='http://dory.mncast.com/'], embed[src^='http://play.mgoon.com/Video/'], embed[src^='http://doc.mgoon.com/'], embed[src^='http://flvr.pandora.tv/flv2pan/'], embed[src^='http://imgcdn.pandora.tv/'], embed[src^='http://live.afreeca.com'], embed[src^='http://afbbs.afreeca.com'], embed[src^='http://w.blogdoc.nate.com'], embed[src^='http://blogdoc.nate.com'], embed[src^='http://www.musicshake.com/'], embed[src^='http://static.plaync.co.kr'], iframe[src^='http://www.travelro.co.kr'], iframe[src^='http://dotsub.com']"),


$fluidEl = jQuery("body");
 
$allVideos.each(function() {
jQuery(this)
.data('aspectRatio', this.height / this.width)
.removeAttr('height')
.removeAttr('width');
});
 
jQuery(window).resize(function() {
var newWidth = $fluidEl.width();
$allVideos.each(function() {
var $el = jQuery(this);
$el
.width('640') // 가로
.height('360'); // 세로
});
}).resize();
