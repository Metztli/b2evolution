<?php
/**
 * This is b2evolution's stats config file.
 *
 * @deprecated TODO: It holds now just things that should be move around due to hitlog refactoring.
 *
 * This file sets how b2evolution will log hits and stats
 * Last significant changes to this file: version 1.6
 *
 * @package conf
 */
if (! defined('EVO_CONFIG_LOADED')) {
    die('Please, do not access this page directly.');
}


/**
 * Self referers that should not be considered as "real" referers in stats.
 * This should typically include this site and maybe other subdomains of this site.
 *
 * The following substrings will be looked up in the referer http header
 * in order to identify referers to hide in the logs.
 *
 * The string must start within the 12 FIRST CHARS of the referer or it will be ignored.
 * note: http://abc.com is already 14 chars. 12 for safety.
 *
 * WARNING: you should *NOT* use a slash at the end of simple domain names, as
 * older Netscape browsers will not send these. For example you should list
 * http://www.example.com instead of http://www.example.com/ .
 *
 * @todo move to admin interface (T_basedomains list editor), but use for upgrading
 * @todo handle multiple blog roots.
 *
 * @global array
 */
$self_referer_list = [
    '://' . $basehost,			// This line will match all pages from the host of your $baseurl
    '://www.' . $basehost,		// This line will also match www.you_base_host because any "www." will have been stripped away from your basehost
    'http://localhost',
    'http://127.0.0.1',
];


/**
 * Speciallist: referrers that should not be considered as "real" referers in stats.
 * This should typically include stat services, online email services, online aggregators, etc.
 *
 * The following substrings will be looked up in the referer http header
 * in order to identify referers to hide in the logs
 *
 * THIS IS NOT FOR SPAM! Use the Antispam features in the admin section to control spam!
 *
 * The string must start within the 12 FIRST CHARS of the referer or it will be ignored.
 * note: http://abc.com is already 14 chars. 12 for safety.
 *
 * WARNING: you should *NOT* use a slash at the end of simple domain names, as
 * older Netscape browsers will not send these. For example you should list
 * http://www.example.com instead of http://www.example.com/ .
 *
 * @todo move to admin interface (T_basedomains list editor), but use for upgrading
 *
 * @global array
 */
$SpecialList = [
    // webmails
    '.mail.yahoo.com/',
    '//mail.google.com/',
    'webmail.aol.com/',
    // stat services
    'sitemeter.com/',
    // aggregators
    'bloglines.com/',
    // caches
    '/search?q=cache:',		// Google cache
    // redirectors
    'googlealert.com/',
    // site status services
    'host-tracker.com',
    // add your own...
];


/**
 * UserAgent identifiers for logging/statistics
 *
 * The following substrings will be looked up in the user_agent http header
 *
 * 'type' aggregator currently gets only used to "translate" user agent strings.
 * An aggregator hit gets detected by accessing the feed.
 *
 * @global array $user_agents
 */
