/*! modernizr 3.3.1 (Custom Build) | MIT *
 * http://modernizr.com/download/?-audio-cssanimations-csscolumns-csstransforms-cssvhunit-cssvwunit-displaytable-nthchild-svg-touchevents-video-printshiv-setclasses !*/
!function(e,t,n){function r(e,t){return typeof e===t}function o(){var e,t,n,o,a,i,s;for(var l in T)if(T.hasOwnProperty(l)){if(e=[],t=T[l],t.name&&(e.push(t.name.toLowerCase()),t.options&&t.options.aliases&&t.options.aliases.length))for(n=0;n<t.options.aliases.length;n++)e.push(t.options.aliases[n].toLowerCase());for(o=r(t.fn,"function")?t.fn():t.fn,a=0;a<e.length;a++)i=e[a],s=i.split("."),1===s.length?Modernizr[s[0]]=o:(!Modernizr[s[0]]||Modernizr[s[0]]instanceof Boolean||(Modernizr[s[0]]=new Boolean(Modernizr[s[0]])),Modernizr[s[0]][s[1]]=o),g.push((o?"":"no-")+s.join("-"))}}function a(e){var t=E.className,n=Modernizr._config.classPrefix||"";if(w&&(t=t.baseVal),Modernizr._config.enableJSClass){var r=new RegExp("(^|\\s)"+n+"no-js(\\s|$)");t=t.replace(r,"$1"+n+"js$2")}Modernizr._config.enableClasses&&(t+=" "+n+e.join(" "+n),w?E.className.baseVal=t:E.className=t)}function i(){return"function"!=typeof t.createElement?t.createElement(arguments[0]):w?t.createElementNS.call(t,"http://www.w3.org/2000/svg",arguments[0]):t.createElement.apply(t,arguments)}function s(){var e=t.body;return e||(e=i(w?"svg":"body"),e.fake=!0),e}function l(e,n,r,o){var a,l,c,d,u="modernizr",f=i("div"),p=s();if(parseInt(r,10))for(;r--;)c=i("div"),c.id=o?o[r]:u+(r+1),f.appendChild(c);return a=i("style"),a.type="text/css",a.id="s"+u,(p.fake?p:f).appendChild(a),p.appendChild(f),a.styleSheet?a.styleSheet.cssText=e:a.appendChild(t.createTextNode(e)),f.id=u,p.fake&&(p.style.background="",p.style.overflow="hidden",d=E.style.overflow,E.style.overflow="hidden",E.appendChild(p)),l=n(f,e),p.fake?(p.parentNode.removeChild(p),E.style.overflow=d,E.offsetHeight):f.parentNode.removeChild(f),!!l}function c(e,t){return!!~(""+e).indexOf(t)}function d(e){return e.replace(/([a-z])-([a-z])/g,function(e,t,n){return t+n.toUpperCase()}).replace(/^-/,"")}function u(e,t){return function(){return e.apply(t,arguments)}}function f(e,t,n){var o;for(var a in e)if(e[a]in t)return n===!1?e[a]:(o=t[e[a]],r(o,"function")?u(o,n||t):o);return!1}function p(e){return e.replace(/([A-Z])/g,function(e,t){return"-"+t.toLowerCase()}).replace(/^ms-/,"-ms-")}function m(t,r){var o=t.length;if("CSS"in e&&"supports"in e.CSS){for(;o--;)if(e.CSS.supports(p(t[o]),r))return!0;return!1}if("CSSSupportsRule"in e){for(var a=[];o--;)a.push("("+p(t[o])+":"+r+")");return a=a.join(" or "),l("@supports ("+a+") { #modernizr { position: absolute; } }",function(e){return"absolute"==getComputedStyle(e,null).position})}return n}function h(e,t,o,a){function s(){u&&(delete z.style,delete z.modElem)}if(a=r(a,"undefined")?!1:a,!r(o,"undefined")){var l=m(e,o);if(!r(l,"undefined"))return l}for(var u,f,p,h,v,y=["modernizr","tspan"];!z.style;)u=!0,z.modElem=i(y.shift()),z.style=z.modElem.style;for(p=e.length,f=0;p>f;f++)if(h=e[f],v=z.style[h],c(h,"-")&&(h=d(h)),z.style[h]!==n){if(a||r(o,"undefined"))return s(),"pfx"==t?h:!0;try{z.style[h]=o}catch(g){}if(z.style[h]!=v)return s(),"pfx"==t?h:!0}return s(),!1}function v(e,t,n,o,a){var i=e.charAt(0).toUpperCase()+e.slice(1),s=(e+" "+N.join(i+" ")+i).split(" ");return r(t,"string")||r(t,"undefined")?h(s,t,o,a):(s=(e+" "+P.join(i+" ")+i).split(" "),f(s,t,n))}function y(e,t,r){return v(e,n,n,t,r)}var g=[],T=[],b={_version:"3.3.1",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,t){var n=this;setTimeout(function(){t(n[e])},0)},addTest:function(e,t,n){T.push({name:e,fn:t,options:n})},addAsyncTest:function(e){T.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=b,Modernizr=new Modernizr,Modernizr.addTest("svg",!!t.createElementNS&&!!t.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect);var E=t.documentElement,w="svg"===E.nodeName.toLowerCase();Modernizr.addTest("audio",function(){var e=i("audio"),t=!1;try{(t=!!e.canPlayType)&&(t=new Boolean(t),t.ogg=e.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/,""),t.mp3=e.canPlayType('audio/mpeg; codecs="mp3"').replace(/^no$/,""),t.opus=e.canPlayType('audio/ogg; codecs="opus"')||e.canPlayType('audio/webm; codecs="opus"').replace(/^no$/,""),t.wav=e.canPlayType('audio/wav; codecs="1"').replace(/^no$/,""),t.m4a=(e.canPlayType("audio/x-m4a;")||e.canPlayType("audio/aac;")).replace(/^no$/,""))}catch(n){}return t}),Modernizr.addTest("video",function(){var e=i("video"),t=!1;try{(t=!!e.canPlayType)&&(t=new Boolean(t),t.ogg=e.canPlayType('video/ogg; codecs="theora"').replace(/^no$/,""),t.h264=e.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/,""),t.webm=e.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/,""),t.vp9=e.canPlayType('video/webm; codecs="vp9"').replace(/^no$/,""),t.hls=e.canPlayType('application/x-mpegURL; codecs="avc1.42E01E"').replace(/^no$/,""))}catch(n){}return t});var S=b._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];b._prefixes=S;var C=b.testStyles=l;Modernizr.addTest("touchevents",function(){var n;if("ontouchstart"in e||e.DocumentTouch&&t instanceof DocumentTouch)n=!0;else{var r=["@media (",S.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");C(r,function(e){n=9===e.offsetTop})}return n}),C("#modernizr{display: table; direction: ltr}#modernizr div{display: table-cell; padding: 10px}",function(e){var t,n=e.childNodes;t=n[0].offsetLeft<n[1].offsetLeft,Modernizr.addTest("displaytable",t,{aliases:["display-table"]})},2),C("#modernizr div {width:1px} #modernizr div:nth-child(2n) {width:2px;}",function(e){for(var t=e.getElementsByTagName("div"),n=!0,r=0;5>r;r++)n=n&&t[r].offsetWidth===r%2+1;Modernizr.addTest("nthchild",n)},5),C("#modernizr { height: 50vh; }",function(t){var n=parseInt(e.innerHeight/2,10),r=parseInt((e.getComputedStyle?getComputedStyle(t,null):t.currentStyle).height,10);Modernizr.addTest("cssvhunit",r==n)}),C("#modernizr { width: 50vw; }",function(t){var n=parseInt(e.innerWidth/2,10),r=parseInt((e.getComputedStyle?getComputedStyle(t,null):t.currentStyle).width,10);Modernizr.addTest("cssvwunit",r==n)});var x="Moz O ms Webkit",N=b._config.usePrefixes?x.split(" "):[];b._cssomPrefixes=N;var P=b._config.usePrefixes?x.toLowerCase().split(" "):[];b._domPrefixes=P;var _={elem:i("modernizr")};Modernizr._q.push(function(){delete _.elem});var z={style:_.elem.style};Modernizr._q.unshift(function(){delete z.style}),b.testAllProps=v,b.testAllProps=y,Modernizr.addTest("cssanimations",y("animationName","a",!0)),function(){Modernizr.addTest("csscolumns",function(){var e=!1,t=y("columnCount");try{(e=!!t)&&(e=new Boolean(e))}catch(n){}return e});for(var e,t,n=["Width","Span","Fill","Gap","Rule","RuleColor","RuleStyle","RuleWidth","BreakBefore","BreakAfter","BreakInside"],r=0;r<n.length;r++)e=n[r].toLowerCase(),t=y("column"+n[r]),("breakbefore"===e||"breakafter"===e||"breakinside"==e)&&(t=t||y(n[r])),Modernizr.addTest("csscolumns."+e,t)}(),Modernizr.addTest("csstransforms",function(){return-1===navigator.userAgent.indexOf("Android 2.")&&y("transform","scale(1)",!0)});w||!function(e,t){function n(e,t){var n=e.createElement("p"),r=e.getElementsByTagName("head")[0]||e.documentElement;return n.innerHTML="x<style>"+t+"</style>",r.insertBefore(n.lastChild,r.firstChild)}function r(){var e=C.elements;return"string"==typeof e?e.split(" "):e}function o(e,t){var n=C.elements;"string"!=typeof n&&(n=n.join(" ")),"string"!=typeof e&&(e=e.join(" ")),C.elements=n+" "+e,c(t)}function a(e){var t=S[e[E]];return t||(t={},w++,e[E]=w,S[w]=t),t}function i(e,n,r){if(n||(n=t),v)return n.createElement(e);r||(r=a(n));var o;return o=r.cache[e]?r.cache[e].cloneNode():b.test(e)?(r.cache[e]=r.createElem(e)).cloneNode():r.createElem(e),!o.canHaveChildren||T.test(e)||o.tagUrn?o:r.frag.appendChild(o)}function s(e,n){if(e||(e=t),v)return e.createDocumentFragment();n=n||a(e);for(var o=n.frag.cloneNode(),i=0,s=r(),l=s.length;l>i;i++)o.createElement(s[i]);return o}function l(e,t){t.cache||(t.cache={},t.createElem=e.createElement,t.createFrag=e.createDocumentFragment,t.frag=t.createFrag()),e.createElement=function(n){return C.shivMethods?i(n,e,t):t.createElem(n)},e.createDocumentFragment=Function("h,f","return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&("+r().join().replace(/[\w\-:]+/g,function(e){return t.createElem(e),t.frag.createElement(e),'c("'+e+'")'})+");return n}")(C,t.frag)}function c(e){e||(e=t);var r=a(e);return!C.shivCSS||h||r.hasCSS||(r.hasCSS=!!n(e,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}template{display:none}")),v||l(e,r),e}function d(e){for(var t,n=e.getElementsByTagName("*"),o=n.length,a=RegExp("^(?:"+r().join("|")+")$","i"),i=[];o--;)t=n[o],a.test(t.nodeName)&&i.push(t.applyElement(u(t)));return i}function u(e){for(var t,n=e.attributes,r=n.length,o=e.ownerDocument.createElement(N+":"+e.nodeName);r--;)t=n[r],t.specified&&o.setAttribute(t.nodeName,t.nodeValue);return o.style.cssText=e.style.cssText,o}function f(e){for(var t,n=e.split("{"),o=n.length,a=RegExp("(^|[\\s,>+~])("+r().join("|")+")(?=[[\\s,>+~#.:]|$)","gi"),i="$1"+N+"\\:$2";o--;)t=n[o]=n[o].split("}"),t[t.length-1]=t[t.length-1].replace(a,i),n[o]=t.join("}");return n.join("{")}function p(e){for(var t=e.length;t--;)e[t].removeNode()}function m(e){function t(){clearTimeout(i._removeSheetTimer),r&&r.removeNode(!0),r=null}var r,o,i=a(e),s=e.namespaces,l=e.parentWindow;return!P||e.printShived?e:("undefined"==typeof s[N]&&s.add(N),l.attachEvent("onbeforeprint",function(){t();for(var a,i,s,l=e.styleSheets,c=[],u=l.length,p=Array(u);u--;)p[u]=l[u];for(;s=p.pop();)if(!s.disabled&&x.test(s.media)){try{a=s.imports,i=a.length}catch(m){i=0}for(u=0;i>u;u++)p.push(a[u]);try{c.push(s.cssText)}catch(m){}}c=f(c.reverse().join("")),o=d(e),r=n(e,c)}),l.attachEvent("onafterprint",function(){p(o),clearTimeout(i._removeSheetTimer),i._removeSheetTimer=setTimeout(t,500)}),e.printShived=!0,e)}var h,v,y="3.7.3",g=e.html5||{},T=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,b=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i,E="_html5shiv",w=0,S={};!function(){try{var e=t.createElement("a");e.innerHTML="<xyz></xyz>",h="hidden"in e,v=1==e.childNodes.length||function(){t.createElement("a");var e=t.createDocumentFragment();return"undefined"==typeof e.cloneNode||"undefined"==typeof e.createDocumentFragment||"undefined"==typeof e.createElement}()}catch(n){h=!0,v=!0}}();var C={elements:g.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video",version:y,shivCSS:g.shivCSS!==!1,supportsUnknownElements:v,shivMethods:g.shivMethods!==!1,type:"default",shivDocument:c,createElement:i,createDocumentFragment:s,addElements:o};e.html5=C,c(t);var x=/^$|\b(?:all|print)\b/,N="html5shiv",P=!v&&function(){var n=t.documentElement;return!("undefined"==typeof t.namespaces||"undefined"==typeof t.parentWindow||"undefined"==typeof n.applyElement||"undefined"==typeof n.removeNode||"undefined"==typeof e.attachEvent)}();C.type+=" print",C.shivPrint=m,m(t),"object"==typeof module&&module.exports&&(module.exports=C)}("undefined"!=typeof e?e:this,t),o(),a(g),delete b.addTest,delete b.addAsyncTest;for(var $=0;$<Modernizr._q.length;$++)Modernizr._q[$]();e.Modernizr=Modernizr}(window,document);
/*!
Waypoints - 4.0.0
Copyright © 2011-2015 Caleb Troughton
Licensed under the MIT license.
https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
*/
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.invokeAll("enable")},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical);t&&e&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s],l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=y+l-f,h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return t.isFunction(arguments[0])&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();
/*!
Waypoints Inview Shortcut - 4.0.0
Copyright © 2011-2015 Caleb Troughton
Licensed under the MIT license.
https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
*/
!function(){"use strict";function t(){}function e(t){this.options=i.Adapter.extend({},e.defaults,t),this.axis=this.options.horizontal?"horizontal":"vertical",this.waypoints=[],this.element=this.options.element,this.createWaypoints()}var i=window.Waypoint;e.prototype.createWaypoints=function(){for(var t={vertical:[{down:"enter",up:"exited",offset:"100%"},{down:"entered",up:"exit",offset:"bottom-in-view"},{down:"exit",up:"entered",offset:0},{down:"exited",up:"enter",offset:function(){return-this.adapter.outerHeight()}}],horizontal:[{right:"enter",left:"exited",offset:"100%"},{right:"entered",left:"exit",offset:"right-in-view"},{right:"exit",left:"entered",offset:0},{right:"exited",left:"enter",offset:function(){return-this.adapter.outerWidth()}}]},e=0,i=t[this.axis].length;i>e;e++){var n=t[this.axis][e];this.createWaypoint(n)}},e.prototype.createWaypoint=function(t){var e=this;this.waypoints.push(new i({context:this.options.context,element:this.options.element,enabled:this.options.enabled,handler:function(t){return function(i){e.options[t[i]].call(e,i)}}(t),offset:t.offset,horizontal:this.options.horizontal}))},e.prototype.destroy=function(){for(var t=0,e=this.waypoints.length;e>t;t++)this.waypoints[t].destroy();this.waypoints=[]},e.prototype.disable=function(){for(var t=0,e=this.waypoints.length;e>t;t++)this.waypoints[t].disable()},e.prototype.enable=function(){for(var t=0,e=this.waypoints.length;e>t;t++)this.waypoints[t].enable()},e.defaults={context:window,enabled:!0,enter:t,entered:t,exit:t,exited:t},i.Inview=e}();
!function(i){"use strict";"function"==typeof define&&define.amd?define(["jquery"],i):"undefined"!=typeof exports?module.exports=i(require("jquery")):i(jQuery)}(function(i){"use strict";var e=window.Slick||{};(e=function(){var e=0;return function(t,o){var s,n=this;n.defaults={accessibility:!0,adaptiveHeight:!1,appendArrows:i(t),appendDots:i(t),arrows:!0,asNavFor:null,prevArrow:'<button class="slick-prev" aria-label="Previous" type="button">Previous</button>',nextArrow:'<button class="slick-next" aria-label="Next" type="button">Next</button>',autoplay:!1,autoplaySpeed:3e3,centerMode:!1,centerPadding:"50px",cssEase:"ease",customPaging:function(e,t){return i('<button type="button" />').text(t+1)},dots:!1,dotsClass:"slick-dots",draggable:!0,easing:"linear",edgeFriction:.35,fade:!1,focusOnSelect:!1,focusOnChange:!1,infinite:!0,initialSlide:0,lazyLoad:"ondemand",mobileFirst:!1,pauseOnHover:!0,pauseOnFocus:!0,pauseOnDotsHover:!1,respondTo:"window",responsive:null,rows:1,rtl:!1,slide:"",slidesPerRow:1,slidesToShow:1,slidesToScroll:1,speed:500,swipe:!0,swipeToSlide:!1,touchMove:!0,touchThreshold:5,useCSS:!0,useTransform:!0,variableWidth:!1,vertical:!1,verticalSwiping:!1,waitForAnimate:!0,zIndex:1e3},n.initials={animating:!1,dragging:!1,autoPlayTimer:null,currentDirection:0,currentLeft:null,currentSlide:0,direction:1,$dots:null,listWidth:null,listHeight:null,loadIndex:0,$nextArrow:null,$prevArrow:null,scrolling:!1,slideCount:null,slideWidth:null,$slideTrack:null,$slides:null,sliding:!1,slideOffset:0,swipeLeft:null,swiping:!1,$list:null,touchObject:{},transformsEnabled:!1,unslicked:!1},i.extend(n,n.initials),n.activeBreakpoint=null,n.animType=null,n.animProp=null,n.breakpoints=[],n.breakpointSettings=[],n.cssTransitions=!1,n.focussed=!1,n.interrupted=!1,n.hidden="hidden",n.paused=!0,n.positionProp=null,n.respondTo=null,n.rowCount=1,n.shouldClick=!0,n.$slider=i(t),n.$slidesCache=null,n.transformType=null,n.transitionType=null,n.visibilityChange="visibilitychange",n.windowWidth=0,n.windowTimer=null,s=i(t).data("slick")||{},n.options=i.extend({},n.defaults,o,s),n.currentSlide=n.options.initialSlide,n.originalSettings=n.options,void 0!==document.mozHidden?(n.hidden="mozHidden",n.visibilityChange="mozvisibilitychange"):void 0!==document.webkitHidden&&(n.hidden="webkitHidden",n.visibilityChange="webkitvisibilitychange"),n.autoPlay=i.proxy(n.autoPlay,n),n.autoPlayClear=i.proxy(n.autoPlayClear,n),n.autoPlayIterator=i.proxy(n.autoPlayIterator,n),n.changeSlide=i.proxy(n.changeSlide,n),n.clickHandler=i.proxy(n.clickHandler,n),n.selectHandler=i.proxy(n.selectHandler,n),n.setPosition=i.proxy(n.setPosition,n),n.swipeHandler=i.proxy(n.swipeHandler,n),n.dragHandler=i.proxy(n.dragHandler,n),n.keyHandler=i.proxy(n.keyHandler,n),n.instanceUid=e++,n.htmlExpr=/^(?:\s*(<[\w\W]+>)[^>]*)$/,n.registerBreakpoints(),n.init(!0)}}()).prototype.activateADA=function(){this.$slideTrack.find(".slick-active").attr({"aria-hidden":"false"}).find("a, input, button, select").attr({tabindex:"0"})},e.prototype.addSlide=e.prototype.slickAdd=function(e,t,o){var s=this;if("boolean"==typeof t)o=t,t=null;else if(t<0||t>=s.slideCount)return!1;s.unload(),"number"==typeof t?0===t&&0===s.$slides.length?i(e).appendTo(s.$slideTrack):o?i(e).insertBefore(s.$slides.eq(t)):i(e).insertAfter(s.$slides.eq(t)):!0===o?i(e).prependTo(s.$slideTrack):i(e).appendTo(s.$slideTrack),s.$slides=s.$slideTrack.children(this.options.slide),s.$slideTrack.children(this.options.slide).detach(),s.$slideTrack.append(s.$slides),s.$slides.each(function(e,t){i(t).attr("data-slick-index",e)}),s.$slidesCache=s.$slides,s.reinit()},e.prototype.animateHeight=function(){var i=this;if(1===i.options.slidesToShow&&!0===i.options.adaptiveHeight&&!1===i.options.vertical){var e=i.$slides.eq(i.currentSlide).outerHeight(!0);i.$list.animate({height:e},i.options.speed)}},e.prototype.animateSlide=function(e,t){var o={},s=this;s.animateHeight(),!0===s.options.rtl&&!1===s.options.vertical&&(e=-e),!1===s.transformsEnabled?!1===s.options.vertical?s.$slideTrack.animate({left:e},s.options.speed,s.options.easing,t):s.$slideTrack.animate({top:e},s.options.speed,s.options.easing,t):!1===s.cssTransitions?(!0===s.options.rtl&&(s.currentLeft=-s.currentLeft),i({animStart:s.currentLeft}).animate({animStart:e},{duration:s.options.speed,easing:s.options.easing,step:function(i){i=Math.ceil(i),!1===s.options.vertical?(o[s.animType]="translate("+i+"px, 0px)",s.$slideTrack.css(o)):(o[s.animType]="translate(0px,"+i+"px)",s.$slideTrack.css(o))},complete:function(){t&&t.call()}})):(s.applyTransition(),e=Math.ceil(e),!1===s.options.vertical?o[s.animType]="translate3d("+e+"px, 0px, 0px)":o[s.animType]="translate3d(0px,"+e+"px, 0px)",s.$slideTrack.css(o),t&&setTimeout(function(){s.disableTransition(),t.call()},s.options.speed))},e.prototype.getNavTarget=function(){var e=this,t=e.options.asNavFor;return t&&null!==t&&(t=i(t).not(e.$slider)),t},e.prototype.asNavFor=function(e){var t=this.getNavTarget();null!==t&&"object"==typeof t&&t.each(function(){var t=i(this).slick("getSlick");t.unslicked||t.slideHandler(e,!0)})},e.prototype.applyTransition=function(i){var e=this,t={};!1===e.options.fade?t[e.transitionType]=e.transformType+" "+e.options.speed+"ms "+e.options.cssEase:t[e.transitionType]="opacity "+e.options.speed+"ms "+e.options.cssEase,!1===e.options.fade?e.$slideTrack.css(t):e.$slides.eq(i).css(t)},e.prototype.autoPlay=function(){var i=this;i.autoPlayClear(),i.slideCount>i.options.slidesToShow&&(i.autoPlayTimer=setInterval(i.autoPlayIterator,i.options.autoplaySpeed))},e.prototype.autoPlayClear=function(){var i=this;i.autoPlayTimer&&clearInterval(i.autoPlayTimer)},e.prototype.autoPlayIterator=function(){var i=this,e=i.currentSlide+i.options.slidesToScroll;i.paused||i.interrupted||i.focussed||(!1===i.options.infinite&&(1===i.direction&&i.currentSlide+1===i.slideCount-1?i.direction=0:0===i.direction&&(e=i.currentSlide-i.options.slidesToScroll,i.currentSlide-1==0&&(i.direction=1))),i.slideHandler(e))},e.prototype.buildArrows=function(){var e=this;!0===e.options.arrows&&(e.$prevArrow=i(e.options.prevArrow).addClass("slick-arrow"),e.$nextArrow=i(e.options.nextArrow).addClass("slick-arrow"),e.slideCount>e.options.slidesToShow?(e.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),e.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"),e.htmlExpr.test(e.options.prevArrow)&&e.$prevArrow.prependTo(e.options.appendArrows),e.htmlExpr.test(e.options.nextArrow)&&e.$nextArrow.appendTo(e.options.appendArrows),!0!==e.options.infinite&&e.$prevArrow.addClass("slick-disabled").attr("aria-disabled","true")):e.$prevArrow.add(e.$nextArrow).addClass("slick-hidden").attr({"aria-disabled":"true",tabindex:"-1"}))},e.prototype.buildDots=function(){var e,t,o=this;if(!0===o.options.dots){for(o.$slider.addClass("slick-dotted"),t=i("<ul />").addClass(o.options.dotsClass),e=0;e<=o.getDotCount();e+=1)t.append(i("<li />").append(o.options.customPaging.call(this,o,e)));o.$dots=t.appendTo(o.options.appendDots),o.$dots.find("li").first().addClass("slick-active")}},e.prototype.buildOut=function(){var e=this;e.$slides=e.$slider.children(e.options.slide+":not(.slick-cloned)").addClass("slick-slide"),e.slideCount=e.$slides.length,e.$slides.each(function(e,t){i(t).attr("data-slick-index",e).data("originalStyling",i(t).attr("style")||"")}),e.$slider.addClass("slick-slider"),e.$slideTrack=0===e.slideCount?i('<div class="slick-track"/>').appendTo(e.$slider):e.$slides.wrapAll('<div class="slick-track"/>').parent(),e.$list=e.$slideTrack.wrap('<div class="slick-list"/>').parent(),e.$slideTrack.css("opacity",0),!0!==e.options.centerMode&&!0!==e.options.swipeToSlide||(e.options.slidesToScroll=1),i("img[data-lazy]",e.$slider).not("[src]").addClass("slick-loading"),e.setupInfinite(),e.buildArrows(),e.buildDots(),e.updateDots(),e.setSlideClasses("number"==typeof e.currentSlide?e.currentSlide:0),!0===e.options.draggable&&e.$list.addClass("draggable")},e.prototype.buildRows=function(){var i,e,t,o,s,n,r,l=this;if(o=document.createDocumentFragment(),n=l.$slider.children(),l.options.rows>1){for(r=l.options.slidesPerRow*l.options.rows,s=Math.ceil(n.length/r),i=0;i<s;i++){var d=document.createElement("div");for(e=0;e<l.options.rows;e++){var a=document.createElement("div");for(t=0;t<l.options.slidesPerRow;t++){var c=i*r+(e*l.options.slidesPerRow+t);n.get(c)&&a.appendChild(n.get(c))}d.appendChild(a)}o.appendChild(d)}l.$slider.empty().append(o),l.$slider.children().children().children().css({width:100/l.options.slidesPerRow+"%",display:"inline-block"})}},e.prototype.checkResponsive=function(e,t){var o,s,n,r=this,l=!1,d=r.$slider.width(),a=window.innerWidth||i(window).width();if("window"===r.respondTo?n=a:"slider"===r.respondTo?n=d:"min"===r.respondTo&&(n=Math.min(a,d)),r.options.responsive&&r.options.responsive.length&&null!==r.options.responsive){s=null;for(o in r.breakpoints)r.breakpoints.hasOwnProperty(o)&&(!1===r.originalSettings.mobileFirst?n<r.breakpoints[o]&&(s=r.breakpoints[o]):n>r.breakpoints[o]&&(s=r.breakpoints[o]));null!==s?null!==r.activeBreakpoint?(s!==r.activeBreakpoint||t)&&(r.activeBreakpoint=s,"unslick"===r.breakpointSettings[s]?r.unslick(s):(r.options=i.extend({},r.originalSettings,r.breakpointSettings[s]),!0===e&&(r.currentSlide=r.options.initialSlide),r.refresh(e)),l=s):(r.activeBreakpoint=s,"unslick"===r.breakpointSettings[s]?r.unslick(s):(r.options=i.extend({},r.originalSettings,r.breakpointSettings[s]),!0===e&&(r.currentSlide=r.options.initialSlide),r.refresh(e)),l=s):null!==r.activeBreakpoint&&(r.activeBreakpoint=null,r.options=r.originalSettings,!0===e&&(r.currentSlide=r.options.initialSlide),r.refresh(e),l=s),e||!1===l||r.$slider.trigger("breakpoint",[r,l])}},e.prototype.changeSlide=function(e,t){var o,s,n,r=this,l=i(e.currentTarget);switch(l.is("a")&&e.preventDefault(),l.is("li")||(l=l.closest("li")),n=r.slideCount%r.options.slidesToScroll!=0,o=n?0:(r.slideCount-r.currentSlide)%r.options.slidesToScroll,e.data.message){case"previous":s=0===o?r.options.slidesToScroll:r.options.slidesToShow-o,r.slideCount>r.options.slidesToShow&&r.slideHandler(r.currentSlide-s,!1,t);break;case"next":s=0===o?r.options.slidesToScroll:o,r.slideCount>r.options.slidesToShow&&r.slideHandler(r.currentSlide+s,!1,t);break;case"index":var d=0===e.data.index?0:e.data.index||l.index()*r.options.slidesToScroll;r.slideHandler(r.checkNavigable(d),!1,t),l.children().trigger("focus");break;default:return}},e.prototype.checkNavigable=function(i){var e,t;if(e=this.getNavigableIndexes(),t=0,i>e[e.length-1])i=e[e.length-1];else for(var o in e){if(i<e[o]){i=t;break}t=e[o]}return i},e.prototype.cleanUpEvents=function(){var e=this;e.options.dots&&null!==e.$dots&&(i("li",e.$dots).off("click.slick",e.changeSlide).off("mouseenter.slick",i.proxy(e.interrupt,e,!0)).off("mouseleave.slick",i.proxy(e.interrupt,e,!1)),!0===e.options.accessibility&&e.$dots.off("keydown.slick",e.keyHandler)),e.$slider.off("focus.slick blur.slick"),!0===e.options.arrows&&e.slideCount>e.options.slidesToShow&&(e.$prevArrow&&e.$prevArrow.off("click.slick",e.changeSlide),e.$nextArrow&&e.$nextArrow.off("click.slick",e.changeSlide),!0===e.options.accessibility&&(e.$prevArrow&&e.$prevArrow.off("keydown.slick",e.keyHandler),e.$nextArrow&&e.$nextArrow.off("keydown.slick",e.keyHandler))),e.$list.off("touchstart.slick mousedown.slick",e.swipeHandler),e.$list.off("touchmove.slick mousemove.slick",e.swipeHandler),e.$list.off("touchend.slick mouseup.slick",e.swipeHandler),e.$list.off("touchcancel.slick mouseleave.slick",e.swipeHandler),e.$list.off("click.slick",e.clickHandler),i(document).off(e.visibilityChange,e.visibility),e.cleanUpSlideEvents(),!0===e.options.accessibility&&e.$list.off("keydown.slick",e.keyHandler),!0===e.options.focusOnSelect&&i(e.$slideTrack).children().off("click.slick",e.selectHandler),i(window).off("orientationchange.slick.slick-"+e.instanceUid,e.orientationChange),i(window).off("resize.slick.slick-"+e.instanceUid,e.resize),i("[draggable!=true]",e.$slideTrack).off("dragstart",e.preventDefault),i(window).off("load.slick.slick-"+e.instanceUid,e.setPosition)},e.prototype.cleanUpSlideEvents=function(){var e=this;e.$list.off("mouseenter.slick",i.proxy(e.interrupt,e,!0)),e.$list.off("mouseleave.slick",i.proxy(e.interrupt,e,!1))},e.prototype.cleanUpRows=function(){var i,e=this;e.options.rows>1&&((i=e.$slides.children().children()).removeAttr("style"),e.$slider.empty().append(i))},e.prototype.clickHandler=function(i){!1===this.shouldClick&&(i.stopImmediatePropagation(),i.stopPropagation(),i.preventDefault())},e.prototype.destroy=function(e){var t=this;t.autoPlayClear(),t.touchObject={},t.cleanUpEvents(),i(".slick-cloned",t.$slider).detach(),t.$dots&&t.$dots.remove(),t.$prevArrow&&t.$prevArrow.length&&(t.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display",""),t.htmlExpr.test(t.options.prevArrow)&&t.$prevArrow.remove()),t.$nextArrow&&t.$nextArrow.length&&(t.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display",""),t.htmlExpr.test(t.options.nextArrow)&&t.$nextArrow.remove()),t.$slides&&(t.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function(){i(this).attr("style",i(this).data("originalStyling"))}),t.$slideTrack.children(this.options.slide).detach(),t.$slideTrack.detach(),t.$list.detach(),t.$slider.append(t.$slides)),t.cleanUpRows(),t.$slider.removeClass("slick-slider"),t.$slider.removeClass("slick-initialized"),t.$slider.removeClass("slick-dotted"),t.unslicked=!0,e||t.$slider.trigger("destroy",[t])},e.prototype.disableTransition=function(i){var e=this,t={};t[e.transitionType]="",!1===e.options.fade?e.$slideTrack.css(t):e.$slides.eq(i).css(t)},e.prototype.fadeSlide=function(i,e){var t=this;!1===t.cssTransitions?(t.$slides.eq(i).css({zIndex:t.options.zIndex}),t.$slides.eq(i).animate({opacity:1},t.options.speed,t.options.easing,e)):(t.applyTransition(i),t.$slides.eq(i).css({opacity:1,zIndex:t.options.zIndex}),e&&setTimeout(function(){t.disableTransition(i),e.call()},t.options.speed))},e.prototype.fadeSlideOut=function(i){var e=this;!1===e.cssTransitions?e.$slides.eq(i).animate({opacity:0,zIndex:e.options.zIndex-2},e.options.speed,e.options.easing):(e.applyTransition(i),e.$slides.eq(i).css({opacity:0,zIndex:e.options.zIndex-2}))},e.prototype.filterSlides=e.prototype.slickFilter=function(i){var e=this;null!==i&&(e.$slidesCache=e.$slides,e.unload(),e.$slideTrack.children(this.options.slide).detach(),e.$slidesCache.filter(i).appendTo(e.$slideTrack),e.reinit())},e.prototype.focusHandler=function(){var e=this;e.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick","*",function(t){t.stopImmediatePropagation();var o=i(this);setTimeout(function(){e.options.pauseOnFocus&&(e.focussed=o.is(":focus"),e.autoPlay())},0)})},e.prototype.getCurrent=e.prototype.slickCurrentSlide=function(){return this.currentSlide},e.prototype.getDotCount=function(){var i=this,e=0,t=0,o=0;if(!0===i.options.infinite)if(i.slideCount<=i.options.slidesToShow)++o;else for(;e<i.slideCount;)++o,e=t+i.options.slidesToScroll,t+=i.options.slidesToScroll<=i.options.slidesToShow?i.options.slidesToScroll:i.options.slidesToShow;else if(!0===i.options.centerMode)o=i.slideCount;else if(i.options.asNavFor)for(;e<i.slideCount;)++o,e=t+i.options.slidesToScroll,t+=i.options.slidesToScroll<=i.options.slidesToShow?i.options.slidesToScroll:i.options.slidesToShow;else o=1+Math.ceil((i.slideCount-i.options.slidesToShow)/i.options.slidesToScroll);return o-1},e.prototype.getLeft=function(i){var e,t,o,s,n=this,r=0;return n.slideOffset=0,t=n.$slides.first().outerHeight(!0),!0===n.options.infinite?(n.slideCount>n.options.slidesToShow&&(n.slideOffset=n.slideWidth*n.options.slidesToShow*-1,s=-1,!0===n.options.vertical&&!0===n.options.centerMode&&(2===n.options.slidesToShow?s=-1.5:1===n.options.slidesToShow&&(s=-2)),r=t*n.options.slidesToShow*s),n.slideCount%n.options.slidesToScroll!=0&&i+n.options.slidesToScroll>n.slideCount&&n.slideCount>n.options.slidesToShow&&(i>n.slideCount?(n.slideOffset=(n.options.slidesToShow-(i-n.slideCount))*n.slideWidth*-1,r=(n.options.slidesToShow-(i-n.slideCount))*t*-1):(n.slideOffset=n.slideCount%n.options.slidesToScroll*n.slideWidth*-1,r=n.slideCount%n.options.slidesToScroll*t*-1))):i+n.options.slidesToShow>n.slideCount&&(n.slideOffset=(i+n.options.slidesToShow-n.slideCount)*n.slideWidth,r=(i+n.options.slidesToShow-n.slideCount)*t),n.slideCount<=n.options.slidesToShow&&(n.slideOffset=0,r=0),!0===n.options.centerMode&&n.slideCount<=n.options.slidesToShow?n.slideOffset=n.slideWidth*Math.floor(n.options.slidesToShow)/2-n.slideWidth*n.slideCount/2:!0===n.options.centerMode&&!0===n.options.infinite?n.slideOffset+=n.slideWidth*Math.floor(n.options.slidesToShow/2)-n.slideWidth:!0===n.options.centerMode&&(n.slideOffset=0,n.slideOffset+=n.slideWidth*Math.floor(n.options.slidesToShow/2)),e=!1===n.options.vertical?i*n.slideWidth*-1+n.slideOffset:i*t*-1+r,!0===n.options.variableWidth&&(o=n.slideCount<=n.options.slidesToShow||!1===n.options.infinite?n.$slideTrack.children(".slick-slide").eq(i):n.$slideTrack.children(".slick-slide").eq(i+n.options.slidesToShow),e=!0===n.options.rtl?o[0]?-1*(n.$slideTrack.width()-o[0].offsetLeft-o.width()):0:o[0]?-1*o[0].offsetLeft:0,!0===n.options.centerMode&&(o=n.slideCount<=n.options.slidesToShow||!1===n.options.infinite?n.$slideTrack.children(".slick-slide").eq(i):n.$slideTrack.children(".slick-slide").eq(i+n.options.slidesToShow+1),e=!0===n.options.rtl?o[0]?-1*(n.$slideTrack.width()-o[0].offsetLeft-o.width()):0:o[0]?-1*o[0].offsetLeft:0,e+=(n.$list.width()-o.outerWidth())/2)),e},e.prototype.getOption=e.prototype.slickGetOption=function(i){return this.options[i]},e.prototype.getNavigableIndexes=function(){var i,e=this,t=0,o=0,s=[];for(!1===e.options.infinite?i=e.slideCount:(t=-1*e.options.slidesToScroll,o=-1*e.options.slidesToScroll,i=2*e.slideCount);t<i;)s.push(t),t=o+e.options.slidesToScroll,o+=e.options.slidesToScroll<=e.options.slidesToShow?e.options.slidesToScroll:e.options.slidesToShow;return s},e.prototype.getSlick=function(){return this},e.prototype.getSlideCount=function(){var e,t,o=this;return t=!0===o.options.centerMode?o.slideWidth*Math.floor(o.options.slidesToShow/2):0,!0===o.options.swipeToSlide?(o.$slideTrack.find(".slick-slide").each(function(s,n){if(n.offsetLeft-t+i(n).outerWidth()/2>-1*o.swipeLeft)return e=n,!1}),Math.abs(i(e).attr("data-slick-index")-o.currentSlide)||1):o.options.slidesToScroll},e.prototype.goTo=e.prototype.slickGoTo=function(i,e){this.changeSlide({data:{message:"index",index:parseInt(i)}},e)},e.prototype.init=function(e){var t=this;i(t.$slider).hasClass("slick-initialized")||(i(t.$slider).addClass("slick-initialized"),t.buildRows(),t.buildOut(),t.setProps(),t.startLoad(),t.loadSlider(),t.initializeEvents(),t.updateArrows(),t.updateDots(),t.checkResponsive(!0),t.focusHandler()),e&&t.$slider.trigger("init",[t]),!0===t.options.accessibility&&t.initADA(),t.options.autoplay&&(t.paused=!1,t.autoPlay())},e.prototype.initADA=function(){var e=this,t=Math.ceil(e.slideCount/e.options.slidesToShow),o=e.getNavigableIndexes().filter(function(i){return i>=0&&i<e.slideCount});e.$slides.add(e.$slideTrack.find(".slick-cloned")).attr({"aria-hidden":"true",tabindex:"-1"}).find("a, input, button, select").attr({tabindex:"-1"}),null!==e.$dots&&(e.$slides.not(e.$slideTrack.find(".slick-cloned")).each(function(t){var s=o.indexOf(t);i(this).attr({role:"tabpanel",id:"slick-slide"+e.instanceUid+t,tabindex:-1}),-1!==s&&i(this).attr({"aria-describedby":"slick-slide-control"+e.instanceUid+s})}),e.$dots.attr("role","tablist").find("li").each(function(s){var n=o[s];i(this).attr({role:"presentation"}),i(this).find("button").first().attr({role:"tab",id:"slick-slide-control"+e.instanceUid+s,"aria-controls":"slick-slide"+e.instanceUid+n,"aria-label":s+1+" of "+t,"aria-selected":null,tabindex:"-1"})}).eq(e.currentSlide).find("button").attr({"aria-selected":"true",tabindex:"0"}).end());for(var s=e.currentSlide,n=s+e.options.slidesToShow;s<n;s++)e.$slides.eq(s).attr("tabindex",0);e.activateADA()},e.prototype.initArrowEvents=function(){var i=this;!0===i.options.arrows&&i.slideCount>i.options.slidesToShow&&(i.$prevArrow.off("click.slick").on("click.slick",{message:"previous"},i.changeSlide),i.$nextArrow.off("click.slick").on("click.slick",{message:"next"},i.changeSlide),!0===i.options.accessibility&&(i.$prevArrow.on("keydown.slick",i.keyHandler),i.$nextArrow.on("keydown.slick",i.keyHandler)))},e.prototype.initDotEvents=function(){var e=this;!0===e.options.dots&&(i("li",e.$dots).on("click.slick",{message:"index"},e.changeSlide),!0===e.options.accessibility&&e.$dots.on("keydown.slick",e.keyHandler)),!0===e.options.dots&&!0===e.options.pauseOnDotsHover&&i("li",e.$dots).on("mouseenter.slick",i.proxy(e.interrupt,e,!0)).on("mouseleave.slick",i.proxy(e.interrupt,e,!1))},e.prototype.initSlideEvents=function(){var e=this;e.options.pauseOnHover&&(e.$list.on("mouseenter.slick",i.proxy(e.interrupt,e,!0)),e.$list.on("mouseleave.slick",i.proxy(e.interrupt,e,!1)))},e.prototype.initializeEvents=function(){var e=this;e.initArrowEvents(),e.initDotEvents(),e.initSlideEvents(),e.$list.on("touchstart.slick mousedown.slick",{action:"start"},e.swipeHandler),e.$list.on("touchmove.slick mousemove.slick",{action:"move"},e.swipeHandler),e.$list.on("touchend.slick mouseup.slick",{action:"end"},e.swipeHandler),e.$list.on("touchcancel.slick mouseleave.slick",{action:"end"},e.swipeHandler),e.$list.on("click.slick",e.clickHandler),i(document).on(e.visibilityChange,i.proxy(e.visibility,e)),!0===e.options.accessibility&&e.$list.on("keydown.slick",e.keyHandler),!0===e.options.focusOnSelect&&i(e.$slideTrack).children().on("click.slick",e.selectHandler),i(window).on("orientationchange.slick.slick-"+e.instanceUid,i.proxy(e.orientationChange,e)),i(window).on("resize.slick.slick-"+e.instanceUid,i.proxy(e.resize,e)),i("[draggable!=true]",e.$slideTrack).on("dragstart",e.preventDefault),i(window).on("load.slick.slick-"+e.instanceUid,e.setPosition),i(e.setPosition)},e.prototype.initUI=function(){var i=this;!0===i.options.arrows&&i.slideCount>i.options.slidesToShow&&(i.$prevArrow.show(),i.$nextArrow.show()),!0===i.options.dots&&i.slideCount>i.options.slidesToShow&&i.$dots.show()},e.prototype.keyHandler=function(i){var e=this;i.target.tagName.match("TEXTAREA|INPUT|SELECT")||(37===i.keyCode&&!0===e.options.accessibility?e.changeSlide({data:{message:!0===e.options.rtl?"next":"previous"}}):39===i.keyCode&&!0===e.options.accessibility&&e.changeSlide({data:{message:!0===e.options.rtl?"previous":"next"}}))},e.prototype.lazyLoad=function(){function e(e){i("img[data-lazy]",e).each(function(){var e=i(this),t=i(this).attr("data-lazy"),o=i(this).attr("data-srcset"),s=i(this).attr("data-sizes")||n.$slider.attr("data-sizes"),r=document.createElement("img");r.onload=function(){e.animate({opacity:0},100,function(){o&&(e.attr("srcset",o),s&&e.attr("sizes",s)),e.attr("src",t).animate({opacity:1},200,function(){e.removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading")}),n.$slider.trigger("lazyLoaded",[n,e,t])})},r.onerror=function(){e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"),n.$slider.trigger("lazyLoadError",[n,e,t])},r.src=t})}var t,o,s,n=this;if(!0===n.options.centerMode?!0===n.options.infinite?s=(o=n.currentSlide+(n.options.slidesToShow/2+1))+n.options.slidesToShow+2:(o=Math.max(0,n.currentSlide-(n.options.slidesToShow/2+1)),s=n.options.slidesToShow/2+1+2+n.currentSlide):(o=n.options.infinite?n.options.slidesToShow+n.currentSlide:n.currentSlide,s=Math.ceil(o+n.options.slidesToShow),!0===n.options.fade&&(o>0&&o--,s<=n.slideCount&&s++)),t=n.$slider.find(".slick-slide").slice(o,s),"anticipated"===n.options.lazyLoad)for(var r=o-1,l=s,d=n.$slider.find(".slick-slide"),a=0;a<n.options.slidesToScroll;a++)r<0&&(r=n.slideCount-1),t=(t=t.add(d.eq(r))).add(d.eq(l)),r--,l++;e(t),n.slideCount<=n.options.slidesToShow?e(n.$slider.find(".slick-slide")):n.currentSlide>=n.slideCount-n.options.slidesToShow?e(n.$slider.find(".slick-cloned").slice(0,n.options.slidesToShow)):0===n.currentSlide&&e(n.$slider.find(".slick-cloned").slice(-1*n.options.slidesToShow))},e.prototype.loadSlider=function(){var i=this;i.setPosition(),i.$slideTrack.css({opacity:1}),i.$slider.removeClass("slick-loading"),i.initUI(),"progressive"===i.options.lazyLoad&&i.progressiveLazyLoad()},e.prototype.next=e.prototype.slickNext=function(){this.changeSlide({data:{message:"next"}})},e.prototype.orientationChange=function(){var i=this;i.checkResponsive(),i.setPosition()},e.prototype.pause=e.prototype.slickPause=function(){var i=this;i.autoPlayClear(),i.paused=!0},e.prototype.play=e.prototype.slickPlay=function(){var i=this;i.autoPlay(),i.options.autoplay=!0,i.paused=!1,i.focussed=!1,i.interrupted=!1},e.prototype.postSlide=function(e){var t=this;t.unslicked||(t.$slider.trigger("afterChange",[t,e]),t.animating=!1,t.slideCount>t.options.slidesToShow&&t.setPosition(),t.swipeLeft=null,t.options.autoplay&&t.autoPlay(),!0===t.options.accessibility&&(t.initADA(),t.options.focusOnChange&&i(t.$slides.get(t.currentSlide)).attr("tabindex",0).focus()))},e.prototype.prev=e.prototype.slickPrev=function(){this.changeSlide({data:{message:"previous"}})},e.prototype.preventDefault=function(i){i.preventDefault()},e.prototype.progressiveLazyLoad=function(e){e=e||1;var t,o,s,n,r,l=this,d=i("img[data-lazy]",l.$slider);d.length?(t=d.first(),o=t.attr("data-lazy"),s=t.attr("data-srcset"),n=t.attr("data-sizes")||l.$slider.attr("data-sizes"),(r=document.createElement("img")).onload=function(){s&&(t.attr("srcset",s),n&&t.attr("sizes",n)),t.attr("src",o).removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading"),!0===l.options.adaptiveHeight&&l.setPosition(),l.$slider.trigger("lazyLoaded",[l,t,o]),l.progressiveLazyLoad()},r.onerror=function(){e<3?setTimeout(function(){l.progressiveLazyLoad(e+1)},500):(t.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"),l.$slider.trigger("lazyLoadError",[l,t,o]),l.progressiveLazyLoad())},r.src=o):l.$slider.trigger("allImagesLoaded",[l])},e.prototype.refresh=function(e){var t,o,s=this;o=s.slideCount-s.options.slidesToShow,!s.options.infinite&&s.currentSlide>o&&(s.currentSlide=o),s.slideCount<=s.options.slidesToShow&&(s.currentSlide=0),t=s.currentSlide,s.destroy(!0),i.extend(s,s.initials,{currentSlide:t}),s.init(),e||s.changeSlide({data:{message:"index",index:t}},!1)},e.prototype.registerBreakpoints=function(){var e,t,o,s=this,n=s.options.responsive||null;if("array"===i.type(n)&&n.length){s.respondTo=s.options.respondTo||"window";for(e in n)if(o=s.breakpoints.length-1,n.hasOwnProperty(e)){for(t=n[e].breakpoint;o>=0;)s.breakpoints[o]&&s.breakpoints[o]===t&&s.breakpoints.splice(o,1),o--;s.breakpoints.push(t),s.breakpointSettings[t]=n[e].settings}s.breakpoints.sort(function(i,e){return s.options.mobileFirst?i-e:e-i})}},e.prototype.reinit=function(){var e=this;e.$slides=e.$slideTrack.children(e.options.slide).addClass("slick-slide"),e.slideCount=e.$slides.length,e.currentSlide>=e.slideCount&&0!==e.currentSlide&&(e.currentSlide=e.currentSlide-e.options.slidesToScroll),e.slideCount<=e.options.slidesToShow&&(e.currentSlide=0),e.registerBreakpoints(),e.setProps(),e.setupInfinite(),e.buildArrows(),e.updateArrows(),e.initArrowEvents(),e.buildDots(),e.updateDots(),e.initDotEvents(),e.cleanUpSlideEvents(),e.initSlideEvents(),e.checkResponsive(!1,!0),!0===e.options.focusOnSelect&&i(e.$slideTrack).children().on("click.slick",e.selectHandler),e.setSlideClasses("number"==typeof e.currentSlide?e.currentSlide:0),e.setPosition(),e.focusHandler(),e.paused=!e.options.autoplay,e.autoPlay(),e.$slider.trigger("reInit",[e])},e.prototype.resize=function(){var e=this;i(window).width()!==e.windowWidth&&(clearTimeout(e.windowDelay),e.windowDelay=window.setTimeout(function(){e.windowWidth=i(window).width(),e.checkResponsive(),e.unslicked||e.setPosition()},50))},e.prototype.removeSlide=e.prototype.slickRemove=function(i,e,t){var o=this;if(i="boolean"==typeof i?!0===(e=i)?0:o.slideCount-1:!0===e?--i:i,o.slideCount<1||i<0||i>o.slideCount-1)return!1;o.unload(),!0===t?o.$slideTrack.children().remove():o.$slideTrack.children(this.options.slide).eq(i).remove(),o.$slides=o.$slideTrack.children(this.options.slide),o.$slideTrack.children(this.options.slide).detach(),o.$slideTrack.append(o.$slides),o.$slidesCache=o.$slides,o.reinit()},e.prototype.setCSS=function(i){var e,t,o=this,s={};!0===o.options.rtl&&(i=-i),e="left"==o.positionProp?Math.ceil(i)+"px":"0px",t="top"==o.positionProp?Math.ceil(i)+"px":"0px",s[o.positionProp]=i,!1===o.transformsEnabled?o.$slideTrack.css(s):(s={},!1===o.cssTransitions?(s[o.animType]="translate("+e+", "+t+")",o.$slideTrack.css(s)):(s[o.animType]="translate3d("+e+", "+t+", 0px)",o.$slideTrack.css(s)))},e.prototype.setDimensions=function(){var i=this;!1===i.options.vertical?!0===i.options.centerMode&&i.$list.css({padding:"0px "+i.options.centerPadding}):(i.$list.height(i.$slides.first().outerHeight(!0)*i.options.slidesToShow),!0===i.options.centerMode&&i.$list.css({padding:i.options.centerPadding+" 0px"})),i.listWidth=i.$list.width(),i.listHeight=i.$list.height(),!1===i.options.vertical&&!1===i.options.variableWidth?(i.slideWidth=Math.ceil(i.listWidth/i.options.slidesToShow),i.$slideTrack.width(Math.ceil(i.slideWidth*i.$slideTrack.children(".slick-slide").length))):!0===i.options.variableWidth?i.$slideTrack.width(5e3*i.slideCount):(i.slideWidth=Math.ceil(i.listWidth),i.$slideTrack.height(Math.ceil(i.$slides.first().outerHeight(!0)*i.$slideTrack.children(".slick-slide").length)));var e=i.$slides.first().outerWidth(!0)-i.$slides.first().width();!1===i.options.variableWidth&&i.$slideTrack.children(".slick-slide").width(i.slideWidth-e)},e.prototype.setFade=function(){var e,t=this;t.$slides.each(function(o,s){e=t.slideWidth*o*-1,!0===t.options.rtl?i(s).css({position:"relative",right:e,top:0,zIndex:t.options.zIndex-2,opacity:0}):i(s).css({position:"relative",left:e,top:0,zIndex:t.options.zIndex-2,opacity:0})}),t.$slides.eq(t.currentSlide).css({zIndex:t.options.zIndex-1,opacity:1})},e.prototype.setHeight=function(){var i=this;if(1===i.options.slidesToShow&&!0===i.options.adaptiveHeight&&!1===i.options.vertical){var e=i.$slides.eq(i.currentSlide).outerHeight(!0);i.$list.css("height",e)}},e.prototype.setOption=e.prototype.slickSetOption=function(){var e,t,o,s,n,r=this,l=!1;if("object"===i.type(arguments[0])?(o=arguments[0],l=arguments[1],n="multiple"):"string"===i.type(arguments[0])&&(o=arguments[0],s=arguments[1],l=arguments[2],"responsive"===arguments[0]&&"array"===i.type(arguments[1])?n="responsive":void 0!==arguments[1]&&(n="single")),"single"===n)r.options[o]=s;else if("multiple"===n)i.each(o,function(i,e){r.options[i]=e});else if("responsive"===n)for(t in s)if("array"!==i.type(r.options.responsive))r.options.responsive=[s[t]];else{for(e=r.options.responsive.length-1;e>=0;)r.options.responsive[e].breakpoint===s[t].breakpoint&&r.options.responsive.splice(e,1),e--;r.options.responsive.push(s[t])}l&&(r.unload(),r.reinit())},e.prototype.setPosition=function(){var i=this;i.setDimensions(),i.setHeight(),!1===i.options.fade?i.setCSS(i.getLeft(i.currentSlide)):i.setFade(),i.$slider.trigger("setPosition",[i])},e.prototype.setProps=function(){var i=this,e=document.body.style;i.positionProp=!0===i.options.vertical?"top":"left","top"===i.positionProp?i.$slider.addClass("slick-vertical"):i.$slider.removeClass("slick-vertical"),void 0===e.WebkitTransition&&void 0===e.MozTransition&&void 0===e.msTransition||!0===i.options.useCSS&&(i.cssTransitions=!0),i.options.fade&&("number"==typeof i.options.zIndex?i.options.zIndex<3&&(i.options.zIndex=3):i.options.zIndex=i.defaults.zIndex),void 0!==e.OTransform&&(i.animType="OTransform",i.transformType="-o-transform",i.transitionType="OTransition",void 0===e.perspectiveProperty&&void 0===e.webkitPerspective&&(i.animType=!1)),void 0!==e.MozTransform&&(i.animType="MozTransform",i.transformType="-moz-transform",i.transitionType="MozTransition",void 0===e.perspectiveProperty&&void 0===e.MozPerspective&&(i.animType=!1)),void 0!==e.webkitTransform&&(i.animType="webkitTransform",i.transformType="-webkit-transform",i.transitionType="webkitTransition",void 0===e.perspectiveProperty&&void 0===e.webkitPerspective&&(i.animType=!1)),void 0!==e.msTransform&&(i.animType="msTransform",i.transformType="-ms-transform",i.transitionType="msTransition",void 0===e.msTransform&&(i.animType=!1)),void 0!==e.transform&&!1!==i.animType&&(i.animType="transform",i.transformType="transform",i.transitionType="transition"),i.transformsEnabled=i.options.useTransform&&null!==i.animType&&!1!==i.animType},e.prototype.setSlideClasses=function(i){var e,t,o,s,n=this;if(t=n.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden","true"),n.$slides.eq(i).addClass("slick-current"),!0===n.options.centerMode){var r=n.options.slidesToShow%2==0?1:0;e=Math.floor(n.options.slidesToShow/2),!0===n.options.infinite&&(i>=e&&i<=n.slideCount-1-e?n.$slides.slice(i-e+r,i+e+1).addClass("slick-active").attr("aria-hidden","false"):(o=n.options.slidesToShow+i,t.slice(o-e+1+r,o+e+2).addClass("slick-active").attr("aria-hidden","false")),0===i?t.eq(t.length-1-n.options.slidesToShow).addClass("slick-center"):i===n.slideCount-1&&t.eq(n.options.slidesToShow).addClass("slick-center")),n.$slides.eq(i).addClass("slick-center")}else i>=0&&i<=n.slideCount-n.options.slidesToShow?n.$slides.slice(i,i+n.options.slidesToShow).addClass("slick-active").attr("aria-hidden","false"):t.length<=n.options.slidesToShow?t.addClass("slick-active").attr("aria-hidden","false"):(s=n.slideCount%n.options.slidesToShow,o=!0===n.options.infinite?n.options.slidesToShow+i:i,n.options.slidesToShow==n.options.slidesToScroll&&n.slideCount-i<n.options.slidesToShow?t.slice(o-(n.options.slidesToShow-s),o+s).addClass("slick-active").attr("aria-hidden","false"):t.slice(o,o+n.options.slidesToShow).addClass("slick-active").attr("aria-hidden","false"));"ondemand"!==n.options.lazyLoad&&"anticipated"!==n.options.lazyLoad||n.lazyLoad()},e.prototype.setupInfinite=function(){var e,t,o,s=this;if(!0===s.options.fade&&(s.options.centerMode=!1),!0===s.options.infinite&&!1===s.options.fade&&(t=null,s.slideCount>s.options.slidesToShow)){for(o=!0===s.options.centerMode?s.options.slidesToShow+1:s.options.slidesToShow,e=s.slideCount;e>s.slideCount-o;e-=1)t=e-1,i(s.$slides[t]).clone(!0).attr("id","").attr("data-slick-index",t-s.slideCount).prependTo(s.$slideTrack).addClass("slick-cloned");for(e=0;e<o+s.slideCount;e+=1)t=e,i(s.$slides[t]).clone(!0).attr("id","").attr("data-slick-index",t+s.slideCount).appendTo(s.$slideTrack).addClass("slick-cloned");s.$slideTrack.find(".slick-cloned").find("[id]").each(function(){i(this).attr("id","")})}},e.prototype.interrupt=function(i){var e=this;i||e.autoPlay(),e.interrupted=i},e.prototype.selectHandler=function(e){var t=this,o=i(e.target).is(".slick-slide")?i(e.target):i(e.target).parents(".slick-slide"),s=parseInt(o.attr("data-slick-index"));s||(s=0),t.slideCount<=t.options.slidesToShow?t.slideHandler(s,!1,!0):t.slideHandler(s)},e.prototype.slideHandler=function(i,e,t){var o,s,n,r,l,d=null,a=this;if(e=e||!1,!(!0===a.animating&&!0===a.options.waitForAnimate||!0===a.options.fade&&a.currentSlide===i))if(!1===e&&a.asNavFor(i),o=i,d=a.getLeft(o),r=a.getLeft(a.currentSlide),a.currentLeft=null===a.swipeLeft?r:a.swipeLeft,!1===a.options.infinite&&!1===a.options.centerMode&&(i<0||i>a.getDotCount()*a.options.slidesToScroll))!1===a.options.fade&&(o=a.currentSlide,!0!==t?a.animateSlide(r,function(){a.postSlide(o)}):a.postSlide(o));else if(!1===a.options.infinite&&!0===a.options.centerMode&&(i<0||i>a.slideCount-a.options.slidesToScroll))!1===a.options.fade&&(o=a.currentSlide,!0!==t?a.animateSlide(r,function(){a.postSlide(o)}):a.postSlide(o));else{if(a.options.autoplay&&clearInterval(a.autoPlayTimer),s=o<0?a.slideCount%a.options.slidesToScroll!=0?a.slideCount-a.slideCount%a.options.slidesToScroll:a.slideCount+o:o>=a.slideCount?a.slideCount%a.options.slidesToScroll!=0?0:o-a.slideCount:o,a.animating=!0,a.$slider.trigger("beforeChange",[a,a.currentSlide,s]),n=a.currentSlide,a.currentSlide=s,a.setSlideClasses(a.currentSlide),a.options.asNavFor&&(l=(l=a.getNavTarget()).slick("getSlick")).slideCount<=l.options.slidesToShow&&l.setSlideClasses(a.currentSlide),a.updateDots(),a.updateArrows(),!0===a.options.fade)return!0!==t?(a.fadeSlideOut(n),a.fadeSlide(s,function(){a.postSlide(s)})):a.postSlide(s),void a.animateHeight();!0!==t?a.animateSlide(d,function(){a.postSlide(s)}):a.postSlide(s)}},e.prototype.startLoad=function(){var i=this;!0===i.options.arrows&&i.slideCount>i.options.slidesToShow&&(i.$prevArrow.hide(),i.$nextArrow.hide()),!0===i.options.dots&&i.slideCount>i.options.slidesToShow&&i.$dots.hide(),i.$slider.addClass("slick-loading")},e.prototype.swipeDirection=function(){var i,e,t,o,s=this;return i=s.touchObject.startX-s.touchObject.curX,e=s.touchObject.startY-s.touchObject.curY,t=Math.atan2(e,i),(o=Math.round(180*t/Math.PI))<0&&(o=360-Math.abs(o)),o<=45&&o>=0?!1===s.options.rtl?"left":"right":o<=360&&o>=315?!1===s.options.rtl?"left":"right":o>=135&&o<=225?!1===s.options.rtl?"right":"left":!0===s.options.verticalSwiping?o>=35&&o<=135?"down":"up":"vertical"},e.prototype.swipeEnd=function(i){var e,t,o=this;if(o.dragging=!1,o.swiping=!1,o.scrolling)return o.scrolling=!1,!1;if(o.interrupted=!1,o.shouldClick=!(o.touchObject.swipeLength>10),void 0===o.touchObject.curX)return!1;if(!0===o.touchObject.edgeHit&&o.$slider.trigger("edge",[o,o.swipeDirection()]),o.touchObject.swipeLength>=o.touchObject.minSwipe){switch(t=o.swipeDirection()){case"left":case"down":e=o.options.swipeToSlide?o.checkNavigable(o.currentSlide+o.getSlideCount()):o.currentSlide+o.getSlideCount(),o.currentDirection=0;break;case"right":case"up":e=o.options.swipeToSlide?o.checkNavigable(o.currentSlide-o.getSlideCount()):o.currentSlide-o.getSlideCount(),o.currentDirection=1}"vertical"!=t&&(o.slideHandler(e),o.touchObject={},o.$slider.trigger("swipe",[o,t]))}else o.touchObject.startX!==o.touchObject.curX&&(o.slideHandler(o.currentSlide),o.touchObject={})},e.prototype.swipeHandler=function(i){var e=this;if(!(!1===e.options.swipe||"ontouchend"in document&&!1===e.options.swipe||!1===e.options.draggable&&-1!==i.type.indexOf("mouse")))switch(e.touchObject.fingerCount=i.originalEvent&&void 0!==i.originalEvent.touches?i.originalEvent.touches.length:1,e.touchObject.minSwipe=e.listWidth/e.options.touchThreshold,!0===e.options.verticalSwiping&&(e.touchObject.minSwipe=e.listHeight/e.options.touchThreshold),i.data.action){case"start":e.swipeStart(i);break;case"move":e.swipeMove(i);break;case"end":e.swipeEnd(i)}},e.prototype.swipeMove=function(i){var e,t,o,s,n,r,l=this;return n=void 0!==i.originalEvent?i.originalEvent.touches:null,!(!l.dragging||l.scrolling||n&&1!==n.length)&&(e=l.getLeft(l.currentSlide),l.touchObject.curX=void 0!==n?n[0].pageX:i.clientX,l.touchObject.curY=void 0!==n?n[0].pageY:i.clientY,l.touchObject.swipeLength=Math.round(Math.sqrt(Math.pow(l.touchObject.curX-l.touchObject.startX,2))),r=Math.round(Math.sqrt(Math.pow(l.touchObject.curY-l.touchObject.startY,2))),!l.options.verticalSwiping&&!l.swiping&&r>4?(l.scrolling=!0,!1):(!0===l.options.verticalSwiping&&(l.touchObject.swipeLength=r),t=l.swipeDirection(),void 0!==i.originalEvent&&l.touchObject.swipeLength>4&&(l.swiping=!0,i.preventDefault()),s=(!1===l.options.rtl?1:-1)*(l.touchObject.curX>l.touchObject.startX?1:-1),!0===l.options.verticalSwiping&&(s=l.touchObject.curY>l.touchObject.startY?1:-1),o=l.touchObject.swipeLength,l.touchObject.edgeHit=!1,!1===l.options.infinite&&(0===l.currentSlide&&"right"===t||l.currentSlide>=l.getDotCount()&&"left"===t)&&(o=l.touchObject.swipeLength*l.options.edgeFriction,l.touchObject.edgeHit=!0),!1===l.options.vertical?l.swipeLeft=e+o*s:l.swipeLeft=e+o*(l.$list.height()/l.listWidth)*s,!0===l.options.verticalSwiping&&(l.swipeLeft=e+o*s),!0!==l.options.fade&&!1!==l.options.touchMove&&(!0===l.animating?(l.swipeLeft=null,!1):void l.setCSS(l.swipeLeft))))},e.prototype.swipeStart=function(i){var e,t=this;if(t.interrupted=!0,1!==t.touchObject.fingerCount||t.slideCount<=t.options.slidesToShow)return t.touchObject={},!1;void 0!==i.originalEvent&&void 0!==i.originalEvent.touches&&(e=i.originalEvent.touches[0]),t.touchObject.startX=t.touchObject.curX=void 0!==e?e.pageX:i.clientX,t.touchObject.startY=t.touchObject.curY=void 0!==e?e.pageY:i.clientY,t.dragging=!0},e.prototype.unfilterSlides=e.prototype.slickUnfilter=function(){var i=this;null!==i.$slidesCache&&(i.unload(),i.$slideTrack.children(this.options.slide).detach(),i.$slidesCache.appendTo(i.$slideTrack),i.reinit())},e.prototype.unload=function(){var e=this;i(".slick-cloned",e.$slider).remove(),e.$dots&&e.$dots.remove(),e.$prevArrow&&e.htmlExpr.test(e.options.prevArrow)&&e.$prevArrow.remove(),e.$nextArrow&&e.htmlExpr.test(e.options.nextArrow)&&e.$nextArrow.remove(),e.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden","true").css("width","")},e.prototype.unslick=function(i){var e=this;e.$slider.trigger("unslick",[e,i]),e.destroy()},e.prototype.updateArrows=function(){var i=this;Math.floor(i.options.slidesToShow/2),!0===i.options.arrows&&i.slideCount>i.options.slidesToShow&&!i.options.infinite&&(i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false"),i.$nextArrow.removeClass("slick-disabled").attr("aria-disabled","false"),0===i.currentSlide?(i.$prevArrow.addClass("slick-disabled").attr("aria-disabled","true"),i.$nextArrow.removeClass("slick-disabled").attr("aria-disabled","false")):i.currentSlide>=i.slideCount-i.options.slidesToShow&&!1===i.options.centerMode?(i.$nextArrow.addClass("slick-disabled").attr("aria-disabled","true"),i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false")):i.currentSlide>=i.slideCount-1&&!0===i.options.centerMode&&(i.$nextArrow.addClass("slick-disabled").attr("aria-disabled","true"),i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled","false")))},e.prototype.updateDots=function(){var i=this;null!==i.$dots&&(i.$dots.find("li").removeClass("slick-active").end(),i.$dots.find("li").eq(Math.floor(i.currentSlide/i.options.slidesToScroll)).addClass("slick-active"))},e.prototype.visibility=function(){var i=this;i.options.autoplay&&(document[i.hidden]?i.interrupted=!0:i.interrupted=!1)},i.fn.slick=function(){var i,t,o=this,s=arguments[0],n=Array.prototype.slice.call(arguments,1),r=o.length;for(i=0;i<r;i++)if("object"==typeof s||void 0===s?o[i].slick=new e(o[i],s):t=o[i].slick[s].apply(o[i].slick,n),void 0!==t)return t;return o}});
/* $Hyena v2.1 jQuery Plugin || Author: Crusader12 */
String.prototype.P=Number.prototype.P=function(){return parseFloat(this);};
String.prototype.S=function(key){return this.toString().split(',')[key];};
(function($){
	var Hyena={
		defaults:{
			sts:false, 					// PLAY STATE
			controls:true,				// USE MEDIA CONTROLS
			style:1, 					// PLAYER STYLE
			control_opacity:'0,0.9',	// BUTTON OPACITY
			fade_speed:'250,250',		// BUTTON FADE SPEED
			player_fade_speed:1000,		// INTIAL PLAYER FADEIN SPEED
			show_button:true,			// SHOW PLAY BUTTON INITIALLY
			on_timer:'0,0',				// TIMER DELAY, TIMER DURATION
			on_scroll:false,			// PLAY WHEN SCROLLED INTO VIEWPORT
			scroll_offset:0.15,			// PERCENTAGE OF WINDOW TO OFFSET
			on_hover:false,				// PLAY ON HOVER
			slate:'350,0.65,0'			// SLATE SPEED, SLATE OPACITY, SLATE TILE
		},

		/* INITIALIZATION */
		init:function(options){
			/* DROP OUT IF CANVAS IS NOT SUPPORTED */
			var e=document.createElement('canvas');
			if(!(!!(e.getContext && e.getContext('2d')))) return;

			// MERGE MAIN USER OPTIONS WITH DEFAULTS
			var mergedData=$.extend({}, Hyena.defaults, options);

			// LOOP THROUGH ALL HYENA GIFS
			for(var i=0, l=this.length; i<l; i++){
				var $t=$(this[i]),
					$d=$t.data('hyena'),
					hD=$d!=undefined ? $d : false,
					$S=$.support.Hyena;
				// MERGE DATA FROM DEFAULTS + GIF -> REASSIGN TO THIS GIF - + CURRENT SOURCE AS DATA ATTR */
				$.data($t, $.extend({}, mergedData, !hD?{} : hD||{}));
				$t.data('hyena',$.data($t))
				var D=$t.data().hyena;

				// SETUP PLAYER SKIN AND ADD SLATE
				$t.wrap('<div class="hy_plyr hy_plyr_'+D.style+'"><div class="hy_fr_'+D.style+'"/>');

				var $P=$t.parents('div.hy_plyr:first');
				$P.prepend('<div class="hy_bt_wr"><div class="hyena_slate"></div></div>');

				// ASSIGN TILE
				if(D.slate.S(2).P()>0) $P.find('div.hyena_slate').css('background','url(Hyena/controls/tiles/bg_'+D.slate.S(2).P()+'.png)');

				// CHECK SETTINGS
				if(D.on_scroll){ D.on_hover=false; D.controls=false; };
				if(D.on_hover){ D.controls=false; if($S.isTablet)D.on_hover=false; };

				// ON_TIMER SETTINGS
				D.tmrOn=D.on_timer.S(0).P();
				if(D.tmrOn>0){
					D.tmr_Off=D.on_timer.S(1).P();
					D.show_button=false;
					D.controls=false;
				};

				// SETUP OPTIONAL USER CONTROLS
				if(D.controls){
					$P.find('div.hy_bt_wr').prepend('<img src="Hyena/controls/'+D.style+'_play.png" class="hy_btn"/>');

					////////////////////////////////////////
					// BUTTON OPACITY CHANGE ON PLAYER HOVER
					////////////////////////////////////////
					$P.on('mouseenter',function(){
						var $this=$(this),
							$T=$this.find('img[src*=".gif"]'),//Edited
							D=$T.data().hyena,
							$B=$this.find('img.hy_btn'),
							curOp=$B.css('opacity').P(),
							newOp=D.control_opacity.S(1).P();
						if(curOp!==newOp){
							$B.stop(true,false).animate({opacity:D.control_opacity.S(1).P()},{duration:D.fade_speed.S(1).P(),queue:false});
						};
					}).on('mouseleave',function(){
						var $B=$(this).find('img.hy_btn'),
							curOp=$B.css('opacity').P(),
							newOp=D.control_opacity.S(0).P();
						if(curOp!==newOp){
							$B.stop(true,false).animate({opacity:D.control_opacity.S(0).P()},{duration:D.fade_speed.S(0).P(),queue:false});
						};
					});


					/////////////
					// START/STOP
					/////////////
					$P.find('div.hy_bt_wr').css('cursor','pointer').on($S.cEv,function(e){
						if(!e.handled){
							var $P=$(this).parents('div.hy_plyr:first'),
								D=$P.find('img[src*=".gif"]').data().hyena;//Edited

							if(D.tmrOn>0) return;

							// PLAY
							if(!D.sts){
								// ANIMATE OPACITY AND CHANGE TO STOP BUTTON
								$P.find('img.hy_btn').stop(true,false).animate({opacity:D.control_opacity.S(0).P()},
									{duration:D.fade_speed.S(0).P(),queue:false,complete:function(){
										$(this).attr('src','Hyena/controls/'+D.style+'_stop.png');
									}});
								Hyena.PL($P,D);
								// STOP
							}else{
								Hyena.ST($P,D,$(this),false);
							};
						};
						return false;
					});

					/////////////////////////////
					// NO CONTROLS - HOVER EVENTS
					/////////////////////////////
				}else if(D.on_hover){
					$P.on('mouseenter touchstart',function(){
						var $P=$(this),
							D=$P.find('img[src*=".gif"]').data().hyena;//Edited
						if(D.sts)return;
						Hyena.PL($P,D);

					}).on('mouseleave touchend',function(){
						var $P=$(this),
							D=$P.find('img[src*=".gif"]').data().hyena;//Edited
						if(!D.sts)return;
						Hyena.ST($P,D,false,false);
					});


					///////////////////
					// SCROLL INTO VIEW
					///////////////////
				}else if(D.on_scroll){
					Hyena.SCR($P,D);
					$(window).on('load',function(){ $(document).scroll(); });

					/////////////////////////////
					// NO CONTROLS - CLICK EVENTS
					/////////////////////////////
				}
				//else{  //Edited
					// ADD EVENT TO PLAY GIF ON CLICK
					$P.css('cursor','pointer').on($S.cEv,function(e){
						if(!e.handled){
							var $P=$(this),
								D=$P.find('img[src*=".gif"]').data().hyena;//Edited

							if(D.tmrOn>0) return;

							if(!D.sts){ Hyena.PL($P,D);
							}else{ 		Hyena.ST($P,D,false,false); };
						};
					});

			//	}; //Edited

				/////////////////////////////////
				// SETUP CANVAS AND PREP CONTROLS
				/////////////////////////////////
				Hyena.ST($P,D,D.show_button,true);
			};


			///////////////////////////////////////////////////////
			// UPDATE MEDIA PLAYER BUTTON POSITION ON WINDOW RESIZE
			///////////////////////////////////////////////////////
			$(window).on('resize',function(){
				var $players=$('div.hy_plyr'),
					numPlayers=$players.length;
				for(var i=0; i<numPlayers; i++){
					var $B=$($players[i]).find('img.hy_btn');
					$B.css({'margin-top':-($B.outerHeight())/2+'px','margin-left':-($B.outerWidth())/2+'px'});
				};
			}).resize();;
		},

////////////////////
// PLAY ANIMATED GIF
////////////////////
		PL:function($P,D){
			if(D.sts) return;

			// SETUP VARIABLES
			var $G=$P.find('img[src*=".gif"]'),//Edited
				$C=$P.find('canvas'),
				$S=$P.find('div.hyena_slate'),
				$B=$P.find('img.hy_btn');

			///////////////////////////
			// HIDE CANVAS AND SHOW GIF
			///////////////////////////
			$C[0].style.display='none';
			$G.css({visibility:'visible', display:'block'});

			//////////////////////////
			// ANIMATE SLATE LAYER OUT
			//////////////////////////
			$S.stop(true,false).animate({'opacity':0},{duration:D.slate.S(0).P(),queue:false});
			if($.support.Hyena.isTablet) $B.css('opacity',0);

			/////////////////////////
			// STOP ON OPTIONAL TIMER
			/////////////////////////
			if(D.tmr_Off>0) D.STMR=setTimeout(function(){
				clearTimeout(D.TMR);
				Hyena.ST($P,D,false,false);
			},D.tmr_Off);

			// UPDATE STATUS
			D.sts=true;
			$P.addClass('play');//Edited (added)
		},


/////////////////////
// PAUSE ANIMATED GIF
/////////////////////
		ST:function($P,D,SHW,init){
			var $G=$P.find('img[src*=".gif"]'),//Edited
				I=new Image(),
				Button=new Image();
			// ADD THE CANVAS
			if(!$P.find('canvas').length) $('<canvas class="hyena_canvas"/>').insertBefore($G);

			/////////////////////
			// LOAD GIF TO CANVAS
			/////////////////////
			I.onload=function(){
				var $C=$P.find('canvas')[0],
					CTX=$C.getContext('2d'),
					W=this.width,
					H=this.height,
					$S=$P.find('div.hyena_slate');

				//////////////////////////////////////////////////////////////////////////
				// DRAW TO THE CANVAS - RESPONSIVENESS CONFORMS TO WIDTH OF PARENT ELEMENT
				//////////////////////////////////////////////////////////////////////////
				$C.width=W;
				$C.height=H;
				$C.style.display='block';
				CTX.drawImage(I,0,0,W,H);

				///////////////////
				// SHOW SLATE LAYER
				///////////////////
				$S.css('opacity',0.01).animate({'opacity':D.slate.S(1).P()},{duration:D.slate.S(0).P(),queue:false});

				//////////////////////////////////
				// IF PLAYER IS HIDDEN, FADE IT IN
				//////////////////////////////////
				if($P.css('visibility')==='hidden') $P.css({visibility:'visible',opacity:0}).fadeTo(D.player_fade_speed.P(),1);

				// HIDE GIF
				$G.css({visibility:'hidden',display:'none'});

				// PLAY ON OPTIONAL TIMER
				if(D.tmrOn>0) D.TMR=setTimeout(function(){
					clearTimeout(D.STMR);
					Hyena.PL($P,D);
				},D.tmrOn);
			};
			I.src=$G.attr('src');


			/////////////////////////
			// INITIAL CONTROLS SETUP
			/////////////////////////
			if(D.controls){
				var BS=init || D.sts ? 'play' : 'stop';
				Button.onload=function(){
					var $B=$P.find('img.hy_btn');

					// SHOW_BUTTON SETTING
					if(!SHW && !$.support.Hyena.isTablet && !$.support.Hyena.isMobile){
						$B[0].style.display='none';
					}else{
						// ASSIGN PLAY BUTTON GRAPHIC AND FADE IN
						$B.attr('src','Hyena/controls/'+D.style+'_'+BS+'.png')
							.css({'margin-top':-($B.outerHeight())/2+'px','margin-left':-($B.outerWidth())/2+'px'})
							.stop(true,false).animate({opacity:D.control_opacity.S(1).P()},{duration:D.fade_speed.S(0).P(),queue:false});
					};
				};
				Button.src='Hyena/controls/'+D.style+'_'+BS+'.png';
			};

			// UPDATE STATUS
			D.sts=false;
			$P.removeClass('play'); //Edited (added)
		},


//////////////////
// SCROLLING EVENT
//////////////////
		SCR:function(o,D){
			// SETUP EVENT FOR ON_SCROLL
			$(document).on('scroll',function(){

				var w=$(window),
					dT=w.scrollTop(),
					dB=dT+w.height(),
					eT=o.offset().top,
					eB=eT+o.height(),
					customOffset=w.height()*D.scroll_offset.P(),
					inView=(eT <= dT+w.height()/2) && (eB >= dT + o.height()/4); //Edited


				/* IF IN VIEW DISPLAY ANIMATION */
				if(inView){
					if(D.sts)return;
					Hyena.PL(o,D);
				}else{
					if(!D.sts)return;
					Hyena.ST(o,D,false,false);
				};
			});
		}};

	$.fn.Hyena=function(method,options){
		if(Hyena[method]){ return Hyena[method].apply(this,Array.prototype.slice.call(arguments,1));
		}else if(typeof method==='object'||!method){ return Hyena.init.apply(this,arguments);
		}else{ $.error('Method '+method+' does not exist'); }
	}})(jQuery);
/* EXTEND JQUERY SUPPORT FUNCTIONS */(function(){var uA=navigator.userAgent.toLowerCase(); jQuery.support.Hyena={'cEv':!!('ontouchstart' in window)?'touchstart':'click','isTablet':uA.match(/iPad|Android|Kindle|NOOK|tablet/i)!==null,'isMobile':(/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()))}})();
/*!
 * Isotope PACKAGED v3.0.4
 *
 * Licensed GPLv3 for open source use
 * or Isotope Commercial License for commercial use
 *
 * http://isotope.metafizzy.co
 * Copyright 2017 Metafizzy
 */

!function(t,e){"function"==typeof define&&define.amd?define("jquery-bridget/jquery-bridget",["jquery"],function(i){return e(t,i)}):"object"==typeof module&&module.exports?module.exports=e(t,require("jquery")):t.jQueryBridget=e(t,t.jQuery)}(window,function(t,e){"use strict";function i(i,s,a){function u(t,e,o){var n,s="$()."+i+'("'+e+'")';return t.each(function(t,u){var h=a.data(u,i);if(!h)return void r(i+" not initialized. Cannot call methods, i.e. "+s);var d=h[e];if(!d||"_"==e.charAt(0))return void r(s+" is not a valid method");var l=d.apply(h,o);n=void 0===n?l:n}),void 0!==n?n:t}function h(t,e){t.each(function(t,o){var n=a.data(o,i);n?(n.option(e),n._init()):(n=new s(o,e),a.data(o,i,n))})}a=a||e||t.jQuery,a&&(s.prototype.option||(s.prototype.option=function(t){a.isPlainObject(t)&&(this.options=a.extend(!0,this.options,t))}),a.fn[i]=function(t){if("string"==typeof t){var e=n.call(arguments,1);return u(this,t,e)}return h(this,t),this},o(a))}function o(t){!t||t&&t.bridget||(t.bridget=i)}var n=Array.prototype.slice,s=t.console,r="undefined"==typeof s?function(){}:function(t){s.error(t)};return o(e||t.jQuery),i}),function(t,e){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",e):"object"==typeof module&&module.exports?module.exports=e():t.EvEmitter=e()}("undefined"!=typeof window?window:this,function(){function t(){}var e=t.prototype;return e.on=function(t,e){if(t&&e){var i=this._events=this._events||{},o=i[t]=i[t]||[];return o.indexOf(e)==-1&&o.push(e),this}},e.once=function(t,e){if(t&&e){this.on(t,e);var i=this._onceEvents=this._onceEvents||{},o=i[t]=i[t]||{};return o[e]=!0,this}},e.off=function(t,e){var i=this._events&&this._events[t];if(i&&i.length){var o=i.indexOf(e);return o!=-1&&i.splice(o,1),this}},e.emitEvent=function(t,e){var i=this._events&&this._events[t];if(i&&i.length){var o=0,n=i[o];e=e||[];for(var s=this._onceEvents&&this._onceEvents[t];n;){var r=s&&s[n];r&&(this.off(t,n),delete s[n]),n.apply(this,e),o+=r?0:1,n=i[o]}return this}},t}),function(t,e){"use strict";"function"==typeof define&&define.amd?define("get-size/get-size",[],function(){return e()}):"object"==typeof module&&module.exports?module.exports=e():t.getSize=e()}(window,function(){"use strict";function t(t){var e=parseFloat(t),i=t.indexOf("%")==-1&&!isNaN(e);return i&&e}function e(){}function i(){for(var t={width:0,height:0,innerWidth:0,innerHeight:0,outerWidth:0,outerHeight:0},e=0;e<h;e++){var i=u[e];t[i]=0}return t}function o(t){var e=getComputedStyle(t);return e||a("Style returned "+e+". Are you running this code in a hidden iframe on Firefox? See http://bit.ly/getsizebug1"),e}function n(){if(!d){d=!0;var e=document.createElement("div");e.style.width="200px",e.style.padding="1px 2px 3px 4px",e.style.borderStyle="solid",e.style.borderWidth="1px 2px 3px 4px",e.style.boxSizing="border-box";var i=document.body||document.documentElement;i.appendChild(e);var n=o(e);s.isBoxSizeOuter=r=200==t(n.width),i.removeChild(e)}}function s(e){if(n(),"string"==typeof e&&(e=document.querySelector(e)),e&&"object"==typeof e&&e.nodeType){var s=o(e);if("none"==s.display)return i();var a={};a.width=e.offsetWidth,a.height=e.offsetHeight;for(var d=a.isBorderBox="border-box"==s.boxSizing,l=0;l<h;l++){var f=u[l],c=s[f],m=parseFloat(c);a[f]=isNaN(m)?0:m}var p=a.paddingLeft+a.paddingRight,y=a.paddingTop+a.paddingBottom,g=a.marginLeft+a.marginRight,v=a.marginTop+a.marginBottom,_=a.borderLeftWidth+a.borderRightWidth,I=a.borderTopWidth+a.borderBottomWidth,z=d&&r,x=t(s.width);x!==!1&&(a.width=x+(z?0:p+_));var S=t(s.height);return S!==!1&&(a.height=S+(z?0:y+I)),a.innerWidth=a.width-(p+_),a.innerHeight=a.height-(y+I),a.outerWidth=a.width+g,a.outerHeight=a.height+v,a}}var r,a="undefined"==typeof console?e:function(t){console.error(t)},u=["paddingLeft","paddingRight","paddingTop","paddingBottom","marginLeft","marginRight","marginTop","marginBottom","borderLeftWidth","borderRightWidth","borderTopWidth","borderBottomWidth"],h=u.length,d=!1;return s}),function(t,e){"use strict";"function"==typeof define&&define.amd?define("desandro-matches-selector/matches-selector",e):"object"==typeof module&&module.exports?module.exports=e():t.matchesSelector=e()}(window,function(){"use strict";var t=function(){var t=window.Element.prototype;if(t.matches)return"matches";if(t.matchesSelector)return"matchesSelector";for(var e=["webkit","moz","ms","o"],i=0;i<e.length;i++){var o=e[i],n=o+"MatchesSelector";if(t[n])return n}}();return function(e,i){return e[t](i)}}),function(t,e){"function"==typeof define&&define.amd?define("fizzy-ui-utils/utils",["desandro-matches-selector/matches-selector"],function(i){return e(t,i)}):"object"==typeof module&&module.exports?module.exports=e(t,require("desandro-matches-selector")):t.fizzyUIUtils=e(t,t.matchesSelector)}(window,function(t,e){var i={};i.extend=function(t,e){for(var i in e)t[i]=e[i];return t},i.modulo=function(t,e){return(t%e+e)%e},i.makeArray=function(t){var e=[];if(Array.isArray(t))e=t;else if(t&&"object"==typeof t&&"number"==typeof t.length)for(var i=0;i<t.length;i++)e.push(t[i]);else e.push(t);return e},i.removeFrom=function(t,e){var i=t.indexOf(e);i!=-1&&t.splice(i,1)},i.getParent=function(t,i){for(;t.parentNode&&t!=document.body;)if(t=t.parentNode,e(t,i))return t},i.getQueryElement=function(t){return"string"==typeof t?document.querySelector(t):t},i.handleEvent=function(t){var e="on"+t.type;this[e]&&this[e](t)},i.filterFindElements=function(t,o){t=i.makeArray(t);var n=[];return t.forEach(function(t){if(t instanceof HTMLElement){if(!o)return void n.push(t);e(t,o)&&n.push(t);for(var i=t.querySelectorAll(o),s=0;s<i.length;s++)n.push(i[s])}}),n},i.debounceMethod=function(t,e,i){var o=t.prototype[e],n=e+"Timeout";t.prototype[e]=function(){var t=this[n];t&&clearTimeout(t);var e=arguments,s=this;this[n]=setTimeout(function(){o.apply(s,e),delete s[n]},i||100)}},i.docReady=function(t){var e=document.readyState;"complete"==e||"interactive"==e?setTimeout(t):document.addEventListener("DOMContentLoaded",t)},i.toDashed=function(t){return t.replace(/(.)([A-Z])/g,function(t,e,i){return e+"-"+i}).toLowerCase()};var o=t.console;return i.htmlInit=function(e,n){i.docReady(function(){var s=i.toDashed(n),r="data-"+s,a=document.querySelectorAll("["+r+"]"),u=document.querySelectorAll(".js-"+s),h=i.makeArray(a).concat(i.makeArray(u)),d=r+"-options",l=t.jQuery;h.forEach(function(t){var i,s=t.getAttribute(r)||t.getAttribute(d);try{i=s&&JSON.parse(s)}catch(a){return void(o&&o.error("Error parsing "+r+" on "+t.className+": "+a))}var u=new e(t,i);l&&l.data(t,n,u)})})},i}),function(t,e){"function"==typeof define&&define.amd?define("outlayer/item",["ev-emitter/ev-emitter","get-size/get-size"],e):"object"==typeof module&&module.exports?module.exports=e(require("ev-emitter"),require("get-size")):(t.Outlayer={},t.Outlayer.Item=e(t.EvEmitter,t.getSize))}(window,function(t,e){"use strict";function i(t){for(var e in t)return!1;return e=null,!0}function o(t,e){t&&(this.element=t,this.layout=e,this.position={x:0,y:0},this._create())}function n(t){return t.replace(/([A-Z])/g,function(t){return"-"+t.toLowerCase()})}var s=document.documentElement.style,r="string"==typeof s.transition?"transition":"WebkitTransition",a="string"==typeof s.transform?"transform":"WebkitTransform",u={WebkitTransition:"webkitTransitionEnd",transition:"transitionend"}[r],h={transform:a,transition:r,transitionDuration:r+"Duration",transitionProperty:r+"Property",transitionDelay:r+"Delay"},d=o.prototype=Object.create(t.prototype);d.constructor=o,d._create=function(){this._transn={ingProperties:{},clean:{},onEnd:{}},this.css({position:"absolute"})},d.handleEvent=function(t){var e="on"+t.type;this[e]&&this[e](t)},d.getSize=function(){this.size=e(this.element)},d.css=function(t){var e=this.element.style;for(var i in t){var o=h[i]||i;e[o]=t[i]}},d.getPosition=function(){var t=getComputedStyle(this.element),e=this.layout._getOption("originLeft"),i=this.layout._getOption("originTop"),o=t[e?"left":"right"],n=t[i?"top":"bottom"],s=this.layout.size,r=o.indexOf("%")!=-1?parseFloat(o)/100*s.width:parseInt(o,10),a=n.indexOf("%")!=-1?parseFloat(n)/100*s.height:parseInt(n,10);r=isNaN(r)?0:r,a=isNaN(a)?0:a,r-=e?s.paddingLeft:s.paddingRight,a-=i?s.paddingTop:s.paddingBottom,this.position.x=r,this.position.y=a},d.layoutPosition=function(){var t=this.layout.size,e={},i=this.layout._getOption("originLeft"),o=this.layout._getOption("originTop"),n=i?"paddingLeft":"paddingRight",s=i?"left":"right",r=i?"right":"left",a=this.position.x+t[n];e[s]=this.getXValue(a),e[r]="";var u=o?"paddingTop":"paddingBottom",h=o?"top":"bottom",d=o?"bottom":"top",l=this.position.y+t[u];e[h]=this.getYValue(l),e[d]="",this.css(e),this.emitEvent("layout",[this])},d.getXValue=function(t){var e=this.layout._getOption("horizontal");return this.layout.options.percentPosition&&!e?t/this.layout.size.width*100+"%":t+"px"},d.getYValue=function(t){var e=this.layout._getOption("horizontal");return this.layout.options.percentPosition&&e?t/this.layout.size.height*100+"%":t+"px"},d._transitionTo=function(t,e){this.getPosition();var i=this.position.x,o=this.position.y,n=parseInt(t,10),s=parseInt(e,10),r=n===this.position.x&&s===this.position.y;if(this.setPosition(t,e),r&&!this.isTransitioning)return void this.layoutPosition();var a=t-i,u=e-o,h={};h.transform=this.getTranslate(a,u),this.transition({to:h,onTransitionEnd:{transform:this.layoutPosition},isCleaning:!0})},d.getTranslate=function(t,e){var i=this.layout._getOption("originLeft"),o=this.layout._getOption("originTop");return t=i?t:-t,e=o?e:-e,"translate3d("+t+"px, "+e+"px, 0)"},d.goTo=function(t,e){this.setPosition(t,e),this.layoutPosition()},d.moveTo=d._transitionTo,d.setPosition=function(t,e){this.position.x=parseInt(t,10),this.position.y=parseInt(e,10)},d._nonTransition=function(t){this.css(t.to),t.isCleaning&&this._removeStyles(t.to);for(var e in t.onTransitionEnd)t.onTransitionEnd[e].call(this)},d.transition=function(t){if(!parseFloat(this.layout.options.transitionDuration))return void this._nonTransition(t);var e=this._transn;for(var i in t.onTransitionEnd)e.onEnd[i]=t.onTransitionEnd[i];for(i in t.to)e.ingProperties[i]=!0,t.isCleaning&&(e.clean[i]=!0);if(t.from){this.css(t.from);var o=this.element.offsetHeight;o=null}this.enableTransition(t.to),this.css(t.to),this.isTransitioning=!0};var l="opacity,"+n(a);d.enableTransition=function(){if(!this.isTransitioning){var t=this.layout.options.transitionDuration;t="number"==typeof t?t+"ms":t,this.css({transitionProperty:l,transitionDuration:t,transitionDelay:this.staggerDelay||0}),this.element.addEventListener(u,this,!1)}},d.onwebkitTransitionEnd=function(t){this.ontransitionend(t)},d.onotransitionend=function(t){this.ontransitionend(t)};var f={"-webkit-transform":"transform"};d.ontransitionend=function(t){if(t.target===this.element){var e=this._transn,o=f[t.propertyName]||t.propertyName;if(delete e.ingProperties[o],i(e.ingProperties)&&this.disableTransition(),o in e.clean&&(this.element.style[t.propertyName]="",delete e.clean[o]),o in e.onEnd){var n=e.onEnd[o];n.call(this),delete e.onEnd[o]}this.emitEvent("transitionEnd",[this])}},d.disableTransition=function(){this.removeTransitionStyles(),this.element.removeEventListener(u,this,!1),this.isTransitioning=!1},d._removeStyles=function(t){var e={};for(var i in t)e[i]="";this.css(e)};var c={transitionProperty:"",transitionDuration:"",transitionDelay:""};return d.removeTransitionStyles=function(){this.css(c)},d.stagger=function(t){t=isNaN(t)?0:t,this.staggerDelay=t+"ms"},d.removeElem=function(){this.element.parentNode.removeChild(this.element),this.css({display:""}),this.emitEvent("remove",[this])},d.remove=function(){return r&&parseFloat(this.layout.options.transitionDuration)?(this.once("transitionEnd",function(){this.removeElem()}),void this.hide()):void this.removeElem()},d.reveal=function(){delete this.isHidden,this.css({display:""});var t=this.layout.options,e={},i=this.getHideRevealTransitionEndProperty("visibleStyle");e[i]=this.onRevealTransitionEnd,this.transition({from:t.hiddenStyle,to:t.visibleStyle,isCleaning:!0,onTransitionEnd:e})},d.onRevealTransitionEnd=function(){this.isHidden||this.emitEvent("reveal")},d.getHideRevealTransitionEndProperty=function(t){var e=this.layout.options[t];if(e.opacity)return"opacity";for(var i in e)return i},d.hide=function(){this.isHidden=!0,this.css({display:""});var t=this.layout.options,e={},i=this.getHideRevealTransitionEndProperty("hiddenStyle");e[i]=this.onHideTransitionEnd,this.transition({from:t.visibleStyle,to:t.hiddenStyle,isCleaning:!0,onTransitionEnd:e})},d.onHideTransitionEnd=function(){this.isHidden&&(this.css({display:"none"}),this.emitEvent("hide"))},d.destroy=function(){this.css({position:"",left:"",right:"",top:"",bottom:"",transition:"",transform:""})},o}),function(t,e){"use strict";"function"==typeof define&&define.amd?define("outlayer/outlayer",["ev-emitter/ev-emitter","get-size/get-size","fizzy-ui-utils/utils","./item"],function(i,o,n,s){return e(t,i,o,n,s)}):"object"==typeof module&&module.exports?module.exports=e(t,require("ev-emitter"),require("get-size"),require("fizzy-ui-utils"),require("./item")):t.Outlayer=e(t,t.EvEmitter,t.getSize,t.fizzyUIUtils,t.Outlayer.Item)}(window,function(t,e,i,o,n){"use strict";function s(t,e){var i=o.getQueryElement(t);if(!i)return void(u&&u.error("Bad element for "+this.constructor.namespace+": "+(i||t)));this.element=i,h&&(this.$element=h(this.element)),this.options=o.extend({},this.constructor.defaults),this.option(e);var n=++l;this.element.outlayerGUID=n,f[n]=this,this._create();var s=this._getOption("initLayout");s&&this.layout()}function r(t){function e(){t.apply(this,arguments)}return e.prototype=Object.create(t.prototype),e.prototype.constructor=e,e}function a(t){if("number"==typeof t)return t;var e=t.match(/(^\d*\.?\d*)(\w*)/),i=e&&e[1],o=e&&e[2];if(!i.length)return 0;i=parseFloat(i);var n=m[o]||1;return i*n}var u=t.console,h=t.jQuery,d=function(){},l=0,f={};s.namespace="outlayer",s.Item=n,s.defaults={containerStyle:{position:"relative"},initLayout:!0,originLeft:!0,originTop:!0,resize:!0,resizeContainer:!0,transitionDuration:"0.4s",hiddenStyle:{opacity:0,transform:"scale(0.001)"},visibleStyle:{opacity:1,transform:"scale(1)"}};var c=s.prototype;o.extend(c,e.prototype),c.option=function(t){o.extend(this.options,t)},c._getOption=function(t){var e=this.constructor.compatOptions[t];return e&&void 0!==this.options[e]?this.options[e]:this.options[t]},s.compatOptions={initLayout:"isInitLayout",horizontal:"isHorizontal",layoutInstant:"isLayoutInstant",originLeft:"isOriginLeft",originTop:"isOriginTop",resize:"isResizeBound",resizeContainer:"isResizingContainer"},c._create=function(){this.reloadItems(),this.stamps=[],this.stamp(this.options.stamp),o.extend(this.element.style,this.options.containerStyle);var t=this._getOption("resize");t&&this.bindResize()},c.reloadItems=function(){this.items=this._itemize(this.element.children)},c._itemize=function(t){for(var e=this._filterFindItemElements(t),i=this.constructor.Item,o=[],n=0;n<e.length;n++){var s=e[n],r=new i(s,this);o.push(r)}return o},c._filterFindItemElements=function(t){return o.filterFindElements(t,this.options.itemSelector)},c.getItemElements=function(){return this.items.map(function(t){return t.element})},c.layout=function(){this._resetLayout(),this._manageStamps();var t=this._getOption("layoutInstant"),e=void 0!==t?t:!this._isLayoutInited;this.layoutItems(this.items,e),this._isLayoutInited=!0},c._init=c.layout,c._resetLayout=function(){this.getSize()},c.getSize=function(){this.size=i(this.element)},c._getMeasurement=function(t,e){var o,n=this.options[t];n?("string"==typeof n?o=this.element.querySelector(n):n instanceof HTMLElement&&(o=n),this[t]=o?i(o)[e]:n):this[t]=0},c.layoutItems=function(t,e){t=this._getItemsForLayout(t),this._layoutItems(t,e),this._postLayout()},c._getItemsForLayout=function(t){return t.filter(function(t){return!t.isIgnored})},c._layoutItems=function(t,e){if(this._emitCompleteOnItems("layout",t),t&&t.length){var i=[];t.forEach(function(t){var o=this._getItemLayoutPosition(t);o.item=t,o.isInstant=e||t.isLayoutInstant,i.push(o)},this),this._processLayoutQueue(i)}},c._getItemLayoutPosition=function(){return{x:0,y:0}},c._processLayoutQueue=function(t){this.updateStagger(),t.forEach(function(t,e){this._positionItem(t.item,t.x,t.y,t.isInstant,e)},this)},c.updateStagger=function(){var t=this.options.stagger;return null===t||void 0===t?void(this.stagger=0):(this.stagger=a(t),this.stagger)},c._positionItem=function(t,e,i,o,n){o?t.goTo(e,i):(t.stagger(n*this.stagger),t.moveTo(e,i))},c._postLayout=function(){this.resizeContainer()},c.resizeContainer=function(){var t=this._getOption("resizeContainer");if(t){var e=this._getContainerSize();e&&(this._setContainerMeasure(e.width,!0),this._setContainerMeasure(e.height,!1))}},c._getContainerSize=d,c._setContainerMeasure=function(t,e){if(void 0!==t){var i=this.size;i.isBorderBox&&(t+=e?i.paddingLeft+i.paddingRight+i.borderLeftWidth+i.borderRightWidth:i.paddingBottom+i.paddingTop+i.borderTopWidth+i.borderBottomWidth),t=Math.max(t,0),this.element.style[e?"width":"height"]=t+"px"}},c._emitCompleteOnItems=function(t,e){function i(){n.dispatchEvent(t+"Complete",null,[e])}function o(){r++,r==s&&i()}var n=this,s=e.length;if(!e||!s)return void i();var r=0;e.forEach(function(e){e.once(t,o)})},c.dispatchEvent=function(t,e,i){var o=e?[e].concat(i):i;if(this.emitEvent(t,o),h)if(this.$element=this.$element||h(this.element),e){var n=h.Event(e);n.type=t,this.$element.trigger(n,i)}else this.$element.trigger(t,i)},c.ignore=function(t){var e=this.getItem(t);e&&(e.isIgnored=!0)},c.unignore=function(t){var e=this.getItem(t);e&&delete e.isIgnored},c.stamp=function(t){t=this._find(t),t&&(this.stamps=this.stamps.concat(t),t.forEach(this.ignore,this))},c.unstamp=function(t){t=this._find(t),t&&t.forEach(function(t){o.removeFrom(this.stamps,t),this.unignore(t)},this)},c._find=function(t){if(t)return"string"==typeof t&&(t=this.element.querySelectorAll(t)),t=o.makeArray(t)},c._manageStamps=function(){this.stamps&&this.stamps.length&&(this._getBoundingRect(),this.stamps.forEach(this._manageStamp,this))},c._getBoundingRect=function(){var t=this.element.getBoundingClientRect(),e=this.size;this._boundingRect={left:t.left+e.paddingLeft+e.borderLeftWidth,top:t.top+e.paddingTop+e.borderTopWidth,right:t.right-(e.paddingRight+e.borderRightWidth),bottom:t.bottom-(e.paddingBottom+e.borderBottomWidth)}},c._manageStamp=d,c._getElementOffset=function(t){var e=t.getBoundingClientRect(),o=this._boundingRect,n=i(t),s={left:e.left-o.left-n.marginLeft,top:e.top-o.top-n.marginTop,right:o.right-e.right-n.marginRight,bottom:o.bottom-e.bottom-n.marginBottom};return s},c.handleEvent=o.handleEvent,c.bindResize=function(){t.addEventListener("resize",this),this.isResizeBound=!0},c.unbindResize=function(){t.removeEventListener("resize",this),this.isResizeBound=!1},c.onresize=function(){this.resize()},o.debounceMethod(s,"onresize",100),c.resize=function(){this.isResizeBound&&this.needsResizeLayout()&&this.layout()},c.needsResizeLayout=function(){var t=i(this.element),e=this.size&&t;return e&&t.innerWidth!==this.size.innerWidth},c.addItems=function(t){var e=this._itemize(t);return e.length&&(this.items=this.items.concat(e)),e},c.appended=function(t){var e=this.addItems(t);e.length&&(this.layoutItems(e,!0),this.reveal(e))},c.prepended=function(t){var e=this._itemize(t);if(e.length){var i=this.items.slice(0);this.items=e.concat(i),this._resetLayout(),this._manageStamps(),this.layoutItems(e,!0),this.reveal(e),this.layoutItems(i)}},c.reveal=function(t){if(this._emitCompleteOnItems("reveal",t),t&&t.length){var e=this.updateStagger();t.forEach(function(t,i){t.stagger(i*e),t.reveal()})}},c.hide=function(t){if(this._emitCompleteOnItems("hide",t),t&&t.length){var e=this.updateStagger();t.forEach(function(t,i){t.stagger(i*e),t.hide()})}},c.revealItemElements=function(t){var e=this.getItems(t);this.reveal(e)},c.hideItemElements=function(t){var e=this.getItems(t);this.hide(e)},c.getItem=function(t){for(var e=0;e<this.items.length;e++){var i=this.items[e];if(i.element==t)return i}},c.getItems=function(t){t=o.makeArray(t);var e=[];return t.forEach(function(t){var i=this.getItem(t);i&&e.push(i)},this),e},c.remove=function(t){var e=this.getItems(t);this._emitCompleteOnItems("remove",e),e&&e.length&&e.forEach(function(t){t.remove(),o.removeFrom(this.items,t)},this)},c.destroy=function(){var t=this.element.style;t.height="",t.position="",t.width="",this.items.forEach(function(t){t.destroy()}),this.unbindResize();var e=this.element.outlayerGUID;delete f[e],delete this.element.outlayerGUID,h&&h.removeData(this.element,this.constructor.namespace)},s.data=function(t){t=o.getQueryElement(t);var e=t&&t.outlayerGUID;return e&&f[e]},s.create=function(t,e){var i=r(s);return i.defaults=o.extend({},s.defaults),o.extend(i.defaults,e),i.compatOptions=o.extend({},s.compatOptions),i.namespace=t,i.data=s.data,i.Item=r(n),o.htmlInit(i,t),h&&h.bridget&&h.bridget(t,i),i};var m={ms:1,s:1e3};return s.Item=n,s}),function(t,e){"function"==typeof define&&define.amd?define("isotope/js/item",["outlayer/outlayer"],e):"object"==typeof module&&module.exports?module.exports=e(require("outlayer")):(t.Isotope=t.Isotope||{},t.Isotope.Item=e(t.Outlayer))}(window,function(t){"use strict";function e(){t.Item.apply(this,arguments)}var i=e.prototype=Object.create(t.Item.prototype),o=i._create;i._create=function(){this.id=this.layout.itemGUID++,o.call(this),this.sortData={}},i.updateSortData=function(){if(!this.isIgnored){this.sortData.id=this.id,this.sortData["original-order"]=this.id,this.sortData.random=Math.random();var t=this.layout.options.getSortData,e=this.layout._sorters;for(var i in t){var o=e[i];this.sortData[i]=o(this.element,this)}}};var n=i.destroy;return i.destroy=function(){n.apply(this,arguments),this.css({display:""})},e}),function(t,e){"function"==typeof define&&define.amd?define("isotope/js/layout-mode",["get-size/get-size","outlayer/outlayer"],e):"object"==typeof module&&module.exports?module.exports=e(require("get-size"),require("outlayer")):(t.Isotope=t.Isotope||{},t.Isotope.LayoutMode=e(t.getSize,t.Outlayer))}(window,function(t,e){"use strict";function i(t){this.isotope=t,t&&(this.options=t.options[this.namespace],this.element=t.element,this.items=t.filteredItems,this.size=t.size)}var o=i.prototype,n=["_resetLayout","_getItemLayoutPosition","_manageStamp","_getContainerSize","_getElementOffset","needsResizeLayout","_getOption"];return n.forEach(function(t){o[t]=function(){return e.prototype[t].apply(this.isotope,arguments)}}),o.needsVerticalResizeLayout=function(){var e=t(this.isotope.element),i=this.isotope.size&&e;return i&&e.innerHeight!=this.isotope.size.innerHeight},o._getMeasurement=function(){this.isotope._getMeasurement.apply(this,arguments)},o.getColumnWidth=function(){this.getSegmentSize("column","Width")},o.getRowHeight=function(){this.getSegmentSize("row","Height")},o.getSegmentSize=function(t,e){var i=t+e,o="outer"+e;if(this._getMeasurement(i,o),!this[i]){var n=this.getFirstItemSize();this[i]=n&&n[o]||this.isotope.size["inner"+e]}},o.getFirstItemSize=function(){var e=this.isotope.filteredItems[0];return e&&e.element&&t(e.element)},o.layout=function(){this.isotope.layout.apply(this.isotope,arguments)},o.getSize=function(){this.isotope.getSize(),this.size=this.isotope.size},i.modes={},i.create=function(t,e){function n(){i.apply(this,arguments)}return n.prototype=Object.create(o),n.prototype.constructor=n,e&&(n.options=e),n.prototype.namespace=t,i.modes[t]=n,n},i}),function(t,e){"function"==typeof define&&define.amd?define("masonry/masonry",["outlayer/outlayer","get-size/get-size"],e):"object"==typeof module&&module.exports?module.exports=e(require("outlayer"),require("get-size")):t.Masonry=e(t.Outlayer,t.getSize)}(window,function(t,e){var i=t.create("masonry");i.compatOptions.fitWidth="isFitWidth";var o=i.prototype;return o._resetLayout=function(){this.getSize(),this._getMeasurement("columnWidth","outerWidth"),this._getMeasurement("gutter","outerWidth"),this.measureColumns(),this.colYs=[];for(var t=0;t<this.cols;t++)this.colYs.push(0);this.maxY=0,this.horizontalColIndex=0},o.measureColumns=function(){if(this.getContainerWidth(),!this.columnWidth){var t=this.items[0],i=t&&t.element;this.columnWidth=i&&e(i).outerWidth||this.containerWidth}var o=this.columnWidth+=this.gutter,n=this.containerWidth+this.gutter,s=n/o,r=o-n%o,a=r&&r<1?"round":"floor";s=Math[a](s),this.cols=Math.max(s,1)},o.getContainerWidth=function(){var t=this._getOption("fitWidth"),i=t?this.element.parentNode:this.element,o=e(i);this.containerWidth=o&&o.innerWidth},o._getItemLayoutPosition=function(t){t.getSize();var e=t.size.outerWidth%this.columnWidth,i=e&&e<1?"round":"ceil",o=Math[i](t.size.outerWidth/this.columnWidth);o=Math.min(o,this.cols);for(var n=this.options.horizontalOrder?"_getHorizontalColPosition":"_getTopColPosition",s=this[n](o,t),r={x:this.columnWidth*s.col,y:s.y},a=s.y+t.size.outerHeight,u=o+s.col,h=s.col;h<u;h++)this.colYs[h]=a;return r},o._getTopColPosition=function(t){var e=this._getTopColGroup(t),i=Math.min.apply(Math,e);return{col:e.indexOf(i),y:i}},o._getTopColGroup=function(t){if(t<2)return this.colYs;for(var e=[],i=this.cols+1-t,o=0;o<i;o++)e[o]=this._getColGroupY(o,t);return e},o._getColGroupY=function(t,e){if(e<2)return this.colYs[t];var i=this.colYs.slice(t,t+e);return Math.max.apply(Math,i)},o._getHorizontalColPosition=function(t,e){var i=this.horizontalColIndex%this.cols,o=t>1&&i+t>this.cols;i=o?0:i;var n=e.size.outerWidth&&e.size.outerHeight;return this.horizontalColIndex=n?i+t:this.horizontalColIndex,{col:i,y:this._getColGroupY(i,t)}},o._manageStamp=function(t){var i=e(t),o=this._getElementOffset(t),n=this._getOption("originLeft"),s=n?o.left:o.right,r=s+i.outerWidth,a=Math.floor(s/this.columnWidth);a=Math.max(0,a);var u=Math.floor(r/this.columnWidth);u-=r%this.columnWidth?0:1,u=Math.min(this.cols-1,u);for(var h=this._getOption("originTop"),d=(h?o.top:o.bottom)+i.outerHeight,l=a;l<=u;l++)this.colYs[l]=Math.max(d,this.colYs[l])},o._getContainerSize=function(){this.maxY=Math.max.apply(Math,this.colYs);var t={height:this.maxY};return this._getOption("fitWidth")&&(t.width=this._getContainerFitWidth()),t},o._getContainerFitWidth=function(){for(var t=0,e=this.cols;--e&&0===this.colYs[e];)t++;return(this.cols-t)*this.columnWidth-this.gutter},o.needsResizeLayout=function(){var t=this.containerWidth;return this.getContainerWidth(),t!=this.containerWidth},i}),function(t,e){"function"==typeof define&&define.amd?define("isotope/js/layout-modes/masonry",["../layout-mode","masonry/masonry"],e):"object"==typeof module&&module.exports?module.exports=e(require("../layout-mode"),require("masonry-layout")):e(t.Isotope.LayoutMode,t.Masonry)}(window,function(t,e){"use strict";var i=t.create("masonry"),o=i.prototype,n={_getElementOffset:!0,layout:!0,_getMeasurement:!0};for(var s in e.prototype)n[s]||(o[s]=e.prototype[s]);var r=o.measureColumns;o.measureColumns=function(){this.items=this.isotope.filteredItems,r.call(this)};var a=o._getOption;return o._getOption=function(t){return"fitWidth"==t?void 0!==this.options.isFitWidth?this.options.isFitWidth:this.options.fitWidth:a.apply(this.isotope,arguments)},i}),function(t,e){"function"==typeof define&&define.amd?define("isotope/js/layout-modes/fit-rows",["../layout-mode"],e):"object"==typeof exports?module.exports=e(require("../layout-mode")):e(t.Isotope.LayoutMode)}(window,function(t){"use strict";var e=t.create("fitRows"),i=e.prototype;return i._resetLayout=function(){this.x=0,this.y=0,this.maxY=0,this._getMeasurement("gutter","outerWidth")},i._getItemLayoutPosition=function(t){t.getSize();var e=t.size.outerWidth+this.gutter,i=this.isotope.size.innerWidth+this.gutter;0!==this.x&&e+this.x>i&&(this.x=0,this.y=this.maxY);var o={x:this.x,y:this.y};return this.maxY=Math.max(this.maxY,this.y+t.size.outerHeight),this.x+=e,o},i._getContainerSize=function(){return{height:this.maxY}},e}),function(t,e){"function"==typeof define&&define.amd?define("isotope/js/layout-modes/vertical",["../layout-mode"],e):"object"==typeof module&&module.exports?module.exports=e(require("../layout-mode")):e(t.Isotope.LayoutMode)}(window,function(t){"use strict";var e=t.create("vertical",{horizontalAlignment:0}),i=e.prototype;return i._resetLayout=function(){this.y=0},i._getItemLayoutPosition=function(t){t.getSize();var e=(this.isotope.size.innerWidth-t.size.outerWidth)*this.options.horizontalAlignment,i=this.y;return this.y+=t.size.outerHeight,{x:e,y:i}},i._getContainerSize=function(){return{height:this.y}},e}),function(t,e){"function"==typeof define&&define.amd?define(["outlayer/outlayer","get-size/get-size","desandro-matches-selector/matches-selector","fizzy-ui-utils/utils","isotope/js/item","isotope/js/layout-mode","isotope/js/layout-modes/masonry","isotope/js/layout-modes/fit-rows","isotope/js/layout-modes/vertical"],function(i,o,n,s,r,a){return e(t,i,o,n,s,r,a)}):"object"==typeof module&&module.exports?module.exports=e(t,require("outlayer"),require("get-size"),require("desandro-matches-selector"),require("fizzy-ui-utils"),require("isotope/js/item"),require("isotope/js/layout-mode"),require("isotope/js/layout-modes/masonry"),require("isotope/js/layout-modes/fit-rows"),require("isotope/js/layout-modes/vertical")):t.Isotope=e(t,t.Outlayer,t.getSize,t.matchesSelector,t.fizzyUIUtils,t.Isotope.Item,t.Isotope.LayoutMode)}(window,function(t,e,i,o,n,s,r){function a(t,e){return function(i,o){for(var n=0;n<t.length;n++){var s=t[n],r=i.sortData[s],a=o.sortData[s];if(r>a||r<a){var u=void 0!==e[s]?e[s]:e,h=u?1:-1;return(r>a?1:-1)*h}}return 0}}var u=t.jQuery,h=String.prototype.trim?function(t){return t.trim()}:function(t){return t.replace(/^\s+|\s+$/g,"")},d=e.create("isotope",{layoutMode:"masonry",isJQueryFiltering:!0,sortAscending:!0});d.Item=s,d.LayoutMode=r;var l=d.prototype;l._create=function(){this.itemGUID=0,this._sorters={},this._getSorters(),e.prototype._create.call(this),this.modes={},this.filteredItems=this.items,this.sortHistory=["original-order"];for(var t in r.modes)this._initLayoutMode(t)},l.reloadItems=function(){this.itemGUID=0,e.prototype.reloadItems.call(this)},l._itemize=function(){for(var t=e.prototype._itemize.apply(this,arguments),i=0;i<t.length;i++){var o=t[i];o.id=this.itemGUID++}return this._updateItemsSortData(t),t},l._initLayoutMode=function(t){var e=r.modes[t],i=this.options[t]||{};this.options[t]=e.options?n.extend(e.options,i):i,this.modes[t]=new e(this)},l.layout=function(){return!this._isLayoutInited&&this._getOption("initLayout")?void this.arrange():void this._layout()},l._layout=function(){var t=this._getIsInstant();this._resetLayout(),this._manageStamps(),this.layoutItems(this.filteredItems,t),this._isLayoutInited=!0},l.arrange=function(t){this.option(t),this._getIsInstant();var e=this._filter(this.items);this.filteredItems=e.matches,this._bindArrangeComplete(),this._isInstant?this._noTransition(this._hideReveal,[e]):this._hideReveal(e),this._sort(),this._layout()},l._init=l.arrange,l._hideReveal=function(t){this.reveal(t.needReveal),this.hide(t.needHide)},l._getIsInstant=function(){var t=this._getOption("layoutInstant"),e=void 0!==t?t:!this._isLayoutInited;return this._isInstant=e,e},l._bindArrangeComplete=function(){function t(){e&&i&&o&&n.dispatchEvent("arrangeComplete",null,[n.filteredItems])}var e,i,o,n=this;this.once("layoutComplete",function(){e=!0,t()}),this.once("hideComplete",function(){i=!0,t()}),this.once("revealComplete",function(){o=!0,t()})},l._filter=function(t){var e=this.options.filter;e=e||"*";for(var i=[],o=[],n=[],s=this._getFilterTest(e),r=0;r<t.length;r++){var a=t[r];if(!a.isIgnored){var u=s(a);u&&i.push(a),u&&a.isHidden?o.push(a):u||a.isHidden||n.push(a)}}return{matches:i,needReveal:o,needHide:n}},l._getFilterTest=function(t){return u&&this.options.isJQueryFiltering?function(e){return u(e.element).is(t)}:"function"==typeof t?function(e){return t(e.element)}:function(e){return o(e.element,t)}},l.updateSortData=function(t){
var e;t?(t=n.makeArray(t),e=this.getItems(t)):e=this.items,this._getSorters(),this._updateItemsSortData(e)},l._getSorters=function(){var t=this.options.getSortData;for(var e in t){var i=t[e];this._sorters[e]=f(i)}},l._updateItemsSortData=function(t){for(var e=t&&t.length,i=0;e&&i<e;i++){var o=t[i];o.updateSortData()}};var f=function(){function t(t){if("string"!=typeof t)return t;var i=h(t).split(" "),o=i[0],n=o.match(/^\[(.+)\]$/),s=n&&n[1],r=e(s,o),a=d.sortDataParsers[i[1]];return t=a?function(t){return t&&a(r(t))}:function(t){return t&&r(t)}}function e(t,e){return t?function(e){return e.getAttribute(t)}:function(t){var i=t.querySelector(e);return i&&i.textContent}}return t}();d.sortDataParsers={parseInt:function(t){return parseInt(t,10)},parseFloat:function(t){return parseFloat(t)}},l._sort=function(){if(this.options.sortBy){var t=n.makeArray(this.options.sortBy);this._getIsSameSortBy(t)||(this.sortHistory=t.concat(this.sortHistory));var e=a(this.sortHistory,this.options.sortAscending);this.filteredItems.sort(e)}},l._getIsSameSortBy=function(t){for(var e=0;e<t.length;e++)if(t[e]!=this.sortHistory[e])return!1;return!0},l._mode=function(){var t=this.options.layoutMode,e=this.modes[t];if(!e)throw new Error("No layout mode: "+t);return e.options=this.options[t],e},l._resetLayout=function(){e.prototype._resetLayout.call(this),this._mode()._resetLayout()},l._getItemLayoutPosition=function(t){return this._mode()._getItemLayoutPosition(t)},l._manageStamp=function(t){this._mode()._manageStamp(t)},l._getContainerSize=function(){return this._mode()._getContainerSize()},l.needsResizeLayout=function(){return this._mode().needsResizeLayout()},l.appended=function(t){var e=this.addItems(t);if(e.length){var i=this._filterRevealAdded(e);this.filteredItems=this.filteredItems.concat(i)}},l.prepended=function(t){var e=this._itemize(t);if(e.length){this._resetLayout(),this._manageStamps();var i=this._filterRevealAdded(e);this.layoutItems(this.filteredItems),this.filteredItems=i.concat(this.filteredItems),this.items=e.concat(this.items)}},l._filterRevealAdded=function(t){var e=this._filter(t);return this.hide(e.needHide),this.reveal(e.matches),this.layoutItems(e.matches,!0),e.matches},l.insert=function(t){var e=this.addItems(t);if(e.length){var i,o,n=e.length;for(i=0;i<n;i++)o=e[i],this.element.appendChild(o.element);var s=this._filter(e).matches;for(i=0;i<n;i++)e[i].isLayoutInstant=!0;for(this.arrange(),i=0;i<n;i++)delete e[i].isLayoutInstant;this.reveal(s)}};var c=l.remove;return l.remove=function(t){t=n.makeArray(t);var e=this.getItems(t);c.call(this,t);for(var i=e&&e.length,o=0;i&&o<i;o++){var s=e[o];n.removeFrom(this.filteredItems,s)}},l.shuffle=function(){for(var t=0;t<this.items.length;t++){var e=this.items[t];e.sortData.random=Math.random()}this.options.sortBy="random",this._sort(),this._layout()},l._noTransition=function(t,e){var i=this.options.transitionDuration;this.options.transitionDuration=0;var o=t.apply(this,e);return this.options.transitionDuration=i,o},l.getFilteredItemElements=function(){return this.filteredItems.map(function(t){return t.element})},d});
(function ( $ ) {
    $.fn.lightModal = function( options ) {

        /* Parameters */
        var settings = $.extend({
            beforeShow: function(){},
            afterShow: function(){}
        }, options );

        /* Variables */
        var modalToggle =  $(this);
        var modal = $('.light-modal');
        var modalBg = $('.light-modal-bg');
        var modalClose = $('.light-modal .modal-close');

        /* Functions */
        var openModal = function(el) {
            closeOtherModals();
            var modalId = $(el).attr('href');
            var modal=$(modalId);
            modalBg.show();
            //$(modalId).fadeIn(300);
            $(modalId).show();
            $(modalId).addClass('light-modal-active');
            scrollProcessing();
        };
        var scrollProcessing = function(){
            var bodyBeforeWidth = $('body').width();
            $('html').addClass('light-modal-lock');
            var bodyAfterWidth = $('body').width();
            if(bodyAfterWidth - bodyBeforeWidth > 1){
                $('body').addClass('scrollbar-fix');
            }
        };
        var closeOtherModals = function() {
            modal.hide();
            modal.removeClass('light-modal-active');
            modalBg.hide();
        };
        var closeModal = function(el) {
            $(el).fadeOut(300, function(){
                modalBg.hide();
                $('html').removeClass('light-modal-lock');
                $(this).removeClass('light-modal-active');
            });
        };

        /* Events */
        modalClose.click(function(e){
	        e.preventDefault();
            closeModal('#'+$(this).closest('.light-modal').attr('id'));
        });

        modalToggle.on('click', function(e) {
            e.preventDefault();
            settings.beforeShow.call(this);
            openModal(this);
            settings.afterShow.call(this);
        });

        modalBg.on('click touchend',function(e) {
            if(!$( e.target ).is('.light-modal *')) {
	            closeModal( '.light-modal-active' );
            }
        });
    };
}( jQuery ));

(function ($) {
    jQuery.fn.bbScrollableArea = function () {

        return this.each(function () {

            // Variables
            var $this = jQuery(this),
                $child = $this.find('ul'),
                $nextBtn = '<a href="#" class="bb-nav bb-arrow-next" title="Next"></a>',
                $prevBtn = '<a href="#" class="bb-nav bb-arrow-prev" title="Prev"></a>',
                scroll = 0,
                delta = 300, // scroll step
                maxScrollLeft = 0; // max scroll area

            var BB = {
                init: function () {
                    if (($child[0].scrollWidth > $child[0].clientWidth)) {
                        $this.addClass('bb-scroll');
                        maxScrollLeft = $child[0].scrollWidth - $child[0].clientWidth;
                        if ($child.scrollLeft() === 0) $this.addClass('bb-scroll-start');

                        // Add arrows if need
                        if (!$this.find('.bb-nav').length) {
                            $this.append(jQuery($nextBtn));
                            $this.append(jQuery($prevBtn));
                            BB.actions();
                        }
                    } else {
                        $this.removeClass('bb-scroll');
                        $this.find('.bb-nav').remove();
                    }
                },
                next: function () {
                    BB.elementScroll(scroll + delta)
                    // After next coll BB.scroll()
                },
                prev: function () {
                    BB.elementScroll(scroll - delta)
                    // After prev coll BB.scroll()
                },
                scroll: function () {
                    BB.afterAction();
                },
                elementScroll: function (position) {
                    $child.animate({
                        scrollLeft: position
                    }, 500);
                },
                actions: function () {
                    // Next/Prev/Scroll Actions
                    $this.find('.bb-arrow-next').on('click', function (e) {
                        e.preventDefault();
                        BB.next();
                    });
                    $this.find('.bb-arrow-prev').on('click', function (e) {
                        e.preventDefault();
                        BB.prev();
                    });
                    $child.on('scroll', function () {
                        BB.scroll();
                    });
                },
                afterAction: function () {

                    // if scroll in the start position
                    if ($child.scrollLeft() === 0) {
                        $this.addClass('bb-scroll-start');
                        $this.removeClass('bb-scroll-end');
                        scroll = 0;

                        // if scroll in the end position
                    } else if ($child.scrollLeft() >= maxScrollLeft) {
                        $this.addClass('bb-scroll-end');
                        $this.removeClass('bb-scroll-start');
                        scroll = maxScrollLeft;
                    } else {
                        // if scroll in the middle
                        $this.removeClass('bb-scroll-start');
                        $this.removeClass('bb-scroll-end');
                        scroll = $child.scrollLeft();
                    }
                }
            };

            // Plugin init
            BB.init();

            // Windows resize coll plugin init for reconstruct
            jQuery(window).resize(function () {
                BB.init();
            });
        });
    };
}(jQuery));


/**
 *
 *  Helper functions
 *
 **/


/**
 *
 *  Gets Max Value
 *
 **/

function getMaxVal(sel, elAttr) {
    var selElements = [];
    sel.each (function(){
        selElements.push(jQuery(this)[elAttr]());
    })
    return Math.max.apply(Math,selElements);
}

/**
 *
 *  Returns Two Digits for Number
 *  For ex: it will return 01 for 1 and 15 for 15
 *
 **/

function numberToTwoDigits(number) {
    var resNumber = number;
    if(resNumber<10)
        resNumber = "0" + resNumber.toString();
    return resNumber;
}

/**
 *
 *  Page Animation
 *
 **/

function bbPageAnimate(pos,speed) {
    jQuery('body,html').animate({
        scrollTop: pos
    }, speed);
}

/**
 * THIS FILE SHOULD INCLUDE GLOBAL FUNCTIONS THAT CAN BE USED THROUGHOUT THE PROJECT
*/

/**
 *  Global Variables
 */
var bb = {
    isMobile : false,
    isRTL : false,
    html  : jQuery('html'),
    windowWidth : jQuery(window).width(),
    windowHeight : jQuery(window).height(),
    stickyBorder : jQuery('#sticky-border').offset().top,
    fixedHeader : 0,
    scrollTop : 0,
    floatingPagination : 0,
    adminBar : 0,
    stickyAdminBar : 0,
    videoOptions : boombox_global_vars.videoOptions
};


/**
 * Set Global Variables
 */
(function ($) {
    "use strict";

    if( bb_detect_mobile() ){
        bb.isMobile = true;
        bb.html.addClass('mobile');
        $('body').trigger('bbMobile');
    } else {
        bb.isMobile = false;
        bb.html.addClass('desktop');
        $('body').trigger('bbDesktop');
    }

    if($('body').hasClass('rtl')){
        bb.isRTL = true;
    }

    function bb_detect_mobile() {
        //var is_mobile = ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) );
        var is_mobile = $('html').hasClass('touchevents');
        return is_mobile;
    }

    if (bb.isMobile && boombox_global_vars.boombox_gif_event == 'hover') {
	    boombox_global_vars.boombox_gif_event = 'click';
    }

    function setSize(){
        bb.windowWidth = $(window).width();
        bb.windowHeight = $(window).height();
        $('.wh').css('height', bb.windowHeight +'px');
        $('.min-wh').css('min-height', bb.windowHeight +'px');
        $('.error404 .page-wrapper').css('min-height', bb.windowHeight);

        getSetAdminBars();
    }

    setSize();
    getSetFixedHeader();

    /* Global Window Load */
    $(window).load(function () {
        setSize();
        getSetFixedHeader();
        bb.html.addClass('page-loaded');
    });

    /* Global Window Resize */
    $(window).resize(function () {
        setSize();
    });

    /* Global Window Scroll */
    jQuery(window).scroll(function () {
        bb.scrollTop = jQuery(window).scrollTop();
        bb.stickyBorder = jQuery('#sticky-border').offset().top;
    });

})(jQuery);


/**
 *  Site Helper Functions
 **/
/* Set Height for Fixed Header */
function getSetFixedHeader(){
    if(jQuery('.bb-sticky.bb-sticky-nav').length && jQuery('.bb-sticky.bb-sticky-nav').is(":visible"))
        bb.fixedHeader = jQuery('.bb-sticky.bb-sticky-nav:visible').innerHeight();
    return bb.fixedHeader;
}

/* Set Height for Admin Bars */
function getSetAdminBars() {
    if(jQuery('#wpadminbar').length){
        bb.adminBar = jQuery('#wpadminbar').outerHeight(true);
        if(jQuery('#wpadminbar').css('position')=='fixed')
            bb.stickyAdminBar = jQuery('#wpadminbar').outerHeight(true);
        else
            bb.stickyAdminBar = 0;
    }
    return bb.stickyAdminBar;
}

/* Sets/Gets Height for Floating Pagination */
function getSetFloatingPagHeight() {
    if (jQuery('.bb-sticky.bb-floating-navbar').length) {
        bb.floatingPagination = jQuery('.bb-floating-navbar .floating-navbar-inner').outerHeight(true);
    }
    return bb.floatingPagination;
}

/* Get Header Height */
function getHeaderAreaHeight() {
    var deskHeader = jQuery('.bb-header.header-desktop');
    var mobileHeader = jQuery('.bb-header.header-mobile');
    if(deskHeader.is(":visible"))
        var headerSel = deskHeader;
    else
        var headerSel = mobileHeader;
    var headerH = 0;
    if(headerSel.length)
        headerH = headerSel.height();

    var headerOffset = headerSel.offset().top;
    return headerH + headerOffset;
}

/**
 *  BB Side Navigation
 **/
function bbSideNav() {

    var $selector = jQuery('.widget_bb-side-navigation .dropdown-toggle');

    $selector.on("touchstart click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $this = jQuery(this);
        var  $target = $this.parent();
        var $subMenu = $this.next('.sub-menu');

        if($target.hasClass('active-menu')) {
            $subMenu.stop( true, true ).slideUp(200, function(){
                $target.removeClass('active-menu');
            });
        }
        else {
            $subMenu.stop( true, true ).slideDown(200, function(){
                $target.addClass('active-menu');
            });
        }
    });
}


