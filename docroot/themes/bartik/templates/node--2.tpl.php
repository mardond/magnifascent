<!DOCTYPE html>
<html lang="en" >

<head>

	
	<script>
window.ts_endpoint_url = "https:\/\/slack.com\/beacon\/timing";

(function(e) {
	var n=Date.now?Date.now():+new Date,r=e.performance||{},t=[],a={},i=function(e,n){for(var r=0,a=t.length,i=[];a>r;r++)t[r][e]==n&&i.push(t[r]);return i},o=function(e,n){for(var r,a=t.length;a--;)r=t[a],r.entryType!=e||void 0!==n&&r.name!=n||t.splice(a,1)};r.now||(r.now=r.webkitNow||r.mozNow||r.msNow||function(){return(Date.now?Date.now():+new Date)-n}),r.mark||(r.mark=r.webkitMark||function(e){var n={name:e,entryType:"mark",startTime:r.now(),duration:0};t.push(n),a[e]=n}),r.measure||(r.measure=r.webkitMeasure||function(e,n,r){n=a[n].startTime,r=a[r].startTime,t.push({name:e,entryType:"measure",startTime:n,duration:r-n})}),r.getEntriesByType||(r.getEntriesByType=r.webkitGetEntriesByType||function(e){return i("entryType",e)}),r.getEntriesByName||(r.getEntriesByName=r.webkitGetEntriesByName||function(e){return i("name",e)}),r.clearMarks||(r.clearMarks=r.webkitClearMarks||function(e){o("mark",e)}),r.clearMeasures||(r.clearMeasures=r.webkitClearMeasures||function(e){o("measure",e)}),e.performance=r,"function"==typeof define&&(define.amd||define.ajs)&&define("performance",[],function(){return r}) // eslint-disable-line
})(window);

</script>
<script>;(function() {

'use strict';


window.TSMark = function(mark_label) {
	if (!window.performance || !window.performance.mark) return;
	performance.mark(mark_label);
};
window.TSMark('start_load');


window.TSMeasureAndBeacon = function(measure_label, start_mark_label) {
	if (start_mark_label === 'start_nav' && window.performance && window.performance.timing) {
		window.TSBeacon(measure_label, (new Date()).getTime() - performance.timing.navigationStart);
		return;
	}
	if (!window.performance || !window.performance.mark || !window.performance.measure) return;
	performance.mark(start_mark_label + '_end');
	try {
		performance.measure(measure_label, start_mark_label, start_mark_label + '_end');
		window.TSBeacon(measure_label, performance.getEntriesByName(measure_label)[0].duration);
	} catch(e) { return; }
};


window.TSBeacon = function(label, value) {
	var endpoint_url = window.ts_endpoint_url || 'https://slack.com/beacon/timing';
	(new Image()).src = endpoint_url + '?data=' + encodeURIComponent(label + ':' + value);
};

})();
</script>
 

<script>
window.TSMark('step_load');
</script>	<noscript><meta http-equiv="refresh" content="0; URL=/files/ccatalina/F206XQU66/node--2.tpl.php?nojsmode=1" /></noscript>
<script>(function() {
        'use strict';

	var start_time = Date.now();
	var logs = [];
	var connecting = true;
	var ever_connected = false;
	var log_namespace;

	var logWorker = function(ob) {
		var log_str = ob.secs+' start_label:'+ob.start_label+' measure_label:'+ob.measure_label+' description:'+ob.description;

		if (TS.metrics.getLatestMark(ob.start_label)) {
			TS.metrics.measure(ob.measure_label, ob.start_label);
			TS.log(88, log_str);

			if (ob.do_reset) {
				window.TSMark(ob.start_label);
			}
		} else {
			TS.maybeWarn(88, 'not timing: '+log_str);
		}
	}

	var log = function(k, description) {
		var secs = (Date.now()-start_time)/1000;

		logs.push({
			k: k,
			d: description,
			t: secs,
			c: !!connecting
		})

		if (!window.boot_data) return;
		if (!window.TS) return;
		if (!TS.metrics) return;
		if (!connecting) return;

		
		log_namespace = log_namespace || (function() {
			if (boot_data.app == 'client') return 'client';
			if (boot_data.app == 'space') return 'post';
			if (boot_data.app == 'api') return 'apisite';
			if (boot_data.app == 'mobile') return 'mobileweb';
			if (boot_data.app == 'web' || boot_data.app == 'oauth') return 'web';
			return 'unknown';
		})();

		var modifier = (TS.boot_data.feature_no_rollups) ? '_no_rollups' : '';

		logWorker({
			k: k,
			secs: secs,
			description: description,
			start_label: ever_connected ? 'start_reconnect' : 'start_load',
			measure_label: 'v2_'+log_namespace+modifier+(ever_connected ? '_reconnect__' : '_load__')+k,
			do_reset: false,
		});
	}

	var setConnecting = function(val) {
		val = !!val;
		if (val == connecting) return;

		if (val) {
			log('start');
			if (ever_connected) {
				
				window.TSMark('start_reconnect');
				window.TSMark('step_reconnect');
				window.TSMark('step_load');
			}

			connecting = val;
			log('start');
		} else {
			log('over');
			ever_connected = true;
			connecting = val;
		}
	}

	window.TSConnLogger = {
		log: log,
		logs: logs,
		start_time: start_time,
		setConnecting: setConnecting
	}
})();</script>

<script type="text/javascript">
if(self!==top)window.document.write("\u003Cstyle>body * {display:none !important;}\u003C\/style>\u003Ca href=\"#\" onclick="+
"\"top.location.href=window.location.href\" style=\"display:block !important;padding:10px\">Go to Slack.com\u003C\/a>");
</script>