$user_agents = [
    // Robots:
    1000 => ['robot', 'Googlebot', 'Google (Googlebot)'], // removed slash in order to also match "Googlebot-Image", "Googlebot-Mobile", "Googlebot-Sitemaps"
    1001 => ['robot', 'Slurp/', 'Inktomi (Slurp)'],
    1002 => ['robot', 'Yahoo! Slurp', 'Yahoo (Slurp)'], // removed ; to also match "Yahoo! Slurp China"
    1003 => ['robot', 'msnbot', 'MSN Search (msnbot)'], // removed slash in order to also match "msnbot-media"
    1004 => ['robot', 'Frontier/', 'Userland (Frontier)'],
    1005 => ['robot', 'ping.blo.gs/', 'blo.gs'],
    1006 => ['robot', 'organica/', 'Organica'],
    1007 => ['robot', 'Blogosphere/', 'Blogosphere'],
    1008 => ['robot', 'blogging ecosystem crawler', 'Blogging ecosystem'],
    1009 => ['robot', 'FAST-WebCrawler/', 'Fast'],			// http://fast.no/support/crawler.asp
    1010 => ['robot', 'timboBot/', 'Breaking Blogs (timboBot)'],
    1011 => ['robot', 'NITLE Blog Spider/', 'NITLE'],
    1012 => ['robot', 'The World as a Blog ', 'The World as a Blog'],
    1013 => ['robot', 'daypopbot/ ', 'DayPop'],
    1014 => ['robot', 'Bitacle bot/', 'Bitacle'],
    1015 => ['robot', 'Sphere Scout', 'Sphere Scout'],
    1016 => ['robot', 'Gigabot/', 'Gigablast (Gigabot)'],
    1017 => ['robot', 'Yandex', 'Yandex'],
    1018 => ['robot', 'Mail.RU/', 'Mail.Ru'],
    1019 => ['robot', 'Baiduspider', 'Baidu spider'],
    1020 => ['robot', 'infometrics-bot', 'Infometrics Bot'],
    1021 => ['robot', 'DotBot/', 'DotBot'],
    1022 => ['robot', 'Twiceler-', 'Cuil (Twiceler)'],
    1023 => ['robot', 'discobot/', 'Discovery Engine'],
    1024 => ['robot', 'Speedy Spider', 'Entireweb (Speedy Spider)'],
    1025 => ['robot', 'monit/', 'Monit'],
    1026 => ['robot', 'Sogou web spider', 'Sogou'],
    1027 => ['robot', 'Tagoobot/', 'Tagoobot'],
    1028 => ['robot', 'MJ12bot/', 'Majestic-12'],
    1029 => ['robot', 'ia_archiver', 'Alexa crawler'],
    1030 => ['robot', 'KaloogaBot', 'Kalooga'],
    1031 => ['robot', 'Flexum/', 'Flexum'],
    1032 => ['robot', 'OOZBOT/', 'OOZBOT'],
    1033 => ['robot', 'ApptusBot', 'Apptus'],
    1034 => ['robot', 'Purebot', 'Pure Search'],
    1035 => ['robot', 'Sosospider', 'Sosospider'],
    1036 => ['robot', 'TopBlogsInfo', 'TopBlogsInfo'],
    1037 => ['robot', 'spbot/', 'SEOprofiler'],
    1038 => ['robot', 'StackRambler', 'Rambler'],
    1039 => ['robot', 'AportWorm', 'Aport.ru'],
    1040 => ['robot', 'ScoutJet', 'ScoutJet'],
    1041 => ['robot', 'bingbot/', 'Bing'],
    1042 => ['robot', 'Nigma.ru/', 'Nigma.ru'],
    1043 => ['robot', 'ichiro/', 'Ichiro'],
    1044 => ['robot', 'YoudaoBot/', 'Youdao'],
    1045 => ['robot', 'Sogou web spider/', 'Sogou web spider'],
    1046 => ['robot', 'findfiles.net', 'findfiles.net'],
    1047 => ['robot', 'SiteBot/', 'SiteBot'],
    1048 => ['robot', 'Nutch-', 'Apache Nutch'],
    1049 => ['robot', 'DoCoMo/', 'DoCoMo'],
    1050 => ['robot', 'findlinks/', 'FindLinks'],
    1051 => ['robot', 'MLBot', 'MLBot'],
    1052 => ['robot', 'facebookexternalhit', 'Facebook'],
    1053 => ['robot', ' oBot/', 'IBM Bot'],
    1054 => ['robot', 'GarlikCrawler/', 'Garlik'],
    1055 => ['robot', 'Yeti/', 'Naver'],
    1056 => ['robot', 'TurnitinBot/', 'Turnitin'],
    1057 => ['robot', 'NerdByNature.Bot', 'NerdByNature'],
    1058 => ['robot', 'SeznamBot/', 'SeznamBot'],
    1059 => ['robot', 'Nymesis/', 'Nymesis'],
    1060 => ['robot', 'YodaoBot/', 'YodaoBot'],
    1061 => ['robot', 'Exabot/', 'Exabot'],
    1062 => ['robot', 'AhrefsBot/', 'AhrefsBot'],
    1063 => ['robot', 'SISTRIX Crawler', 'SISTRIX'],
    1064 => ['robot', 'AcoonBot/', 'AcoonBot'],
    1065 => ['robot', 'VoilaBot', 'VoilaBot'],
    1066 => ['robot', 'SiteExplorer', 'SiteExplorer'],
    1067 => ['robot', 'IstellaBot/', 'IstellaBot'],
    1068 => ['robot', 'exb.de/crawler', 'ExB Language Crawler'],
    1069 => ['robot', 'SemrushBot', 'SemrushBot'],
    1070 => ['robot', 'UptimeRobot', 'UptimeRobot'],
    1071 => ['robot', 'Qwantify', 'Qwant'],
    // Unknown robots:
    5000 => ['robot', 'psycheclone', 'Psycheclone'],
    // Aggregators:
    10000 => ['aggregator', 'AppleSyndication/', 'Safari RSS (AppleSyndication)'],
    10001 => ['aggregator', 'Feedreader', 'Feedreader'],
    10002 => ['aggregator', 'Syndirella/', 'Syndirella'],
    10003 => ['aggregator', 'rssSearch Harvester/', 'rssSearch Harvester'],
    10004 => ['aggregator', 'Newz Crawler',	'Newz Crawler'],
    10005 => ['aggregator', 'MagpieRSS/', 'Magpie RSS'],
    10006 => ['aggregator', 'CoologFeedSpider', 'CoologFeedSpider'],
    10007 => ['aggregator', 'Pompos/', 'Pompos'],
    10008 => ['aggregator', 'SharpReader/', 'SharpReader'],
    10009 => ['aggregator', 'Straw ', 'Straw'],
    10010 => ['aggregator', 'YandexBlog', 'YandexBlog'],
    10011 => ['aggregator', ' Planet/', 'Planet Feed Reader'],
    10012 => ['aggregator', 'UniversalFeedParser/', 'Universal Feed Parser'],
];