/**
 *  Shows Full Post
 **/
function ShowFullPost(obj) {

    // var $selector = jQuery('.post-list.standard .post-thumbnail img');
    //
    // if (!$selector.length) {
    //     return;
    // }

    var oW = obj.attr('width');
    var oH = obj.attr('height');

    if (oH / oW >= 3) {
        obj.parents('.post-thumbnail').addClass('show-short-media');
        obj.parents('.post').addClass('full-post-show');
    }
}

/**
 *  Sets Form Placeholders
 **/
function setFormPlaceholders(wrapperSel, rowSel){
    jQuery(wrapperSel + ' ' +rowSel).each(function(){
        if(jQuery(this).children('label').text()) {
            jQuery(this).find('input').attr('placeholder',jQuery(this).children('label').text());
        }
    });
}


/**
 *  Tabs
 **/
function initializeTabs() {
    var tabActive = jQuery('.bb-tabs .tabs-menu>li.active');
    if( tabActive.length > 0 ){
        for (var i = 0; i < tabActive.length; i++) {
            var tab_id = jQuery(tabActive[i]).children().attr('href');

            jQuery(tab_id).addClass('active').show();
        }
    }

    jQuery('.bb-tabs .tabs-menu a').on("click", function(e){
        var tab = jQuery(this);
        var tab_id = tab.attr('href');
        var tab_wrap = tab.closest('.bb-tabs');
        var tab_content = tab_wrap.find('.tab-content');

        tab.parent().addClass("active");
        tab.parent().siblings().removeClass('active');
        tab_content.not(tab_id).removeClass('active').hide();
        jQuery(tab_id).addClass('active').fadeIn(500);

        e.preventDefault();
    });
}


