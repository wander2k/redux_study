<?php

require_once(__DIR__."/../vendor/autoload.php");
require_once(__DIR__.'/../config/config.php');

use FeedWriter\Item;
use FeedWriter\Feed;
use FeedWriter\RSS2;
use FeedWriter\InvalidOperationException;

use cli\RoverClient;

$rover = new RoverClient( 
    HFJRSS_ROVER_HOST,
    ROVER_TIMEOUT,
    HFJRSS_ROVER_USER_EMAIL,
    HFJRSS_ROVER_USER_PASSWORD,
    ""
);

// elle-jp
$siteId = "2bb3c0e5-8b6f-4968-8aa7-5fdc0868aad8";
// Long Form Article
//$displayTypeId = "fb518225-f53e-499f-b254-e0e5f1d66f46";
// Standard Article
$displayTypeId = "210d7745-8bc9-4cfc-8b24-a81b36aa80cd";

$contents = $rover->getContentBySiteAndDisplayType($siteId, "3", $displayTypeId, "-updated_at", 1);

//header("Content-Type: application/json; charset=utf-8");
//$json = json_encode($contents);

//echo json_encode($contents, JSON_UNESCAPED_UNICODE);
//var_dump($rover);
//var_dump($contents);


$TestFeed = new RSS2();
// Setting some basic channel elements. These three elements are mandatory.
$TestFeed->setTitle('Antenna:テスト');
$TestFeed->setLink('http://www.elle.co.jp/?cmpid=antenna_test');
$TestFeed->setDescription('RSS2 Feed output test.');
$TestFeed->setChannelElement('language', 'jp');

//var_dump($contents);

foreach($contents["data"] as $key => $value) {
    // Create a new feed item.
    $newItem = $TestFeed->createNewItem();

    $metadata = $value["metadata"];
    $media = $value["media"];
    $indexImageId = array_search(12, array_column($media, "role"));
    $indexImageObj = $media[$indexImageId];
    //var_dump($indexImageObj);
    
    // Add basic elements to the feed item
    // These are again mandatory for a valid feed.
    $newItem->setTitle($value["title"]);
    $newItem->setLink($metadata["links"]["frontend"][HFJRSS_ROVER_ENV_IDENTITY]);
    if (array_key_exists("dek", $metadata)) {
        $newItem->setDescription($metadata["dek"]);
    } else {
        $newItem->setDescription("");
    }
    
    // The following method calls add some optional elements to the feed item.
    // Let's set the publication date of this item. You could also use a UNIX timestamp or
    // an instance of PHP's DateTime class.
    $newItem->setDate($value["created_at"]);
    // You can also attach a media object to a feed item. You just need the URL, the byte length
    // and the MIME type of the media. Here's a quirk: The RSS2 spec says "The url must be an http url.".
    // Other schemes like ftp, https, etc. produce an error in feed validators.

    // https://github.com/HearstCorp/rover/blob/master/content/constants.py
    // media_role = 12 -> index
    $newItem->addEnclosure($indexImageObj["media_object"]["hips_url"], 0, "image/jpeg");
    // If you want you can set the name (and email address) of the author of this feed item.
    //$newItem->setAuthor('Anis uddin Ahmad', 'admin@ajaxray.com');
    // You can set a globally unique identifier. This can be a URL or any other string.
    // If you set permaLink to true, the identifier must be an URL. The default of the
    // permaLink parameter is false.
    $newItem->setId($metadata["links"]["frontend"][HFJRSS_ROVER_ENV_IDENTITY], true);
    // Use the addElement() method for other optional elements.
    // This here will add the 'source' element. The second parameter is the value of the element
    // and the third is an array containing the element attributes.
    //$newItem->addElement('source', 'Mike\'s page', array('url' => 'http://www.example.com'));
    // Now add the feed item to the main feed.
    $TestFeed->addItem($newItem);
    // Another method to add feeds items is by using an array which contains key-value pairs
    // of every item element. Elements which have attributes cannot be added by this way.
    //$newItem = $TestFeed->createNewItem();
    //$newItem->addElementArray(array('title'=> 'The 2nd item', 'link' => 'http://www.google.com', 'description' => 'Just another test.'));
    //$TestFeed->addItem($newItem);

}
// OK. Everything is done. Now generate the feed.
// Then do anything (e,g cache, save, attach, print) you want with the feed in $myFeed.
$myFeed = $TestFeed->generateFeed();
header("Content-Type: text/xml; charset=utf-8");

print_r($myFeed);
// If you want to send the feed directly to the browser, use the printFeed() method.
//$TestFeed->printFeed(true);


