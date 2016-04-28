<?

libxml_use_internal_errors(true); // Yeah if you are so worried about using @ with warnings

if(isset($_GET['igurl'] )) 
{
    $postURL =  htmlspecialchars($_GET["igurl"]);
}
else {
    $postURL = "https://www.instagram.com/p/BEt32Q0Hawr/?hl=en";
}


$html = file_get_contents($postURL);
//echo $html;

$doc = new DomDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);
$query = '//*/meta[starts-with(@property, \'og:\')]';
$metas = $xpath->query($query);
$rmetas = array();
foreach ($metas as $meta) {
    $property = $meta->getAttribute('property');
    $content = $meta->getAttribute('content');
    $rmetas[$property] = $content;
}


//echo "\n\n" . $rmetas["og:site_name"];

$ogtitle = str_replace("Instagram photo by ", "", $rmetas["og:title"]);
/*
$str = 'http://localhost/wpmu/testsite/files/2012/06/testimage.jpg';
$result = substr( $str, strpos( $str, '/files/') + 7);
*/

$igname = substr($ogtitle, 0, strpos($ogtitle," •"));


$image = substr($rmetas["og:image"], 0, strpos($rmetas["og:image"],"?"));




// first strip anything before @
$ogdescription = substr($rmetas["og:description"],strpos($rmetas["og:description"],"@"), strlen($rmetas["og:description"]));



$oglikes = substr($ogdescription, strpos($ogdescription," • "), strlen($ogdescription));

$likes = str_replace(" • ","", $oglikes);
$likes = str_replace(" likes","", $likes);

$likes = convertNumberAbbreviation($likes);
$author = substr($ogdescription, 0, strpos($ogdescription," • "));


$ogdate = substr($ogtitle, strpos($ogtitle," • "),strlen($ogtitle));
$ogdate = str_replace (" • ","", $ogdate);
//Apr 27, 2016 8:15pm UTC

$ogdate =  str_replace (" at "," ", $ogdate);
$ogtype= $rmetas["og:type"];

$jsonResponse["igname"] = $igname;
$jsonResponse["author"] = $author;
$jsonResponse["likes"] = $likes;
$jsonResponse["imageurl"] = $image;
$jsonResponse["posturl"] = $rmetas["og:url"];
$jsonResponse["type"] = substr($ogtype,strpos($ogtype,":"),strlen($ogtype));
$jsonResponse["ogdate"] = $ogdate;
$jsonResponse["ig_timestamp"] = strtotime($ogdate);
$jsonResponse["ig_date"] = date("Y-m-d",strtotime($ogdate));

echo json_encode($jsonResponse);



// converts abbreviated numbers like 20k to 20000
function convertNumberAbbreviation($number) {
	$map = array("k" => 1000,"m" => 1000000,"b" => 1000000000);
	list($value,$suffix) = sscanf($number, "%f%s");
	$final = $value*$map[$suffix];
	return $final;
}



//echo json_encode($rmetas);

/*
{
"og:site_name":"Instagram",
"og:title":"Instagram photo by Chelsea Houska • Apr 27, 2016 at 8:15pm UTC",
"og:image":"https://scontent-lax3-1.cdninstagram.com/t51.2885-15/e35/p480x480/13102521_926387780813742_2035330356_n.jpg?ig_cache_key=MTIzNzg5MTA5NDA2MDgzNzkzMQ%3D%3D.2",
"og:description":"See this Instagram photo by @chelseahouska • 23.8k likes",
"og:url":"https://www.instagram.com/p/BEt32Q0Hawr/",
"og:type":"instapp:photo"
}

*/