/**
 *  Sticky Sidebar
 **/
jQuery.fn.bbStickySidebar = function (action) {

    if(bb.isMobile) return; // not init in mobile

    return this.each(function () {

        //not init if sidebar height more than main content height
        if (jQuery(this).parent().outerHeight(true) >= jQuery('.site-main').outerHeight(true))  return;

        // Variables
        var $sticky = jQuery(this);
        var $parent = $sticky.parent();
        var $child = '<div class="bb-sticky-el"></div>';
        var stickyHeight = 1;
        var stickyWidth = 1;
        var stickyOffset = 25;

        var BB = {
            init: function () {
                BB.build();
                BB.calculate();
                BB.offset();
            },
            refresh: function () {
                BB.calculate();
                BB.offset();
                jQuery(window).scroll();
            },
            build: function () {
                jQuery($child).appendTo($sticky);
                var $next = $sticky.nextAll('.widget');
                jQuery($next).appendTo($sticky.find('.bb-sticky-el'));
            },
            calculate: function () {
                stickyHeight = $sticky.innerHeight(); //calculate sticky widget height
                stickyWidth = $parent.outerWidth(); //calculate sticky widget width
            },
            offset: function () {
                stickyOffset = getSetFloatingPagHeight() + getSetFixedHeader() + getSetAdminBars() + 25;
            },
            waypoint:function () {
                $sticky.bbSticky({
                    fixedOffsetFunc: function(){
                        return getSetFloatingPagHeight() + getSetFixedHeader() + getSetAdminBars() + 25;
                    }
                });
            },
            scroll: function () {
                if (bb.scrollTop >= (bb.stickyBorder - stickyHeight - stickyOffset)) {
                    if($sticky.find('.bb-sticky-el').css('position') === 'fixed') {

                        $sticky.addClass('non-fix');
                    }
                } else {
                    $sticky.removeClass('non-fix');
                }
            }
        };

        if (action === 'refresh') {
            BB.refresh();
            return;
        }

        // Plugin init
        BB.init();

        // Refresh when new content loaded
        jQuery('body').on('bbNewContentLoaded',function () {
            BB.refresh();
        });

        // Windows scroll coll plugin scroll
        jQuery(window).scroll(function () {
            BB.scroll();
        });

        // Windows resize coll plugin refresh for recolculation
        jQuery(window).resize(function () {
            BB.refresh();
        });

        // Windows load coll plugin refresh for recolculation
        jQuery(window).load(function () {
            BB.refresh();
            BB.waypoint();
        });
    });
};


