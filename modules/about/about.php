<?php

// about.php

$page = <<<EndOfPage
<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

	<div id="aboutpopup" style="width: 520px; height: 300px; overflow: hidden">

	    <div style="padding: 10px; line-height: 150%" >
		<div style="float:left; width:125px; height:175px; border: 1px solid silver; margin: 10px; background-image: url('/../../lib/images/ika.jpg'); background-repeat: no-repeat;background-position: center;"></div>
		<div style="margin: 20px; text-align:center;">
		    <span><img id="aboutlogo" src="/../../lib/images/tiretracker.png" alt="MultiDB" ></span>
		    <p>Developed by Ian K. Armstrong<br /><p>ian.k.armstrong@gmail.com</p>
		</div>

	    </div>

	</div>
    </body>
</html>
EndOfPage;

exit($page);

?>