<script>(function() {
        'use strict';

        window.callSlackAPIUnauthed = function(method, args, callback) {
                var timestamp = Date.now() / 1000;  
                var version = (window.TS && TS.boot_data) ? TS.boot_data.version_uid.substring(0, 8) : 'noversion';
                var url = '/api/' + method + '?_x_id=' + version + '-' + timestamp;
                var req = new XMLHttpRequest();

                req.onreadystatechange = function() {
                        if (req.readyState == 4) {
                                req.onreadystatechange = null;
                                var obj;

                                if (req.status == 200 || req.status == 429) {
                                        try {
                                                obj = JSON.parse(req.responseText);
                                        } catch (err) {
                                                console.warn('unable to do anything with api rsp');
                                        }
                                }

                                obj = obj || {
                                        ok: false
                                }

                                callback(obj.ok, obj, args);
                        }
                }

                var async = true;
                req.open('POST', url, async);

                var form_data = new FormData();
                var has_data = false;
                Object.keys(args).map(function(k) {
                        if (k[0] === '_') return;
                        form_data.append(k, args[k]);
                        has_data = true;
                });

                if (has_data) {
                        req.send(form_data);
                } else {
                        req.send();
                }
        }
})();</script>

						
	
		<script>
			if (window.location.host == 'slack.com' && window.location.search.indexOf('story') < 0) {
				document.cookie = '__cvo_skip_doc=' + escape(document.URL) + '|' + escape(document.referrer) + ';path=/';
			}
		</script>
	

	
		<script type="text/javascript">
		
		try {
			if(window.location.hash && !window.location.hash.match(/^(#?[a-zA-Z0-9_]*)$/)) {
				window.location.hash = '';
			}
		} catch(e) {}
		
	</script>

	<script type="text/javascript">
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', "UA-106458-17", 'slack.com');

				
		ga('send', 'pageview');
	
		(function(e,c,b,f,d,g,a){e.SlackBeaconObject=d;
		e[d]=e[d]||function(){(e[d].q=e[d].q||[]).push([1*new Date(),arguments])};
		e[d].l=1*new Date();g=c.createElement(b);a=c.getElementsByTagName(b)[0];
		g.async=1;g.src=f;a.parentNode.insertBefore(g,a)
		})(window,document,"script","https://a.slack-edge.com/dcf8/js/libs/beacon.js","sb");
		sb('set', 'token', '3307f436963e02d4f9eb85ce5159744c');

					sb('set', 'user_id', "U0TMU5MMZ");
							sb('set', 'user_' + "batch", "?");
							sb('set', 'user_' + "created", "2016-03-18");
						sb('set', 'name_tag', "webny" + '/' + "danielmardon");
				sb('track', 'pageview');

		function track(a){ga('send','event','web',a);sb('track',a);}

	</script>



<script type='text/javascript'>
	
	/* safety stub */
	window.mixpanel = {
		track: function() {},
		track_links: function() {},
		track_forms: function() {}
	};

	function mixpanel_track(){}
	function mixpanel_track_forms(){}
	function mixpanel_track_links(){}
	
</script>
	
	<meta name="referrer" content="no-referrer">
		<meta name="superfish" content="nofish">

	<script type="text/javascript">



var TS_last_log_date = null;
var TSMakeLogDate = function() {
	var date = new Date();

	var y = date.getFullYear();
	var mo = date.getMonth()+1;
	var d = date.getDate();

	var time = {
	  h: date.getHours(),
	  mi: date.getMinutes(),
	  s: date.getSeconds(),
	  ms: date.getMilliseconds()
	};

	Object.keys(time).map(function(moment, index) {
		if (moment == 'ms') {
			if (time[moment] < 10) {
				time[moment] = time[moment]+'00';
			} else if (time[moment] < 100) {
				time[moment] = time[moment]+'0';
			}
		} else if (time[moment] < 10) {
			time[moment] = '0' + time[moment];
		}
	});

	var str = y + '/' + mo + '/' + d + ' ' + time.h + ':' + time.mi + ':' + time.s + '.' + time.ms;
	if (TS_last_log_date) {
		var diff = date-TS_last_log_date;
		//str+= ' ('+diff+'ms)';
	}
	TS_last_log_date = date;
	return str+' ';
}

var parseDeepLinkRequest = function(code) {
	var m = code.match(/"id":"([CDG][A-Z0-9]{8})"/);
	var id = m ? m[1] : null;

	m = code.match(/"team":"(T[A-Z0-9]{8})"/);
	var team = m ? m[1] : null;

	m = code.match(/"message":"([0-9]+\.[0-9]+)"/);
	var message = m ? m[1] : null;

	return { id: id, team: team, message: message };
}

if ('rendererEvalAsync' in window) {
	var origRendererEvalAsync = window.rendererEvalAsync;
	window.rendererEvalAsync = function(blob) {
		try {
			var data = JSON.parse(decodeURIComponent(atob(blob)));
			if (data.code.match(/handleDeepLink/)) {
				var request = parseDeepLinkRequest(data.code);
				if (!request.id || !request.team || !request.message) return;

				request.cmd = 'channel';
				TSSSB.handleDeepLinkWithArgs(JSON.stringify(request));
				return;
			} else {
				origRendererEvalAsync(blob);
			}
		} catch (e) {
		}
	}
}
</script>



<script type="text/javascript">

	var TSSSB = {
		call: function() {
			return false;
		}
	};

</script>
<script>TSSSB.env = (function() {
	'use strict';

	var v = {
		win_ssb_version: null,
		win_ssb_version_minor: null,
		mac_ssb_version: null,
		mac_ssb_version_minor: null,
		mac_ssb_build: null,
		lin_ssb_version: null,
		lin_ssb_version_minor: null,
		desktop_app_version: null
	};
	
	var is_win = (navigator.appVersion.indexOf("Windows") !== -1);
	var is_lin = (navigator.appVersion.indexOf("Linux") !== -1);
	var is_mac = !!(navigator.userAgent.match(/(OS X)/g));

	if (navigator.userAgent.match(/(Slack_SSB)/g) || navigator.userAgent.match(/(Slack_WINSSB)/g)) {
		
		var parts = navigator.userAgent.split('/');
		var version_str = parts[parts.length-1];
		var version_float = parseFloat(version_str);
		var version_parts = version_str.split('.');
		var version_minor = (version_parts.length == 3) ? parseInt(version_parts[2]) : 0;

		if (navigator.userAgent.match(/(AtomShell)/g)) {
			
			if (is_lin) {
				v.lin_ssb_version = version_float;
				v.lin_ssb_version_minor = version_minor;
			} else if (is_win) {
				v.win_ssb_version = version_float;
				v.win_ssb_version_minor = version_minor;
			} else if (is_mac) {
				v.mac_ssb_version = version_float;
				v.mac_ssb_version_minor = version_minor;
			}
			
			if (version_parts.length >= 3) {
				v.desktop_app_version = {
					major: parseInt(version_parts[0]),
					minor: parseInt(version_parts[1]),
					patch: parseInt(version_parts[2])
				}
			}
		} else {
			
			v.mac_ssb_version = version_float;
			v.mac_ssb_version_minor = version_minor;
			
			
			
			var app_ver = window.macgap && macgap.app && macgap.app.buildVersion && macgap.app.buildVersion();
			var matches = String(app_ver).match(/(?:\()(.*)(?:\))/);
			v.mac_ssb_build = (matches && matches.length == 2) ? parseInt(matches[1] || 0) : 0;
		}
	}

	return v;
})();
</script>


	<script type="text/javascript">
		
		var was_TS = window.TS;
		delete window.TS;
		TSSSB.call('didFinishLoading');
		if (was_TS) window.TS = was_TS;
	</script>
	    <title>node--2.tpl.php | NYS ITS WebNY Slack</title>
    <meta name="author" content="Slack">

	
		
	
	
					
	
				
	
	
	
	
			<!-- output_css "core" -->
    <link href="https://a.slack-edge.com/1165/style/rollup-plastic.css" rel="stylesheet" type="text/css" crossorigin="anonymous">

		<!-- output_css "before_file_pages" -->
    <link href="https://a.slack-edge.com/ce3a/style/libs/codemirror.css" rel="stylesheet" type="text/css" crossorigin="anonymous">
    <link href="https://a.slack-edge.com/b2f1e/style/codemirror_overrides.css" rel="stylesheet" type="text/css" crossorigin="anonymous">

	<!-- output_css "file_pages" -->
    <link href="https://a.slack-edge.com/8b55/style/rollup-file_pages.css" rel="stylesheet" type="text/css" crossorigin="anonymous">

	<!-- output_css "regular" -->
    <link href="https://a.slack-edge.com/054b/style/print.css" rel="stylesheet" type="text/css" crossorigin="anonymous">
    <link href="https://a.slack-edge.com/1d9c/style/libs/lato-1-compressed.css" rel="stylesheet" type="text/css" crossorigin="anonymous">

	

	
	
	
	

	
<link id="favicon" rel="shortcut icon" href="https://a.slack-edge.com/66f9/img/icons/favicon-32.png" sizes="16x16 32x32 48x48" type="image/png" />

<link rel="icon" href="https://a.slack-edge.com/0180/img/icons/app-256.png" sizes="256x256" type="image/png" />

<link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://a.slack-edge.com/66f9/img/icons/ios-152.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://a.slack-edge.com/66f9/img/icons/ios-144.png" />
<link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://a.slack-edge.com/66f9/img/icons/ios-120.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://a.slack-edge.com/66f9/img/icons/ios-114.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://a.slack-edge.com/0180/img/icons/ios-72.png" />
<link rel="apple-touch-icon-precomposed" href="https://a.slack-edge.com/66f9/img/icons/ios-57.png" />

<meta name="msapplication-TileColor" content="#FFFFFF" />
<meta name="msapplication-TileImage" content="https://a.slack-edge.com/66f9/img/icons/app-144.png" />
	
	<!--[if lt IE 9]>
	<script src="https://a.slack-edge.com/ef0d/js/libs/html5shiv.js"></script>
	<![endif]-->

</head>

<body class="			">

		  			<script>
		
			var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
			if (w > 1440) document.querySelector('body').classList.add('widescreen');
		
		</script>
	
  	
	

			<nav id="site_nav" class="no_transition">

	<div id="site_nav_contents">

		<div id="user_menu">
			<div id="user_menu_contents">
				<div id="user_menu_avatar">
										<span class="member_image thumb_48" style="background-image: url('https://secure.gravatar.com/avatar/6f9da29ea9b69b0520fd5cb5607c90c6.jpg?s=192&d=https%3A%2F%2Fa.slack-edge.com%2F7fa9%2Fimg%2Favatars%2Fava_0020-192.png')" data-thumb-size="48" data-member-id="U0TMU5MMZ"></span>
					<span class="member_image thumb_36" style="background-image: url('https://secure.gravatar.com/avatar/6f9da29ea9b69b0520fd5cb5607c90c6.jpg?s=72&d=https%3A%2F%2Fa.slack-edge.com%2F66f9%2Fimg%2Favatars%2Fava_0020-72.png')" data-thumb-size="36" data-member-id="U0TMU5MMZ"></span>
				</div>
				<h3>Signed in as</h3>
				<span id="user_menu_name">danielmardon</span>
			</div>
		</div>

		<div class="nav_contents">

			<ul class="primary_nav">
				<li><a href="/home" data-qa="home"><i class="ts_icon ts_icon_home"></i>Home</a></li>
				<li><a href="/account" data-qa="account_profile"><i class="ts_icon ts_icon_user"></i>Account & Profile</a></li>
				<li><a href="/apps/manage" data-qa="configure_apps" target="_blank"><i class="ts_icon ts_icon_plug"></i>Configure Apps</a></li>
				<li><a href="/archives"data-qa="archives"><i class="ts_icon ts_icon_archive" ></i>Message Archives</a></li>
				<li><a href="/files" data-qa="files"><i class="ts_icon ts_icon_all_files clear_blue"></i>Files</a></li>
				<li><a href="/team" data-qa="team_directory"><i class="ts_icon ts_icon_team_directory"></i>Team Directory</a></li>
									<li><a href="/stats" data-qa="statistics"><i class="ts_icon ts_icon_dashboard"></i>Statistics</a></li>
													<li><a href="/customize" data-qa="customize"><i class="ts_icon ts_icon_magic"></i>Customize</a></li>
													<li><a href="/account/team" data-qa="team_settings"><i class="ts_icon ts_icon_cog_o"></i>Team Settings</a></li>
							</ul>

			
		</div>

		<div id="footer">

			<ul id="footer_nav">
				<li><a href="/is" data-qa="tour">Tour</a></li>
				<li><a href="/downloads" data-qa="download_apps">Download Apps</a></li>
				<li><a href="/brand-guidelines" data-qa="brand_guidelines">Brand Guidelines</a></li>
				<li><a href="/help" data-qa="help">Help</a></li>
				<li><a href="https://api.slack.com" target="_blank" data-qa="api">API<i class="ts_icon ts_icon_external_link small_left_margin ts_icon_inherit"></i></a></li>
								<li><a href="/pricing" data-qa="pricing">Pricing</a></li>
				<li><a href="/help/requests/new" data-qa="contact">Contact</a></li>
				<li><a href="/terms-of-service" data-qa="policies">Policies</a></li>
				<li><a href="http://slackhq.com/" target="_blank" data-qa="our_blog">Our Blog</a></li>
				<li><a href="https://slack.com/signout/15068431972?crumb=s-1470856820-14f096ca68-%E2%98%83" data-qa="sign_out">Sign Out<i class="ts_icon ts_icon_sign_out small_left_margin ts_icon_inherit"></i></a></li>
			</ul>

			<p id="footer_signature">Made with <i class="ts_icon ts_icon_heart"></i> by Slack</p>

		</div>

	</div>
</nav>	
			<header>
			<a id="menu_toggle" class="no_transition" data-qa="menu_toggle_hamburger">
			<span class="menu_icon"></span>
			<span class="menu_label">Menu</span>
			<span class="vert_divider"></span>
		</a>
		<h1 id="header_team_name" class="inline_block no_transition" data-qa="header_team_name">
			<a href="/home">
				<i class="ts_icon ts_icon_home" /></i>
				NYS ITS WebNY
			</a>
		</h1>
		<div class="header_nav">
			<div class="header_btns float_right">
				<a id="team_switcher" data-qa="team_switcher">
					<i class="ts_icon ts_icon_th_large ts_icon_inherit"></i>
					<span class="block label">Teams</span>
				</a>
				<a href="/help" id="help_link" data-qa="help_link">
					<i class="ts_icon ts_icon_life_ring ts_icon_inherit"></i>
					<span class="block label">Help</span>
				</a>
									<a href="/messages" data-qa="launch">
						<img src="https://a.slack-edge.com/66f9/img/icons/ios-64.png" srcset="https://a.slack-edge.com/66f9/img/icons/ios-32.png 1x, https://a.slack-edge.com/66f9/img/icons/ios-64.png 2x" />
						<span class="block label">Launch</span>
					</a>
							</div>
				                    <ul id="header_team_nav" data-qa="team_switcher_menu">
	                        	                            <li class="active">
	                            	<a href="https://webny.slack.com/home" target="https://webny.slack.com/">
	                            			                            			<i class="ts_icon small ts_icon_check_circle_o active_icon s"></i>
	                            			                            				                            		<i class="team_icon small" style="background-image: url('https://s3-us-west-2.amazonaws.com/slack-files2/avatars/2016-03-18/27789684771_e532d6d9b22731edf0eb_88.png');"></i>
		                            		                            		<span class="switcher_label team_name">NYS ITS WebNY</span>
	                            	</a>
	                            </li>
	                        	                        <li id="add_team_option"><a href="https://slack.com/signin" target="_blank"><i class="ts_icon ts_icon_plus team_icon small"></i> <span class="switcher_label">Sign in to another team...</span></a></li>
	                    </ul>
	                		</div>
	
	
</header>	
	<div id="page" >

		<div id="page_contents" data-qa="page_contents" class="">

<p class="print_only">
	<strong>Created by Christopher Catalina on August 10, 2016 at 3:17 PM</strong><br />
	<span class="subtle_silver break_word">https://webny.slack.com/files/ccatalina/F206XQU66/node--2.tpl.php</span>
</p>

<div class="file_header_container no_print"></div>

<div class="alert_container">
		<div class="file_public_link_shared alert" style="display: none;">
		
	<i class="ts_icon ts_icon_link"></i> Public Link: <a class="file_public_link" href="https://slack-files.com/T0F20CPUL-F206XQU66-fcac94b49f" target="new">https://slack-files.com/T0F20CPUL-F206XQU66-fcac94b49f</a>
</div></div>

<div id="file_page" class="card top_padding">

	<p class="small subtle_silver no_print meta">
		88KB HTML snippet created on <span class="date">August 10th 2016</span>.
		This file is private.		<span class="file_share_list"></span>
	</p>

	<a id="file_action_cog" class="action_cog action_cog_snippet float_right no_print">
		<span>Actions </span><i class="ts_icon ts_icon_cog"></i>
	</a>
	<a id="snippet_expand_toggle" class="float_right no_print">
		<i class="ts_icon ts_icon_expand "></i>
		<i class="ts_icon ts_icon_compress hidden"></i>
	</a>

	<div class="large_bottom_margin clearfix">
		<pre id="file_contents">



&lt;!DOCTYPE html&gt;
&lt;html lang=&quot;en&quot; class=&quot; is-copy-enabled emoji-size-boost is-u2f-enabled&quot;&gt;
  &lt;head prefix=&quot;og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object# article: http://ogp.me/ns/article# profile: http://ogp.me/ns/profile#&quot;&gt;
    &lt;meta charset=&#039;utf-8&#039;&gt;
    

    &lt;link crossorigin=&quot;anonymous&quot; href=&quot;https://assets-cdn.github.com/assets/frameworks-be4c6e0e479a2d4d0eb3159c8772b5bfc4aa39831cb28f5f92cc2e448d93eaa1.css&quot; integrity=&quot;sha256-vkxuDkeaLU0OsxWch3K1v8SqOYMcso9fkswuRI2T6qE=&quot; media=&quot;all&quot; rel=&quot;stylesheet&quot; /&gt;
    &lt;link crossorigin=&quot;anonymous&quot; href=&quot;https://assets-cdn.github.com/assets/github-cafce9070920fab83a8c1b7df81cfa66d3ca70cdea6d05f18674f6bf5b22f046.css&quot; integrity=&quot;sha256-yvzpBwkg+rg6jBt9+Bz6ZtPKcM3qbQXxhnT2v1si8EY=&quot; media=&quot;all&quot; rel=&quot;stylesheet&quot; /&gt;
    
    
    
    

    &lt;link as=&quot;script&quot; href=&quot;https://assets-cdn.github.com/assets/frameworks-464bf5ab70b907f4e70bb71b522033c4d0d7a9bda32935ed8afe27ae7c1905ed.js&quot; rel=&quot;preload&quot; /&gt;
    
    &lt;link as=&quot;script&quot; href=&quot;https://assets-cdn.github.com/assets/github-cee0dbfad60eb6e9fcaf1b24f741c6bce79747414a45c68605978a0239ee6cfa.js&quot; rel=&quot;preload&quot; /&gt;

    &lt;meta http-equiv=&quot;X-UA-Compatible&quot; content=&quot;IE=edge&quot;&gt;
    &lt;meta http-equiv=&quot;Content-Language&quot; content=&quot;en&quot;&gt;
    &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width&quot;&gt;
    
    &lt;title&gt;magnifascent/node--2.tpl.php at feature-2-chriscatalina · ccatalina/magnifascent&lt;/title&gt;
    &lt;link rel=&quot;search&quot; type=&quot;application/opensearchdescription+xml&quot; href=&quot;/opensearch.xml&quot; title=&quot;GitHub&quot;&gt;
    &lt;link rel=&quot;fluid-icon&quot; href=&quot;https://github.com/fluidicon.png&quot; title=&quot;GitHub&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; href=&quot;/apple-touch-icon.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;57x57&quot; href=&quot;/apple-touch-icon-57x57.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;60x60&quot; href=&quot;/apple-touch-icon-60x60.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;72x72&quot; href=&quot;/apple-touch-icon-72x72.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;76x76&quot; href=&quot;/apple-touch-icon-76x76.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;114x114&quot; href=&quot;/apple-touch-icon-114x114.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;120x120&quot; href=&quot;/apple-touch-icon-120x120.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;144x144&quot; href=&quot;/apple-touch-icon-144x144.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;152x152&quot; href=&quot;/apple-touch-icon-152x152.png&quot;&gt;
    &lt;link rel=&quot;apple-touch-icon&quot; sizes=&quot;180x180&quot; href=&quot;/apple-touch-icon-180x180.png&quot;&gt;
    &lt;meta property=&quot;fb:app_id&quot; content=&quot;1401488693436528&quot;&gt;

      &lt;meta content=&quot;https://avatars1.githubusercontent.com/u/6247922?v=3&amp;amp;s=400&quot; name=&quot;twitter:image:src&quot; /&gt;&lt;meta content=&quot;@github&quot; name=&quot;twitter:site&quot; /&gt;&lt;meta content=&quot;summary&quot; name=&quot;twitter:card&quot; /&gt;&lt;meta content=&quot;ccatalina/magnifascent&quot; name=&quot;twitter:title&quot; /&gt;&lt;meta content=&quot;magnifascent - Going through &amp;quot;Building Your First Drupal 7 Website&amp;quot; from BuildAModule.com&quot; name=&quot;twitter:description&quot; /&gt;
      &lt;meta content=&quot;https://avatars1.githubusercontent.com/u/6247922?v=3&amp;amp;s=400&quot; property=&quot;og:image&quot; /&gt;&lt;meta content=&quot;GitHub&quot; property=&quot;og:site_name&quot; /&gt;&lt;meta content=&quot;object&quot; property=&quot;og:type&quot; /&gt;&lt;meta content=&quot;ccatalina/magnifascent&quot; property=&quot;og:title&quot; /&gt;&lt;meta content=&quot;https://github.com/ccatalina/magnifascent&quot; property=&quot;og:url&quot; /&gt;&lt;meta content=&quot;magnifascent - Going through &amp;quot;Building Your First Drupal 7 Website&amp;quot; from BuildAModule.com&quot; property=&quot;og:description&quot; /&gt;
      &lt;meta name=&quot;browser-stats-url&quot; content=&quot;https://api.github.com/_private/browser/stats&quot;&gt;
    &lt;meta name=&quot;browser-errors-url&quot; content=&quot;https://api.github.com/_private/browser/errors&quot;&gt;
    &lt;link rel=&quot;assets&quot; href=&quot;https://assets-cdn.github.com/&quot;&gt;
    &lt;link rel=&quot;web-socket&quot; href=&quot;wss://live.github.com/_sockets/NjI0NzkyMjo0YjQ3MGY2NWRhZDA3NzZhYzAxM2ZiMjA0YmZmNDA4Zjo1Zjg4ZGExNzc4ZTFlYzY3MWQ1ZGMyNjYyZDlkMjU4OTJiMTE5YmU1MjJiNTMwZTllMjNmY2ZmMDdlOTc2NjUy--75ada3a5920778f25d7c2e1fad462b4c57055ed9&quot;&gt;
    &lt;meta name=&quot;pjax-timeout&quot; content=&quot;1000&quot;&gt;
    &lt;link rel=&quot;sudo-modal&quot; href=&quot;/sessions/sudo_modal&quot;&gt;
    &lt;meta name=&quot;request-id&quot; content=&quot;AA7B0404:318C:1EADFED:57AB7DA8&quot; data-pjax-transient&gt;

    &lt;meta name=&quot;msapplication-TileImage&quot; content=&quot;/windows-tile.png&quot;&gt;
    &lt;meta name=&quot;msapplication-TileColor&quot; content=&quot;#ffffff&quot;&gt;
    &lt;meta name=&quot;selected-link&quot; value=&quot;repo_source&quot; data-pjax-transient&gt;

    &lt;meta name=&quot;google-site-verification&quot; content=&quot;KT5gs8h0wvaagLKAVWq8bbeNwnZZK1r1XQysX3xurLU&quot;&gt;
&lt;meta name=&quot;google-site-verification&quot; content=&quot;ZzhVyEFwb7w3e0-uOTltm8Jsck2F5StVihD0exw2fsA&quot;&gt;
    &lt;meta name=&quot;google-analytics&quot; content=&quot;UA-3769691-2&quot;&gt;

&lt;meta content=&quot;collector.githubapp.com&quot; name=&quot;octolytics-host&quot; /&gt;&lt;meta content=&quot;github&quot; name=&quot;octolytics-app-id&quot; /&gt;&lt;meta content=&quot;AA7B0404:318C:1EADFED:57AB7DA8&quot; name=&quot;octolytics-dimension-request_id&quot; /&gt;&lt;meta content=&quot;6247922&quot; name=&quot;octolytics-actor-id&quot; /&gt;&lt;meta content=&quot;ccatalina&quot; name=&quot;octolytics-actor-login&quot; /&gt;&lt;meta content=&quot;db93a0158014702061fb71113670eb874921d74f7a63bd856b1a4cf6cfe80d74&quot; name=&quot;octolytics-actor-hash&quot; /&gt;
&lt;meta content=&quot;/&amp;lt;user-name&amp;gt;/&amp;lt;repo-name&amp;gt;/blob/show&quot; data-pjax-transient=&quot;true&quot; name=&quot;analytics-location&quot; /&gt;



  &lt;meta class=&quot;js-ga-set&quot; name=&quot;dimension1&quot; content=&quot;Logged In&quot;&gt;



        &lt;meta name=&quot;hostname&quot; content=&quot;github.com&quot;&gt;
    &lt;meta name=&quot;user-login&quot; content=&quot;ccatalina&quot;&gt;

        &lt;meta name=&quot;expected-hostname&quot; content=&quot;github.com&quot;&gt;
      &lt;meta name=&quot;js-proxy-site-detection-payload&quot; content=&quot;NDU2ODkzZjMyOGRhMmExZTNiMWQwYmUxNDk3MGViY2U4YjJlM2M1NGE1M2MzM2U5NjI4Nzc4YWFlMjgyOWQxZnx7InJlbW90ZV9hZGRyZXNzIjoiMTcwLjEyMy40LjQiLCJyZXF1ZXN0X2lkIjoiQUE3QjA0MDQ6MzE4QzoxRUFERkVEOjU3QUI3REE4IiwidGltZXN0YW1wIjoxNDcwODU2NjIyfQ==&quot;&gt;


      &lt;link rel=&quot;mask-icon&quot; href=&quot;https://assets-cdn.github.com/pinned-octocat.svg&quot; color=&quot;#4078c0&quot;&gt;
      &lt;link rel=&quot;icon&quot; type=&quot;image/x-icon&quot; href=&quot;https://assets-cdn.github.com/favicon.ico&quot;&gt;

    &lt;meta name=&quot;html-safe-nonce&quot; content=&quot;3c49e7abb2a5c5cc42fd942dc2fdb493bf5f37fc&quot;&gt;
    &lt;meta content=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; name=&quot;form-nonce&quot; /&gt;

    &lt;meta http-equiv=&quot;x-pjax-version&quot; content=&quot;12a5e18e67d79b229862be28de4a6372&quot;&gt;
    

      
  &lt;meta name=&quot;description&quot; content=&quot;magnifascent - Going through &amp;quot;Building Your First Drupal 7 Website&amp;quot; from BuildAModule.com&quot;&gt;
  &lt;meta name=&quot;go-import&quot; content=&quot;github.com/ccatalina/magnifascent git https://github.com/ccatalina/magnifascent.git&quot;&gt;

  &lt;meta content=&quot;6247922&quot; name=&quot;octolytics-dimension-user_id&quot; /&gt;&lt;meta content=&quot;ccatalina&quot; name=&quot;octolytics-dimension-user_login&quot; /&gt;&lt;meta content=&quot;64421953&quot; name=&quot;octolytics-dimension-repository_id&quot; /&gt;&lt;meta content=&quot;ccatalina/magnifascent&quot; name=&quot;octolytics-dimension-repository_nwo&quot; /&gt;&lt;meta content=&quot;true&quot; name=&quot;octolytics-dimension-repository_public&quot; /&gt;&lt;meta content=&quot;true&quot; name=&quot;octolytics-dimension-repository_is_fork&quot; /&gt;&lt;meta content=&quot;63423282&quot; name=&quot;octolytics-dimension-repository_parent_id&quot; /&gt;&lt;meta content=&quot;robtryson/magnifascent&quot; name=&quot;octolytics-dimension-repository_parent_nwo&quot; /&gt;&lt;meta content=&quot;63423282&quot; name=&quot;octolytics-dimension-repository_network_root_id&quot; /&gt;&lt;meta content=&quot;robtryson/magnifascent&quot; name=&quot;octolytics-dimension-repository_network_root_nwo&quot; /&gt;
  &lt;link href=&quot;https://github.com/ccatalina/magnifascent/commits/feature-2-chriscatalina.atom&quot; rel=&quot;alternate&quot; title=&quot;Recent Commits to magnifascent:feature-2-chriscatalina&quot; type=&quot;application/atom+xml&quot;&gt;


      &lt;link rel=&quot;canonical&quot; href=&quot;https://github.com/ccatalina/magnifascent/blob/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; data-pjax-transient&gt;
  &lt;/head&gt;


  &lt;body class=&quot;logged-in  env-production macintosh vis-public fork page-blob&quot;&gt;
    &lt;div id=&quot;js-pjax-loader-bar&quot; class=&quot;pjax-loader-bar&quot;&gt;&lt;div class=&quot;progress&quot;&gt;&lt;/div&gt;&lt;/div&gt;
    &lt;a href=&quot;#start-of-content&quot; tabindex=&quot;1&quot; class=&quot;accessibility-aid js-skip-to-content&quot;&gt;Skip to content&lt;/a&gt;

    
    
    



        &lt;div class=&quot;header header-logged-in true&quot; role=&quot;banner&quot;&gt;
  &lt;div class=&quot;container clearfix&quot;&gt;

    &lt;a class=&quot;header-logo-invertocat&quot; href=&quot;https://github.com/&quot; data-hotkey=&quot;g d&quot; aria-label=&quot;Homepage&quot; data-ga-click=&quot;Header, go to dashboard, icon:logo&quot;&gt;
  &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-mark-github&quot; height=&quot;28&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;28&quot;&gt;&lt;path d=&quot;M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
&lt;/a&gt;


        &lt;div class=&quot;header-search scoped-search site-scoped-search js-site-search&quot; role=&quot;search&quot;&gt;
  &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/search&quot; class=&quot;js-site-search-form&quot; data-scoped-search-url=&quot;/ccatalina/magnifascent/search&quot; data-unscoped-search-url=&quot;/search&quot; method=&quot;get&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;/div&gt;
    &lt;label class=&quot;form-control header-search-wrapper js-chromeless-input-container&quot;&gt;
      &lt;div class=&quot;header-search-scope&quot;&gt;This repository&lt;/div&gt;
      &lt;input type=&quot;text&quot;
        class=&quot;form-control header-search-input js-site-search-focus js-site-search-field is-clearable&quot;
        data-hotkey=&quot;s&quot;
        name=&quot;q&quot;
        placeholder=&quot;Search&quot;
        aria-label=&quot;Search this repository&quot;
        data-unscoped-placeholder=&quot;Search GitHub&quot;
        data-scoped-placeholder=&quot;Search&quot;
        autocapitalize=&quot;off&quot;&gt;
    &lt;/label&gt;
&lt;/form&gt;&lt;/div&gt;


      &lt;ul class=&quot;header-nav left&quot; role=&quot;navigation&quot;&gt;
        &lt;li class=&quot;header-nav-item&quot;&gt;
          &lt;a href=&quot;/pulls&quot; class=&quot;js-selected-navigation-item header-nav-link&quot; data-ga-click=&quot;Header, click, Nav menu - item:pulls context:user&quot; data-hotkey=&quot;g p&quot; data-selected-links=&quot;/pulls /pulls/assigned /pulls/mentioned /pulls&quot;&gt;
            Pull requests
&lt;/a&gt;        &lt;/li&gt;
        &lt;li class=&quot;header-nav-item&quot;&gt;
          &lt;a href=&quot;/issues&quot; class=&quot;js-selected-navigation-item header-nav-link&quot; data-ga-click=&quot;Header, click, Nav menu - item:issues context:user&quot; data-hotkey=&quot;g i&quot; data-selected-links=&quot;/issues /issues/assigned /issues/mentioned /issues&quot;&gt;
            Issues
&lt;/a&gt;        &lt;/li&gt;
          &lt;li class=&quot;header-nav-item&quot;&gt;
            &lt;a class=&quot;header-nav-link&quot; href=&quot;https://gist.github.com/&quot; data-ga-click=&quot;Header, go to gist, text:gist&quot;&gt;Gist&lt;/a&gt;
          &lt;/li&gt;
      &lt;/ul&gt;

    
&lt;ul class=&quot;header-nav user-nav right&quot; id=&quot;user-links&quot;&gt;
  &lt;li class=&quot;header-nav-item&quot;&gt;
    
    &lt;a href=&quot;/notifications&quot; aria-label=&quot;You have unread notifications&quot; class=&quot;header-nav-link notification-indicator tooltipped tooltipped-s js-socket-channel js-notification-indicator&quot; data-channel=&quot;tenant:1:notification-changed:6247922&quot; data-ga-click=&quot;Header, go to notifications, icon:unread&quot; data-hotkey=&quot;g n&quot;&gt;
        &lt;span class=&quot;mail-status unread&quot;&gt;&lt;/span&gt;
        &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-bell&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M14 12v1H0v-1l.73-.58c.77-.77.81-2.55 1.19-4.42C2.69 3.23 6 2 6 2c0-.55.45-1 1-1s1 .45 1 1c0 0 3.39 1.23 4.16 5 .38 1.88.42 3.66 1.19 4.42l.66.58H14zm-7 4c1.11 0 2-.89 2-2H5c0 1.11.89 2 2 2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
&lt;/a&gt;
  &lt;/li&gt;

  &lt;li class=&quot;header-nav-item dropdown js-menu-container&quot;&gt;
    &lt;a class=&quot;header-nav-link tooltipped tooltipped-s js-menu-target&quot; href=&quot;/new&quot;
       aria-label=&quot;Create new…&quot;
       data-ga-click=&quot;Header, create new, icon:add&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-plus left&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 9H7v5H5V9H0V7h5V2h2v5h5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;span class=&quot;dropdown-caret&quot;&gt;&lt;/span&gt;
    &lt;/a&gt;

    &lt;div class=&quot;dropdown-menu-content js-menu-content&quot;&gt;
      &lt;ul class=&quot;dropdown-menu dropdown-menu-sw&quot;&gt;
        
&lt;a class=&quot;dropdown-item&quot; href=&quot;/new&quot; data-ga-click=&quot;Header, create new repository&quot;&gt;
  New repository
&lt;/a&gt;

  &lt;a class=&quot;dropdown-item&quot; href=&quot;/new/import&quot; data-ga-click=&quot;Header, import a repository&quot;&gt;
    Import repository
  &lt;/a&gt;


  &lt;a class=&quot;dropdown-item&quot; href=&quot;/organizations/new&quot; data-ga-click=&quot;Header, create new organization&quot;&gt;
    New organization
  &lt;/a&gt;



  &lt;div class=&quot;dropdown-divider&quot;&gt;&lt;/div&gt;
  &lt;div class=&quot;dropdown-header&quot;&gt;
    &lt;span title=&quot;ccatalina/magnifascent&quot;&gt;This repository&lt;/span&gt;
  &lt;/div&gt;
    &lt;a class=&quot;dropdown-item&quot; href=&quot;/ccatalina/magnifascent/settings/collaboration&quot; data-ga-click=&quot;Header, create new collaborator&quot;&gt;
      New collaborator
    &lt;/a&gt;

      &lt;/ul&gt;
    &lt;/div&gt;
  &lt;/li&gt;

  &lt;li class=&quot;header-nav-item dropdown js-menu-container&quot;&gt;
    &lt;a class=&quot;header-nav-link name tooltipped tooltipped-sw js-menu-target&quot; href=&quot;/ccatalina&quot;
       aria-label=&quot;View profile and more&quot;
       data-ga-click=&quot;Header, show menu, icon:avatar&quot;&gt;
      &lt;img alt=&quot;@ccatalina&quot; class=&quot;avatar&quot; height=&quot;20&quot; src=&quot;https://avatars0.githubusercontent.com/u/6247922?v=3&amp;amp;s=40&quot; width=&quot;20&quot; /&gt;
      &lt;span class=&quot;dropdown-caret&quot;&gt;&lt;/span&gt;
    &lt;/a&gt;

    &lt;div class=&quot;dropdown-menu-content js-menu-content&quot;&gt;
      &lt;div class=&quot;dropdown-menu dropdown-menu-sw&quot;&gt;
        &lt;div class=&quot;dropdown-header header-nav-current-user css-truncate&quot;&gt;
          Signed in as &lt;strong class=&quot;css-truncate-target&quot;&gt;ccatalina&lt;/strong&gt;
        &lt;/div&gt;

        &lt;div class=&quot;dropdown-divider&quot;&gt;&lt;/div&gt;

        &lt;a class=&quot;dropdown-item&quot; href=&quot;/ccatalina&quot; data-ga-click=&quot;Header, go to profile, text:your profile&quot;&gt;
          Your profile
        &lt;/a&gt;
        &lt;a class=&quot;dropdown-item&quot; href=&quot;/stars&quot; data-ga-click=&quot;Header, go to starred repos, text:your stars&quot;&gt;
          Your stars
        &lt;/a&gt;
        &lt;a class=&quot;dropdown-item&quot; href=&quot;/explore&quot; data-ga-click=&quot;Header, go to explore, text:explore&quot;&gt;
          Explore
        &lt;/a&gt;
          &lt;a class=&quot;dropdown-item&quot; href=&quot;/integrations&quot; data-ga-click=&quot;Header, go to integrations, text:integrations&quot;&gt;
            Integrations
          &lt;/a&gt;
        &lt;a class=&quot;dropdown-item&quot; href=&quot;https://help.github.com&quot; data-ga-click=&quot;Header, go to help, text:help&quot;&gt;
          Help
        &lt;/a&gt;


        &lt;div class=&quot;dropdown-divider&quot;&gt;&lt;/div&gt;

        &lt;a class=&quot;dropdown-item&quot; href=&quot;/settings/profile&quot; data-ga-click=&quot;Header, go to settings, icon:settings&quot;&gt;
          Settings
        &lt;/a&gt;

        &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/logout&quot; class=&quot;logout-form&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;KZ2PW6ewYwVbD8HnPakAJIIwpTdzjZCd8WS4b4g0i2H7vy83+VJZFRg3TBuEIPGHbgEYwKTfUjsHIH8oDKA3pw==&quot; /&gt;&lt;/div&gt;
          &lt;button class=&quot;dropdown-item dropdown-signout&quot; data-ga-click=&quot;Header, sign out, icon:logout&quot;&gt;
            Sign out
          &lt;/button&gt;
&lt;/form&gt;      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/li&gt;
&lt;/ul&gt;


    
  &lt;/div&gt;
&lt;/div&gt;


      


    &lt;div id=&quot;start-of-content&quot; class=&quot;accessibility-aid&quot;&gt;&lt;/div&gt;

      &lt;div id=&quot;js-flash-container&quot;&gt;
&lt;/div&gt;


    &lt;div role=&quot;main&quot;&gt;
        &lt;div itemscope itemtype=&quot;http://schema.org/SoftwareSourceCode&quot;&gt;
    &lt;div id=&quot;js-repo-pjax-container&quot; data-pjax-container&gt;
      
&lt;div class=&quot;pagehead repohead instapaper_ignore readability-menu experiment-repo-nav&quot;&gt;
  &lt;div class=&quot;container repohead-details-container&quot;&gt;

    

&lt;ul class=&quot;pagehead-actions&quot;&gt;

  &lt;li&gt;
        &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/notifications/subscribe&quot; class=&quot;js-social-container&quot; data-autosubmit=&quot;true&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; data-remote=&quot;true&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;SCX/G5toNJ+WGxcvB+tDM+S2Ou54DhCXRq9no5QKWC9dtWwsVY0VzIEVwDrBVzjiDVTggx5nVqi0aXubCY66qA==&quot; /&gt;&lt;/div&gt;      &lt;input class=&quot;form-control&quot; id=&quot;repository_id&quot; name=&quot;repository_id&quot; type=&quot;hidden&quot; value=&quot;64421953&quot; /&gt;

        &lt;div class=&quot;select-menu js-menu-container js-select-menu&quot;&gt;
          &lt;a href=&quot;/ccatalina/magnifascent/subscription&quot;
            class=&quot;btn btn-sm btn-with-count select-menu-button js-menu-target&quot; role=&quot;button&quot; tabindex=&quot;0&quot; aria-haspopup=&quot;true&quot;
            data-ga-click=&quot;Repository, click Watch settings, action:blob#show&quot;&gt;
            &lt;span class=&quot;js-select-button&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-eye&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8.06 2C3 2 0 8 0 8s3 6 8.06 6C13 14 16 8 16 8s-3-6-7.94-6zM8 12c-2.2 0-4-1.78-4-4 0-2.2 1.8-4 4-4 2.22 0 4 1.8 4 4 0 2.22-1.78 4-4 4zm2-4c0 1.11-.89 2-2 2-1.11 0-2-.89-2-2 0-1.11.89-2 2-2 1.11 0 2 .89 2 2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              Unwatch
            &lt;/span&gt;
          &lt;/a&gt;
          &lt;a class=&quot;social-count js-social-count&quot; href=&quot;/ccatalina/magnifascent/watchers&quot;&gt;
            1
          &lt;/a&gt;

        &lt;div class=&quot;select-menu-modal-holder&quot;&gt;
          &lt;div class=&quot;select-menu-modal subscription-menu-modal js-menu-content&quot; aria-hidden=&quot;true&quot;&gt;
            &lt;div class=&quot;select-menu-header js-navigation-enable&quot; tabindex=&quot;-1&quot;&gt;
              &lt;svg aria-label=&quot;Close&quot; class=&quot;octicon octicon-x js-menu-close&quot; height=&quot;16&quot; role=&quot;img&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-title&quot;&gt;Notifications&lt;/span&gt;
            &lt;/div&gt;

              &lt;div class=&quot;select-menu-list js-navigation-container&quot; role=&quot;menu&quot;&gt;

                &lt;div class=&quot;select-menu-item js-navigation-item &quot; role=&quot;menuitem&quot; tabindex=&quot;0&quot;&gt;
                  &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                  &lt;div class=&quot;select-menu-item-text&quot;&gt;
                    &lt;input id=&quot;do_included&quot; name=&quot;do&quot; type=&quot;radio&quot; value=&quot;included&quot; /&gt;
                    &lt;span class=&quot;select-menu-item-heading&quot;&gt;Not watching&lt;/span&gt;
                    &lt;span class=&quot;description&quot;&gt;Be notified when participating or @mentioned.&lt;/span&gt;
                    &lt;span class=&quot;js-select-button-text hidden-select-button-text&quot;&gt;
                      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-eye&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8.06 2C3 2 0 8 0 8s3 6 8.06 6C13 14 16 8 16 8s-3-6-7.94-6zM8 12c-2.2 0-4-1.78-4-4 0-2.2 1.8-4 4-4 2.22 0 4 1.8 4 4 0 2.22-1.78 4-4 4zm2-4c0 1.11-.89 2-2 2-1.11 0-2-.89-2-2 0-1.11.89-2 2-2 1.11 0 2 .89 2 2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                      Watch
                    &lt;/span&gt;
                  &lt;/div&gt;
                &lt;/div&gt;

                &lt;div class=&quot;select-menu-item js-navigation-item selected&quot; role=&quot;menuitem&quot; tabindex=&quot;0&quot;&gt;
                  &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                  &lt;div class=&quot;select-menu-item-text&quot;&gt;
                    &lt;input checked=&quot;checked&quot; id=&quot;do_subscribed&quot; name=&quot;do&quot; type=&quot;radio&quot; value=&quot;subscribed&quot; /&gt;
                    &lt;span class=&quot;select-menu-item-heading&quot;&gt;Watching&lt;/span&gt;
                    &lt;span class=&quot;description&quot;&gt;Be notified of all conversations.&lt;/span&gt;
                    &lt;span class=&quot;js-select-button-text hidden-select-button-text&quot;&gt;
                      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-eye&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8.06 2C3 2 0 8 0 8s3 6 8.06 6C13 14 16 8 16 8s-3-6-7.94-6zM8 12c-2.2 0-4-1.78-4-4 0-2.2 1.8-4 4-4 2.22 0 4 1.8 4 4 0 2.22-1.78 4-4 4zm2-4c0 1.11-.89 2-2 2-1.11 0-2-.89-2-2 0-1.11.89-2 2-2 1.11 0 2 .89 2 2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                      Unwatch
                    &lt;/span&gt;
                  &lt;/div&gt;
                &lt;/div&gt;

                &lt;div class=&quot;select-menu-item js-navigation-item &quot; role=&quot;menuitem&quot; tabindex=&quot;0&quot;&gt;
                  &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                  &lt;div class=&quot;select-menu-item-text&quot;&gt;
                    &lt;input id=&quot;do_ignore&quot; name=&quot;do&quot; type=&quot;radio&quot; value=&quot;ignore&quot; /&gt;
                    &lt;span class=&quot;select-menu-item-heading&quot;&gt;Ignoring&lt;/span&gt;
                    &lt;span class=&quot;description&quot;&gt;Never be notified.&lt;/span&gt;
                    &lt;span class=&quot;js-select-button-text hidden-select-button-text&quot;&gt;
                      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-mute&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8 2.81v10.38c0 .67-.81 1-1.28.53L3 10H1c-.55 0-1-.45-1-1V7c0-.55.45-1 1-1h2l3.72-3.72C7.19 1.81 8 2.14 8 2.81zm7.53 3.22l-1.06-1.06-1.97 1.97-1.97-1.97-1.06 1.06L11.44 8 9.47 9.97l1.06 1.06 1.97-1.97 1.97 1.97 1.06-1.06L13.56 8l1.97-1.97z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
                      Stop ignoring
                    &lt;/span&gt;
                  &lt;/div&gt;
                &lt;/div&gt;

              &lt;/div&gt;

            &lt;/div&gt;
          &lt;/div&gt;
        &lt;/div&gt;
&lt;/form&gt;
  &lt;/li&gt;

  &lt;li&gt;
    
  &lt;div class=&quot;js-toggler-container js-social-container starring-container &quot;&gt;

    &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/unstar&quot; class=&quot;starred&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; data-remote=&quot;true&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;5Z1j8F2zLyQgiKWAtiVllGYkQ9+5ru6I4A6Rt3U86ZiWkXku2iU8k9NrHvGtJE5aWcSjEvgR/gbs5os4Mv0eXQ==&quot; /&gt;&lt;/div&gt;
      &lt;button
        class=&quot;btn btn-sm btn-with-count js-toggler-target&quot;
        aria-label=&quot;Unstar this repository&quot; title=&quot;Unstar ccatalina/magnifascent&quot;
        data-ga-click=&quot;Repository, click unstar button, action:blob#show; text:Unstar&quot;&gt;
        &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-star&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
        Unstar
      &lt;/button&gt;
        &lt;a class=&quot;social-count js-social-count&quot; href=&quot;/ccatalina/magnifascent/stargazers&quot;&gt;
          0
        &lt;/a&gt;
&lt;/form&gt;
    &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/star&quot; class=&quot;unstarred&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; data-remote=&quot;true&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;rzpd4yjLsx8DYVlZGR7BZOm19nBTTVweB2PMWF8SvpvTqV0Jx7t6tl4R4aMF/9n8g9YUa++8V/bRmvbIUTiThg==&quot; /&gt;&lt;/div&gt;
      &lt;button
        class=&quot;btn btn-sm btn-with-count js-toggler-target&quot;
        aria-label=&quot;Star this repository&quot; title=&quot;Star ccatalina/magnifascent&quot;
        data-ga-click=&quot;Repository, click star button, action:blob#show; text:Star&quot;&gt;
        &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-star&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
        Star
      &lt;/button&gt;
        &lt;a class=&quot;social-count js-social-count&quot; href=&quot;/ccatalina/magnifascent/stargazers&quot;&gt;
          0
        &lt;/a&gt;
&lt;/form&gt;  &lt;/div&gt;

  &lt;/li&gt;

  &lt;li&gt;
          &lt;a href=&quot;#fork-destination-box&quot; class=&quot;btn btn-sm btn-with-count&quot;
              title=&quot;Fork your own copy of ccatalina/magnifascent to your account&quot;
              aria-label=&quot;Fork your own copy of ccatalina/magnifascent to your account&quot;
              rel=&quot;facebox&quot;
              data-ga-click=&quot;Repository, show fork modal, action:blob#show; text:Fork&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-repo-forked&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 10 16&quot; width=&quot;10&quot;&gt;&lt;path d=&quot;M8 1a1.993 1.993 0 0 0-1 3.72V6L5 8 3 6V4.72A1.993 1.993 0 0 0 2 1a1.993 1.993 0 0 0-1 3.72V6.5l3 3v1.78A1.993 1.993 0 0 0 5 15a1.993 1.993 0 0 0 1-3.72V9.5l3-3V4.72A1.993 1.993 0 0 0 8 1zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3 10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3-10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
            Fork
          &lt;/a&gt;

          &lt;div id=&quot;fork-destination-box&quot; style=&quot;display: none;&quot;&gt;
            &lt;h2 class=&quot;facebox-header&quot; data-facebox-id=&quot;facebox-header&quot;&gt;Where should we fork this repository?&lt;/h2&gt;
            &lt;include-fragment src=&quot;&quot;
                class=&quot;js-fork-select-fragment fork-select-fragment&quot;
                data-url=&quot;/ccatalina/magnifascent/fork?fragment=1&quot;&gt;
              &lt;img alt=&quot;Loading&quot; height=&quot;64&quot; src=&quot;https://assets-cdn.github.com/images/spinners/octocat-spinner-128.gif&quot; width=&quot;64&quot; /&gt;
            &lt;/include-fragment&gt;
          &lt;/div&gt;

    &lt;a href=&quot;/ccatalina/magnifascent/network&quot; class=&quot;social-count&quot;&gt;
      1
    &lt;/a&gt;
  &lt;/li&gt;
&lt;/ul&gt;

    &lt;h1 class=&quot;public &quot;&gt;
  &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-repo-forked&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 10 16&quot; width=&quot;10&quot;&gt;&lt;path d=&quot;M8 1a1.993 1.993 0 0 0-1 3.72V6L5 8 3 6V4.72A1.993 1.993 0 0 0 2 1a1.993 1.993 0 0 0-1 3.72V6.5l3 3v1.78A1.993 1.993 0 0 0 5 15a1.993 1.993 0 0 0 1-3.72V9.5l3-3V4.72A1.993 1.993 0 0 0 8 1zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3 10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3-10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
  &lt;span class=&quot;author&quot; itemprop=&quot;author&quot;&gt;&lt;a href=&quot;/ccatalina&quot; class=&quot;url fn&quot; rel=&quot;author&quot;&gt;ccatalina&lt;/a&gt;&lt;/span&gt;&lt;!--
--&gt;&lt;span class=&quot;path-divider&quot;&gt;/&lt;/span&gt;&lt;!--
--&gt;&lt;strong itemprop=&quot;name&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent&quot; data-pjax=&quot;#js-repo-pjax-container&quot;&gt;magnifascent&lt;/a&gt;&lt;/strong&gt;

    &lt;span class=&quot;fork-flag&quot;&gt;
      &lt;span class=&quot;text&quot;&gt;forked from &lt;a href=&quot;/robtryson/magnifascent&quot;&gt;robtryson/magnifascent&lt;/a&gt;&lt;/span&gt;
    &lt;/span&gt;
&lt;/h1&gt;

  &lt;/div&gt;
  &lt;div class=&quot;container&quot;&gt;
    
&lt;nav class=&quot;reponav js-repo-nav js-sidenav-container-pjax&quot;
     itemscope
     itemtype=&quot;http://schema.org/BreadcrumbList&quot;
     role=&quot;navigation&quot;
     data-pjax=&quot;#js-repo-pjax-container&quot;&gt;

  &lt;span itemscope itemtype=&quot;http://schema.org/ListItem&quot; itemprop=&quot;itemListElement&quot;&gt;
    &lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina&quot; aria-selected=&quot;true&quot; class=&quot;js-selected-navigation-item selected reponav-item&quot; data-hotkey=&quot;g c&quot; data-selected-links=&quot;repo_source repo_downloads repo_commits repo_releases repo_tags repo_branches /ccatalina/magnifascent/tree/feature-2-chriscatalina&quot; itemprop=&quot;url&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-code&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M9.5 3L8 4.5 11.5 8 8 11.5 9.5 13 14 8 9.5 3zm-5 0L0 8l4.5 5L6 11.5 2.5 8 6 4.5 4.5 3z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;span itemprop=&quot;name&quot;&gt;Code&lt;/span&gt;
      &lt;meta itemprop=&quot;position&quot; content=&quot;1&quot;&gt;
&lt;/a&gt;  &lt;/span&gt;


  &lt;span itemscope itemtype=&quot;http://schema.org/ListItem&quot; itemprop=&quot;itemListElement&quot;&gt;
    &lt;a href=&quot;/ccatalina/magnifascent/pulls&quot; class=&quot;js-selected-navigation-item reponav-item&quot; data-hotkey=&quot;g p&quot; data-selected-links=&quot;repo_pulls /ccatalina/magnifascent/pulls&quot; itemprop=&quot;url&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-git-pull-request&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M11 11.28V5c-.03-.78-.34-1.47-.94-2.06C9.46 2.35 8.78 2.03 8 2H7V0L4 3l3 3V4h1c.27.02.48.11.69.31.21.2.3.42.31.69v6.28A1.993 1.993 0 0 0 10 15a1.993 1.993 0 0 0 1-3.72zm-1 2.92c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zM4 3c0-1.11-.89-2-2-2a1.993 1.993 0 0 0-1 3.72v6.56A1.993 1.993 0 0 0 2 15a1.993 1.993 0 0 0 1-3.72V4.72c.59-.34 1-.98 1-1.72zm-.8 10c0 .66-.55 1.2-1.2 1.2-.65 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;span itemprop=&quot;name&quot;&gt;Pull requests&lt;/span&gt;
      &lt;span class=&quot;counter&quot;&gt;0&lt;/span&gt;
      &lt;meta itemprop=&quot;position&quot; content=&quot;3&quot;&gt;
&lt;/a&gt;  &lt;/span&gt;

    &lt;a href=&quot;/ccatalina/magnifascent/wiki&quot; class=&quot;js-selected-navigation-item reponav-item&quot; data-hotkey=&quot;g w&quot; data-selected-links=&quot;repo_wiki /ccatalina/magnifascent/wiki&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-book&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M3 5h4v1H3V5zm0 3h4V7H3v1zm0 2h4V9H3v1zm11-5h-4v1h4V5zm0 2h-4v1h4V7zm0 2h-4v1h4V9zm2-6v9c0 .55-.45 1-1 1H9.5l-1 1-1-1H2c-.55 0-1-.45-1-1V3c0-.55.45-1 1-1h5.5l1 1 1-1H15c.55 0 1 .45 1 1zm-8 .5L7.5 3H2v9h6V3.5zm7-.5H9.5l-.5.5V12h6V3z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      Wiki
&lt;/a&gt;

  &lt;a href=&quot;/ccatalina/magnifascent/pulse&quot; class=&quot;js-selected-navigation-item reponav-item&quot; data-selected-links=&quot;pulse /ccatalina/magnifascent/pulse&quot;&gt;
    &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-pulse&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M11.5 8L8.8 5.4 6.6 8.5 5.5 1.6 2.38 8H0v2h3.6l.9-1.8.9 5.4L9 8.5l1.6 1.5H14V8z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
    Pulse
&lt;/a&gt;
  &lt;a href=&quot;/ccatalina/magnifascent/graphs&quot; class=&quot;js-selected-navigation-item reponav-item&quot; data-selected-links=&quot;repo_graphs repo_contributors /ccatalina/magnifascent/graphs&quot;&gt;
    &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-graph&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M16 14v1H0V0h1v14h15zM5 13H3V8h2v5zm4 0H7V3h2v10zm4 0h-2V6h2v7z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
    Graphs
&lt;/a&gt;
    &lt;a href=&quot;/ccatalina/magnifascent/settings&quot; class=&quot;js-selected-navigation-item reponav-item&quot; data-selected-links=&quot;repo_settings repo_branch_settings hooks integration_installations /ccatalina/magnifascent/settings&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-gear&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M14 8.77v-1.6l-1.94-.64-.45-1.09.88-1.84-1.13-1.13-1.81.91-1.09-.45-.69-1.92h-1.6l-.63 1.94-1.11.45-1.84-.88-1.13 1.13.91 1.81-.45 1.09L0 7.23v1.59l1.94.64.45 1.09-.88 1.84 1.13 1.13 1.81-.91 1.09.45.69 1.92h1.59l.63-1.94 1.11-.45 1.84.88 1.13-1.13-.92-1.81.47-1.09L14 8.75v.02zM7 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      Settings
&lt;/a&gt;
&lt;/nav&gt;

  &lt;/div&gt;
&lt;/div&gt;

&lt;div class=&quot;container new-discussion-timeline experiment-repo-nav&quot;&gt;
  &lt;div class=&quot;repository-content&quot;&gt;

    

&lt;a href=&quot;/ccatalina/magnifascent/blob/8d2f9246772282e67daa5e6379c7a49c3c45a8b2/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;hidden js-permalink-shortcut&quot; data-hotkey=&quot;y&quot;&gt;Permalink&lt;/a&gt;

&lt;!-- blob contrib key: blob_contributors:v21:e67e7a2deba129663f51201a80e09bcd --&gt;

&lt;div class=&quot;file-navigation js-zeroclipboard-container&quot;&gt;
  
&lt;div class=&quot;select-menu branch-select-menu js-menu-container js-select-menu left&quot;&gt;
  &lt;button class=&quot;btn btn-sm select-menu-button js-menu-target css-truncate&quot; data-hotkey=&quot;w&quot;
    title=&quot;feature-2-chriscatalina&quot;
    type=&quot;button&quot; aria-label=&quot;Switch branches or tags&quot; tabindex=&quot;0&quot; aria-haspopup=&quot;true&quot;&gt;
    &lt;i&gt;Branch:&lt;/i&gt;
    &lt;span class=&quot;js-select-button css-truncate-target&quot;&gt;feature-2-chri…&lt;/span&gt;
  &lt;/button&gt;

  &lt;div class=&quot;select-menu-modal-holder js-menu-content js-navigation-container&quot; data-pjax aria-hidden=&quot;true&quot;&gt;

    &lt;div class=&quot;select-menu-modal&quot;&gt;
      &lt;div class=&quot;select-menu-header&quot;&gt;
        &lt;svg aria-label=&quot;Close&quot; class=&quot;octicon octicon-x js-menu-close&quot; height=&quot;16&quot; role=&quot;img&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
        &lt;span class=&quot;select-menu-title&quot;&gt;Switch branches/tags&lt;/span&gt;
      &lt;/div&gt;

      &lt;div class=&quot;select-menu-filters&quot;&gt;
        &lt;div class=&quot;select-menu-text-filter&quot;&gt;
          &lt;input type=&quot;text&quot; aria-label=&quot;Find or create a branch…&quot; id=&quot;context-commitish-filter-field&quot; class=&quot;form-control js-filterable-field js-navigation-enable&quot; placeholder=&quot;Find or create a branch…&quot;&gt;
        &lt;/div&gt;
        &lt;div class=&quot;select-menu-tabs&quot;&gt;
          &lt;ul&gt;
            &lt;li class=&quot;select-menu-tab&quot;&gt;
              &lt;a href=&quot;#&quot; data-tab-filter=&quot;branches&quot; data-filter-placeholder=&quot;Find or create a branch…&quot; class=&quot;js-select-menu-tab&quot; role=&quot;tab&quot;&gt;Branches&lt;/a&gt;
            &lt;/li&gt;
            &lt;li class=&quot;select-menu-tab&quot;&gt;
              &lt;a href=&quot;#&quot; data-tab-filter=&quot;tags&quot; data-filter-placeholder=&quot;Find a tag…&quot; class=&quot;js-select-menu-tab&quot; role=&quot;tab&quot;&gt;Tags&lt;/a&gt;
            &lt;/li&gt;
          &lt;/ul&gt;
        &lt;/div&gt;
      &lt;/div&gt;

      &lt;div class=&quot;select-menu-list select-menu-tab-bucket js-select-menu-tab-bucket&quot; data-tab-filter=&quot;branches&quot; role=&quot;menu&quot;&gt;

        &lt;div data-filterable-for=&quot;context-commitish-filter-field&quot; data-filterable-type=&quot;substring&quot;&gt;


            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/dev/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;dev&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                dev
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/feature-1-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;feature-1-chriscatalina&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                feature-1-chriscatalina
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open selected&quot;
               href=&quot;/ccatalina/magnifascent/blob/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;feature-2-chriscatalina&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                feature-2-chriscatalina
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/feature-3-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;feature-3-chriscatalina&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                feature-3-chriscatalina
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/master/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;master&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                master
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/prod/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;prod&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                prod
              &lt;/span&gt;
            &lt;/a&gt;
            &lt;a class=&quot;select-menu-item js-navigation-item js-navigation-open &quot;
               href=&quot;/ccatalina/magnifascent/blob/stage/docroot/themes/bartik/templates/node--2.tpl.php&quot;
               data-name=&quot;stage&quot;
               data-skip-pjax=&quot;true&quot;
               rel=&quot;nofollow&quot;&gt;
              &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-check select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
              &lt;span class=&quot;select-menu-item-text css-truncate-target js-select-menu-filter-text&quot;&gt;
                stage
              &lt;/span&gt;
            &lt;/a&gt;
        &lt;/div&gt;

          &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/branches&quot; class=&quot;js-create-branch select-menu-item select-menu-new-item-form js-navigation-item js-new-item-form&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;NNsk+3JhQduV/ud0GPtHCMYRqwEuLjuiBfla/ph19p1sy2mlYQA/pFDGUcmZZ2FOLf41hbnVBn4vE6Wg/opjOQ==&quot; /&gt;&lt;/div&gt;
          &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-git-branch select-menu-item-icon&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 10 16&quot; width=&quot;10&quot;&gt;&lt;path d=&quot;M10 5c0-1.11-.89-2-2-2a1.993 1.993 0 0 0-1 3.72v.3c-.02.52-.23.98-.63 1.38-.4.4-.86.61-1.38.63-.83.02-1.48.16-2 .45V4.72a1.993 1.993 0 0 0-1-3.72C.88 1 0 1.89 0 3a2 2 0 0 0 1 1.72v6.56c-.59.35-1 .99-1 1.72 0 1.11.89 2 2 2 1.11 0 2-.89 2-2 0-.53-.2-1-.53-1.36.09-.06.48-.41.59-.47.25-.11.56-.17.94-.17 1.05-.05 1.95-.45 2.75-1.25S8.95 7.77 9 6.73h-.02C9.59 6.37 10 5.73 10 5zM2 1.8c.66 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2C1.35 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2zm0 12.41c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm6-8c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
            &lt;div class=&quot;select-menu-item-text&quot;&gt;
              &lt;span class=&quot;select-menu-item-heading&quot;&gt;Create branch: &lt;span class=&quot;js-new-item-name&quot;&gt;&lt;/span&gt;&lt;/span&gt;
              &lt;span class=&quot;description&quot;&gt;from ‘feature-2-chriscatalina’&lt;/span&gt;
            &lt;/div&gt;
            &lt;input type=&quot;hidden&quot; name=&quot;name&quot; id=&quot;name&quot; class=&quot;js-new-item-value&quot;&gt;
            &lt;input type=&quot;hidden&quot; name=&quot;branch&quot; id=&quot;branch&quot; value=&quot;feature-2-chriscatalina&quot;&gt;
            &lt;input type=&quot;hidden&quot; name=&quot;path&quot; id=&quot;path&quot; value=&quot;docroot/themes/bartik/templates/node--2.tpl.php&quot;&gt;
&lt;/form&gt;
      &lt;/div&gt;

      &lt;div class=&quot;select-menu-list select-menu-tab-bucket js-select-menu-tab-bucket&quot; data-tab-filter=&quot;tags&quot;&gt;
        &lt;div data-filterable-for=&quot;context-commitish-filter-field&quot; data-filterable-type=&quot;substring&quot;&gt;


        &lt;/div&gt;

        &lt;div class=&quot;select-menu-no-results&quot;&gt;Nothing to show&lt;/div&gt;
      &lt;/div&gt;

    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;

  &lt;div class=&quot;btn-group right&quot;&gt;
    &lt;a href=&quot;/ccatalina/magnifascent/find/feature-2-chriscatalina&quot;
          class=&quot;js-pjax-capture-input btn btn-sm&quot;
          data-pjax
          data-hotkey=&quot;t&quot;&gt;
      Find file
    &lt;/a&gt;
    &lt;button aria-label=&quot;Copy file path to clipboard&quot; class=&quot;js-zeroclipboard btn btn-sm zeroclipboard-button tooltipped tooltipped-s&quot; data-copied-hint=&quot;Copied!&quot; type=&quot;button&quot;&gt;Copy path&lt;/button&gt;
  &lt;/div&gt;
  &lt;div class=&quot;breadcrumb js-zeroclipboard-target&quot;&gt;
    &lt;span class=&quot;repo-root js-repo-root&quot;&gt;&lt;span class=&quot;js-path-segment&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina&quot;&gt;&lt;span&gt;magnifascent&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;separator&quot;&gt;/&lt;/span&gt;&lt;span class=&quot;js-path-segment&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina/docroot&quot;&gt;&lt;span&gt;docroot&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;span class=&quot;separator&quot;&gt;/&lt;/span&gt;&lt;span class=&quot;js-path-segment&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina/docroot/themes&quot;&gt;&lt;span&gt;themes&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;span class=&quot;separator&quot;&gt;/&lt;/span&gt;&lt;span class=&quot;js-path-segment&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina/docroot/themes/bartik&quot;&gt;&lt;span&gt;bartik&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;span class=&quot;separator&quot;&gt;/&lt;/span&gt;&lt;span class=&quot;js-path-segment&quot;&gt;&lt;a href=&quot;/ccatalina/magnifascent/tree/feature-2-chriscatalina/docroot/themes/bartik/templates&quot;&gt;&lt;span&gt;templates&lt;/span&gt;&lt;/a&gt;&lt;/span&gt;&lt;span class=&quot;separator&quot;&gt;/&lt;/span&gt;&lt;strong class=&quot;final-path&quot;&gt;node--2.tpl.php&lt;/strong&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;include-fragment class=&quot;commit-tease&quot; src=&quot;/ccatalina/magnifascent/contributors/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot;&gt;
  &lt;div&gt;
    Fetching contributors&amp;hellip;
  &lt;/div&gt;

  &lt;div class=&quot;commit-tease-contributors&quot;&gt;
    &lt;img alt=&quot;&quot; class=&quot;loader-loading left&quot; height=&quot;16&quot; src=&quot;https://assets-cdn.github.com/images/spinners/octocat-spinner-32-EAF2F5.gif&quot; width=&quot;16&quot; /&gt;
    &lt;span class=&quot;loader-error&quot;&gt;Cannot retrieve contributors at this time&lt;/span&gt;
  &lt;/div&gt;
&lt;/include-fragment&gt;
&lt;div class=&quot;file&quot;&gt;
  &lt;div class=&quot;file-header&quot;&gt;
  &lt;div class=&quot;file-actions&quot;&gt;

    &lt;div class=&quot;btn-group&quot;&gt;
      &lt;a href=&quot;/ccatalina/magnifascent/raw/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;btn btn-sm &quot; id=&quot;raw-url&quot;&gt;Raw&lt;/a&gt;
        &lt;a href=&quot;/ccatalina/magnifascent/blame/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;btn btn-sm js-update-url-with-hash&quot;&gt;Blame&lt;/a&gt;
      &lt;a href=&quot;/ccatalina/magnifascent/commits/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;btn btn-sm &quot; rel=&quot;nofollow&quot;&gt;History&lt;/a&gt;
    &lt;/div&gt;

        &lt;a class=&quot;btn-octicon tooltipped tooltipped-nw&quot;
           href=&quot;github-mac://openRepo/https://github.com/ccatalina/magnifascent?branch=feature-2-chriscatalina&amp;amp;filepath=docroot%2Fthemes%2Fbartik%2Ftemplates%2Fnode--2.tpl.php&quot;
           aria-label=&quot;Open this file in GitHub Desktop&quot;
           data-ga-click=&quot;Repository, open with desktop, type:mac&quot;&gt;
            &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-device-desktop&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M15 2H1c-.55 0-1 .45-1 1v9c0 .55.45 1 1 1h5.34c-.25.61-.86 1.39-2.34 2h8c-1.48-.61-2.09-1.39-2.34-2H15c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm0 9H1V3h14v8z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
        &lt;/a&gt;

        &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/edit/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;inline-form js-update-url-with-hash&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;Fbeb0sFp505d/gSLo0Aiy7tlePdPMd6+rVgmIUtOdg3fWtytsHSIZm21Oh/S9Z8H6vG1Wfhy/Yhnw2vpL+G2lw==&quot; /&gt;&lt;/div&gt;
          &lt;button class=&quot;btn-octicon tooltipped tooltipped-nw&quot; type=&quot;submit&quot;
            aria-label=&quot;Edit this file&quot; data-hotkey=&quot;e&quot; data-disable-with&gt;
            &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-pencil&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 14 16&quot; width=&quot;14&quot;&gt;&lt;path d=&quot;M0 12v3h3l8-8-3-3-8 8zm3 2H1v-2h1v1h1v1zm10.3-9.3L12 6 9 3l1.3-1.3a.996.996 0 0 1 1.41 0l1.59 1.59c.39.39.39 1.02 0 1.41z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
          &lt;/button&gt;
&lt;/form&gt;        &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;/ccatalina/magnifascent/delete/feature-2-chriscatalina/docroot/themes/bartik/templates/node--2.tpl.php&quot; class=&quot;inline-form&quot; data-form-nonce=&quot;f1126aeeab2bf2e3452558dcd005baed3e70940a&quot; method=&quot;post&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;input name=&quot;authenticity_token&quot; type=&quot;hidden&quot; value=&quot;vLTXyTdDhp1vCXOVT8xN3l/6C4aINK1ZIx+9D2AR6zWnCcN/f9ImMlF1mB0ffWY5vvYPaovx3UDfqkWpUawQ2g==&quot; /&gt;&lt;/div&gt;
          &lt;button class=&quot;btn-octicon btn-octicon-danger tooltipped tooltipped-nw&quot; type=&quot;submit&quot;
            aria-label=&quot;Delete this file&quot; data-disable-with&gt;
            &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-trashcan&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
          &lt;/button&gt;
&lt;/form&gt;  &lt;/div&gt;

  &lt;div class=&quot;file-info&quot;&gt;
      127 lines (120 sloc)
      &lt;span class=&quot;file-info-divider&quot;&gt;&lt;/span&gt;
    5.39 KB
  &lt;/div&gt;
&lt;/div&gt;

  

  &lt;div itemprop=&quot;text&quot; class=&quot;blob-wrapper data type-php&quot;&gt;
      &lt;table class=&quot;highlight tab-size js-file-line-container&quot; data-tab-size=&quot;2&quot;&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L1&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;1&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC1&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L2&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;2&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC2&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L3&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;3&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC3&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt;/**&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L4&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;4&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC4&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * @file&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L5&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;5&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC5&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * Bartik&amp;#39;s theme implementation to display a node.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L6&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;6&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC6&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L7&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;7&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC7&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * Available variables:&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L8&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;8&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC8&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $title: the (sanitized) title of the node.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L9&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;9&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC9&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $content: An array of node items. Use render($content) to print them all,&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L10&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;10&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC10&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   or print a subset such as render($content[&amp;#39;field_example&amp;#39;]). Use&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L11&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;11&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC11&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   hide($content[&amp;#39;field_example&amp;#39;]) to temporarily suppress the printing of a&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L12&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;12&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC12&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   given element.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L13&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;13&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC13&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $user_picture: The node author&amp;#39;s picture from user-picture.tpl.php.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L14&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;14&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC14&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $date: Formatted creation date. Preprocess functions can reformat it by&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L15&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;15&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC15&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   calling format_date() with the desired parameters on the $created variable.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L16&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;16&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC16&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $name: Themed username of node author output from theme_username().&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L17&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;17&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC17&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $node_url: Direct URL of the current node.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L18&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;18&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC18&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $display_submitted: Whether submission information should be displayed.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L19&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;19&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC19&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $submitted: Submission information created from $name and $date during&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L20&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;20&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC20&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   template_preprocess_node().&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L21&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;21&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC21&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $classes: String of classes that can be used to style contextually through&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L22&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;22&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC22&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   CSS. It can be manipulated through the variable $classes_array from&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L23&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;23&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC23&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   preprocess functions. The default values can be one or more of the&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L24&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;24&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC24&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   following:&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L25&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;25&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC25&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node: The current template type; for example, &amp;quot;theming hook&amp;quot;.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L26&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;26&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC26&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-[type]: The current node type. For example, if the node is a&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L27&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;27&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC27&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *     &amp;quot;Blog entry&amp;quot; it would result in &amp;quot;node-blog&amp;quot;. Note that the machine&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L28&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;28&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC28&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *     name will often be in a short form of the human readable label.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L29&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;29&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC29&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-teaser: Nodes in teaser form.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L30&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;30&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC30&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-preview: Nodes in preview mode.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L31&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;31&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC31&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   The following are controlled through the node publishing options.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L32&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;32&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC32&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-promoted: Nodes promoted to the front page.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L33&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;33&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC33&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L34&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;34&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC34&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *     listings.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L35&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;35&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC35&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   - node-unpublished: Unpublished nodes visible only to administrators.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L36&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;36&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC36&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $title_prefix (array): An array containing additional output populated by&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L37&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;37&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC37&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   modules, intended to be displayed in front of the main title tag that&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L38&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;38&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC38&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   appears in the template.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L39&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;39&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC39&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $title_suffix (array): An array containing additional output populated by&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L40&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;40&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC40&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   modules, intended to be displayed after the main title tag that appears in&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L41&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;41&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC41&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   the template.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L42&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;42&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC42&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L43&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;43&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC43&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * Other variables:&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L44&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;44&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC44&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $node: Full node object. Contains data that may not be safe.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L45&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;45&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC45&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $type: Node type; for example, story, page, blog, etc.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L46&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;46&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC46&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $comment_count: Number of comments attached to the node.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L47&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;47&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC47&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $uid: User ID of the node author.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L48&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;48&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC48&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $created: Time the node was published formatted in Unix timestamp.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L49&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;49&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC49&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $classes_array: Array of html class attribute values. It is flattened&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L50&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;50&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC50&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   into a string within the variable $classes.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L51&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;51&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC51&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $zebra: Outputs either &amp;quot;even&amp;quot; or &amp;quot;odd&amp;quot;. Useful for zebra striping in&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L52&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;52&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC52&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   teaser listings.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L53&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;53&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC53&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $id: Position of the node. Increments each time it&amp;#39;s output.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L54&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;54&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC54&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L55&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;55&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC55&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * Node status variables:&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L56&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;56&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC56&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $view_mode: View mode; for example, &amp;quot;full&amp;quot;, &amp;quot;teaser&amp;quot;.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L57&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;57&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC57&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $teaser: Flag for the teaser state (shortcut for $view_mode == &amp;#39;teaser&amp;#39;).&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L58&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;58&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC58&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $page: Flag for the full page state.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L59&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;59&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC59&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $promote: Flag for front page promotion state.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L60&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;60&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC60&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $sticky: Flags for sticky post setting.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L61&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;61&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC61&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $status: Flag for published status.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L62&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;62&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC62&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $comment: State of comment settings for the node.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L63&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;63&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC63&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $readmore: Flags true if the teaser content of the node cannot hold the&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L64&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;64&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC64&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *   main body content.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L65&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;65&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC65&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $is_front: Flags true when presented in the front page.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L66&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;66&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC66&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $logged_in: Flags true when the current user is a logged-in member.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L67&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;67&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC67&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * - $is_admin: Flags true when the current user is an administrator.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L68&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;68&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC68&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L69&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;69&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC69&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * Field variables: for each field instance attached to the node a corresponding&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L70&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;70&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC70&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * variable is defined; for example, $node-&amp;gt;body becomes $body. When needing to&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L71&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;71&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC71&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * access a field&amp;#39;s raw values, developers/themers are strongly encouraged to&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L72&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;72&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC72&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * use these variables. Otherwise they will have to explicitly specify the&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L73&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;73&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC73&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * desired field language; for example, $node-&amp;gt;body[&amp;#39;en&amp;#39;], thus overriding any&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L74&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;74&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC74&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * language negotiation rule that was previously applied.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L75&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;75&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC75&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; *&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L76&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;76&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC76&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * &lt;span class=&quot;pl-k&quot;&gt;@see&lt;/span&gt; template_preprocess()&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L77&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;77&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC77&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * &lt;span class=&quot;pl-k&quot;&gt;@see&lt;/span&gt; template_preprocess_node()&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L78&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;78&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC78&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; * &lt;span class=&quot;pl-k&quot;&gt;@see&lt;/span&gt; template_process()&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L79&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;79&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC79&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;span class=&quot;pl-c&quot;&gt; */&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L80&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;80&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC80&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L81&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;81&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC81&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;id&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;node-&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$node&lt;/span&gt;&lt;span class=&quot;pl-k&quot;&gt;-&amp;gt;&lt;/span&gt;&lt;span class=&quot;pl-smi&quot;&gt;nid&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;class&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$classes&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt; clearfix&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$attributes&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L82&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;82&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC82&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;class&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;you-did-it&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L83&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;83&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC83&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;img&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;src&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;/themes/bartik/images/example2-youdidit.gif&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;alt&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;You Did it!&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L84&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;84&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC84&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L85&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;85&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC85&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; render(&lt;span class=&quot;pl-smi&quot;&gt;$title_prefix&lt;/span&gt;); &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L86&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;86&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC86&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-k&quot;&gt;if&lt;/span&gt; (&lt;span class=&quot;pl-k&quot;&gt;!&lt;/span&gt;&lt;span class=&quot;pl-smi&quot;&gt;$page&lt;/span&gt;): &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L87&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;87&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC87&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;h2&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$title_attributes&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L88&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;88&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC88&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;      &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;a&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;href&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$node_url&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&amp;gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$title&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;a&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L89&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;89&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC89&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;h2&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L90&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;90&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC90&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-k&quot;&gt;endif&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L91&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;91&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC91&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; render(&lt;span class=&quot;pl-smi&quot;&gt;$title_suffix&lt;/span&gt;); &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L92&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;92&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC92&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;
&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L93&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;93&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC93&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-k&quot;&gt;if&lt;/span&gt; (&lt;span class=&quot;pl-smi&quot;&gt;$display_submitted&lt;/span&gt;): &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L94&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;94&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC94&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;class&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;meta submitted&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L95&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;95&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC95&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;      &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$user_picture&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L96&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;96&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC96&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;      &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$submitted&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L97&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;97&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC97&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L98&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;98&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC98&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-k&quot;&gt;endif&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L99&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;99&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC99&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;
&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L100&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;100&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC100&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;class&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;content clearfix&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$content_attributes&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L101&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;101&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC101&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L102&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;102&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC102&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;      &lt;span class=&quot;pl-c&quot;&gt;// We hide the comments and links now so that we can render them later.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L103&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;103&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC103&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;      hide(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comments&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;]);&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L104&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;104&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC104&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;      hide(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;links&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;]);&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L105&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;105&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC105&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;      &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; render(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;);&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L106&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;106&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC106&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L107&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;107&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC107&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L108&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;108&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC108&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;
&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L109&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;109&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC109&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L110&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;110&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC110&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-c&quot;&gt;// Remove the &amp;quot;Add new comment&amp;quot; link on the teaser page or if the comment&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L111&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;111&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC111&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-c&quot;&gt;// form is being displayed on the same page.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L112&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;112&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC112&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-k&quot;&gt;if&lt;/span&gt; (&lt;span class=&quot;pl-smi&quot;&gt;$teaser&lt;/span&gt; &lt;span class=&quot;pl-k&quot;&gt;||&lt;/span&gt; &lt;span class=&quot;pl-k&quot;&gt;!&lt;/span&gt;&lt;span class=&quot;pl-c1&quot;&gt;empty&lt;/span&gt;(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comments&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;][&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comment_form&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;])) {&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L113&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;113&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC113&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;      &lt;span class=&quot;pl-c1&quot;&gt;unset&lt;/span&gt;(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;links&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;][&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comment&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;][&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;#links&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;][&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comment-add&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;]);&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L114&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;114&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC114&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    }&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L115&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;115&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC115&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-c&quot;&gt;// Only display the wrapper div if there are links.&lt;/span&gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L116&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;116&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC116&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-smi&quot;&gt;$links&lt;/span&gt; &lt;span class=&quot;pl-k&quot;&gt;=&lt;/span&gt; render(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;links&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;]);&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L117&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;117&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC117&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;    &lt;span class=&quot;pl-k&quot;&gt;if&lt;/span&gt; (&lt;span class=&quot;pl-smi&quot;&gt;$links&lt;/span&gt;):&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L118&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;118&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC118&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;  &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L119&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;119&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC119&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt; &lt;span class=&quot;pl-e&quot;&gt;class&lt;/span&gt;=&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;link-wrapper&lt;span class=&quot;pl-pds&quot;&gt;&amp;quot;&lt;/span&gt;&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L120&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;120&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC120&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;      &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; &lt;span class=&quot;pl-smi&quot;&gt;$links&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L121&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;121&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC121&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;    &amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L122&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;122&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC122&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-k&quot;&gt;endif&lt;/span&gt;; &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L123&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;123&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC123&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;
&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L124&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;124&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC124&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;  &lt;span class=&quot;pl-pse&quot;&gt;&amp;lt;?php&lt;/span&gt;&lt;span class=&quot;pl-s1&quot;&gt; &lt;span class=&quot;pl-c1&quot;&gt;print&lt;/span&gt; render(&lt;span class=&quot;pl-smi&quot;&gt;$content&lt;/span&gt;[&lt;span class=&quot;pl-s&quot;&gt;&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;comments&lt;span class=&quot;pl-pds&quot;&gt;&amp;#39;&lt;/span&gt;&lt;/span&gt;]); &lt;/span&gt;&lt;span class=&quot;pl-pse&quot;&gt;&lt;span class=&quot;pl-s1&quot;&gt;?&lt;/span&gt;&amp;gt;&lt;/span&gt;&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L125&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;125&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC125&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;
&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td id=&quot;L126&quot; class=&quot;blob-num js-line-number&quot; data-line-number=&quot;126&quot;&gt;&lt;/td&gt;
        &lt;td id=&quot;LC126&quot; class=&quot;blob-code blob-code-inner js-file-line&quot;&gt;&amp;lt;/&lt;span class=&quot;pl-ent&quot;&gt;div&lt;/span&gt;&amp;gt;&lt;/td&gt;
      &lt;/tr&gt;
&lt;/table&gt;

  &lt;/div&gt;

&lt;/div&gt;

&lt;button type=&quot;button&quot; data-facebox=&quot;#jump-to-line&quot; data-facebox-class=&quot;linejump&quot; data-hotkey=&quot;l&quot; class=&quot;hidden&quot;&gt;Jump to Line&lt;/button&gt;
&lt;div id=&quot;jump-to-line&quot; style=&quot;display:none&quot;&gt;
  &lt;!-- &lt;/textarea&gt; --&gt;&lt;!-- &#039;&quot;` --&gt;&lt;form accept-charset=&quot;UTF-8&quot; action=&quot;&quot; class=&quot;js-jump-to-line-form&quot; method=&quot;get&quot;&gt;&lt;div style=&quot;margin:0;padding:0;display:inline&quot;&gt;&lt;input name=&quot;utf8&quot; type=&quot;hidden&quot; value=&quot;&amp;#x2713;&quot; /&gt;&lt;/div&gt;
    &lt;input class=&quot;form-control linejump-input js-jump-to-line-field&quot; type=&quot;text&quot; placeholder=&quot;Jump to line&amp;hellip;&quot; aria-label=&quot;Jump to line&quot; autofocus&gt;
    &lt;button type=&quot;submit&quot; class=&quot;btn&quot;&gt;Go&lt;/button&gt;
&lt;/form&gt;&lt;/div&gt;

  &lt;/div&gt;
  &lt;div class=&quot;modal-backdrop js-touch-events&quot;&gt;&lt;/div&gt;
&lt;/div&gt;


    &lt;/div&gt;
  &lt;/div&gt;

    &lt;/div&gt;

        &lt;div class=&quot;container site-footer-container&quot;&gt;
  &lt;div class=&quot;site-footer&quot; role=&quot;contentinfo&quot;&gt;
    &lt;ul class=&quot;site-footer-links right&quot;&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/contact&quot; data-ga-click=&quot;Footer, go to contact, text:contact&quot;&gt;Contact GitHub&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;https://developer.github.com&quot; data-ga-click=&quot;Footer, go to api, text:api&quot;&gt;API&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;https://training.github.com&quot; data-ga-click=&quot;Footer, go to training, text:training&quot;&gt;Training&lt;/a&gt;&lt;/li&gt;
      &lt;li&gt;&lt;a href=&quot;https://shop.github.com&quot; data-ga-click=&quot;Footer, go to shop, text:shop&quot;&gt;Shop&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/blog&quot; data-ga-click=&quot;Footer, go to blog, text:blog&quot;&gt;Blog&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/about&quot; data-ga-click=&quot;Footer, go to about, text:about&quot;&gt;About&lt;/a&gt;&lt;/li&gt;

    &lt;/ul&gt;

    &lt;a href=&quot;https://github.com&quot; aria-label=&quot;Homepage&quot; class=&quot;site-footer-mark&quot; title=&quot;GitHub&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-mark-github&quot; height=&quot;24&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;24&quot;&gt;&lt;path d=&quot;M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
&lt;/a&gt;
    &lt;ul class=&quot;site-footer-links&quot;&gt;
      &lt;li&gt;&amp;copy; 2016 &lt;span title=&quot;0.09810s from github-fe139-cp1-prd.iad.github.net&quot;&gt;GitHub&lt;/span&gt;, Inc.&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/site/terms&quot; data-ga-click=&quot;Footer, go to terms, text:terms&quot;&gt;Terms&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/site/privacy&quot; data-ga-click=&quot;Footer, go to privacy, text:privacy&quot;&gt;Privacy&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://github.com/security&quot; data-ga-click=&quot;Footer, go to security, text:security&quot;&gt;Security&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://status.github.com/&quot; data-ga-click=&quot;Footer, go to status, text:status&quot;&gt;Status&lt;/a&gt;&lt;/li&gt;
        &lt;li&gt;&lt;a href=&quot;https://help.github.com&quot; data-ga-click=&quot;Footer, go to help, text:help&quot;&gt;Help&lt;/a&gt;&lt;/li&gt;
    &lt;/ul&gt;
  &lt;/div&gt;
&lt;/div&gt;



    

    &lt;div id=&quot;ajax-error-message&quot; class=&quot;ajax-error-message flash flash-error&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-alert&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8.865 1.52c-.18-.31-.51-.5-.87-.5s-.69.19-.87.5L.275 13.5c-.18.31-.18.69 0 1 .19.31.52.5.87.5h13.7c.36 0 .69-.19.86-.5.17-.31.18-.69.01-1L8.865 1.52zM8.995 13h-2v-2h2v2zm0-3h-2V6h2v4z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;button type=&quot;button&quot; class=&quot;flash-close js-flash-close js-ajax-error-dismiss&quot; aria-label=&quot;Dismiss error&quot;&gt;
        &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-x&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;/button&gt;
      You can&#039;t perform that action at this time.
    &lt;/div&gt;


      
      &lt;script crossorigin=&quot;anonymous&quot; integrity=&quot;sha256-Rkv1q3C5B/TnC7cbUiAzxNDXqb2jKTXtiv4nrnwZBe0=&quot; src=&quot;https://assets-cdn.github.com/assets/frameworks-464bf5ab70b907f4e70bb71b522033c4d0d7a9bda32935ed8afe27ae7c1905ed.js&quot;&gt;&lt;/script&gt;
      &lt;script async=&quot;async&quot; crossorigin=&quot;anonymous&quot; integrity=&quot;sha256-zuDb+tYOtun8rxsk90HGvOeXR0FKRcaGBZeKAjnubPo=&quot; src=&quot;https://assets-cdn.github.com/assets/github-cee0dbfad60eb6e9fcaf1b24f741c6bce79747414a45c68605978a0239ee6cfa.js&quot;&gt;&lt;/script&gt;
      
      
      
      
      
      
    &lt;div class=&quot;js-stale-session-flash stale-session-flash flash flash-warn flash-banner hidden&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-alert&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 16 16&quot; width=&quot;16&quot;&gt;&lt;path d=&quot;M8.865 1.52c-.18-.31-.51-.5-.87-.5s-.69.19-.87.5L.275 13.5c-.18.31-.18.69 0 1 .19.31.52.5.87.5h13.7c.36 0 .69-.19.86-.5.17-.31.18-.69.01-1L8.865 1.52zM8.995 13h-2v-2h2v2zm0-3h-2V6h2v4z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
      &lt;span class=&quot;signed-in-tab-flash&quot;&gt;You signed in with another tab or window. &lt;a href=&quot;&quot;&gt;Reload&lt;/a&gt; to refresh your session.&lt;/span&gt;
      &lt;span class=&quot;signed-out-tab-flash&quot;&gt;You signed out in another tab or window. &lt;a href=&quot;&quot;&gt;Reload&lt;/a&gt; to refresh your session.&lt;/span&gt;
    &lt;/div&gt;
    &lt;div class=&quot;facebox&quot; id=&quot;facebox&quot; style=&quot;display:none;&quot;&gt;
  &lt;div class=&quot;facebox-popup&quot;&gt;
    &lt;div class=&quot;facebox-content&quot; role=&quot;dialog&quot; aria-labelledby=&quot;facebox-header&quot; aria-describedby=&quot;facebox-description&quot;&gt;
    &lt;/div&gt;
    &lt;button type=&quot;button&quot; class=&quot;facebox-close js-facebox-close&quot; aria-label=&quot;Close modal&quot;&gt;
      &lt;svg aria-hidden=&quot;true&quot; class=&quot;octicon octicon-x&quot; height=&quot;16&quot; version=&quot;1.1&quot; viewBox=&quot;0 0 12 16&quot; width=&quot;12&quot;&gt;&lt;path d=&quot;M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48z&quot;&gt;&lt;/path&gt;&lt;/svg&gt;
    &lt;/button&gt;
  &lt;/div&gt;
&lt;/div&gt;

  &lt;/body&gt;
&lt;/html&gt;

</pre>

		<p class="file_page_meta no_print" style="line-height: 1.5rem;">
			<label class="checkbox normal mini float_right no_top_padding no_min_width">
				<input type="checkbox" id="file_preview_wrap_cb"> wrap long lines
			</label>
		</p>

	</div>

	<div id="comments_holder" class="clearfix clear_both">
	<div class="col span_1_of_6"></div>
	<div class="col span_4_of_6 no_right_padding">
		<div id="file_page_comments">
					</div>	
		<form action="https://webny.slack.com/files/ccatalina/F206XQU66/node--2.tpl.php"
		id="file_comment_form"
					class="comment_form"
				method="post">
			<a href="/team/danielmardon" class="member_preview_link" data-member-id="U0TMU5MMZ" >
			<span class="member_image thumb_36" style="background-image: url('https://secure.gravatar.com/avatar/6f9da29ea9b69b0520fd5cb5607c90c6.jpg?s=72&d=https%3A%2F%2Fa.slack-edge.com%2F66f9%2Fimg%2Favatars%2Fava_0020-72.png')" data-thumb-size="36" data-member-id="U0TMU5MMZ"></span>
		</a>
		<input type="hidden" name="addcomment" value="1" />
	<input type="hidden" name="crumb" value="s-1470856820-7d8118818b-☃" />

	<textarea id="file_comment" data-el-id-to-keep-in-view="file_comment_submit_btn" class="small comment_input small_bottom_margin autogrow-short" name="comment" wrap="virtual" ></textarea>
	<span class="input_note float_left cloud_silver file_comment_tip">shift+enter to add a new line</span>	<button id="file_comment_submit_btn" type="submit" class="btn float_right  ladda-button" data-style="expand-right"><span class="ladda-label">Add Comment</span></button>
</form>

<form
		id="file_edit_comment_form"
					class="edit_comment_form hidden"
				method="post">
		<textarea id="file_edit_comment" class="small comment_input small_bottom_margin" name="comment" wrap="virtual"></textarea><br>
	<span class="input_note float_left cloud_silver file_comment_tip">shift+enter to add a new line</span>	<input type="submit" class="save btn float_right " value="Save" />
	<button class="cancel btn btn_outline float_right small_right_margin ">Cancel</button>
</form>	
	</div>
	<div class="col span_1_of_6"></div>
</div>
</div>



		
	</div>
	<div id="overlay"></div>
</div>





<script type="text/javascript">
var cdn_url = "https:\/\/slack.global.ssl.fastly.net";
var inc_js_setup_data = {
	emoji_sheets: {
		apple: 'https://a.slack-edge.com/f360/img/emoji_2016_06_08/sheet_apple_64_indexed_256colors.png',
		google: 'https://a.slack-edge.com/f360/img/emoji_2016_06_08/sheet_google_64_indexed_128colors.png',
		twitter: 'https://a.slack-edge.com/f360/img/emoji_2016_06_08/sheet_twitter_64_indexed_128colors.png',
		emojione: 'https://a.slack-edge.com/f360/img/emoji_2016_06_08/sheet_emojione_64_indexed_128colors.png',
	},
};
</script>
			<script type="text/javascript">
<!--
	// common boot_data
	var boot_data = {
		start_ms: Date.now(),
		app: 'web',
		user_id: 'U0TMU5MMZ',
		no_login: false,
		version_ts: '1470855025',
		version_uid: '0936cf9af28321a66cbb6be1bac76d58e22de90e',
		cache_version: "v13-tiger",
		cache_ts_version: "v1-cat",
		redir_domain: 'slack-redir.net',
		signin_url: 'https://slack.com/signin',
		abs_root_url: 'https://slack.com/',
		api_url: '/api/',
		team_url: 'https://webny.slack.com/',
		image_proxy_url: 'https://slack-imgs.com/',
		beacon_timing_url: "https:\/\/slack.com\/beacon\/timing",
		beacon_error_url: "https:\/\/slack.com\/beacon\/error",
		clog_url: "clog\/track\/",
		api_token: 'xoxs-15068431972-27742191747-59062326180-bc8dca5356',
		ls_disabled: false,

		notification_sounds: [{"value":"b2.mp3","label":"Ding","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/b2.mp3"},{"value":"animal_stick.mp3","label":"Boing","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/animal_stick.mp3"},{"value":"been_tree.mp3","label":"Drop","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/been_tree.mp3"},{"value":"complete_quest_requirement.mp3","label":"Ta-da","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/complete_quest_requirement.mp3"},{"value":"confirm_delivery.mp3","label":"Plink","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/confirm_delivery.mp3"},{"value":"flitterbug.mp3","label":"Wow","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/flitterbug.mp3"},{"value":"here_you_go_lighter.mp3","label":"Here you go","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/here_you_go_lighter.mp3"},{"value":"hi_flowers_hit.mp3","label":"Hi","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/hi_flowers_hit.mp3"},{"value":"item_pickup.mp3","label":"Yoink","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/item_pickup.mp3"},{"value":"knock_brush.mp3","label":"Knock Brush","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/knock_brush.mp3"},{"value":"save_and_checkout.mp3","label":"Woah!","url":"https:\/\/slack.global.ssl.fastly.net\/dfc0\/sounds\/push\/save_and_checkout.mp3"},{"value":"none","label":"None"}],
		alert_sounds: [{"value":"frog.mp3","label":"Frog","url":"https:\/\/slack.global.ssl.fastly.net\/a34a\/sounds\/frog.mp3"}],
		call_sounds: [{"value":"call\/alert_v2.mp3","label":"Alert","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/alert_v2.mp3"},{"value":"call\/incoming_ring_v2.mp3","label":"Incoming ring","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/incoming_ring_v2.mp3"},{"value":"call\/outgoing_ring_v2.mp3","label":"Outgoing ring","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/outgoing_ring_v2.mp3"},{"value":"call\/pop_v2.mp3","label":"Incoming reaction","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/pop_v2.mp3"},{"value":"call\/they_left_call_v2.mp3","label":"They left call","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/they_left_call_v2.mp3"},{"value":"call\/you_left_call_v2.mp3","label":"You left call","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/you_left_call_v2.mp3"},{"value":"call\/they_joined_call_v2.mp3","label":"They joined call","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/they_joined_call_v2.mp3"},{"value":"call\/you_joined_call_v2.mp3","label":"You joined call","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/you_joined_call_v2.mp3"},{"value":"call\/confirmation_v2.mp3","label":"Confirmation","url":"https:\/\/slack.global.ssl.fastly.net\/08f7\/sounds\/call\/confirmation_v2.mp3"}],
		call_sounds_version: "v2",
		max_team_handy_rxns: 5,
		max_channel_handy_rxns: 5,
		max_poll_handy_rxns: 7,
		max_handy_rxns_title_chars: 30,
		
		feature_tinyspeck: false,
		feature_create_team_google_auth: false,
		feature_api_extended_2fa_backup: false,
		feature_see_all_members_dialog: true,
		feature_admin_long_list_view: false,
		feature_rtm_start_over_ms: false,
		feature_emoji_usage_stats: false,
		feature_viewmodel_proto: false,
		feature_beacon_dom_node_count: true,
		feature_message_replies: false,
		feature_message_replies_simple: false,
		feature_no_rollups: false,
		feature_web_lean: false,
		feature_web_lean_all_users: false,
		feature_reminders_v3: true,
		feature_all_skin_tones: false,
		feature_import_batch_actions: true,
		feature_server_side_emoji_counts: true,
		feature_a11y_keyboard_shortcuts: false,
		feature_email_ingestion: false,
		feature_msg_consistency: false,
		feature_sli_channel_priority: false,
		feature_sli_similar_channels: true,
		feature_sli_channel_suggestbot: true,
		feature_emoji_keywords: true,
		feature_thanks: false,
		feature_attachments_inline: false,
		feature_fix_files: true,
		feature_files_list: true,
		feature_channel_eventlog_client: true,
		feature_macssb1_banner: true,
		feature_macssb2_banner: true,
		feature_latest_event_ts: true,
		feature_elide_closed_dms: true,
		feature_no_redirects_in_ssb: true,
		feature_referer_policy: true,
		feature_more_field_in_message_attachments: false,
		feature_calls: true,
		feature_calls_no_rtm_start: true,
		feature_integrations_message_preview: true,
		feature_paging_api: false,
		feature_enterprise_dashboard: true,
		feature_enterprise_api: true,
		feature_enterprise_create: true,
		feature_enterprise_api_auth: true,
		feature_enterprise_profile: true,
		feature_enterprise_search: true,
		feature_enterprise_team_invite: true,
		feature_enterprise_locked_settings: false,
		feature_frecency_migration: false,
		feature_enterprise_team_overview_page: false,
		feature_enterprise_search_ui: false,
		feature_enterprise_mandatory_2fa: false,
		feature_enterprise_user_account_settings: false,
		feature_private_channels: true,
		feature_mpim_restrictions: false,
		feature_subteams_hard_delete: false,
		feature_no_unread_counts: true,
		feature_js_raf_queue: false,
		feature_shared_channels: false,
		feature_shared_channels_ui: false,
		feature_external_shared_channels_ui: false,
		feature_batch_users: false,
		feature_manage_shared_channel_teams: false,
		feature_shared_channels_settings: false,
		feature_fast_files_flexpane: true,
		feature_no_has_files: true,
		feature_custom_saml_signin_button_label: true,
		feature_optimistic_im_close: false,
		feature_admin_approved_apps: true,
		feature_winssb_beta_channel: false,
		feature_inline_video: false,
		feature_branch_io_deeplink: false,
		feature_developers_lp: true,
		feature_clog_whats_new: true,
		feature_upload_file_switch_channel: true,
		feature_presence_sub: false,
		feature_live_support: true,
		feature_dm_yahself: true,
		feature_slackbot_goes_to_college: false,
		feature_popover_dismiss_only: true,
		feature_attachment_actions: true,
		feature_shared_invites: true,
		feature_lato_2_ssb: true,
		feature_refactor_buildmsghtml: false,
		feature_reduce_files_page_size: true,
		feature_allow_cdn_experiments: false,
		feature_omit_localstorage_users_bots: false,
		feature_disable_ls_compression: false,
		feature_sign_in_with_slack: true,
		feature_sign_in_with_slack_ui_elements: true,
		feature_prevent_msg_rebuild: false,
		feature_app_review_part_2: false,
		feature_new_app_modal: false,
		feature_app_directory_search_solr: true,
		feature_name_tagging_client: false,
		feature_name_tagging_client_extras: false,
		feature_msg_input_contenteditable: false,
		feature_browse_date: true,
		feature_use_imgproxy_resizing: false,
		feature_events_api_frontend: true,
		feature_update_message_file: false,
		feature_custom_clogs: true,
		feature_channels_view_introspect_messages: false,
		feature_intercept_format_copy: true,
		feature_calls_linux: true,
		feature_emoji_hover_styles: true,
		feature_emoji_speed: true,
		feature_a11y_preference: false,
		feature_a11y_deanimation: false,
		feature_share_mention_comment_cleanup: false,
		feature_search_menu: true,
		feature_unread_view: false,
		feature_tw: false,
		feature_tw_ls_disabled: false,
		feature_external_files: false,
		feature_channel_info_pins_and_guests: false,
		feature_min_web: false,
		feature_electron_memory_logging: false,
		feature_limit_jl_rollups: true,
		feature_jumper_open_state: true,
		feature_jumper_archived_channels: true,
		feature_optimize_mentions_stars_paging: true,
		feature_simple_file_events: true,
		feature_empty_flexpanes: true,
		feature_backend_frecency_validation: true,
		feature_backend_frecency_pruning: true,
		feature_devrel_try_it_now: false,
		feature_wait_for_all_mentions_in_client: false,
		feature_free_inactive_domains: true,
		feature_invitebulk_method_in_modal: false,
		feature_invite_modal_refresh: false,
		feature_invite_modal_contacts: false,
		feature_slackbot_feels: true,
		feature_global_frecency: true,
		feature_autocomplete_frecency: false,
		feature_frecency_bonus_points: false,
		feature_platform_calls: false,
		feature_a11y_tab: false,
		feature_admin_billing_refactor: false,
		feature_attachment_limits: false,
		feature_wrapped_mention_parsing: false,
		feature_member_actions: false,
		feature_take_profile_photo: false,
		feature_ajax_billing_history: false,
		feature_msg_input_placeholder: false,
		feature_update_coachmarks: false,
		feature_multnomah: false,

		img: {
			app_icon: 'https://a.slack-edge.com/272a/img/slack_growl_icon.png'
		},
		page_needs_custom_emoji: false,
		page_needs_team_profile_fields: false,
		page_needs_enterprise: false,
		slackbot_help_enabled: true
	};

	
	
	
	
	// client boot data
	
	
//-->
</script>	
	
				<!-- output_js "core" -->
<script type="text/javascript" src="https://a.slack-edge.com/10487/js/rollup-core_required_libs.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/a7bd6/js/rollup-core_required_ts.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/ad781/js/TS.web.js" crossorigin="anonymous"></script>

		<!-- output_js "core_web" -->
<script type="text/javascript" src="https://a.slack-edge.com/2296/js/rollup-core_web.js" crossorigin="anonymous"></script>

		<!-- output_js "secondary" -->
<script type="text/javascript" src="https://a.slack-edge.com/4c466/js/rollup-secondary_a_required.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/b26e/js/rollup-secondary_b_required.js" crossorigin="anonymous"></script>

					
	<!-- output_js "regular" -->
<script type="text/javascript" src="https://a.slack-edge.com/8e19/js/TS.web.comments.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/0ee63/js/TS.web.file.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/cb0fd/js/libs/codemirror.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://a.slack-edge.com/db4a/js/codemirror_load.js" crossorigin="anonymous"></script>

		<script type="text/javascript">
	<!--
		boot_data.page_needs_custom_emoji = true;

		boot_data.file = {"id":"F206XQU66","created":1470856637,"timestamp":1470856637,"name":"node--2.tpl.php","title":"node--2.tpl.php","mimetype":"text\/plain","filetype":"html","pretty_type":"HTML","user":"U0TP140NP","editable":true,"size":90614,"mode":"snippet","is_external":false,"external_type":"","is_public":false,"public_url_shared":false,"display_as_bot":false,"username":"","url_private":"https:\/\/files.slack.com\/files-pri\/T0F20CPUL-F206XQU66\/node--2.tpl.php","url_private_download":"https:\/\/files.slack.com\/files-pri\/T0F20CPUL-F206XQU66\/download\/node--2.tpl.php","permalink":"https:\/\/webny.slack.com\/files\/ccatalina\/F206XQU66\/node--2.tpl.php","permalink_public":"https:\/\/slack-files.com\/T0F20CPUL-F206XQU66-fcac94b49f","edit_link":"https:\/\/webny.slack.com\/files\/ccatalina\/F206XQU66\/node--2.tpl.php\/edit","preview":"\n\n\n\n\u003C!DOCTYPE html\u003E","preview_highlight":"\u003Cdiv class=\"CodeMirror cm-s-default CodeMirrorServer\" oncopy=\"if(event.clipboardData){event.clipboardData.setData('text\/plain',window.getSelection().toString().replace(\/\\u200b\/g,''));event.preventDefault();event.stopPropagation();}\"\u003E\n\u003Cdiv class=\"CodeMirror-code\"\u003E\n\u003Cdiv\u003E\u003Cpre\u003E&#8203;\u003C\/pre\u003E\u003C\/div\u003E\n\u003Cdiv\u003E\u003Cpre\u003E&#8203;\u003C\/pre\u003E\u003C\/div\u003E\n\u003Cdiv\u003E\u003Cpre\u003E&#8203;\u003C\/pre\u003E\u003C\/div\u003E\n\u003Cdiv\u003E\u003Cpre\u003E&#8203;\u003C\/pre\u003E\u003C\/div\u003E\n\u003Cdiv\u003E\u003Cpre\u003E\u003Cspan class=\"cm-meta\"\u003E&lt;!DOCTYPE html&gt;\u003C\/span\u003E\u003C\/pre\u003E\u003C\/div\u003E\n\u003C\/div\u003E\n\u003C\/div\u003E\n","lines":1260,"lines_more":1255,"channels":[],"groups":["G1KLXFUSZ"],"ims":[],"comments_count":0};
		boot_data.file.comments = [];

		

		var g_editor;

		$(function(){

			var wrap_long_lines = !!TS.model.code_wrap_long_lines;

			g_editor = CodeMirror(function(elt){
				var content = document.getElementById("file_contents");
				content.parentNode.replaceChild(elt, content);
			}, {
				value: $('#file_contents').text(),
				lineNumbers: true,
				matchBrackets: true,
				indentUnit: 4,
				indentWithTabs: true,
				enterMode: "keep",
				tabMode: "shift",
				viewportMargin: Infinity,
				readOnly: true,
				lineWrapping: wrap_long_lines
			});

			$('#file_preview_wrap_cb').bind('change', function(e) {
				TS.model.code_wrap_long_lines = $(this).prop('checked');
				g_editor.setOption('lineWrapping', TS.model.code_wrap_long_lines);
			})

			$('#file_preview_wrap_cb').prop('checked', wrap_long_lines);

			CodeMirror.switchSlackMode(g_editor, "html");
		});

		
		$('#file_comment').css('overflow', 'hidden').autogrow();
	//-->
	</script>

			<script type="text/javascript">TS.boot(boot_data);</script>
	
<style>.color_9f69e7:not(.nuc) {color:#9F69E7;}.color_4bbe2e:not(.nuc) {color:#4BBE2E;}.color_e7392d:not(.nuc) {color:#E7392D;}.color_3c989f:not(.nuc) {color:#3C989F;}.color_674b1b:not(.nuc) {color:#674B1B;}.color_e96699:not(.nuc) {color:#E96699;}.color_e0a729:not(.nuc) {color:#E0A729;}.color_684b6c:not(.nuc) {color:#684B6C;}.color_5b89d5:not(.nuc) {color:#5B89D5;}.color_2b6836:not(.nuc) {color:#2B6836;}.color_99a949:not(.nuc) {color:#99A949;}.color_df3dc0:not(.nuc) {color:#DF3DC0;}.color_4cc091:not(.nuc) {color:#4CC091;}.color_9b3b45:not(.nuc) {color:#9B3B45;}.color_d58247:not(.nuc) {color:#D58247;}.color_bb86b7:not(.nuc) {color:#BB86B7;}.color_5a4592:not(.nuc) {color:#5A4592;}.color_db3150:not(.nuc) {color:#DB3150;}.color_235e5b:not(.nuc) {color:#235E5B;}.color_9e3997:not(.nuc) {color:#9E3997;}.color_53b759:not(.nuc) {color:#53B759;}.color_c386df:not(.nuc) {color:#C386DF;}.color_385a86:not(.nuc) {color:#385A86;}.color_a63024:not(.nuc) {color:#A63024;}.color_5870dd:not(.nuc) {color:#5870DD;}.color_ea2977:not(.nuc) {color:#EA2977;}.color_50a0cf:not(.nuc) {color:#50A0CF;}.color_d55aef:not(.nuc) {color:#D55AEF;}.color_d1707d:not(.nuc) {color:#D1707D;}.color_43761b:not(.nuc) {color:#43761B;}.color_e06b56:not(.nuc) {color:#E06B56;}.color_8f4a2b:not(.nuc) {color:#8F4A2B;}.color_902d59:not(.nuc) {color:#902D59;}.color_de5f24:not(.nuc) {color:#DE5F24;}.color_a2a5dc:not(.nuc) {color:#A2A5DC;}.color_827327:not(.nuc) {color:#827327;}.color_3c8c69:not(.nuc) {color:#3C8C69;}.color_8d4b84:not(.nuc) {color:#8D4B84;}.color_84b22f:not(.nuc) {color:#84B22F;}.color_4ec0d6:not(.nuc) {color:#4EC0D6;}.color_e23f99:not(.nuc) {color:#E23F99;}.color_e475df:not(.nuc) {color:#E475DF;}.color_619a4f:not(.nuc) {color:#619A4F;}.color_a72f79:not(.nuc) {color:#A72F79;}.color_7d414c:not(.nuc) {color:#7D414C;}.color_aba727:not(.nuc) {color:#ABA727;}.color_965d1b:not(.nuc) {color:#965D1B;}.color_4d5e26:not(.nuc) {color:#4D5E26;}.color_dd8527:not(.nuc) {color:#DD8527;}.color_bd9336:not(.nuc) {color:#BD9336;}.color_e85d72:not(.nuc) {color:#E85D72;}.color_dc7dbb:not(.nuc) {color:#DC7DBB;}.color_bc3663:not(.nuc) {color:#BC3663;}.color_9d8eee:not(.nuc) {color:#9D8EEE;}.color_8469bc:not(.nuc) {color:#8469BC;}.color_73769d:not(.nuc) {color:#73769D;}.color_b14cbc:not(.nuc) {color:#B14CBC;}</style>

<!-- slack-www1100 / 2016-08-10 12:20:20 / v0936cf9af28321a66cbb6be1bac76d58e22de90e / B:P -->

</body>
</html>