/**
 *  Masonry
 **/
function postMasonry() {
    if (!jQuery.fn.isotope) {
        return;
    }

    var $selector = jQuery('.masonry-grid .post-items');

    if (!$selector.length) {
        return;
    }


    var $masonryGrid = $selector.isotope({
        itemSelector:   '.post-item',
        layoutMode:     'masonry'
    });

    if($selector.find('video').length) {
        var vid = $selector.find('video');
        var count = vid.length;

        jQuery(vid).on( 'load loadeddata', function() {
            -- count;
            if( 0 === count ) {
                $selector.isotope('layout');
            }
        });

        for (i = 0; i < vid.length; i++) {
            if ( vid[ i ].readyState === 4 ) {
                -- count;
            }
        }
        // todo -frontend: should we call $selector.isotope('layout'); if all the videos are already loaded here and 0 === count

        setTimeout(function(){ $selector.isotope('layout'); }, 3000);
    }


    jQuery('body').on( 'bbNewContentLoaded', function(e, newItems) {
        $selector.isotope('appended', newItems);

        if(newItems.find('video').length) {
            var vid = newItems.find('video');
            var count = vid.length;

            jQuery(vid).on( 'load loadeddata', function() {
                -- count;
                if( 0 === count ) {
                    $selector.isotope('layout');
                }
            });

            for (i = 0; i < vid.length; i++) {
                if ( vid[ i ].readyState === 4 ) {
                    -- count;
                }
            }

            setTimeout(function(){ $selector.isotope('layout'); }, 1500);
        }
    });

    // Windows load
    jQuery(window).load(function () {
        setTimeout(function(){ $selector.isotope('layout'); }, 1500);
    });
}

