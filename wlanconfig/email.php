<?php 
include('../config/glancrConfig.php');

if(!array_key_exists('connectionType', $_POST) || !array_key_exists('ssid', $_POST) || 
		!array_key_exists('invisibleSsid', $_POST) || !array_key_exists('pass', $_POST) || !array_key_exists('invisibleWlan', $_POST)) {
	header("Location: index.php");
	exit;
} 
setConfigValue('connectionType', $_POST['connectionType']);

if($_POST['connectionType'] == 'wlan') {	
	if($_POST['invisibleWlan']) {
		setConfigValue('wlanName', $_POST['invisibleSsid']);
		setConfigValue('wlanInvisible', '1');
	} else {
		setConfigValue('wlanName', $_POST['ssid']);
		setConfigValue('wlanInvisible', '0');
	}
	setConfigValue('wlanPass', $_POST['pass']);
}

$language = getConfigValue('language');
putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain($basedir ."/locale");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?php echo _('setup step 2');?></title>
  <link rel="stylesheet" type="text/css" href="../config/css/main.css">
  <style type="text/css">
  .ui-autocomplete { 
  	max-height: 200px; 
  	overflow-y: scroll; 
  	overflow-x: hidden;
  }
  .error {
  	border: 2px solid red;
  }
  </style>
  <link rel="stylesheet" type="text/css" href="../config/bower_components/jquery/dist/jquery-ui.min.css">
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery-ui.min.js"></script>
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery.ui.autocomplete.html.js"></script>
  <script type="text/javascript" src="../config/bower_components/foundation-sites/dist/foundation.min.js"></script>
</head>
<body>

<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="../config/assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="../config/assets/glancr_logo.png 57w, ../config/assets/glancr_logo@2x.png 114w, ../config/assets/glancr_logo@2x.png 171w">
    </div>
</header>

<?php 
$email = getConfigValue('email');
$firstname = getConfigValue('firstname');

$cityId = getConfigValue('city');

$servername = "localhost";
$username = "root";
$password = "glancr";
$dbname = "glancr";

$conn = new mysqli($servername, $username, $password, $dbname);

$sql = 'SELECT name FROM owm_cities WHERE id="' . $cityId . '"';

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
} else {
	$row['name'] = "";
}

$conn->close();
?>

<main class="container">
    <section class="row">
        <div class="small-12 columns">
            <p class="instruction__stepper"><?php echo _('step 2 of 2');?></p>
            <h2 class="instruction__title"><?php echo _('who are you?');?></h2>
            <p class="instruction__text">
                
            </p>
            <form class="" action="message.php" method="post" id="emailForm">
            	<input type="hidden" id="cityId" name="cityId" value="<?php echo $cityId;?>"/>
            	<input type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>" placeholder="<?php echo _('what is your first name?')?>">
				<input type="text" name="city" id="city" value="<?php echo $row['name'];?>" placeholder="<?php echo _('where are you living?');?>">
                <?php echo _('email hint');?>
                <input type="email" name="email" id="email" value="<?php echo $email;?>" placeholder="<?php echo _('your email address');?>" autocorrect="off" autocomplete="address-level2">
                <?php echo _('email again hint');?>
                <input type="email" name="againemail" id="againemail" value="" placeholder="<?php echo _('your email address');?>" autocorrect="off" autocomplete="address-level2">                
                <input class="button expanded" type="submit" name="advance" value="<?php echo _('go on');?>" id="submit">
            </form>
            <small>
                
            </small>
        </div>
    </section>
</main>
<script>
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

$('form input[type="text"]').focus(function() {
	$(this).removeClass('error');
	if($(this).attr('id') == 'email') $('#againemail').removeClass('error');
})

$('form').submit(function(event) {
	//error handling
	var error = false;
	
	$('form input[type="text"]').each(function(){
        if ($(this).val().length == 0) {
			$(this).addClass('error');
			error = true;
        }
	});
	if(!validateEmail($('#email').val())) {
		$('#email').addClass('error');
		error = true;
	}
	if($('#email').val() != $('#againemail').val()) {
		$('#againemail').addClass('error');
		error = true;
	}
	if(error) event.preventDefault();;
})



$("#city").autocomplete({
    source: "autocompleteCities.php",
    html: true, 
    minLength: 2,
    change: function(event, ui) {
        if(ui.item) {
    		$(this).val(ui.item.value);
    		$('#cityId').val(ui.item.id);
        } else {
        	$(this).val("");
    		$('#cityId').val("");
        }
   	}
});
</script>
</body>
</html>
