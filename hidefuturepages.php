<?php
// register plugin
$thisfile = basename(__FILE__, ".php");
register_plugin($thisfile,
  'Hide future pages',
  '0.2',
  'Carlos Navarro',
  'http://www.cyberiada.org/cnb/',
  'Protects pages with future date from viewing, excludes them from I18N Search results and Sitemap', '', ''
);

add_filter('search-veto','hidefuturepages_search'); // hide in I18N Search results
add_filter('sitemap','hidefuturepages_sitemap'); // exclude from Sitemap
add_action('index-pretemplate','hidefuturepages_view'); // page visibility

function hidefuturepages_view() {
  global $private;
  if ($private != 'Y') {
    global $date;
    if (intval(strtotime($date)) >= intval(strtotime(date('r')))) {
      global $USR;
      if (($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
        //ok, allow the person to see it then
      } else {
        redirect(find_url('404',''));
      }
    }
  }
}

function hidefuturepages_search($item) {
  return intval($item->pubDate) >= intval(strtotime(date('r')));
}

function hidefuturepages_sitemap($sitemap){	
  global $pagesArray;
  foreach ($pagesArray as $page) {
    if (intval(strtotime($page['pubDate'])) >= intval(strtotime(date('r')))) {
      $url = find_url($page['slug'], $page['parent']);
      foreach($sitemap->xpath("url/loc[.='".$url."']/parent::*") as $node){ 
        unset($node[0]);
      }
    }
	}
	return $sitemap;
}

// end of file