/**
 *  Toggle Functionality
 **/
jQuery.bbToggle = function() {
    var toggleElSel = ".bb-toggle .element-toggle";
    var toggleContentSel = ".bb-toggle .toggle-content";

    jQuery(toggleElSel).on('touchstart click', function(e){
       if(jQuery(this).hasClass('only-mobile') && !bb.isMobile){
           return;
       } else {
           e.preventDefault();
           jQuery(this).toggleClass('active');
           var toggleContent = jQuery(this).attr('data-toggle');
           jQuery(toggleContentSel).not(jQuery(toggleContent)).removeClass('active');
           jQuery(toggleContent).toggleClass('active');
       }
    });

    var closeToggleContent = function(event){
        var exceptElemsStr = toggleElSel  + ' , ' + toggleElSel + ' *' + ' , ' + toggleContentSel + ' , ' + toggleContentSel + ' *';
        if(!jQuery( event.target ).is(exceptElemsStr)) {
            jQuery(toggleElSel).removeClass('active');
            jQuery(toggleContentSel).removeClass('active');
        }

    }
    jQuery(document).on("click", 'body', function (e) {
        closeToggleContent(e);
    });
    jQuery(document).on("touchend", 'body', function (e) {
        closeToggleContent(e);
    });
};

/**
 *  Focus Functionality
 **/
