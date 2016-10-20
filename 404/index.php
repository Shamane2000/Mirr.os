<?php 
include('../config/glancrConfig.php');

$language = getConfigValue('language');

putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain(GLANCR_ROOT ."/locale");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>404</title>
  <link rel="stylesheet" type="text/css" href="../config/css/main.css">
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../config/bower_components/foundation-sites/dist/foundation.min.js"></script>
</head>
<body>

<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="../config/assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="../config/assets/glancr_logo.png 57w, ../config/assets/glancr_logo@2x.png 114w, ../config/assets/glancr_logo@3x.png 171w">
    </div>
</header>

<!-- Container für den Extra-Rand -->
<main class="container">
    <section class="row">
        <div class="small-12 columns">
            <p class="instruction__stepper"><?php echo _('sorry');?>,</p>
            <h2 class="instruction__title"><?php echo _('404');?></h2>
            <p class="instruction__text">
                <a href="../config/"><?php echo _('back to setup');?></a>
            </p>

 		</div>
    </section>
</main>

</body>
</html>