/* Set user devices */
// MOBILE
$mobile_user_devices = [
    'iphone' => '(iphone|ipod)',
    'android' => 'android.*mobile',
    'blkberry' => 'blackberry',
    'winphone' => 'windows phone os',
    'wince' => 'windows ce; (iemobile|ppc|smartphone)',
    'palm' => '(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)',
    'gendvice' => '(kindle|mobile|mmp|midp|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap|opera mini)',
];

// TABLET
$tablet_user_devices = [
    'ipad' => '(ipad)',
    'andrtab' => 'android(?!.*mobile)',
    'berrytab' => 'rim tablet os',
];

// PC
$pc_user_devices = [
    'win311' => 'win16',
    'win95' => '(windows 95)|(win95)|(windows_95)',
    'win98' => '(windows 98)|(win98)',
    'win2000' => '(windows nt 5.0)|(windows 2000)',
    'winxp' => '(windows nt 5.1)|(windows XP)',
    'win2003' => '(windows nt 5.2)',
    'winvista' => '(windows nt 6.0)',
    'win7' => '(windows nt 6.1)',
    'winnt40' => '(windows nt 4.0)|(winnt4.0)|(winnt)|(windows nt)',
    'winme' => '(windows me)|(win 9x 4.90)',
    'openbsd' => 'openbsd',
    'sunos' => 'sunos',
    'linux' => '(linux)|(x11)',
    'ubuntu' => 'ubuntu',
    'macosx' => 'mac os x',
    'macos' => '(mac_powerpc)|(macintosh)',
    'qnx' => 'qnx',
    'beos' => 'beos',
    'os2' => 'os/2',
];

$user_devices = array_merge(
    $tablet_user_devices,
    $mobile_user_devices,
    $pc_user_devices
);

$user_devices_color = [
    // Mobile
    'iphone' => 'd8c1a1',
    'ipad' => 'c5aa8c',
    'andrtab' => 'cdba9c',
    'android' => 'e0caa5',
    'berrytab' => 'b29575',
    'blkberry' => 'baa286',
    'winphone' => 'ceb28b',
    'wince' => 'e4d6b9',
    'palm' => 'c8ac84',
    'gendvice' => 'e6d4bf',
    // PC
    'win311' => 'CCCCCC',
    'win95' => '676767',
    'win98' => 'ABABAB',
    'win2000' => '898989',
    'winxp' => 'DEDEDE',
    'win2003' => 'A3A3A3',
    'winvista' => 'EEEEEE',
    'win7' => '999999',
    'winnt40' => 'B9B9B9',
    'winme' => '7F7F7F',
    'openbsd' => 'AFAFAF',
    'sunos' => '808080',
    'linux' => 'E0E0E0',
    'ubuntu' => 'B4B4B4',
    'macosx' => '9F9F9F',
    'macos' => 'F0F0F0',
    'qnx' => 'D0D0D0',
    'beos' => '8F8F8F',
    'os2' => 'C0C0C0',
];

$referer_type_array = [
    '0' => 'All',
    'search' => 'Search',
    'referer' => 'Referer',
    'direct' => 'Direct',
    'self' => 'Self',
    'special' => 'Special',
    'spam' => 'Spam',
    'admin' => 'Admin',
];

$referer_type_color = [
    'session' => '006699',
    'search' => '0099FF',
    'special' => 'ff00ff',
    'referer' => '00CCFF',
    'direct' => '00FFCC',
    'spam' => 'FF0000',
    'self' => '00FF99',
    'admin' => '999999',
    'ajax' => '339966',
];

$agent_type_array = [
    '0' => 'All',
    'robot' => 'Robot',
    'browser' => 'Browser',
    'unknown' => 'Unknown',
];

$agent_type_color = [
    'rss' => 'FF6600',
    'robot' => 'FF9900',
    'browser' => 'FFCC00',
    'unknown' => 'cccccc',
];

$hit_type_array = [
    '0' => 'All',
    'rss' => 'RSS',
    'standard' => 'Standard',
    'ajax' => 'AJAX',
    'service' => 'Service',
    'admin' => 'Admin',
    'api' => 'API',
];

$hit_type_color = [
    'standard' => 'FFBB00',
    'service' => '6699CC',
    'rss' => 'FF6600',
    'ajax' => '339966',
    'admin' => 'AAE0E0',
    'standard_robot' => 'FF9900',
    'standard_browser' => 'FFCC00',
    'api' => '5BC0DE',
    'unknown' => 'CCCCCC',
];

$hit_method_color = [
    'GET' => '000000',
    'POST' => 'FFBB00',
    'PUT' => 'ff00ff',
    'DELETE' => 'FF0000',
    'HEAD' => '00CCFF',
];

$user_gender_color = [
    'women_active' => '990066',
    'women_notactive' => 'c72290',
    'women_closed' => 'ff66cc',
    'men_active' => '003399',
    'men_notactive' => '3268d4',
    'men_closed' => '6699ff',
    'nogender_active' => '666666',
    'nogender_notactive' => '999999',
    'nogender_closed' => 'cccccc',
];

$activity_type_color = [
    'users' => 'FF9900',
    'posts' => '6699CC',
    'comments' => '5BC0DE',
];