jQuery.fn.bbFocus = function() {
    return this.each(function () {
        var _this = jQuery(this);
        var element = _this.find('.element-focus');
        var target = jQuery(element.attr('data-focus'));

        jQuery(element).on('touchstart click', function(e){
            e.preventDefault();
            setTimeout(function(){_this.find(target).focus(); }, 1000);
        });
    })
};


/**
 * Sticky Functionality
 **/
jQuery.fn.bbSticky = function( options ) {
    if(!jQuery(this).length) return;

    this.each(function(){
        /* **Parameters** */
        var settings = jQuery.extend({
            scrollStyle: 'classic', // 'smart', 'none'
            topOffsetFunc: null, // by default current element offset top will be used
            fixedOffset: null, // by default current element offset top will be used
            fullWidth: false, // by default sticky takes auto width
            animation: false, // by default no animation
            keepWrapperHeight: true, // This option keeps wrapper fictive height
            fixedOffsetFunc: function(){}, // by default current element offset top will be used
            scrollFunc: function(){}, // ex. jQuery(window).scroll(...), needs to be defined
            resizeFunc: function(){}
        }, options);

        /* **Variables** */
        var curElSel = this;
        var curEl = jQuery(this);
        var childEl = curEl.children('.bb-sticky-el');
        var offsetFromTop;
        var fixedClass = 'affix';
        var notFixedClass = 'no-affix';
        var posAnimateClass = 'pos-animate';
        var lastScrollTop = 0;
        var fixedOffset = settings.fixedOffsetFunc.call(this);

        /* **Functions** */
        /**
         * Sets sticky element size
         */
        var setStickySize = function() {
            /* Width Set */
            if(!settings.fullWidth) {
                curEl.css('width', 'auto');
                childEl.outerWidth(curEl.outerWidth());
            }
            else
                childEl.css('left',0);

            /* Height Set */
            if(settings.keepWrapperHeight) {
                curEl.css('height', 'auto');
                curEl.height(childEl.outerHeight(true));
            }

            /* Offset from Top */
            offsetFromTop = (settings.topOffsetFunc==null)? curEl.offset().top : settings.topOffsetFunc.call(curElSel);


            /* Fixed Elements Offset */
            offsetFromTop = (fixedOffset==null)? offsetFromTop : offsetFromTop - fixedOffset;
        };

        var smartStickyFunc = function(){
            var st = jQuery(this).scrollTop();
            if (st > lastScrollTop){ // if scrolled down
                if((fixedOffset!=null))
                    childEl.css('top',0);
                curEl.removeClass(fixedClass);
                if (jQuery(window).scrollTop() > offsetFromTop+curEl.height() && offsetFromTop >= 0) {
                    setTimeout(
                        function(){
                            curEl.addClass(notFixedClass);
                            curEl.removeClass(posAnimateClass);
                            lastScrollTop = jQuery(this).scrollTop();
                        },
                        50);
                }
                lastScrollTop = jQuery(this).scrollTop();
            }
            if(st < lastScrollTop) {
                if (jQuery(window).scrollTop() >= offsetFromTop) {
                    if(fixedOffset!=null)
                        childEl.css('top',fixedOffset);
                    curEl.addClass(fixedClass);
                    setTimeout(
                        function(){
                            curEl.removeClass(notFixedClass);
                            curEl.removeClass(posAnimateClass);
                            lastScrollTop = jQuery(this).scrollTop();
                        },
                        50);
                }
                else {
                    if(fixedOffset!=null) childEl.css('top',0);
                    curEl.removeClass(fixedClass);
                    curEl.addClass(posAnimateClass);
                }
                lastScrollTop = jQuery(this).scrollTop();
            }
            lastScrollTop = st;
        }

        var classicStickyFunc = function(){
            if (jQuery(window).scrollTop() >= offsetFromTop) {
                if(fixedOffset!=null)
                    childEl.css('top',fixedOffset);
                curEl.addClass(fixedClass);
                if(settings.animation)
                    setTimeout(
                        function(){
                            curEl.removeClass(posAnimateClass);
                        },
                        50);
            }
            else {
                if(fixedOffset!=null)
                    childEl.css('top',0);
                curEl.removeClass(fixedClass);
                if(settings.animation)
                    setTimeout(
                        function(){
                            curEl.addClass(posAnimateClass);
                        },
                        50);
            }
        }

        /* **Main Functionality** */
        setStickySize();
        jQuery(window).resize(function(){
            settings.resizeFunc.call(this);
            fixedOffset = settings.fixedOffsetFunc.call(this);
            setStickySize();

            /* Smart Scroll Functionality */
            if(settings.scrollStyle =='smart') {
                smartStickyFunc();
            }

            /* Classic Scroll Functionality */
            if(settings.scrollStyle =='classic') {
                classicStickyFunc();
            }
        });

        /* **Settings** */
        /* Animate */
        if(settings.animation)
            curEl.addClass('animated');

        /* Scroll Function */
        settings.scrollFunc.call(this);

        /* Smart Scroll Functionality */
        if(settings.scrollStyle =='smart') {
            jQuery(window).scroll(function(event){
                smartStickyFunc();
            });
        }

        /* Classic Scroll Functionality */
        if(settings.scrollStyle =='classic') {
            jQuery(window).scroll(function (event) {
                classicStickyFunc();
            });
        }
     })

};


