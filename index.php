<?
require __DIR__ . '/vendor/autoload.php';
use InstagramScraper\InstagramScraper;

$instagram = new InstagramScraper();

if(isset($_GET['username'] )) 
{
    $username =  htmlspecialchars($_GET["username"]);
}
else {
    $username = "ipsy";
}

if(isset($_GET['mediacount'] )) 
{
    $mediacount =  $_GET["mediacount"];
}
else {
    $mediacount = "50";
}

if(isset($_GET['type'] )) 
{
    $type =  $_GET["type"];
}
else {
    $type = "default";
}


//http://safe-ocean-57906.herokuapp.com/?username=ipsy&mediacount=200


$account = $instagram->getAccount($username);
/*
Available properties: 
    $username;
    $followsCount;
    $followedByCount;
    $profilePicUrl;
    $id;
    $biography;
    $fullName;
    $mediaCount;
    $isPrivate;
    $externalUrl;
*/
$follows= $account->followedByCount;



//echo 'folllow: ' . $follows;

$medias = $instagram->getMedias($username, $mediacount);

/*
Available properties: 
    $id;
    $createdTime;
    $type;
    $link;
    $imageLowResolutionUrl;
    $imageThumbnailUrl;
    $imageStandardResolutionUrl;
    $imageHighResolutionUrl;
    $caption;
    $videoLowResolutionUrl;
    $videoStandardResolutionUrl;
    $videoLowBandwidthUrl;
*/
$textresponse = "";



$jsonres = [];


// Counters
$iglike=0;
$igcomments=0;
$igCounter = 0;

foreach( $medias as $key ) {
      if ($igCounter>=$mediacount) break;
      $igCounter++;
      $iglike += $key->likes;
      $igcomments += $key->comments;

}


$iglikeavg = $iglike/$igCounter;
$igcommentsavg = $igcomments/$igCounter;
$avgLikes = round(($iglike/($follows*$igCounter))*100,4);
$avgComments = round(($igcomments/($follows*$igCounter))*100,4);

$jsonres['username'] = $username;
$jsonres['totalPosts'] = $igCounter;
$jsonres['avgLikes'] = $avgLikes;
$jsonres['avgComments'] = $avgComments;
$jsonres['followers'] = $follows;
$jsonres['totalLikes'] = $iglike;
$jsonres['totalComments'] = $igcomments;

$textresponse .= '<h2>' . $username . '</h2>';
$textresponse .= '<h3>Followers: ' . $follows . '</h3>';
$textresponse .= '<p><b>Total Posts:</b> ' . $igCounter;
$textresponse .= ' | <b>Total Likes:</b> <u>' . $iglike . '</u>';
$textresponse .= ' | <b>Total Comments:</b> <u>' . $igcomments . '</u></p>';
$textresponse .= '<p><b>Avg Likes and Avg Comments:</b><u>' . $avgLikes . '%</u> | <u>' . $avgComments. '%</u> </p>';
$textresponse .= '<table border = "1">';
$textresponse .= '<tr>';
$textresponse .= '<td>id</td>';
$textresponse .= '<td>time</td>';
$textresponse .= '<td>type</td>';
$textresponse .= '<td>imgurl</td>';
$textresponse .= '<td>caption</td>';
$textresponse .= '<td>follows: '. $follows . '</td>';
$textresponse .= '<td>likes: ' . round($iglikeavg,1) . '</td>';
$textresponse .= '<td>comments: ' . round($igcommentsavg,1) . '</td>';
$textresponse .= '<td>likes%</td>';
$textresponse .= '<td>comments%</td>';
$textresponse .= '</tr>';




$igCounter = 0;
  foreach( $medias as $key ) {
        if ($igCounter>=$mediacount) break;
        $igCounter++;

   		$textresponse .= "<tr>";
		$textresponse .= "<td>" . $igCounter . "</td>";
        $textresponse .= "<td>" . date('Y-m-d H:i:s', $key->createdTime) . "</td>";
        $textresponse .= "<td>" . $key->type. "</td>";
        $textresponse .= "<td>".$key->imageThumbnailUrl."</td>";
        $textresponse .= "<td>" . $key->caption. "</td>";
        $textresponse .= "<td>" . $follows. "</td>";
        $textresponse .= "<td>" . $key->likes. "</td>";
        $textresponse .= "<td>" . $key->comments. "</td>";
        $textresponse .= "<td>" . round((($key->likes/$follows)* 100),4). "%</td>";
        $textresponse .= "<td>" . round((($key->comments/$follows)* 100),4). "%</td>";
        $textresponse .= "</tr>";


      }

$textresponse .= '</table>';

//echo $medias[0]->imageHighResolutionUrl;
//echo $medias[0]->caption;
if ($type=="default"){
echo $textresponse;
}
else if ($type == "json") {
  echo json_encode($jsonres);
}

else if ($type == "chart") {

}

else {
  echo "invalid type";
}