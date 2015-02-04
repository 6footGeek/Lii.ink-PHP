<?php
/*
COPYRIGHT 6footGeek.com
This is the original link submission tool

basically a form that takes a URL, submits it to an SQLITE db and then displays the link of a feed on
the index page. Users can then upvote or downvote the link.

HTML ripped from twitbully.com (Simples :D)


            #TODO
            
            * Look at converting the SQLITE shit into PDO objects (should be pretty simple)

            * Make sure to strip Title grab function and append http:// before url so file opens properly (need to check if it has http)

            * Tag system? hashtags or categorys whatever
                - Automatically add a catagory if over 5 hashtags are used

            * Voting system working
                - Get votes based on ip? or just the auth session (How secure does this have to be, e.g)

            * Voting algorithm (YCOMBINATOR)
                    function calculate_score($votes, $item_hour_age, $gravity=1.8) {
                        return ($votes - 1) / pow(($item_hour_age+2), $gravity);
                    }

            * Add pagination.




*/

/*

Regex shit for url checking

*/

    // ini_set('display_errors', 'On');
    // error_reporting(E_ALL);




function processUrl($website) {

 preg_match("/<title>(.+)<\/title>/siU", file_get_contents($website), $matches);
        $title = $matches[1];
        return $title;
}




$regex = '((https?|ftp)\:\/\/)?';
// SCHEME
$regex .= '([a-z0-9+!*(),;?&=$_.-]+(\:[a-z0-9+!*(),;?&=$_.-]+)?@)?';
// User and Pass
$regex .= '([a-z0-9-.]*)\.([a-z]{2,4})';
// Host or IP
$regex .= '(\:[0-9]{2,5})?';
// Port
$regex .= '(\/([a-z0-9+$_-]\.?)+)*\/?';
// Path
$regex .= '(\?[a-z+&$_.-][a-z0-9;:@&%=+\/$_.-]*)?';
// GET Query
$regex .= '(#[a-z_.-][a-z0-9+$_.-]*)?';
// Anchor





function makeContentBox($boxMessage)
{
    echo '<div class="container"><div class="row"><div class="box"><div class="col-lg-12">';
    echo $boxMessage;
    echo '</div></div></div></div>';
}





//make db instance? is it required?
class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('finalDB.db');
    }
}

$db = new MyDB();
//make db

//create table if doesnt exist
$db->exec('CREATE TABLE IF NOT EXISTS `liiink` (
`id`    INTEGER,
`url`   TEXT,
`timesubmitted` INTEGER,
`urltitle` TEXT,
`upvote` INTEGER,
`downvote` INTEGER,
PRIMARY KEY(id)
)');


$website;

function upDownVotes() {
    
    $upDownVotes = '&nbsp;&nbsp;<button class="btn btn-default btn-success btn-xs"><span class="glyphicon glyphicon-chevron-up"></span></button>&nbsp;<button class="btn btn-default btn-danger btn-xs"><span class="glyphicon glyphicon-chevron-down"></span></button>';
return $upDownVotes;
}
//downvotes


// define variables and set to empty values

if ($_SERVER["REQUEST_METHOD"] == "POST") {
                //if post is being set do this
    if (empty($_POST["website"])) {
        $website = "";   //if website field empty then set website to null so nothing entered
            } else {    //if submission not empty
       


         $website = $_POST["website"]; // pass post as website variable
       



        #if doesnt pass regex then fail
                    if (!preg_match("/^$regex$/", $website)) {
                         makeContentBox("The URL is borked..");

                                } 
                                    else  //it has failed and therefore must pass.
                                            {

                                            #passed regex checking so try to grab <title>. if title length smaller than 1 character print original url.
                             
                                        
                                                        // processUrl($website);
                                                        $finalTitleName = processUrl($website);
                                                      
                                                        if (strlen($finalTitleName) < 1) {
                                                            $finalTitleName = $website;
                                                       } 


                                 makeContentBox("The URL Validated!");
                                 $db->exec("INSERT INTO liiink VALUES ( NULL, '$website', (SELECT datetime('now')), '$finalTitleName', 0, 0)"); //insert auto incremement (NULL) $website (URL) and date (select date) *ADDED up/downvotes
                                    header('index.php');
                        }
                    }


            } //close of entire logic

?>


<?php
include('header.php');
?>




<!-- Form for submit url -->

<div class="container">
    <div class="row">
        <div class="box">
            <div class="col-lg-12">


                <!-- new form -->

                <form class="col-md-6 col-md-offset-3 form-signin" method="post" action="<?=$_SERVER['PHP_SELF']?>"> <!--   set process to same page-->
                    <p>
                    <div class="input-group">
                        <div class="input-group-addon">http://</div>
                        <input type="text" class="form-control" id="website"  name="website" placeholder="lii.ink">
<span class="input-group-btn">
<button class="btn btn-default" type="submit">Submit!</button>
</span>
                    </div>
                    </p>


                </form>

                </p>



            </div>
        </div>
    </div>

    <!--    results - have to add pagination it at some point -->

    <div class="row">
        <div class="box">
            <div class="col-lg-12">
                <hr>
                <h2 class="intro-text text-center">What are the <strong>latest liiinks?</strong>
                </h2>
                <hr>

                <hr class="visible-xs">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>URL</th>
                        <th>Date</th>
                        <th>Up/Down</th>
                    </tr>
                    </thead>
                    <tbody> 
<?php
$tablesquery = $db->query("SELECT * FROM liiink ORDER BY rowid DESC");
//display results as table and centered.



 while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)) {

$up = $table['downvote'];
$down = $table['downvote'];
  echo "<tr><td><a href=" . $table['url'] . ">" . $table['urltitle'] . "</a>" . "</td>" . "<td>" . " on " . $table['timesubmitted'] . "</td>" . "<td>" . upDownVotes() . "</td></tr>"; //enter the hyperlinks!!! 



    }

echo "</tbody></table>";
?>



            </div>
        </div>
    </div>
</div>
<!-- /.container -->









<?php
include('footer.php');
?>