/**
 *  Toggles Mobile Menu
 **/
function mobileMenuToggle(e, curEl) {
    e.preventDefault();
    e.stopPropagation();
    var targetSel =  curEl.next('.sub-menu');
    if(curEl.hasClass('toggled-on')) {
        targetSel.stop( true, true ).slideUp(300, function(){
            curEl.removeClass('toggled-on');
        });
    }
    else {
        targetSel.stop( true, true ).slideDown(300, function(){
            curEl.addClass('toggled-on');
        });
    }
}


/**
 *  Mobile Navigation
 **/
function bbMobileNavigation() {
    /* Mobile navigation sidebar open/close  */
    jQuery(document).on("click", '#menu-button', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var curEl = jQuery(this);
        var mbNavEl = jQuery('.bb-mobile-navigation');
        if(curEl.hasClass('pos-left')) {
            mbNavEl.addClass('pos-left');
            mbNavEl.removeClass('pos-right');
        }
        else {
            mbNavEl.addClass('pos-right');
            mbNavEl.removeClass('pos-left');
        }
        setTimeout(function(){
            bb.html.toggleClass('main-menu-open');
        }, 50);

    });
    jQuery(document).on("click", '#menu-close', function (e) {
        e.preventDefault();
        e.stopPropagation();
        setTimeout(function(){
            bb.html.toggleClass('main-menu-open');
        }, 50);

    });
    /* Mobile menu toggle */
    jQuery('.bb-mobile-navigation .dropdown-toggle').on('touchstart click',function(e){
        mobileMenuToggle(e, jQuery(this));
    });
    /* Mobile Nav Bg Click Events */
    var mbNavBgClickEvents = function(target) {
        jQuery('.toggled-on').removeClass('toggled-on');
        if (bb.html.hasClass('main-menu-open')) {
            target.preventDefault();
            bb.html.removeClass('main-menu-open');
        }
    }
    /* When closing something on background click, we need to set touchend and click events.
     Because otherwise when clicking on any target under which we have link, the link will be clicked and the page will redirect */
    jQuery(document).on("click", '#mobile-nav-bg', function (target) {
        mbNavBgClickEvents(target);
    });
    jQuery(document).on("touchend", '#mobile-nav-bg', function (target) {
        mbNavBgClickEvents(target);
    });
}


/**
 *  Shows and Hides Some Elements on Scroll
 **/
/* *** Shows/ hides go top button on scroll *** */
function showHideGoTopOnScroll() {
    jQuery(window).scroll(function () {
        if (bb.scrollTop >= 500) {
            jQuery('#go-top').addClass('show');
        } else {
            jQuery('#go-top').removeClass('show');
        }
    });
}
/* *** Shows/ hides fixed navigation on scroll *** */
function showHideFixedNavOnScroll() {
    if(jQuery('.bb-post-single').length) {
        var sPostContent = jQuery('.bb-post-single .s-post-content');
        // Set default offset from top
        var topOffset = 500 + bb.windowHeight;
        // If post has content, set post content offset as top offset
        if(sPostContent.length)
             topOffset = sPostContent.offset().top;

        jQuery(window).scroll(function () {
            // If footer sticky border and content are both visible, no need to hide fixed pagination when reaching footer
            if(bb.stickyBorder - topOffset < bb.windowHeight && bb.scrollTop > topOffset -bb.windowHeight )
                jQuery('.bb-fixed-pagination').removeClass('hide');
            else
            {
                // If content is visible and footer sticky border is not visible, show fixed pagination
                if(bb.scrollTop > topOffset -bb.windowHeight && bb.scrollTop < bb.stickyBorder - bb.windowHeight)
                    jQuery('.bb-fixed-pagination').removeClass('hide');
                else
                // If footer sticky border is visible, hide fixed pagination
                if (bb.scrollTop > bb.stickyBorder-bb.windowHeight)
                    jQuery('.bb-fixed-pagination').addClass('hide');
            }
        });
    }

}
function showHideElementsOnScroll() {
    showHideGoTopOnScroll();
    showHideFixedNavOnScroll();
}

/**
 *  Featured Carousel
 **/
function bbFeaturedCarousel(){
    jQuery(".featured-carousel").each(function(){

        var itemWidth = jQuery(this).hasClass('big-item')? 200 : 150;
        var containerWidth = jQuery(this).width();
        var slidesToShow = Math.round(containerWidth / itemWidth);

        if(slidesToShow < 1) return;

        jQuery(this).find('ul').slick({
            infinite: true,
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToShow,
            prevArrow:'<a type="button" href="#" class="bb-arrow-prev"></a>',
            nextArrow:'<a type="button" href="#" class="bb-arrow-next"></a>',
            swipe: true,
            rtl: bb.isRTL,
            arrows: true,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: Math.round(768 / itemWidth),
                    slidesToScroll: Math.round(768 / itemWidth)-1,
                }
            },{
                breakpoint: 480,
                settings: {
                    slidesToShow: Math.round(480 / itemWidth),
                    slidesToScroll: Math.round(480 / itemWidth)-1,
                }
            },]
        });
    });
}

/**
 *  Hyena GIF
 **/
