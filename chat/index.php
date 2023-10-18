<?php
// MobiChat by John Spahr - based on https://github.com/E-H-Q/PHP-chat-system/
session_start ();
function loginForm() {
    echo '
   <div id="loginform">
   <style>.important{color: #FF0000;}</style>
   <form action="index.php" method="post">
        <h1>MobiChat</h1>
        <p>Please enter your name to continue:</p>
        <label for="name">Name:</label>
        <input style="border: 1px solid #474747;" type="text" autofocus="" name="name" id="name" />
        <input type="submit" name="enter" id="enter" value="Enter" />
        <br><br>
        <p class="important"><b>THIS APP IS NOT PRIVATE. DO NOT SHARE ANY PERSONAL INFORMATION.</b></p>
        <br>
        <hr>
        <h3>About MobiChat:</h3>
        <p>This is a simple chat app that allows you to chat with other users of vintage mobile phones.</p>
        <p>The chat log clears after 24 hours of inactivity.</p>
        <p>Although I co-own <a href="http://lunarproject.org" target="_blank">Lunar Project</a>, this app is not a part of it and is its own entity.</p>
        <hr>
        <h3>One More Thing...</h3>
        <p>Please keep conversations civil, the world is already crazy enough as it is. :)</p>
        <hr>
        <img width="24" src="images/chat.png" alt="MobiChat Logo"/>
        <p>Created by <a href="https://github.com/johnspahr/" target="_blank">John Spahr</a>. Based on <a href="https://github.com/E-H-Q/PHP-chat-system/" target="_blank">this app</a>.</p>
   </form>
   </div>
   ';
}

if (isset ($_POST ['enter'])) {
	if (strlen($_POST ['name']) > 15) {
		echo "<span class='error'>Please enter a name under 15 characters.</span>";
	}
	elseif (strlen($_POST ['name']) < 1) {
		echo "<span class='error'>Please enter a name.</span>";
	}
	elseif (ctype_space($_POST ['name'])) {
		echo "<span class='error' id='error'>Please enter a name.</span>";
	}
    elseif ($_POST ['name'] == "GH057") {
        //spectator mode
        $_SESSION ["name"] = stripslashes (htmlspecialchars($_POST ["name"]));
        $fp = fopen ("log.html", "a");
        fclose ($fp);
    }
    else {
        $_SESSION ["name"] = stripslashes (htmlspecialchars($_POST ["name"]));
        if ((time()-filectime( "log.html" )) > 86400) {
            //if 24 hours (86,400 seconds) have elapsed since the last message, clear log
            unlink( "log.html" );
        }
        //update log to show arrival of new user
        $fp = fopen ( "log.html", "a" );
        fwrite ($fp, "<alert><div class='msgln'><i><span>(".date("g:i A").")</span> User " . $_SESSION ['name'] . " has joined the chat session.</i><br></div></alert>");
        fclose ($fp);
    }
}

if (isset ($_GET ["logout"])) {
    if ($_SESSION ['name'] == "GH057") {
        //leave as spectator
        session_destroy ();
        header ("Location: index.php");
    }
    else {
        //update log to reflect that user has left
        $fp = fopen ("log.html", "a");
        fwrite ($fp, "<alert><div class='msgln'><i><span>(".date("g:i A").")</span> User " . $_SESSION ['name'] . " has left the chat session.</i><br></div></alert>");
        fclose ($fp);
        session_destroy ();
        header ("Location: index.php"); //refresh the page and destroy the session
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <!-- meta stuff -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta property="og:title" content="MobiChat by John Spahr" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="http://chat.johnspahr.org" />
    <meta property="og:image" content="http://chat.johnspahr.org/images/chat.png" />
    <meta property="og:description" content="Online chat app for vintage mobile phones." />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Online chat app for vintage mobile phones." />
    <meta name="author" content="John Spahr" />
    <meta charset="UTF-8" />

    <!-- page title -->
    <title>MobiChat by John Spahr</title>

    <!-- link to stylesheet -->
    <link rel="stylesheet" href="main.css" />

    <!-- favicon and theme stuff -->
    <link rel="apple-touch-icon" href="images/chat.png" sizes="180x180" />
    <link rel="icon" href="images/chat.png" sizes="32x32" type="image/png" />
    <link rel="icon" href="images/chat.png" sizes="16x16" type="image/png" />
    <link rel="icon" href="images/chat.ico" type="image/ico" />
    <meta name="theme-color" content="#4458be" />
</head>
<body>
    <?php
    if (! isset ($_SESSION ['name'])) {
        loginForm ();
    } else {
        ?>
    <div id="wrapper">
        <div id="menu">
            <p class="welcome">Howdy, <b><?php echo $_SESSION['name']; ?></b>!</p>
            <p class="logout"><a id="exit" href="#">Exit Chat</a></p>
            <div style="clear: both"></div>
        </div>
        <div id="chatbox"><?php
        if (file_exists ("log.html") && filesize ("log.html") > 0) {
            $handle = fopen ("log.html", "r");
            $contents = fread ($handle, filesize ("log.html"));
            fclose ($handle);
           
            echo $contents;
        }
        ?></div>
        <?php
        if ($_POST ["name"] != "GH057") {
            echo '
            <form name="message" action="">
                <input name="usermsg" autofocus="" spellcheck="true" type="text" id="usermsg" size="63"/> <input name="submitmsg" type="submit" id="submitmsg" value="Send"/>
            </form>';
        }
        else {
            echo ">>> CANNOT SEND MESSAGES WHILE IN SPECTATOR MODE <<<";
        }
        ?>
    </div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>

<script>
    window.onbeforeunload = function(evt) {
    return true;
}
window.onbeforeunload = function(evt) {
    var message = "Are you sure you want to log out?";
    if (typeof evt == "undefined") {
        evt = window.event.srcElement;
    }
    if (evt) {
        evt.returnValue = message;
    }
}
</script>

<script type="text/javascript">
// jQuery Document
$(document).ready(function(){
    var scrollHeight = $("#chatbox").attr("scrollHeight") - 50;
    var scroll = true;
    if (scroll == true) {
        $("#chatbox").animate({ scrollTop: scrollHeight }, "normal");
        load = false;
    }
});
 
//jQuery Document
$(document).ready(function(){
    //If user wants to end session
    $("#exit").click(function(){
        var exit = true;
        if(exit==true){window.location = 'index.php?logout=true';}
    });
});

//If user submits the form
$("#submitmsg").click(function(){
        var clientmsg = $("#usermsg").val();
        $.post("post.php", {text: clientmsg});
        $("#usermsg").attr("value", "");
        loadLog;
    return false;
});

function loadLog(){ //convert from jQuery to JavaScript
    var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 50; //Scroll height before the request
    $.ajax({
        url: "log.html",
        cache: false,
        success: function(html){
            $("#chatbox").html(html); //Insert chat log into the #chatbox div
           
            //Auto-scroll
            var newscrollHeight = $("#chatbox").attr("scrollHeight") - 50; //Scroll height after the request
            if(newscrollHeight > oldscrollHeight){
                $("#chatbox").animate({ scrollTop: newscrollHeight }, "normal"); //Autoscroll to bottom of div
            }
        },
    });
}
 
setInterval (loadLog, 2000);
</script>
<?php
    }
    ?>
    <script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
    <script type="text/javascript">
</script>
</body>
</html>
