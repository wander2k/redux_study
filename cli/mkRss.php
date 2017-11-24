<?php
date_default_timezone_set('Asia/Tokyo');
header("Content-Type: application/xml; charset=utf-8");


$URL = "http://www.cosmopolitan-jp.com/api/json/all.xml?type=home&id=0&n=100&dynamic";
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
$list = json_decode(curl_exec($ch));

echo "<rss  version=\"2.0\" xmlns:oa=\"http://news.line.me/rss/1.0/oa\">\n";
echo "<channel>\n";

echo "<title><![CDATA[コスモポリタン]]></title>\n";
echo "<link>http://www.cosmopolitan-jp.com</link>\n";
echo "<description>恋愛、セレブ、海外トレンド情報</description>\n";
echo "<language>ja</language>\n";


$now = date("r");
echo "<lastBuildDate>{$now}</lastBuildDate>\n";


$n = 0;
$title = array();
foreach($list->items as $item){

/*
if($n >= 1){
	break;
}
*/


	sleep(1);
	curl_setopt($ch, CURLOPT_URL, "http://www.cosmopolitan-jp.com/api/json/{$item->type}.{$item->id}");
	$detail = json_decode(curl_exec($ch));
	
	echo "<item>\n";
	
	echo "<guid>{$detail->metadata->complete_url}</guid>\n";
	echo "<title><![CDATA[{$detail->raw->title}]]></title>\n";
	echo "<link>{$detail->metadata->complete_url}</link>\n";
	echo "<oa:pubStatus>0</oa:pubStatus>\n";
	

	$categories = array();
	$categories[] = array('category' => 1, 'path' => array('/entertainment/brand-new-entertainment/'));
	$categories[] = array('category' => 2, 'path' => array('/beauty-fashion/fashion/','/beauty-fashion/hair/','/beauty-fashion/nail/','/beauty-fashion/makeup/','/beauty-fashion/workout/','/beauty-fashion/brand-new-beauty-fashion/'));
	$categories[] = array('category' => 7, 'path' => array('/entertainment/internet/'));	
	$categories[] = array('category' => 8, 'path' => array('/entertainment/travel/'));	
	$categories[] = array('category' => 9, 'path' => array('/trends/recipe/','/trends/gourmet/'));	
	$categories[] = array('category' => 10, 'path' => array('/love/relationships/','/love/date/','/love/wedding/','/love/lgbt/','/love/brand-new/'));	
	$categories[] = array('category' => 12, 'path' => array('/entertainment/entertainment-news/','/entertainment/celebrity/','/entertainment/tv/','/entertainment/movies/','/entertainment/books/'));	
	$categories[] = array('category' => 14, 'path' => array('/beauty-fashion/beauty/','/beauty-fashion/health/','/entertainment/horoscope/','/trends/trend-news/','/trends/lifestyle/','/trends/career/','/trends/brand-new-trends/'));	
	$categories[] = array('category' => 17, 'path' => array('/trends/politics/','/trends/society/'));	

	$category_id = 0;
	foreach($categories as $category){
		foreach($category['path'] as $path){
			if(strstr($detail->metadata->complete_url,$path)){
				$category_id = $category['category'];
			}
		}
	}
	
	if($category_id){
		echo "<oa:category>{$category_id}</oa:category>\n";
	}


	$body = $detail->body;


	if($item->type == 'gallery'){
		$body = $detail->raw->abs;
		foreach($detail->gallery_image_data as $slide){
			foreach($detail->images as $image){
				if(isset($image->gallery->{320}->id) && isset($slide->image_id) && ($image->gallery->{320}->id == $slide->image_id) ){
					$body .= "<p><img src=\"{$image->gallery->{320}->url}\" /></p>";
				}
			}
			$body .= "<p>{$slide->site_title}</p>";
			$body .= $slide->site_caption;
		}
	}


	// Removing Content Links
	preg_match_all('/<p class="body-el-text standard-body-el-text">(<em data-redactor-tag="em" data-verified="redactor">|)(<em data-redactor-tag="em" data-verified="redactor">|)<a class="body-el-link standard-body-el-link" href="(.*?)<\/a><\/p>/s', $body, $match);
	foreach($match[0] as $k => $item){
  		$body = str_replace( $item, '', $body);
	}


	// Removing link other than the first
/*
	preg_match_all('/<a (.*?) data-tracking-id="recirc-text-link">(.*?)<\/a>/s', $body, $match);
    foreach($match[0] as $k => $item){
		if($k){
			$body = str_replace($item, $match[2][$k], $body);
		}
    }
*/
	preg_match_all('/<a(?: .+?)?>.*?<\/a>/s', $body, $match);
    foreach($match[0] as $k => $item){
        if($k){
        	if(strstr($item,'data-tracking-id="recirc-text-link"')){
            	$body = str_replace($item, strip_tags($item), $body);
			}
		}
    }

	 
	// Adding script for Instagram
	$instagram_script = "<script async defer src=\"https://platform.instagram.com/en_US/embeds.js\"></script>";
	preg_match_all('/<blockquote class="instagram-media"(.*?)<\/blockquote>/s', $body, $match);
	foreach($match[0] as $k => $item){
		$body = str_replace($item, "{$item}{$instagram_script}", $body);
	}
	
	// Adding script for Twitter
	$twitter_script = "<script async src=\"https://platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>";
	preg_match_all('/<blockquote class="twitter-tweet"(.*?)<\/blockquote>/s', $body, $match);
    foreach($match[0] as $k => $item){
        $body = str_replace($item, "{$item}{$twitter_script}", $body);
    }

	echo "<description><![CDATA[{$body}]]></description>\n";


	$image = "";
	$copyright = "";
	if($detail->images[0]->{hd-aspect}->{640}){
    	$image = $detail->images[0]->{hd-aspect}->{640}->{url};
		$copyright = $detail->images[0]->{hd-aspect}->{640}->{copyright};
	}else if($detail->images[0]->{hd-aspect}->{480}){
    	$image = $detail->images[0]->{hd-aspect}->{480}->{url};
		$copyright = $detail->images[0]->{hd-aspect}->{480}->{copyright};
	}else if($detail->images[0]->{hd-aspect}->{320}){
    	$image = $detail->images[0]->{hd-aspect}->{320}->{url};
		$copyright = $detail->images[0]->{hd-aspect}->{320}->{copyright};
	}else if($detail->images[0]->{hd-aspect}->{160}){
    	$image = $detail->images[0]->{hd-aspect}->{160}->{url};
		$copyright = $detail->images[0]->{hd-aspect}->{160}->{copyright};
	}else if($detail->images[0]->{landscape}->{640}){
    	$image = $detail->images[0]->{landscape}->{640}->{url};
		$copyright = $detail->images[0]->{landscape}->{640}->{copyright};
	}else if($detail->images[0]->{landscape}->{480}){
    	$image = $detail->images[0]->{landscape}->{480}->{url};
		$copyright = $detail->images[0]->{landscape}->{480}->{copyright};
	}else if($detail->images[0]->{landscape}->{320}){
    	$image = $detail->images[0]->{landscape}->{320}->{url};
		$copyright = $detail->images[0]->{landscape}->{320}->{copyright};
	}else if($detail->images[0]->{landscape}->{160}){
    	$image = $detail->images[0]->{landscape}->{160}->{url};
		$copyright = $detail->images[0]->{landscape}->{160}->{copyright};
	}
	echo "<enclosure url=\"{$image}\" />\n";

	$pubdate = date("r",$detail->raw->date);
	echo "<pubDate>{$pubdate}</pubDate>\n";

	if($copyright){
		echo "<oa:imgAuthor><oa:authorName><![CDATA[{$copyright}]]></oa:authorName><oa:authorUrl></oa:authorUrl></oa:imgAuthor>\n";
	}

	$lastpubdate = date("r",$detail->metadata->last_publish_date);
    echo "<oa:lastPubDate>{$lastpubdate}</oa:lastPubDate>\n";

	$related = getRelated($detail->metadata->complete_url);
	foreach($related as $item){
		echo "<oa:reflink><oa:refTitle><![CDATA[{$item[0]}]]></oa:refTitle><oa:refUrl>{$item[1]}?utm_source=line-am&amp;utm_medium=social</oa:refUrl></oa:reflink>\n";
	}


	echo "</item>\n";

	$n++;
}


echo "</channel>\n";
echo "</rss>\n";


curl_close($ch);


/*** Related content from Cxence ***/
function getRelated($url){

	$ch = curl_init();
	//$cxenseApiUrl = "http://api.cxense.com/public/widget/data?json={%22widgetId%22:%22866e94bd6764bec3d53e6d6185a60961de0108a2%22,%22context%22:{%22url%22:%22" . $url . "%22}}";
	$cxenseApiUrl = "http://api.cxense.com/public/widget/data?json={%22widgetId%22:%2211ae96e78c9ea57a25adeee1f91e318af9f53903%22,%22context%22:{%22url%22:%22" . $url . "%22}}";
	curl_setopt($ch, CURLOPT_URL, $cxenseApiUrl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	$items = json_decode( $result, true );
	$related = array();
	foreach($items['items'] as $item){
		$related[] = array($item['title'],$item['url']);	
	}

	return $related;

}

?>

