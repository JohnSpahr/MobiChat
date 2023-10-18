<?php
session_start();

if(isset($_SESSION['name'])){
    //remove spaces to ensure that text isn't blank
    $spaceRemoved = str_replace(' ', '', $_POST['text']);
    if ($spaceRemoved != "") {
        //if text is not blank, execute code
	    if (filesize("log.html") > 10000000) {
	        //delete log file if size is >10 megabytes (prevents excessive file size from spam, etc.)
	        unlink("log.html");
	    }
	    //add message to log
	    $text = $_POST['text'];
        $fp = fopen("log.html", 'a');
        fwrite($fp, "<div class='msgln'><span>(".date("g:i A").") <b><user>".$_SESSION['name']."</user></b>: ".stripslashes (htmlspecialchars($text))."<br></span></div>");
        fclose($fp);
    }
}
?>