function HyenaGIF() {

    var excluded_selectors = [
        '.gallery-item img[src*=".gif"]',
        '.regif_row_parent img[src*=".gif"]',
        '.next-prev-pagination img[src*=".gif"]',
        '.bb-no-play img[src*=".gif"]',
        '.bb-post-gallery-content img[src*=".gif"]',
        '.zf-result_media img[src*=".gif"]',
        'img.fr-fil[src*=".gif"]'
    ];
    excluded_selectors = excluded_selectors.concat( boombox_global_vars.single_post_animated_hyena_gifs_excluded_js_selectors );
    excluded_selectors = excluded_selectors.join( ', ' );

    var hyena_possible_nodes = jQuery('.bb-post-single .s-post-content .size-full[src*=".gif"], .bb-post-single .s-post-thumbnail img[src*=".gif"], .bb-media-playable img[src*=".gif"]').not( excluded_selectors );

    if( hyena_possible_nodes.length ) {
        hyena_possible_nodes.Hyena({
            "style": 1,
            "controls": false,
            "on_hover": (boombox_global_vars.boombox_gif_event == 'hover'),
            "on_scroll": (boombox_global_vars.boombox_gif_event == 'scroll')
        });
    }

    jQuery('body').on( 'bbNewContentLoaded', function(e, newItems) {

        var $selector = jQuery('.bb-media-playable .item-added  img[src*=".gif"]');

        if( $selector.length ) {
            $selector.Hyena({
                "style": 1,
                "controls": false,
                "on_hover": (boombox_global_vars.boombox_gif_event == 'hover'),
                "on_scroll": (boombox_global_vars.boombox_gif_event == 'scroll')
            });
        }

    });
}

/**
 * Plays featured video based on the options below
 * Options:
 bb.videoOptions = {
         playerControls: ( string ) mute | full_controls
         autoPlay: 		( string ) scroll | hover|none
         sound: 			( string ) muted | with_sound
         clickEvent: 	( string ) mute_unmute | play_pause
         loop: 			( int )    0 | 1 ( 0 if disabled, 1 if enabled )
         }
 */
function featuredVideo(video) {

    /* *** Variables *** */
    var $videoWrapper = jQuery(video).parent();
    var $btnVolume= $videoWrapper.find('.btn-volume');
    var durationBadge = $videoWrapper.find('.badge-duration');
    var durationTimeout;

    /* *** Helper Functions *** */
    // Play video
    var playVideo = function() {
        if (!$videoWrapper.hasClass('play')) {
            $videoWrapper.addClass('play');
            video.play();
            $btnVolume.removeClass('hidden');
            if(bb.videoOptions.playerControls =='mute')
                durationTimeout = setTimeout(function(){
                    durationBadge.addClass('hidden');
                }, 3000);
        }
    };

    // Pause video
    var pauseVideo = function() {
        if ($videoWrapper.hasClass('play')) {
            $videoWrapper.removeClass('play');
            video.pause();
            $btnVolume.addClass('hidden');
            clearTimeout(durationTimeout);
            if(bb.videoOptions.playerControls =='mute')

                durationBadge.removeClass('hidden');
        }
    };

    // Mute/Unmute click event
    var clickMuteUnmute = function() {
        var mutedVal = jQuery(video).prop('muted');
        if(mutedVal)
            jQuery(video).prop('muted', false);
        else
            jQuery(video).prop('muted', true);
        $videoWrapper.find('.btn-volume .bb-icon').toggleClass('hidden');
    };

    // Play/Pause click event
    var clickPlayPause = function() {
        if($videoWrapper.hasClass('play'))
            pauseVideo();
        else
            playVideo();
    };

    // Set video duration
    var setVideoDuration = function(){
        var durationInterval = window.setInterval(function(t){
            if(video.readyState > 0) {
                var videoDuration = numberToTwoDigits(Math.floor(video.duration));
                if (videoDuration < 60)
                    videoDuration = "00:" + videoDuration.toString();
                if (videoDuration > 60) {
                    var timeRoundPart = numberToTwoDigits(Math.floor(videoDuration / 60));
                    var timeReminder = numberToTwoDigits(Math.round(videoDuration % 60));
                    videoDuration = timeRoundPart + ":" + timeReminder;
                }

                durationBadge.text(videoDuration);
                durationBadge.removeClass('hidden');
                clearInterval(durationInterval);
            }
        },300);

    };

    /* *** General *** */
    $videoWrapper.find('.btn-volume').on("click", function (e) {
        e.stopPropagation();
        clickMuteUnmute();
    });
    // Video click play functionality by default
    $videoWrapper.on("click", function () {
        playVideo();
    });

    /* *** Options *** */
    // Video scroll autoplay/ pause functionality
    var videoView = new Waypoint.Inview({
        element: video,
        entered: function () {
            if(bb.videoOptions.autoPlay =='scroll' && !bb.isMobile)
                playVideo();
        },
        exited: function () {
            setTimeout(function () {
                pauseVideo();
            }, 150);
        }
    });

    // Video hover play functionality
    if(bb.videoOptions.autoPlay =='hover' && !bb.isMobile)
        $videoWrapper.on('mouseenter touchstart', function () {
            playVideo();
        })

    // Video click play/ pause functionality
    if(bb.videoOptions.clickEvent =='play_pause') {
        $videoWrapper.off('click');
        $videoWrapper.on("click", function (e) {
            clickPlayPause();
        });
    }

    // Video click mute/ unmute functionality
    if(bb.videoOptions.clickEvent =='mute_unmute') {
        $videoWrapper.off('click');
        $videoWrapper.on("click", function (e) {
            if($videoWrapper.hasClass('play'))
                clickMuteUnmute();
            else
                playVideo();
        });
    }

    // Video duration
    if(bb.videoOptions.playerControls =='mute') {
        setVideoDuration();
    }

    // Video Loop
    if(bb.videoOptions.loop == 0)
    {
        video.onended = function() {
            $videoWrapper.removeClass('play');
            if(bb.videoOptions.playerControls =='mute')
                durationBadge.removeClass('hidden');
        };
    }
}

/**
 *  This function works with GIF Cloud Converter
 **/
function GIFvideo(video) {

    video.pause();

    jQuery(video).attr('width', '100%').attr('height', 'auto');

    var $videoWrapper = jQuery(video).parent();
    var canPlay = true;


    if (bb.isMobile) {
        jQuery(video).attr('webkit-playsinline', 'playsinline');
        boombox_global_vars.boombox_gif_event = 'click';
    }
    if (boombox_global_vars.boombox_gif_event == 'hover') {

        $videoWrapper.on('mouseenter touchstart', function () {
            $videoWrapper.addClass('play');
            video.play();

        }).on('mouseleave touchend', function () {
            $videoWrapper.removeClass('play');
            video.pause();
        });

    } else if (boombox_global_vars.boombox_gif_event == 'scroll') {

        var videoView = new Waypoint.Inview({
            element: video,
            entered: function () {
                if (canPlay) {
                    $videoWrapper.addClass('play');
                    video.play();
                }

            },
            exited: function () {
                if (canPlay) {
                    setTimeout(function () {
                        $videoWrapper.removeClass('play');
                        video.pause();
                    }, 150);

                }
            }
        });
    }
    $videoWrapper.on('click', function (e) {
        if(!$videoWrapper.parents('.bb-post-collection').hasClass('masonry-grid')) e.stopPropagation();

        if (!$videoWrapper.hasClass('play')) {
            video.play();
            $videoWrapper.addClass('play');
        } else {
            video.pause();
            $videoWrapper.removeClass('play');
        }

        if(!$videoWrapper.parents('.bb-post-collection').hasClass('masonry-grid'))  return false;;
    });
}

/**
 *  This function works with GIF Cloud Converter
 *  Only in mobile
 **/
function GIFtoVideo(img) {
    var $videoWrapper = jQuery(img).parent();
    var imgUrl = jQuery(img).attr('src');
    var video;

    $videoWrapper[0].addEventListener('click', function () {

        if (!jQuery(this).hasClass('video-done')) {

            var videoUrl = jQuery(img).data('video');

            video = document.createElement("video");

            video.setAttribute("loop", true);
            video.setAttribute("poster", imgUrl);
            video.setAttribute("webkit-playsinline", "playsinline");

            var videoSrc = document.createElement("source");

            videoSrc.setAttribute("src", videoUrl);
            videoSrc.setAttribute("type", "video/mp4");

            video.appendChild(videoSrc);
            jQuery(this)[0].appendChild(video);

            toggleVideoPlaying(video);

            jQuery(this).find('img').remove();
            jQuery(this).addClass('video-done');

        } else {

            toggleVideoPlaying(video);
        }
    });

    var videoView = new Waypoint.Inview({
        element: $videoWrapper,
        exited: function () {
            if ($videoWrapper.hasClass('video-done')) {
                var img = '<img  src=' + imgUrl + ' alt="">';
                jQuery(img).appendTo($videoWrapper);
                $videoWrapper.find('video').remove();
                $videoWrapper.removeClass('play');
                $videoWrapper.removeClass('video-done');
            }
        }
    });
}

function toggleVideoPlaying(video) {
    if (video.paused) {

        var promise = video.play();

        // promise won�t be defined in browsers that don't support promisified play()
        if (promise === undefined) {

            //Promisified video play() not supported

            video.setAttribute("controls", true);

        } else {
            promise.then(function () {
                // Video playback successfully initiated, returning a promise
            }).catch(function (error) {
                // Error initiating video playback

                video.setAttribute("controls", true);
            });
        }

        jQuery(video).parent().addClass('play');

    } else {
        video.pause();
        jQuery(video).parent().removeClass('play');

    }
}

/**
 *  Animation to page top
 **/
function animationPageTop() {


    jQuery(document).on("click", '#go-top', function () {
        bbPageAnimate(0,500);
        return false;
    });
}


/**
 *  Disabled Links Behaviour
 **/
function disabledLinksBehaviour() {
    jQuery('.bb-disabled a').click(function(e){
        e.preventDefault();
    });
}

/**
 * Post gallery
 * @returns {*}
 */
jQuery.fn.bbPostGallery = function(){

	return this.each(function () {

		// Variables
		var $this = jQuery(this);
		var $link = $this.find('.bb-js-gallery-link');
		var	ID = $link.data('id');
		var	$popup = jQuery(ID);
		var topScroll = 0;

		var BB = {
			openPopup : function(id){
			    $popup = jQuery(id);
			    if($popup) {
                    $popup.addClass('bb-open');
                    BB.actions();
                    topScroll = bb.scrollTop;
                    jQuery('html').addClass('bb-gl-open');
                }
			},
			closePopup : function(){
                $popup.removeClass('bb-open');
				BB.clearLocation();
				BB.switchMode('slide');
                jQuery('html').removeClass('bb-gl-open');
                window.scrollTo(0, topScroll);
			},
			switchMode : function(mode){
				switch(mode) {
					case 'slide':
						$popup.removeClass('bb-mode-grid');
						$popup.addClass('bb-mode-slide');
						break;
					case 'grid':
						$popup.removeClass('bb-mode-slide');
						$popup.addClass('bb-mode-grid');
						break;
					default:
						$popup.removeClass('bb-mode-grid');
						$popup.addClass('bb-mode-slide');
				}
			},
			changeLocation: function(param){
				// Change Window Location
				window.location.hash = param;

				// Call function onLocationChange
				BB.onLocationChange(param);
			},
			getLocation: function(){
				return window.location.hash;
			},
			clearLocation: function(){
				// Clear window hash
				history.pushState("", document.title, window.location.pathname
					+ window.location.search);
			},
			onLocationChange : function(location){
				// Get img index from window hash
				imgNum = parseInt(location.split('_')[1]);

				// Call slide
				BB.slide(imgNum);

			},
			slide : function(index){
				$popup.find('.bb-gl-slide .bb-active').removeClass('bb-active');
				$popup.find('.bb-gl-slide').find('li').eq(index).addClass('bb-active');
			},
			actions:function(){
                $popup.find('.bb-js-gl-close').on('click', function(e){
                    e.preventDefault();
                    BB.closePopup(ID);
                });

                $popup.find('.bb-js-mode-switcher').on('click',function(e){
                    e.preventDefault();
                    var mode = jQuery(this).data('mode');
                    BB.switchMode(mode);
                });

                $popup.find('.bb-js-slide').on('click',function(e){
                    e.preventDefault();
                    var _this = jQuery(this);
                    var	hash = _this.attr('href');

                    BB.changeLocation(hash);
                });

                $popup.find('.bb-js-gl-item').on('click',function(e){
                    e.preventDefault();
                    var _this = jQuery(this);
                    var	hash = _this.attr('href');

                    BB.changeLocation(hash);
                    BB.switchMode('slide');
                });

                //Close gallery on click 'ESC'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 27) {
                        BB.closePopup(ID);
                    }
                });

                //Slide gallery on click 'left'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 37) {
                        jQuery('.bb-post-gallery-content.bb-open .bb-active .bb-js-slide.bb-gl-prev').trigger('click');
                    }
                });

                //Slide gallery on click 'right'
                jQuery(document).keyup(function(e) {
                    if (e.keyCode === 39) {
                        jQuery('.bb-post-gallery-content.bb-open .bb-active .bb-js-slide.bb-gl-next').trigger('click');
                    }
                });
            }
		};

		$this.on('click',function(e){
			e.preventDefault();
			//var _this = jQuery(this);
			var	hash = $link.attr('href');

			BB.openPopup(ID);
			BB.switchMode('slide');
			BB.changeLocation(hash);
		});

		// Windows load
        if(BB.getLocation()) {
            var url = BB.getLocation();
            //if it is gallery hash
           if(url.includes("post-gallery")){
               var location = BB.getLocation();
               var id = location.substring(0, location.indexOf('_'));
               if(!jQuery(id+'.bb-open').length) {
                   BB.openPopup(id);
                   BB.switchMode('slide');
                   BB.onLocationChange(BB.getLocation());
               }
           }
        }
	});
}

/**
 * Sticky Bottom Functionality
 */
;(function( window, document, undefined ){

    // Plugin constructor
    var bbStickyBottom = function( elem ){
        this.elem = elem;
        this.$elem = jQuery(elem);
        this.stickyBtmEl = this.$elem.find('> div');
        this.elHeight = this.stickyBtmEl.height();
    };

    // Plugin prototype
    bbStickyBottom.prototype = {
        init: function() {
            this.stickyBtmEl.addClass('bb-sticky-btm-el');
            this.setStickyElHeight();
            this.stickyBannerClose();
            this.resizeFunc();
            return this;
        },

        stickyBannerClose: function() {
            var self = this;
            jQuery(self.stickyBtmEl).prepend( "<a href='#' class='sticky-btm-close'>X</a>" );
            self.$elem.find('.sticky-btm-close').on('click', function(e){
                e.preventDefault();
                self.stickyBtmEl.css('opacity', 0);
                setTimeout(function(){
                    self.$elem.hide();
                },300);
            })
        },

        setStickyElHeight: function() {
            var self = this;
            self.$elem.height(self.elHeight);
        },

        resizeFunc: function() {
            var self = this;
            jQuery(window).resize(function(){
                self.setStickyElHeight();
            });

        }
    };

    jQuery.fn.bbStickyBottom = function() {
        return this.each(function() {
            new bbStickyBottom(this).init();
        });
    };

})( window , document );


/**
 * ************ Dom Ready ************
 */
(function ($) {
    "use strict";

    /* Functions */
    $('.has-full-post-button .post-list.standard .post-thumbnail img').each(function(){
        ShowFullPost($(this));
    });

    /* BB Side Navigation */
    bbSideNav();

    /* Post Gallery  */
    $('.bb-post-gallery').bbPostGallery();

    /* Sticky Sidebar  */
    $('.sticky-sidebar').bbStickySidebar();

    /* Masonry Post   */
    postMasonry();

    /* Tabs */
    initializeTabs();

    /* Set Placeholders */
    setFormPlaceholders('.woocommerce','.form-row');

    /* Featured Carousel */
    bbFeaturedCarousel();

    /* LightModal Popup Plugin */
    $('.js-inline-popup').lightModal ({});

    /* Scroll Area Plugin */
    $('.bb-scroll-area.arrow-control').bbScrollableArea({});

    /* Mobile Navigation */
    bbMobileNavigation();

    /* Shows and Hides Some Elements on Scroll */
    showHideElementsOnScroll();

    /* Page Top Animation */
    animationPageTop();

    /* Disabled Links Behaviour */
    disabledLinksBehaviour();

    /* Hyena GIF  */
    HyenaGIF();


    // Post Featured Video autoplay
    if (bb.html.hasClass('video')) {
        $('.post-thumbnail video').not('.gif-video').each(function () {
            var video = $(this)[0];
            featuredVideo(video);
        });
  
        $(' video.gif-video').each(function () {
            var video = $(this)[0];
            GIFvideo(video);
        });

        $(' img.gif-image').each(function () {
            var img = $(this)[0];
            GIFtoVideo(img);
        });
    }

    /* ************ Ends - Gif and Video Functionality ************ */


    /**
     * ************ Load More Content ************
     */
    if ($('#load-more-button').length) {

        var load_more_btn = $('#load-more-button');
        var loading = false;
        var firstClick = false;
        var loadType = load_more_btn.data('scroll');


        $('#load-more-button').on("click", function () {
            if (loading) return;

            loading = true;

            var next_page_url = load_more_btn.attr('data-next_url');

            load_more_btn.parent().addClass('loading');
            jQuery.post(next_page_url, {},
                function (response) {
                    var html = $(response);
                    var container = html.find('#post-items');
                    var articles = container.find('.post-item').addClass('item-added');
                    var more_btn = html.find('#load-more-button');

                    $('#post-items').append(articles);


                    // load new content
                    $('body').trigger( 'bbNewContentLoaded', [ articles ] );

                    // Post Featured Video autoplay
                    if ($("html").hasClass('video')) {
                        $('#post-items  .item-added video').not('.gif-video').each(function () {
                            var video = $(this)[0];
                            featuredVideo(video);
                        });
                        $('#post-items  .item-added video.gif-video').each(function () {
                            var video = $(this)[0];
                            GIFvideo(video);
                        });

                        $('#post-items  .item-added img.gif-image').each(function () {
                            var img = $(this)[0];
                            GIFtoVideo(img);
                        });
                    }

                    $('.has-full-post-button .post-list.standard .item-added .post-thumbnail img').each(function(){
                        ShowFullPost($(this));
                    });

                    $('#post-items  .item-added').removeClass('item-added');

                    load_more_btn.parent().removeClass('loading');

                    if (more_btn.length > 0) {
                        var next_url = more_btn.data('next_url');
                        load_more_btn.attr('data-next_url', next_url);
                    } else {
                        load_more_btn.parent().remove();
                    }

                    loading = false;
                    firstClick = true;
                    if (loadType === 'on_demand' || loadType === 'infinite_scroll') {
                        infiniteScroll();
                    }
                }
            );

        });

        var lm_scrollPos;
        var lm_buttonPos;
        var infiniteScroll = function () {

            if(!$('#load-more-button').length) {
                return;
            }
            if (loadType === 'on_demand' && !firstClick) {
                return false;
            }

            lm_scrollPos = $(window).scrollTop();
            lm_buttonPos = $('#load-more-button').offset();

            $(window).scroll(function () {
                var scroll = $(window).scrollTop();

                if (scroll > lm_scrollPos) {
                    if (scroll >= lm_buttonPos.top - bb.windowHeight) {
                        load_more_btn.trigger("click");
                    }
                }
            });
        }

        if (loadType === 'infinite_scroll') {
            infiniteScroll();
        }

    }

    $("body").on( "alnp-post-loaded", function(){
        $("div#balnp_content_container  .item-added video").not(".gif-video").each(function () {
            var video = $(this)[0];
            featuredVideo(video);
        });

        $("div#balnp_content_container  .item-added video.gif-video").each(function () {
            var video = $(this)[0];
            GIFvideo(video);
        });

        $("div#balnp_content_container  .item-added img.gif-image").each(function () {
            var img = $(this)[0];
            GIFtoVideo(img);
        });


        if (typeof ZombifyOnAjax !== 'undefined' && ZombifyOnAjax) {
            ZombifyOnAjax();
        }

        $("div#balnp_content_container .item-added").removeClass("item-added");
    } );

    /* ************ Ends - Load More Content ************ */

})(jQuery);

/**
 * ************ Window Load ************
 */
jQuery(window).load(function () {
    /* Sticky Navbar */
    jQuery('.bb-sticky.sticky-smart').bbSticky({
        scrollStyle: 'smart',
        fixedOffsetFunc: function(){
            return  getSetAdminBars();
        },
        animation: true
    });
    jQuery('.bb-sticky.sticky-classic').bbSticky({
        fixedOffsetFunc: function(){
            return  getSetAdminBars();
        }
    });

    /* Sticky Fixed Next Page */
    jQuery('.bb-sticky.bb-floating-navbar').bbSticky({
        scrollStyle: 'classic',
        topOffsetFunc: function() {
            return getHeaderAreaHeight();
        },
        keepWrapperHeight: false,
        fullWidth: true,
        animation: true
    });

    jQuery('.bb-sticky.bb-floating-navbar').bbSticky({
        scrollStyle: 'classic',
        topOffsetFunc: function() {
            return getHeaderAreaHeight();
        },
        keepWrapperHeight: false,
        fullWidth: true,
        animation: true
    });

    /* Toggle Functionality */
    jQuery.bbToggle();

    /* Focus Functionality */
    jQuery('.bb-focus').bbFocus();

    /* Floating Pagination */
    setFormPlaceholders('.woocommerce','.form-row');

    /* Sticky Bottom Functionality */
    jQuery('.bb-sticky-btm').bbStickyBottom();
});


