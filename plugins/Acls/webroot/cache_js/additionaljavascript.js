/*!
* Velocity.js: Accelerated JavaScript animation.
* @version 0.0.0
* @requires jQuery.js
* @docs julian.com/research/velocity
* @license Copyright 2014 Julian Shapiro. MIT License: http://en.wikipedia.org/wiki/MIT_License
*/
!function(e,t,a,r){function o(e){for(var t=-1,a=e?e.length:0,r=[];++t<a;){var o=e[t];o&&r.push(o)}return r}function i(e){return"[object Function]"===Object.prototype.toString.call(e)}function l(t){if(t)for(var a=(new Date).getTime(),o=0,i=e.velocity.State.calls.length;i>o;o++)if(e.velocity.State.calls[o]){var s=e.velocity.State.calls[o],g=s[0],d=s[2],f=s[3];f||(f=e.velocity.State.calls[o][3]=a-16);for(var y=Math.min((a-f)/d.duration,1),m=0,h=g.length;h>m;m++){var v=g[m],x=v.element;if(e.data(x,u)){var P=!1;d.display&&"none"!==d.display&&(p.setPropertyValue(x,"display",d.display),e.velocity.State.calls[o][2].display=!1);for(var b in v)if("element"!==b){var V=v[b],S=V.currentValue,k;if(k=1===y?V.endValue:V.startValue+(V.endValue-V.startValue)*e.easing[V.easing](y),V.currentValue=k,p.Hooks.registered[b]){var w=p.Hooks.getRoot(b),C=e.data(x,u).rootPropertyValueCache[w];C&&(V.rootPropertyValue=C)}var T=p.setPropertyValue(x,b,V.currentValue+("auto"===k?"":V.unitType),V.rootPropertyValue);p.Hooks.registered[b]&&(e.data(x,u).rootPropertyValueCache[w]=p.Normalizations.registered[w]?p.Normalizations.registered[w]("extract",null,T[1]):T[1]),"transform"===T[0]&&(P=!0)}d.mobileHA&&(e.data(x,u).transformCache.translate3d===r?(e.data(x,u).transformCache.translate3d="(0, 0, 0)",P=!0):1===y&&(delete e.data(x,u).transformCache.translate3d,P=!0)),P&&p.flushTransformCache(x)}}1===y&&n(o)}e.velocity.State.isTicking&&c(l)}function n(t){for(var a=e.velocity.State.calls[t][0],o=e.velocity.State.calls[t][1],i=e.velocity.State.calls[t][2],l=!1,n=0,s=a.length;s>n;n++){var c=a[n].element;"none"===i.display&&i.loop===!1&&p.setPropertyValue(c,"display",i.display),e.queue(c)[1]!==r&&/\$\.velocity\.queueEntryFlag/i.test(e.queue(c)[1])||e.data(c,u)&&(e.data(c,u).isAnimating=!1,e.data(c,u).rootPropertyValueCache={}),e.dequeue(c)}e.velocity.State.calls[t]=!1;for(var g=0,d=e.velocity.State.calls.length;d>g;g++)if(e.velocity.State.calls[g]!==!1){l=!0;break}l===!1&&(e.velocity.State.isTicking=!1,delete e.velocity.State.calls,e.velocity.State.calls=[]),i.complete&&i.complete.call(o)}var s=function(){if(a.documentMode)return a.documentMode;for(var e=7;e>4;e--){var t=a.createElement("div");if(t.innerHTML="<!--[if IE "+e+"]><span></span><![endif]-->",t.getElementsByTagName("span").length)return t=null,e}return r}(),c=t.requestAnimationFrame||function(){var e=0;return t.webkitRequestAnimationFrame||t.mozRequestAnimationFrame||function(t){var a=(new Date).getTime(),r;return r=Math.max(0,16-(a-e)),e=a+r,setTimeout(function(){t(a+r)},r)}}();if(7>=s)return void(e.fn.velocity=e.fn.animate);if(e.velocity!==r||e.fn.velocity!==r)return void console.log("Velocity is already loaded or its namespace is occupied.");!function(){var t={};e.each(["Quad","Cubic","Quart","Quint","Expo"],function(e,a){t[a]=function(t){return Math.pow(t,e+2)}}),e.extend(t,{Sine:function(e){return 1-Math.cos(e*Math.PI/2)},Circ:function(e){return 1-Math.sqrt(1-e*e)},Elastic:function(e){return 0===e||1===e?e:-Math.pow(2,8*(e-1))*Math.sin((80*(e-1)-7.5)*Math.PI/15)},Back:function(e){return e*e*(3*e-2)},Bounce:function(e){for(var t,a=4;e<((t=Math.pow(2,--a))-1)/11;);return 1/Math.pow(4,3-a)-7.5625*Math.pow((3*t-2)/22-e,2)}}),e.each(t,function(t,a){e.easing["easeIn"+t]=a,e.easing["easeOut"+t]=function(e){return 1-a(1-e)},e.easing["easeInOut"+t]=function(e){return.5>e?a(2*e)/2:1-a(-2*e+2)/2}}),e.easing.spring=function(e){return 1-Math.cos(4.5*e*Math.PI)*Math.exp(6*-e)}}();var u="velocity";e.velocity={State:{isMobile:/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),prefixElement:a.createElement("div"),prefixMatches:{},scrollAnchor:null,scrollProperty:null,isTicking:!1,calls:[]},Classes:{extracted:{},extract:function(){}},CSS:{},Sequence:{},animate:function(){},debug:!1},t.pageYOffset!==r?(e.velocity.State.scrollAnchor=t,e.velocity.State.scrollProperty="pageYOffset"):(e.velocity.State.scrollAnchor=a.documentElement||a.body.parentNode||a.body,e.velocity.State.scrollProperty="scrollTop"),e.velocity.Classes.extract=function(){for(var t=a.styleSheets,r={},o=0,i=t.length;i>o;o++){var l=t[o],n;try{if(!l.cssText&&!l.cssRules)continue;n=l.cssText?l.cssText.replace(/[\r\n]/g,"").match(/[^}]+\{[^{]+\}/g):l.cssRules;for(var s=0,c=n.length;c>s;s++){var u;if(l.cssText)u=n[s];else{if(!n[s].cssText)continue;u=n[s].cssText}var p=u.match(/\.animate_([A-z0-9_-]+)(?:(\s+)?{)/);if(p){var g=p[1],d=u.toLowerCase().match(/\{([\S\s]*)\}/)[1].match(/[A-z-][^;]+/g);r[g]||(r[g]={});for(var f=0,y=d.length;y>f;f++){var m=d[f].match(/([^:]+):\s*(.+)/);r[g][m[1]]=m[2]}}}}catch(h){}}return e.velocity.Classes.extracted=r,e.velocity.debug&&console.log("Classes: "+JSON.stringify(e.velocity.Classes.extracted)),r};var p=e.velocity.CSS={RegEx:{valueUnwrap:/^[A-z]+\((.*)\)$/i,wrappedValueAlreadyExtracted:/[0-9.]+ [0-9.]+ [0-9.]+( [0-9.]+)?/,valueSplit:/([A-z]+\(.+\))|(([A-z0-9#-.]+?)(?=\s|$))/gi},Hooks:{templates:{color:["Red Green Blue Alpha","255 255 255 1"],backgroundColor:["Red Green Blue Alpha","255 255 255 1"],borderColor:["Red Green Blue Alpha","255 255 255 1"],outlineColor:["Red Green Blue Alpha","255 255 255 1"],textShadow:["Color X Y Blur","black 0px 0px 0px"],boxShadow:["Color X Y Blur Spread","black 0px 0px 0px 0px"],clip:["Top Right Bottom Left","0px 0px 0px 0px"],backgroundPosition:["X Y","0% 0%"],transformOrigin:["X Y Z","50% 50% 0%"],perspectiveOrigin:["X Y","50% 50%"]},registered:{},register:function(){var e,t,a;if(s)for(e in p.Hooks.templates){t=p.Hooks.templates[e],a=t[0].split(" ");var r=t[1].match(p.RegEx.valueSplit);"Color"===a[0]&&(a.push(a.shift()),r.push(r.shift()),p.Hooks.templates[e]=[a.join(" "),r.join(" ")])}for(e in p.Hooks.templates){t=p.Hooks.templates[e],a=t[0].split(" ");for(var o in a){var i=e+a[o],l=o;p.Hooks.registered[i]=[e,l]}}},getRoot:function(e){var t=p.Hooks.registered[e];return t?t[0]:e},cleanRootPropertyValue:function(e,t){return p.RegEx.valueUnwrap.test(t)&&(t=t.match(p.Hooks.RegEx.valueUnwrap)[1]),p.Values.isCSSNullValue(t)&&(t=p.Hooks.templates[e][1]),t},extractValue:function(e,t){var a=p.Hooks.registered[e];if(a){var r=a[0],o=a[1];return t=p.Hooks.cleanRootPropertyValue(r,t),t.toString().match(p.RegEx.valueSplit)[o]}return t},injectValue:function(e,t,a){var r=p.Hooks.registered[e];if(r){var o=r[0],i=r[1],l,n;return a=p.Hooks.cleanRootPropertyValue(o,a),l=a.toString().match(p.RegEx.valueSplit),l[i]=t,n=l.join(" ")}return a}},Normalizations:{registered:{clip:function(e,t,a){switch(e){case"name":return"clip";case"extract":var r;return p.RegEx.wrappedValueAlreadyExtracted.test(a)?r=a:(r=a.toString().match(p.RegEx.valueUnwrap),r&&(r=r[1].replace(/,(\s+)?/g," "))),r;case"inject":return"rect("+a+")"}},opacity:function(e,t,a){if(8>=s)switch(e){case"name":return"filter";case"extract":var r=a.toString().match(/alpha\(opacity=(.*)\)/i);return a=r?r[1]/100:1;case"inject":return t.style.zoom=1,"alpha(opacity="+parseInt(100*a)+")"}else switch(e){case"name":return"opacity";case"extract":return a;case"inject":return a}}},register:function(){function t(e){var t=/^#?([a-f\d])([a-f\d])([a-f\d])$/i,a=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i,r;return e=e.replace(t,function(e,t,a,r){return t+t+a+a+r+r}),r=a.exec(e),r?"rgb("+(parseInt(r[1],16)+" "+parseInt(r[2],16)+" "+parseInt(r[3],16))+")":"rgb(0 0 0)"}var a=["translateX","translateY","scale","scaleX","scaleY","skewX","skewY","rotateZ"];9>=s||(a=a.concat(["translateZ","scaleZ","rotateX","rotateY"]));for(var o=0,i=a.length;i>o;o++)!function(){var t=a[o];p.Normalizations.registered[t]=function(a,o,i){switch(a){case"name":return"transform";case"extract":return e.data(o,u).transformCache[t]===r?/^scale/i.test(t)?1:0:e.data(o,u).transformCache[t].replace(/[()]/g,"");case"inject":var l=!1;switch(t.substr(0,t.length-1)){case"translate":l=!/(%|px|em|rem|\d)$/i.test(i);break;case"scale":l=!/(\d)$/i.test(i);break;case"skew":l=!/(deg|\d)$/i.test(i);break;case"rotate":l=!/(deg|\d)$/i.test(i)}return l||(e.data(o,u).transformCache[t]="("+i+")"),e.data(o,u).transformCache[t]}}}();for(var l=["color","backgroundColor","borderColor","outlineColor"],o=0,n=l.length;n>o;o++)!function(){var e=l[o];p.Normalizations.registered[e]=function(a,o,i){switch(a){case"name":return e;case"extract":var l;if(p.RegEx.wrappedValueAlreadyExtracted.test(i))l=i;else{var n,c={aqua:"rgb(0, 255, 255);",black:"rgb(0, 0, 0)",blue:"rgb(0, 0, 255)",fuchsia:"rgb(255, 0, 255)",gray:"rgb(128, 128, 128)",green:"rgb(0, 128, 0)",lime:"rgb(0, 255, 0)",maroon:"rgb(128, 0, 0)",navy:"rgb(0, 0, 128)",olive:"rgb(128, 128, 0)",purple:"rgb(128, 0, 128)",red:"rgb(255, 0, 0)",silver:"rgb(192, 192, 192)",teal:"rgb(0, 128, 128)",white:"rgb(255, 255, 255)",yellow:"rgb(255, 255, 0)"};/^[A-z]+$/i.test(i)?n=c[i]!==r?c[i]:c.black:/^#([A-f\d]{3}){1,2}$/i.test(i)?n=t(i):/^rgba?\(/i.test(i)||(n=c.black),l=(n||i).toString().match(p.RegEx.valueUnwrap)[1].replace(/,(\s+)?/g," ")}return 8>=s||3!==l.split(" ").length||(l+=" 1"),l;case"inject":return 8>=s?4===i.split(" ").length&&(i=i.split(/\s+/).slice(0,3).join(" ")):3===i.split(" ").length&&(i+=" 1"),(8>=s?"rgb":"rgba")+"("+i.replace(/\s+/g,",").replace(/\.(\d)+(?=,)/g,"")+")"}}}()}},Names:{camelCase:function(e){return e.replace(/-(\w)/g,function(e,t){return t.toUpperCase()})},prefixCheck:function(t){if(e.velocity.State.prefixMatches[t])return[e.velocity.State.prefixMatches[t],!0];for(var a=["","Webkit","Moz","ms","O"],r=0,o=a.length;o>r;r++){var i;if(i=0===r?t:a[r]+t.replace(/^\w/,function(e){return e.toUpperCase()}),"string"==typeof e.velocity.State.prefixElement.style[i])return e.velocity.State.prefixMatches[t]=i,[i,!0]}return[t,!1]}},Values:{isCSSNullValue:function(e){return 0==e||/^(none|auto|transparent|(rgba\(0, ?0, ?0, ?0\)))$/i.test(e)},getUnitType:function(e){return/^(rotate|skew)/i.test(e)?"deg":/(^(scale|scaleX|scaleY|scaleZ|opacity|alpha|fillOpacity|flexGrow|flexHeight|zIndex|fontWeight)$)|color/i.test(e)?"":"px"}},getPropertyValue:function(a,o,i,l){function n(a,o){if(!l){if("height"===o&&"border-box"!==p.getPropertyValue(a,"boxSizing").toLowerCase())return a.offsetHeight-(parseFloat(p.getPropertyValue(a,"borderTopWidth"))||0)-(parseFloat(p.getPropertyValue(a,"borderBottomWidth"))||0)-(parseFloat(p.getPropertyValue(a,"paddingTop"))||0)-(parseFloat(p.getPropertyValue(a,"paddingBottom"))||0);if("width"===o&&"border-box"!==p.getPropertyValue(a,"boxSizing").toLowerCase())return a.offsetWidth-(parseFloat(p.getPropertyValue(a,"borderLeftWidth"))||0)-(parseFloat(p.getPropertyValue(a,"borderRightWidth"))||0)-(parseFloat(p.getPropertyValue(a,"paddingLeft"))||0)-(parseFloat(p.getPropertyValue(a,"paddingRight"))||0)}var i=0;if(8>=s)i=e.css(a,o);else{var c;c=e.data(a,u)===r?t.getComputedStyle(a,null):e.data(a,u).computedStyle?e.data(a,u).computedStyle:e.data(a,u).computedStyle=t.getComputedStyle(a,null),s&&"borderColor"===o&&(o="borderTopColor"),i=9===s&&"filter"===o?c.getPropertyValue(o):c[o],""===i&&(i=a.style[o])}if("auto"===i&&/^(top|right|bottom|left)$/i.test(o)){var g=n(a,"position");("fixed"===g||"absolute"===g&&/top|left/i.test(o))&&(i=e(a).position()[o]+"px")}return i}var c;if(p.Hooks.registered[o]){var g=o,d=p.Hooks.getRoot(g);i===r&&(i=p.getPropertyValue(a,p.Names.prefixCheck(d)[0])),p.Normalizations.registered[d]&&(i=p.Normalizations.registered[d]("extract",a,i)),c=p.Hooks.extractValue(g,i)}else if(p.Normalizations.registered[o]){var f,y;f=p.Normalizations.registered[o]("name",a),"transform"!==f&&(y=n(a,p.Names.prefixCheck(f)[0]),p.Values.isCSSNullValue(y)&&p.Hooks.templates[o]&&(y=p.Hooks.templates[o][1])),c=p.Normalizations.registered[o]("extract",a,y)}return/^[\d-]/.test(c)||(c=n(a,p.Names.prefixCheck(o)[0])),p.Values.isCSSNullValue(c)&&(c=0),e.velocity.debug>=2&&console.log("Get "+o+": "+c),c},setPropertyValue:function(a,r,o,i){var l=r;if("scroll"===r)t.scrollTo(null,o);else if(p.Normalizations.registered[r]&&"transform"===p.Normalizations.registered[r]("name",a))p.Normalizations.registered[r]("inject",a,o),l="transform",o=e.data(a,u).transformCache[r];else{if(p.Hooks.registered[r]){var n=r,c=p.Hooks.getRoot(r);i=i||p.getPropertyValue(a,c),o=p.Hooks.injectValue(n,o,i),r=c}if(p.Normalizations.registered[r]&&(o=p.Normalizations.registered[r]("inject",a,o),r=p.Normalizations.registered[r]("name",a)),l=p.Names.prefixCheck(r)[0],8>=s)try{a.style[l]=o}catch(g){console.log("Error setting ["+l+"] to ["+o+"]")}else a.style[l]=o;e.velocity.debug>=2&&console.log("Set "+r+" ("+l+"): "+o)}return[l,o]},flushTransformCache:function(t){var a="",r,o;for(r in e.data(t,u).transformCache)o=e.data(t,u).transformCache[r],9===s&&"rotateZ"===r&&(r="rotate"),a+=r+o+" ";p.setPropertyValue(t,"transform",a)}};p.Hooks.register(),p.Normalizations.register(),e.fn.velocity=e.velocity.animate=function(){function t(){var t=this,n=e.extend({},e.fn.velocity.defaults,g),d={};if("stop"===y)return e.queue(t,"string"==typeof g?g:"",[]),!0;switch(e.data(t,u)===r&&e.data(t,u,{isAnimating:!1,computedStyle:null,tweensContainer:null,rootPropertyValueCache:{},transformCache:{}}),n.duration.toString().toLowerCase()){case"fast":n.duration=200;break;case"normal":n.duration=400;break;case"slow":n.duration=600;break;default:n.duration=parseFloat(n.duration)||parseFloat(e.fn.velocity.defaults.duration)||400}e.easing[n.easing]||(n.easing=e.easing[e.fn.velocity.defaults.easing]?e.fn.velocity.defaults.easing:"swing"),/^\d/.test(n.delay)&&e.queue(t,n.queue,function(t){e.velocity.queueEntryFlag=!0,setTimeout(t,parseFloat(n.delay))}),n.display&&(n.display=n.display.toLowerCase()),n.mobileHA=n.mobileHA&&e.velocity.State.isMobile,e.queue(t,n.queue,function(f){function m(a){var o=r,l=r,s=r;return"[object Array]"===Object.prototype.toString.call(a)?(o=a[0],/^[\d-]/.test(a[1])||i(a[1])?s=a[1]:"string"==typeof a[1]&&(e.easing[a[1]]!==r&&(l=a[1]),a[2]&&(s=a[2]))):o=a,l=l||n.easing,i(o)&&(o=o.call(t,x,v)),i(s)&&(s=s.call(t,x,v)),[o||0,l,s]}function h(e,t){var a,r;return r=(t||0).toString().toLowerCase().replace(/[%A-z]+$/,function(e){return a=e,""}),a||(a=p.Values.getUnitType(e)),[r,a]}function V(){var r={parent:t.parentNode,position:p.getPropertyValue(t,"position"),fontSize:p.getPropertyValue(t,"fontSize")},o=r.position===P.lastPosition&&r.parent===P.lastParent,i=r.fontSize===P.lastFontSize;P.lastParent=r.parent,P.lastPosition=r.position,P.lastFontSize=r.fontSize,null===P.remToPxRatio&&(P.remToPxRatio=parseFloat(p.getPropertyValue(a.body,"fontSize"))||16);var l={overflowX:null,overflowY:null,boxSizing:null,width:null,minWidth:null,maxWidth:null,height:null,minHeight:null,maxHeight:null,paddingLeft:null},n={},s=10;n.remToPxRatio=P.remToPxRatio,l.overflowX=p.getPropertyValue(t,"overflowX"),l.overflowY=p.getPropertyValue(t,"overflowY"),l.boxSizing=p.getPropertyValue(t,"boxSizing"),l.width=p.getPropertyValue(t,"width",null,!0),l.minWidth=p.getPropertyValue(t,"minWidth"),l.maxWidth=p.getPropertyValue(t,"maxWidth")||"none",l.height=p.getPropertyValue(t,"height",null,!0),l.minHeight=p.getPropertyValue(t,"minHeight"),l.maxHeight=p.getPropertyValue(t,"maxHeight")||"none",l.paddingLeft=p.getPropertyValue(t,"paddingLeft"),o?(n.percentToPxRatioWidth=P.lastPercentToPxWidth,n.percentToPxRatioHeight=P.lastPercentToPxHeight):(p.setPropertyValue(t,"overflowX","hidden"),p.setPropertyValue(t,"overflowY","hidden"),p.setPropertyValue(t,"boxSizing","content-box"),p.setPropertyValue(t,"width",s+"%"),p.setPropertyValue(t,"minWidth",s+"%"),p.setPropertyValue(t,"maxWidth",s+"%"),p.setPropertyValue(t,"height",s+"%"),p.setPropertyValue(t,"minHeight",s+"%"),p.setPropertyValue(t,"maxHeight",s+"%")),i?n.emToPxRatio=P.lastEmToPx:p.setPropertyValue(t,"paddingLeft",s+"em"),o||(n.percentToPxRatioWidth=P.lastPercentToPxWidth=(parseFloat(p.getPropertyValue(t,"width",null,!0))||0)/s,n.percentToPxRatioHeight=P.lastPercentToPxHeight=(parseFloat(p.getPropertyValue(t,"height",null,!0))||0)/s),i||(n.emToPxRatio=P.lastEmToPx=(parseFloat(p.getPropertyValue(t,"paddingLeft"))||0)/s);for(var c in l)p.setPropertyValue(t,c,l[c]);return e.velocity.debug>=1&&console.log("Unit ratios: "+JSON.stringify(n),t),n}if(e.velocity.queueEntryFlag=!0,"scroll"===y){var S=e.velocity.State.scrollAnchor[e.velocity.State.scrollProperty],k=parseFloat(n.offset)||0;d={scroll:{rootPropertyValue:!1,startValue:S,currentValue:S,endValue:e(t).offset().top+k,unitType:"",easing:n.easing},element:t}}else if("reverse"===y){if(!e.data(t,u).tweensContainer)return void e.dequeue(t,n.queue);"none"===e.data(t,u).opts.display&&(e.data(t,u).opts.display="block"),e.data(t,u).opts.loop=!1,n=e.extend({},e.data(t,u).opts,g);var w=e.extend(!0,{},e.data(t,u).tweensContainer);for(var C in w)if("element"!==C){var T=w[C].startValue;w[C].startValue=w[C].currentValue=w[C].endValue,w[C].endValue=T,g&&(w[C].easing=n.easing)}d=w}else if("start"===y){var w;e.data(t,u).tweensContainer&&e.data(t,u).isAnimating===!0&&(w=e.data(t,u).tweensContainer);for(var H in c){H=p.Names.camelCase(H);var R=m(c[H]),z=R[0],N=R[1],A=R[2],E=p.Hooks.getRoot(H),q=!1;if(p.Names.prefixCheck(E)[1]!==!1||p.Normalizations.registered[E]!==r){n.display&&"none"!==n.display&&/opacity|filter/.test(H)&&!A&&0!==z&&(A=0),n._cacheValues&&w&&w[H]?(A=w[H].endValue+w[H].unitType,q=e.data(t,u).rootPropertyValueCache[E]):p.Hooks.registered[H]?A===r?(q=p.getPropertyValue(t,E),A=p.getPropertyValue(t,H,q)):q=p.Hooks.templates[E][1]:A===r&&(A=p.getPropertyValue(t,H));var F,M,j,W;F=h(H,A),A=F[0],j=F[1],F=h(H,z),z=F[0].replace(/^([+-\/*])=/,function(e,t){return W=t,""}),M=F[1],A=parseFloat(A)||0,z=parseFloat(z)||0;var $;if("%"===M&&(/^(fontSize|lineHeight)$/.test(H)?(z/=100,M="em"):/^scale/.test(H)?(z/=100,M=""):/(Red|Green|Blue)$/i.test(H)&&(z=z/100*255,M="")),/[\/*]/.test(W))M=j;else if(j!==M&&0!==A)if(0===z)M=j;else{$=$||V();var O=/margin|padding|left|right|width|text|word|letter/i.test(H)||/X$/.test(H)?"x":"y";switch(j){case"%":A*="x"===O?$.percentToPxRatioWidth:$.percentToPxRatioHeight;break;case"em":A*=$.emToPxRatio;break;case"rem":A*=$.remToPxRatio;break;case"px":}switch(M){case"%":A*=1/("x"===O?$.percentToPxRatioWidth:$.percentToPxRatioHeight);break;case"em":A*=1/$.emToPxRatio;break;case"rem":A*=1/$.remToPxRatio;break;case"px":}}switch(W){case"+":z=A+z;break;case"-":z=A-z;break;case"*":z=A*z;break;case"/":z=A/z}d[H]={rootPropertyValue:q,startValue:A,currentValue:A,endValue:z,unitType:M,easing:N},e.velocity.debug&&console.log("tweensContainer ("+H+"): "+JSON.stringify(d[H]),t)}else e.velocity.debug&&console.log("Skipping ["+E+"] due to a lack of browser support.")}d.element=t}d.element&&(b.push(d),e.data(t,u).tweensContainer=d,e.data(t,u).opts=n,e.data(t,u).isAnimating=!0,x===v-1?(e.velocity.State.calls.length>1e4&&(e.velocity.State.calls=o(e.velocity.State.calls)),e.velocity.State.calls.push([b,s,n]),e.velocity.State.isTicking===!1&&(e.velocity.State.isTicking=!0,l())):x++),""!==n.queue&&"fx"!==n.queue&&setTimeout(f,n.duration+n.delay)}),(n.queue===!1||(""===n.queue||"fx"===n.queue)&&"inprogress"!==e.queue(t)[0])&&e.dequeue(t)}var n,s,c,g,d,f;this.jquery?(n=!0,s=this,c=arguments[0],g=arguments[1]):(n=!1,s=arguments[0],c=arguments[1],g=arguments[2]);var y;switch(c){case"scroll":y="scroll";break;case"reverse":y="reverse";break;case"stop":y="stop";break;default:if(e.isPlainObject(c)&&!e.isEmptyObject(c))y="start";else{if("string"==typeof c&&e.velocity.Sequence[c])return e.velocity.Sequence[c].call(s,g);if("string"!=typeof c||!e.velocity.Classes.extracted[c])return e.velocity.debug&&console.log("First argument was not a property map, a CSS class reference, or a known action. Aborting."),s;c=e.velocity.Classes.extracted[c],y="start"}}if("stop"!==y&&"object"!=typeof g){var m=n?1:2;g={};for(var h=m;h<arguments.length;h++)/^\d/.test(arguments[h])?g.duration=parseFloat(arguments[h]):"string"==typeof arguments[h]?g.easing=arguments[h].replace(/^\s+|\s+$/g,""):i(arguments[h])&&(g.complete=arguments[h])}var v=s.length||1,x=0,P={lastParent:null,lastPosition:null,lastFontSize:null,lastPercentToPxWidth:null,lastPercentToPxHeight:null,lastEmToPx:null,remToPxRatio:null},b=[];if(g&&!i(g.complete)&&(g.complete=null),n)s.each(t);else if(s.nodeType)t.call(s);else if(s[0]&&s[0].nodeType)for(var V in s)t.call(s[V]);var S=e.extend({},e.fn.velocity.defaults,g);if(S.loop=parseInt(S.loop),S.loop)for(var k=0;k<2*S.loop-1;k++)n?s.velocity("reverse",{delay:S.delay}):e.velocity.animate(s,"reverse",{delay:S.delay});return s}}(jQuery,window,document),jQuery.fn.velocity.defaults={queue:"",duration:400,easing:"swing",complete:null,display:null,loop:!1,delay:!1,mobileHA:!0,_cacheValues:!0};
(function($) {
  var NumberProgressBar = function(element, options) {
    var settings = $.extend ({
      duration: 10000,
      style: 'basic',
      min: 0,
      max: 100,
      current: 0,
      shownQuery: '.number-pb-shown',
      numQuery: '.number-pb-num'
    }, options || {});

    this.duration = settings.duration;
    if (settings.style == 'percentage') {
      this.style = 'percentage';
      this.min   = 0;
      this.max   = 100;
    } else if (settings.style == 'step') {
      this.style = 'step';
      this.min   = 0;
      this.max   = (settings.max > this.min) ? settings.max : 100;
    } else {
      this.style = 'basic';
      if (settings.min < settings.max) {
        this.min = settings.min;
        this.max = settings.max;
      } else {
        this.min = 0;
        this.max = 100;
      }
    }
    this.current = (settings.current >= this.min && settings.current <= this.max) ? settings.current : this.min;
    this.interval = this.max - this.min;
    this.last = this.min;
    this.$element = $(element);
    this.$shownBar = this.$element.find(settings.shownQuery);
    this.$num = this.$element.find(settings.numQuery);

    this.reach(this.current);
  }

  NumberProgressBar.prototype.calDestination = function(dest) {
    return (dest < this.min) ? this.min : ( (dest > this.max) ? this.max : dest )
  }

  NumberProgressBar.prototype.calDuration = function() {
    return this.duration * Math.abs(this.current - this.last) / this.interval;
  }

  NumberProgressBar.prototype.shuffle = function(callback) {
    var dest = Math.round(Math.random() * this.interval) + this.min;
    this.reach(dest, null, callback);
  }

  NumberProgressBar.prototype.calPercentage = function() {
    return (this.current - this.min) / this.interval * 100
  }

  NumberProgressBar.prototype.numStyle = function(num) {
    var n = Math.ceil(num);
    var s = "";
    switch (this.style) {
      case 'percentage': s = n + '%';            break;
      case 'step'      : s = n + '/' + this.max; break;
      default          : s = n;
    }
    return s;
  }

  NumberProgressBar.prototype.reach = function(dest, duration, callback) {
    this.current = this.calDestination(dest);
    this.moveShown(duration);
    this.moveNum(duration, callback);
    this.last = this.current;
  }

  NumberProgressBar.prototype.moveShown = function(duration) {
    this.$shownBar.velocity({
      width: this.calPercentage() + '%'
    }, {
      duration: duration || this.calDuration()
    })
  }

  NumberProgressBar.prototype.moveNum = function(duration, callback) {
    var self = this;
    var duration = duration || this.calDuration();

    this.$num.velocity({
      left: this.calPercentage() + '%'
    }, {
      duration: duration,
      complete: callback
    });

    // number
    $({num: this.last}).animate({
      num: this.current
    }, {
      queue: true,
      duration: duration,
      step: function() {
        self.$num.text(self.numStyle(this.num));
      },
      complete: function() {
        self.$num.text(self.numStyle(self.current));
      }
    })
  }

  $.fn.NumberProgressBar = function(options) {
    return this.each(function () {
      var element = $(this);
      if (element.data('number-pb')) return;
      element.data('number-pb', new NumberProgressBar(this, options));
    })
  }

  $.fn.reach = function(dest, options) {
    var settings = $.extend ({
      duration : null,
      complete : null
    }, options || {});
    return this.each(function() {
      var element = $(this);
      var progressbar = element.data('number-pb');
      if (!progressbar) return;
      if (typeof dest === "undefined") {
        progressbar.shuffle(settings.complete);
      } else {
        progressbar.reach(dest, settings.duration, settings.complete);
      }
    })
  }

})(jQuery);
(function ($, window, document, undefined) {
	'use strict';

	var pluginName = "loader",
			defaults = {
				progress: 0,
				frontSpeed: 0.021,
				frontColor: 'green',
				frontOpacity: 0.5,
				backSpeed: 0.025,
				backColor: 'green',
				backOpacity: 0.2
			},
			requestAnimationFrame = (function () {
				var vendors = ['ms', 'moz', 'webkit', 'o'],
						raf = null,
						lastTime = 0;

				for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
					raf = window[vendors[x] + 'RequestAnimationFrame'];
				}

				if (!raf) {
					raf = function (callback, element) {
						var currTime = new Date().getTime(),
								timeToCall = Math.max(0, 16 - (currTime - lastTime)),
								id = window.setTimeout(function () {
											callback(currTime + timeToCall);
										},
										timeToCall);

						lastTime = currTime + timeToCall;

						return id;
					};
				}

				return raf;
			}()),
			cancelAnimationFrame = (function () {
				var vendors = ['ms', 'moz', 'webkit', 'o'],
						caf = null;

				for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
					caf = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
				}

				if (!caf) {
					caf = function (id) {
						clearTimeout(id);
					};
				}

				return caf;
			}());

	/**
	 * Oscillator class for sine wave generation
	 * @param variation
	 * @param max
	 * @param speed
	 * @param horizon
	 * @constructor
	 */
	function Oscillator(variation, max, speed, horizon) {
		this.variation = variation || 0.1;
		this.max = max || 5;
		this.speed = speed || 0.02;
		this.horizon = horizon || 0;

		this._pt = 0;
		this._max = this._getMax();
	}

	$.extend(Oscillator.prototype, {

		/**
		 * Get next point in sine amplitude
		 * @returns {*} amplitude
		 */
		getAmp: function () {
			this._pt += this.speed;

			if (this._pt >= 2.0) {
				this._pt = 0;
				this._max = this._getMax();
			}

			return (this._max * Math.sin(this._pt * Math.PI)) + this.horizon;
		},

		_getMax: function () {
			return Math.random() * this.max * this.variation + this.max * (1 - this.variation);
		}

	});

	function Plugin(element, options) {
		this.element = element;
		this.$el = $(element);
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;
		this._name = pluginName;

		this._width = this.$el.width();
		this._height = this.$el.height();

		this.init();
	}

	Plugin.prototype = {

		init: function () {
			var canvas = $('<canvas></canvas>'),
					counter = $('<div></div>', {
						'class': 'counter'
					}),
					STEPS = 20;

			canvas[0].width = this._width;
			canvas[0].height = this._height;

			this._ctx = canvas[0].getContext('2d');
			this._counter = counter;

			this.$el.append(canvas);
			this.$el.append(counter);

			this._frontOscillator = new Oscillator();
			this._backOscillator = new Oscillator();

			// Config
			this._frontOscillator.speed = this.options.frontSpeed;
			this._frontOscillator.horizon = this.options.progress;
			this._backOscillator.speed = this.options.backSpeed;
			this._backOscillator.horizon = this.options.progress;

			// Points
			this._frontOscillatorPts = new Array(STEPS);
			this._backOscillatorPts = new Array(STEPS);

			// Fill initial points values
			this._fillOscillatorPts(this._frontOscillatorPts, this._frontOscillator);
			this._fillOscillatorPts(this._backOscillatorPts, this._backOscillator);

			this._step = Math.ceil(this._width / (STEPS - 1));

			this._writeProgress(this.options.progress);
			this.animate();
		},

		_fillOscillatorPts: function (pts, oscillator) {
			for (var i = 0, l = pts.length; i < l; i++) {
				pts[i] = oscillator.getAmp();
			}
		},

		_shiftPts: function (pts) {
			for (var i = 0, l = pts.length - 1; i < l; i++) {
				pts[i] = pts[i + 1];
			}
		},

		_draw: function (pts) {
			this._ctx.beginPath();

			// Draw each point
			for (var i = 0, l = pts.length; i < l; i++) {
				this._ctx.lineTo(i * this._step, this._height - pts[i]);
			}

			// Close path to bottom
			this._ctx.lineTo(this._width, this._height);
			this._ctx.lineTo(0, this._height);
			this._ctx.lineTo(0, this._height - pts[0]);

			this._ctx.fill();
		},

		_writeProgress: function (progress) {
			this._counter.text(progress + '%');
		},

		animate: function () {
			// Shift points
			this._shiftPts(this._frontOscillatorPts);
			this._shiftPts(this._backOscillatorPts);

			// Get new points
			this._frontOscillatorPts[this._frontOscillatorPts.length - 1] = this._frontOscillator.getAmp();
			this._backOscillatorPts[this._backOscillatorPts.length - 1] = this._backOscillator.getAmp();

			// Clear canvas
			this._ctx.clearRect(0, 0, this._width, this._height);

			// Draw lines
			this._ctx.globalAlpha = this.options.backOpacity;
			this._ctx.fillStyle = this.options.backColor;
			this._draw(this._backOscillatorPts);
			this._ctx.globalAlpha = this.options.frontOpacity;
			this._ctx.fillStyle = this.options.frontColor;
			this._draw(this._frontOscillatorPts);

			this._animateFrame = requestAnimationFrame($.proxy(this.animate, this));
		},

		setProgress: function (progress) {
			if (progress < 0) {
				progress = 0;
			} else if (progress > 100) {
				progress = 100;
			}

			this._frontOscillator.horizon = progress;
			this._backOscillator.horizon = progress;

			this._writeProgress(progress);
		},

		destroy: function () {
			cancelAnimationFrame(this._animateFrame);
		}
	};

	$.fn[pluginName] = function (options) {
		var args = Array.prototype.slice.call(arguments, 0);

		return this.each(function () {
			var plugin = $.data(this, "plugin_" + pluginName),
					method = null;

			if (!plugin) {
				$.data(this, "plugin_" + pluginName, new Plugin(this, options));
			} else if ($.type(args[0]) === 'string' && args[0].charAt(0) !== '_') {
				method = plugin[args[0]];

				if (method) {
					method.apply(plugin, args.slice(1));
				}
			}
		});
	};

})(jQuery, window, document);
$(function() {
    $('.loader').loader({
        progress: 0,
        frontSpeed: 0.021,
        frontColor: '#E33244',
        frontOpacity: 0.5,
        backSpeed: 0.025,
        backColor: '#E33244',
        backOpacity: 0.2
    });

    var progr = 1;
    setInterval(function() {
        $('.loader').loader('setProgress', ++progr


        );
    }, 300);
});