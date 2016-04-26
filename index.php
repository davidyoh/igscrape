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

$medias = $instagram->getMedias($username, 50);

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
echo '<table border = "1">';
echo '<tr>';
echo '<td>id</td>';
echo '<td>time</td>';
echo '<td>type</td>';
echo '<td>imgurl</td>';
echo '<td>caption</td>';
echo '<td>follows</td>';
echo '<td>likes</td>';
echo '<td>comments</td>';
echo '<td>likes%</td>';
echo '<td>comments%</td>';
echo '</tr>';
$igCounter = 0;
  foreach( $medias as $key ) {
  			echo "<tr>";
  			echo "<td>" . $igCounter++ . "</td>";
            echo "<td>" . date('Y-m-d H:i:s', $key->createdTime) . "</td>";
             echo "<td>" . $key->type. "</td>";
            // echo "<td><img width = '50' src = '" . $key->imageThumbnailUrl. "''></td>";
               echo "<td>".$key->imageThumbnailUrl."</td>";
            echo "<td>" . $key->caption. "</td>";
                    echo "<td>" . $follows. "</td>";
             echo "<td>" . $key->likes. "</td>";
            echo "<td>" . $key->comments. "</td>";
              echo "<td>" . round((($key->likes/$follows)* 100),4). "%</td>";
               echo "<td>" . round((($key->comments/$follows)* 100),4). "%</td>";
            echo "</tr>";
         }
echo '</table>';
//echo $medias[0]->imageHighResolutionUrl;
//echo $medias[0]->caption;