<?php 
include('../config/glancrConfig.php');

$basedir = substr(__DIR__, 0, strrpos(__DIR__, '/'));
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
  <title><?php echo _('setup step 1');?></title>
  <link rel="stylesheet" type="text/css" href="../config/css/main.css">
  <script type="text/javascript" src="../config/bower_components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="../config/bower_components/foundation-sites/dist/foundation.min.js"></script>
  <style type="text/css">
  label {
  	color: white;
  }
  #languageSelector {
  	position: absolute;
  	right: 10px;
  	cursor: pointer;
  }
  #languageMenu {
  	position: absolute;
  	right: 0;
  	display: none;
  }
  .row {
  	position: relative;
  }
  .languageItem {
    background-color: white;
    padding: 5px 10px;	
    color: #333;
    cursor: pointer;
    border: 1px solid #eee;
   }
   .languageItem:hover {
    background-color: #eee;
    }
   .languageItem img {
   	margin: 5px 10px;
   }
  </style>
</head>
<body>

<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="../config/assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="../config/assets/glancr_logo.png 57w, ../config/assets/glancr_logo@2x.png 114w, ../config/assets/glancr_logo@3x.png 171w">
    </div>
</header>
<?php 
if(isset($_POST['ssid'])) {
	$mywlan[0] = $_POST['invisibleWlan'];
	if($mywlan[0]) {
		$mywlan[1] = $_POST['invisibleSsid'];
	} else {
		$mywlan[1] = $_POST['ssid'];
	}
	$mywlan[2] = $_POST['pass'];
	
	$con[0] = $_POST['connectionType'];
} else {
	$mywlan[0] = getConfigValue('wlanInvisible');
	$mywlan[1] = getConfigValue('wlanName');
	$mywlan[2] = getConfigValue('wlanPass');
	$con[0] = getConfigValue('connectionType');
}

if(trim($mywlan[0])) {
	$invisibleStyle = 'block';
	$visibleStyle = 'none';
} else {
	$visibleStyle = 'block';
	$invisibleStyle = 'none';
}

if($con[0] == 'eth') {
	$wlanStyle = 'none';
} else {
	$wlanStyle = 'block';
}

?>

<!-- Container für den Extra-Rand -->
<main class="container">

    <section class="row">
    	<div id="languageMenu">
    	<?php 
	            	$languagesAvailableFile = file($basedir .'/config/languages_available');
					foreach ($languagesAvailableFile as $languageAvailableRow) {
						$languageParts = explode("\t", $languageAvailableRow);
						$selected = '';
						if($languageParts[0] == $language) {
							$selected = ' selected';
							$flag = trim($languageParts[2]);
						} 
						echo '<div class="languageItem" id="' . $languageParts[0] . '"' . $selected . '><img src="../config/img/00_cctld/' . trim($languageParts[2]) . '.png" alt="' . trim($languageParts[2]) . '"/>  ' . trim($languageParts[1]) . "</div>\n";
					}
				?>
			</select>
    	</div>
    	<div id="languageSelector">
    		<img src="../config/img/00_cctld/<?php echo $flag;?>.png" alt="<?php echo $flag;?>"/>
    	</div>
        <div class="small-12 columns">
            <p class="instruction__stepper"><?php echo _('step 1 of 2');?></p>
            <h2 class="instruction__title"><?php echo _('connecting to wlan')?></h2>
            
            
			<form id="form" action="../wlanconfig/email.php" method="post">
				<input type="radio" id="wlan" name="connectionType" value="wlan"><label for="wlan"> <?php echo _('wireless');?></label>
				<input type="radio" id="eth" name="connectionType" value="eth" disabled="disabled"><label for="eth"> <?php echo _('ethernet');?></label><br>
				<div id="wlanParams" style="display: <?php echo $wlanStyle;?>">
					<p class="instruction__text">
	                	<?php echo _('wlan config text');?>
	            	</p>
					<input type="radio" id="invisibleWlan-0" name="invisibleWlan" value="0"><label for="invisibleWlan-0"> <?php echo _('visible ssid');?></label>
					<input type="radio" id="invisibleWlan-1" name="invisibleWlan" value="1"><label for="invisibleWlan-1"> <?php echo _('invisible ssid');?></label><br>
				      <select id="ssid" name="ssid" style="display: <?php echo $visibleStyle;?>">
					      <optgroup label="<?php echo _('choose wlan');?>">
					      <?php 
					      $wlans = file('../wlans.txt');
				//	      $wlanInRange = false;
					      foreach($wlans as $wlan) {
					      	if(trim($wlan) == trim($mywlan[1])) {
					      		echo "<option selected>$wlan</option>\n";
					//      		$wlanInRange = true;
					      	} else { 
					      		echo "<option>$wlan</option>\n";
					      	}
					      }
					      
					      ?>
					      </optgroup>
				 	  </select>
				 	  <input type="text" name="invisibleSsid"  style="display: <?php echo $invisibleStyle;?>" id="invisibleSsid" value="<?php echo $mywlan[1];?>" placeholder="<?php echo _('invisible ssid');?>" autocorrect="off">
				 	 <input type="text" name="pass" id="password" value="<?php if(isset($mywlan[2])) echo $mywlan[2];?>" placeholder="<?php echo _('input password');?>" autocorrect="off" autocomplete="current-password">
			 	 </div>
                <input class="button expanded" type="submit" name="advance" value="<?php echo _('go on');?>">
			  </form>
 		</div>
    </section>
</main>

</body>
<script>
<?php 					      
exec('ip -f inet -o addr show eth0|cut -d\  -f 7 | cut -d/ -f 1', $ip);
if(is_array($ip) && array_key_exists(0, $ip)) {
	if($ip[0] != '') {
		echo "$('#eth').removeAttr('disabled');\n";		
	} else {
		$con[0] = 'wlan';
		echo "$('label[for=\"eth0\"]').text($('label[for=\"eth0\"]').text() + ' (" . _('insert cable and reload site') . ")');\n";
		echo "$('#wlanParams').show();\n";
	}
} else {
	$con[0] = 'wlan';
	echo "$('label[for=\"eth0\"]').text($('label[for=\"eth0\"]').text() + ' (" . _('insert cable and reload site') . ")');\n";
	echo "$('#wlanParams').show();\n";
}
?>
$('#<?php echo trim($con[0])?>').prop('checked', true);		
$('#invisibleWlan-<?php echo trim($mywlan[0])?>').prop('checked', true);		


$('input:radio[name=connectionType]').change(function() {
	$('#wlanParams').toggle('fast');
})

$('input:radio[name=invisibleWlan]').change(function() {
	$('#ssid').toggle('fast');
	$('#invisibleSsid').toggle('fast');
})

$(document).mousedown(function() {
	$('#languageMenu').hide();
	$('#languageSelector').show();
});		      
$('#languageSelector').mouseup(function() {
	$('#languageMenu').show();
	$('#languageSelector').hide();
});
$('.languageItem').mousedown(function() {
	$.post('../config/setConfigValueAjax.php', {'key':'language', 'value' : $(this).attr('id')})
	.done(function() { 
		$('#form').attr('action', '../wlanconfig/');
		$('#form').submit();
	});
});

$('#ssid').change(function() {
	if($(this).val() == 'glancrInvisbileWLAN' ) {
		$('#invisibleSsid').show('fast');
	} else {
		$('#invisibleSsid').val('');
		$('#invisibleSsid').hide('fast'); 	
	}
});
</script>

</html>
