<?php 
if(!array_key_exists('firstname', $_POST) || !array_key_exists('email', $_POST) ||
		!array_key_exists('city', $_POST)) {
	header("Location: index.php");
	exit;
}
include('../config/glancrConfig.php');

$language = getConfigValue('language');
putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain("/var/www/html/locale");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?php echo _('setup complete'); ?></title>
  <link rel="stylesheet" type="text/css" href="../config/css/main.css">
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../config/bower_components/foundation-sites/dist/foundation.min.js"></script>
</head>
<body>

<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="../config/assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="../config/assets/glancr_logo.png 57w, ../config/assets/glancr_logo@2x.png 114w, ../config/assets/glancr_logo@2x.png 171w">
    </div>
</header>

<main class="container">
    <section class="row">
        <div class="small-12 columns">
            <p class="instruction__stepper">&nbsp;</p>
            <h2 class="instruction__title"><?php echo _('done');?></h2>
            <p class="instruction__text">
                <?php echo _('setup complete text'); ?>
            </p>

			<form id="form" name="myform" action="activatewlan.php" method="post">
			      <input type="hidden" id="firstname" name="firstname" value="<?php echo $_POST['firstname']?>"/>
			      <input type="hidden" id="email"  name="email" value="<?php echo $_POST['email']?>"/>
			      <input type="hidden" id="city"  name="city" value="<?php echo $_POST['cityId']?>"/>
			</form>
			
<script type="text/javascript">
window.setTimeout(function() {
	document.myform.submit();
}, 5000);
</script>
</body>
</html>