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

//?username=fabfitfun&mediacount=200
$more10 = '/?username='. $username . '&mediacount=10';
$more20 = '/?username='. $username . '&mediacount=20';
$more30 = '/?username='. $username . '&mediacount=30';
$more50 = '/?username='. $username . '&mediacount=50';

$textresponse .= '<h2>' . $username . '</h2>';
$textresponse .= '<h3>';
$textresponse .= '<a href = "'.  $more10  .'">10 Posts</a> | ';
$textresponse .= '<a href = "'.  $more20  .'">20 Posts</a> | ';
$textresponse .= '<a href = "'.  $more30  .'">30 Posts</a> | ';
$textresponse .= '<a href = "'.  $more50  .'">50 Posts</a> | ';
$textresponse .= 'Followers: ' . $follows;
$textresponse .= '</h3>';
$textresponse .= '<p><b>Total Posts:</b> ' . $igCounter;
$textresponse .= ' | <b>Total Likes:</b> <u>' . $iglike . '</u>';
$textresponse .= ' | <b>Total Comments:</b> <u>' . $igcomments . '</u></p>';
$textresponse .= '<p><b>Avg Likes and Avg Comments:</b><u>' . $avgLikes . '%</u> | <u>' . $avgComments. '%</u> </p>';


$textresponse .= '<p class="bg-info">Click on the header cells to sort</p>';


$textresponse .= '<table class="table" id="simpleTable" >';
$textresponse .= '<thead>';
$textresponse .= '<tr>';
$textresponse .= '<th  data-sort="int" >id</th>';

$textresponse .= '<th>time</th>';
$textresponse .= '<th>type</th>';
$textresponse .= '<th>imgurl</th>';
$textresponse .= '<th>caption</th>';
$textresponse .= '<th data-sort="int">follows: '. $follows . '</th>';
$textresponse .= '<th data-sort="int">likes: ' . round($iglikeavg,1) . '</th>';
$textresponse .= '<th data-sort="int">comments: ' . round($igcommentsavg,1) . '</th>';
$textresponse .= '<th data-sort="float" >likes%</th>';
$textresponse .= '<th data-sort="float" >comments%</th>';
$textresponse .= '<th>id</th>';
$textresponse .= '</thead>';
$textresponse .= '</tr><tbody>';

// table array

//

$igCounter = 0;
  foreach( $medias as $key ) {
        if ($igCounter>=$mediacount) break;

        $igCounter++;
   		$textresponse .= "<tr>";
		$textresponse .= "<td>" . $igCounter . "</td>";

        $textresponse .= "<td>" . date('Y-m-d H:i:s', $key->createdTime) . "</td>";

        $textresponse .= "<td>" . $key->type. "</td>";
        $textresponse .= "<td><img src = '".$key->imageThumbnailUrl."' border = 0></td>";
        $textresponse .= "<td>" . $key->caption. "</td>";
        $textresponse .= "<td>" . $follows. "</td>";
        $textresponse .= "<td>" . $key->likes. "</td>";
        $textresponse .= "<td>" . $key->comments. "</td>";
        $textresponse .= "<td>" . round((($key->likes/$follows)* 100),4). "%</td>";
        $textresponse .= "<td>" . round((($key->comments/$follows)* 100),4). "%</td>";
                    $textresponse .= "<td>" . $key->id . "</td>";
        $textresponse .= "</tr>";


        //lets put this into an array



      }

$textresponse .= '</tbody></table>';

//echo $medias[0]->imageHighResolutionUrl;
//echo $medias[0]->caption;
if ($type=="default"){
?>
<!doctype html><html lang=en-us><meta charset=utf-8>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
  <script src="/stupidtable.min.js?dev"></script>
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<div class="row">
<?
echo $textresponse;

?>
</div>

<footer class="footer">
      <div class="container">
        <p class="text-muted">
          <a href = "/?username=ipsy&mediacount=50">Ipsy</a> |
          <a href = "/?username=fabfitfun&mediacount=50">FabFitFun</a> |
          <a href = "/?username=birchbox&mediacount=50">BirchBox</a> |
          <a href = "/?username=lootcrate&mediacount=50">LootCrate</a> |


        </p>
      </div>
    </footer>

</div>
<script>
$(document).ready(function() 
    { 
         $("#simpleTable").stupidtable();
    } 
); 
    
</script>
</body>
</html>

<?
}
else if ($type == "json") {
  echo json_encode($jsonres);
}

else if ($type == "chart") {

}

else {
  echo "invalid type";
}