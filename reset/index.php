<?php 
include('../config/glancrConfig.php');

$language = getConfigValue('language');
putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain('config', GLANCR_ROOT ."/locale");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?php echo _('factory settings');?></title>
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
            <h2 class="instruction__title"><?php echo _('factory settings');?></h2>
            <p class="instruction__text">
                <?php echo _('factory setting text');?>
            </p>
            <form class="" action="doreset.php" method="post">
                <input class="button expanded" type="submit" name="advance" value="<?php echo _('go on');?>">
            </form>
        </div>
    </section>
</main>

</body>
</html>