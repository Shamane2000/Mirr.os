<?php
include('glancrConfig.php');

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
  <title><?php echo _('module overview');?></title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="bower_components/foundation-icon-fonts/foundation-icons.css" media="screen" title="no title" charset="utf-8">
  <script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
<style type="text/css">
  .errorMessage {
    position: relative;
    top: -19px;
    font-size: small;
    color: #b00;
    left: 2px;
  }
  table {
    color: black;
  }
  </style>
</head>
<body>
<?php 
$firstname = getConfigValue('firstname');
$modules_content = scandir('/var/www/html/modules');

foreach($modules_content as $file) {
	if(is_dir('/var/www/html/modules/' . $file) && $file != '.' && $file != '..') {
		$modules_available[] = $file;
	}
}
$serverVersions = json_decode(file('http://localhost/server/getVersions.php')[0], true);
$onServer = array_keys($serverVersions);

?>
<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="assets/glancr_logo.png 57w, assets/glancr_logo@2x.png 114w, assets/glancr_logo@2x.png 171w">
    </div>
</header>
<main class="container">
    <section>
        <div class="row">
            <div class="small-12 columns">
                <p class="instruction__stepper"><?php echo _('hi') . ' ' . $firstname;?></p>
                <h2 class="instruction__title"><?php echo _("manage modules");?></h2>
			<table>
				<?php 
				foreach($modules_available as $module_available) { 
					$versionFile = file('/var/www/html/modules/' . $module_available . '/version.txt');
					echo "\t\t\t\t<tr><td>$module_available</td><td>" . $versionFile[0] . "</td>";
					if(array_key_exists($module_available, $serverVersions)) {
						unset($onServer[array_search($module_available, $onServer)]);
					
						if($versionFile[0] < $serverVersions[$module_available]) {
							echo "<td><a href='#' class='deleteModule' id='delete_$module_available'>delete</a></td><td><a class='updateModule' id='update_$module_available' href='#'>update to " . $serverVersions[$module_available] . "</a></td></tr>\n";
						} else {
							echo "<td><a href='#' class='deleteModule' id='delete_$module_available'>delete</a></td><td>up to date</td></tr>\n";
						}
					} else {
						echo "<td><a href='#' class='deleteModule' id='delete_$module_available'>delete</a></td><td>not on server</td></tr>\n";
					}
					
				}	
				foreach($onServer as $moduleOnServer) {
					echo "\t\t\t\t<tr><td>$moduleOnServer</td><td>" . $serverVersions[$moduleOnServer] . "</td><td>not installed</td><td><a class='installModule' id='install_$moduleOnServer' href='#'>install</a></td></tr>\n";
				}
				?>	

			</table>
         		<p><?php echo _("upload module text");?></p>
         		<form>
         			<?php echo _('module zip file') . ': ';?><input type="file" name="moduleZip" id="moduleZip" accept=".zip">
         			<div style="height: 0">
         				<div class="errorMessage" id="fileError"></div>
         			</div>
         			<button id="uploadModule" style="display:none">upload &amp; install</button>
         		</form>
            </div>
    
        </div>  
     </section> 
</main>
</body>
<script>
<?php 
foreach($modules_available as $val) {
	$trimmedModulesAvailable[] = trim($val);
}
echo 'var modulesAvailable = ["' . implode('","', $trimmedModulesAvailable) . '"];';?>

$('.deleteModule').click(function() {
	moduleName = $(this).attr('id').substr(7);
	if(confirm("<?php echo _('really delete module');?> " + moduleName)) {
		$.ajax({
			url: "deleteModule.php",
	       		type: "POST",
	       		data: {module: moduleName},
			success: function(response) {
				console.log(response);
			//	$('#delete_' + moduleName).parents('tr').remove();
				location.reload();
			}
	    });
	}
});

$('.updateModule').click(function() {
	moduleName = $(this).attr('id').substr(7);
	if(confirm("<?php echo _('really update module');?> " + moduleName)) {
		$.ajax({
			url: "deleteModule.php",
	       		type: "POST",
	       		data: {module: moduleName},
			success: function(response) {
				console.log(response);
				$.ajax({
					url: "downloadModule.php",
			       		type: "POST",
			       		data: {module: moduleName},
					success: function(response) {
						console.log(response);
						$.ajax({
							url: "installModule.php",
					       		type: "POST",
					       		data: {module: moduleName},
							success: function(response) {
								console.log(response);
								$('#delete_' + moduleName).parents('tr').remove();
								location.reload();
							}
					    });
						location.reload();
					}
			    });
			}
	    });
	}
});

$('.uploadModule').click(function() {
	if() {
		if(confirm("<?php echo _('really overwrite module');?> " + moduleName)) {
			$.ajax({
				url: "deleteModule.php",
		       		type: "POST",
		       		data: {module: moduleName},
				success: function(response) {
					console.log(response);
				//	$('#delete_' + moduleName).parents('tr').remove();
					uploadAndInstallModule();
				}
		    });
		}
	} else {
		uploadAndInstallModule();
	}	
});

function uploadAndInstallModule()
	var fd = new FormData();
    fd.append("moduleZip", file);
    
	$.ajax({
		url: "uploadModuleZip.php",
	    type: "POST",
	    data: fd,
	    processData: false,
	    contentType: false,
	    success: function(response) {
	        console.log(response);
	        $('#uploadModule').show();
	    },
	    error: function(jqXHR, textStatus, errorMessage) {
	        console.log(errorMessage); // Optional
	    }
	 });
}

$('#moduleZip').change(function() {
	$('#uploadModule').hide();
	
	var file = $('#moduleZip')[0].files[0];
	if(file.type != 'application/zip') {
		$('#moduleZip').val('');
		$('#fileError').text('<?php echo _('not a zip file'); ?>').animate({
		    opacity: 0,
		  }, 3000, function() {
			  $('#fileError').text('');
			  $('#fileError').css('opacity',1);
		  });
				
	} else {
		var fd = new FormData();
	    fd.append("moduleZip", file);
	
	    $.ajax({
	       url: "checkModuleZip.php",
	       type: "POST",
	       data: fd,
	       processData: false,
	       contentType: false,
	       success: function(response) {
	           console.log(response);
	           $('#uploadModule').show();
	       },
	       error: function(jqXHR, textStatus, errorMessage) {
	           console.log(errorMessage); // Optional
	       }
	    });
	}
});

</script>
</html>
