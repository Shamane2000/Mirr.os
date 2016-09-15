<?php 
include('glancrConfig.php');

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
  <title><?php echo _('module overview');?></title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="bower_components/foundation-icon-fonts/foundation-icons.css" media="screen" title="no title" charset="utf-8">
     <style type="text/css">
  .error {
  	border: 2px solid red;
  }
  .reveal a {
 	color: #14243C;
  }
  a {
  	text-decoration: underline;
  }
  .validate {
    position: relative;
    top: -19px;
    font-size: small;
    color: #999;
    left: 2px;
    display: none;
   }
  </style>
  <script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
  <script type="text/javascript" src="js/animateRotate.js"></script>
</head>
<body>

<header class="expanded row">
    <div class="small-12 columns site__title">
        <img src="assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="assets/glancr_logo.png 57w, assets/glancr_logo@2x.png 114w, assets/glancr_logo@2x.png 171w">
    </div>
</header>
<?php 
$firstname = getConfigValue('firstname');
$modules_content = scandir($basedir .'/modules');

$modules_available = [];

foreach($modules_content as $file) {
	if(is_dir($basedir .'/modules/' . $file) && $file != '.' && $file != '..') {
		$modules_available[] = $file;
	}
}

$modules_enabled = file($basedir .'/config/modules_enabled');
?>
<script type="text/javascript">
var modules = [];
var moduleNames = {};
var modulesInUse = []
<?php 
for($i = 0; $i < 6; $i++) {
	$modules = explode("\t", substr($modules_enabled[$i], 0, -1));
	echo  "modules[$i] = [];\n";
	foreach ($modules AS $key => $module) {
		echo "modules[$i][$key] = '$module';\n";
		echo "modulesInUse.push('$module');\n";
	}
}

foreach($modules_available as $module_available) {
	setGetTextDomain($basedir ."/modules/$module_available/locale");
	
	echo "moduleNames['" . $module_available . "'] = '" . _($module_available . '_title') . "';\n";
}

setGetTextDomain($basedir ."/locale");
?>
</script>

<main class="container">
    <section>
        <div class="row">
            <div class="small-12 columns">
                <p class="instruction__stepper"><?php echo _('hi') . ' ' . $firstname;?></p>
                <h2 class="instruction__title"><?php echo _("setup modules");?></h2>
                <p>
                	<?php echo _("basic setup back link");?>
                </p>
            </div>
    
        </div>  
<?php 
for($i = 0; $i < 6; $i++) {
	$modules = explode("\t", substr($modules_enabled[$i], 0, -1));
?>     
        <div class="row module-row">
            <div class="small-12 columns">
                <div class="block__actions">
                    <!-- bearbeiten-Button für die gesamte Zeile -->
                    <input type="hidden" name="size-row" value="<?php echo $i;?>">
                    <button class="block__actions--edit" data-open="gr-modal-size">
                        <span class="fi-widget"></span>
                    </button>
                </div>
                <!-- zwei Hinzufügen-Buttons für neue Widgets -->
                <div class="row">
<?php 
	if(sizeof($modules) > 1) {
		$nextModule = $modules[1];
?>
                    <div class="small-6 columns">
                        <!-- Ein aktiviertes Modul "Uhr" mit Buttons zum bearbeiten und löschen -->
                        <div class="block__add">
<?php 
		if(strlen($modules[0]) > 0 && in_array($modules[0], $modules_available)) {
			setGetTextDomain($basedir ."/modules/" . $modules[0] . "/locale");
?>                        
                            <div class="module">
                                <button class="module__edit" data-open="gr-modal-<?php echo $modules[0];?>">
                                    <span class="fi-pencil"></span>
                                </button>
                                <span class="module__title"><?php echo _($modules[0] . '_title');?></span>
                                <button class="module__delete" href="#">
                                    <span class="fi-trash"></span>
                                </button>
                            </div>
<?php 
		} else {
?>              
							<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">
                                <span class="fi-plus"></span>
                            </button>          
<?php 
		}
?>    
                        </div>
                    </div>
                    <div class="small-6 columns">
<?php 
	} else {
		$nextModule = $modules[0];
?>
					<div class="small-12 columns">
					
<?php 
	}
?>
                        <div class="block__add">
                            <?php 
	if(strlen($nextModule) > 0 && in_array($nextModule, $modules_available)) {
		setGetTextDomain($basedir ."/modules/$nextModule/locale");
		
?>                        
                            <div class="module">
                                <button class="module__edit" data-open="gr-modal-<?php echo $nextModule;?>">
                                    <span class="fi-pencil"></span>
                                </button>
                                <span class="module__title"><?php echo _($nextModule . '_title');?></span>
                                <button class="module__delete" href="#">
                                    <span class="fi-trash"></span>
                                </button>
                            </div>
<?php 
	} else {
?>              
							<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">
                                <span class="fi-plus"></span>
                            </button>          
<?php 
	}
?>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php 
}
setGetTextDomain($basedir ."/locale");
?>
   </section>

    <!-- Modals, die dann geladen werden. -->
     <section>

    	<div class="large reveal" data-reveal id="gr-modal-size" data-animation-in="fade-in" data-animation-out="fade-out" tabindex="1" role="dialog">
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="text-center reveal__title"><?php echo _("config module's width");?></h5>
            <input type="hidden" name="row" id="size-row" value="-1">
                <select id="layout">
                    <optgroup label="Modulebreite">
                        <option value="2"><?php echo _("half width");?></option>
                        <option value="1"><?php echo _("full width");?></option>
                    </optgroup>
                </select>

       	</div>

        <!-- Modal f�r neue Module. Wird getriggert mit  -->
        <div class="large reveal" data-reveal id="gr-modal-add" data-animation-in="fade-in" data-animation-out="fade-out" tabindex="1" role="dialog">
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="text-center reveal__title uppercase"><?php echo _("add new module");?></h5>

            <div class="row small-up-1">
            <input type="hidden" name="newModulCol" id="newModulCol" value="-1">
           	<input type="hidden" name="newModulRow" id="newModulRow" value="-1">
                        	
 <?php 
 foreach ($modules_available AS $module_available) { 
 	setGetTextDomain($basedir ."/modules/$module_available/locale");
 ?>           
                <div class="column module-<?php echo $module_available;?>">
                    <a href="#" class="chooseModule">
                    	<input type="hidden" name="newModulId" class="newModulId" value="<?php echo $module_available;?>">
                    	<div class="small-3 columns">
                            <img width="55" height="55" style="background-color: #ddd;" src="../modules/<?php echo $module_available;?>/assets/icon.svg" alt="logo" />
                        </div>
                        <div class="small-9 columns">  	
                            <h6><?php echo _($module_available . '_title');?></h6>
                            <p><?php echo _($module_available . '_description');?></p>
                        </div>
                    </a>
                </div>
<?php 
 }
 ?>
            </div>
        </div>
<?php 
foreach ($modules_available AS $module_available) { 
	setGetTextDomain($basedir ."/modules/$module_available/locale");
	echo "<link rel=\"stylesheet\" href=\"../modules/" . $module_available . "/backend/styles.css\">\n";
?>
<div class="large reveal" data-reveal id="gr-modal-<?php echo $module_available;?>" data-animation-in="fade-in" data-animation-out="fade-out" tabindex="1" role="dialog">
            <button class="close-button" data-close aria-label="Close modal" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="text-center reveal__title"><?php echo _($module_available . '_title');?></h5>	
<?php 	
	include('../modules/' . $module_available . '/backend/template.php');
?>

</div>
<?php 
	echo '<script>';
	include('../modules/' . $module_available . '/backend/script.js');
	echo '</script>';
	
}

?>
        
       
       <img style="z-index:1006; display: none; top: 300px; position: fixed; left:50%; margin-left: -150px;" id="ok" src="img/OK.png" alt="OK"/> 
       <img style="z-index:1006; display: none; top: 300px; position: fixed; left:50%; margin-left: -150px;" id="error" src="img/ERROR.png" alt="ERROR"/> 
    </section>

</main>

<script type="text/javascript" src="bower_components/foundation-sites/dist/foundation.min.js"></script>
<script type="text/javascript" src="bower_components/foundation-sites/js/foundation.util.mediaQuery.js"></script>

<script type="text/javascript">
    $(document).foundation(); 

   	$(document).ready(function() {
		$.each(modulesInUse, function(index, value) {
			$('.module-' + value).hide();
		});
		
			
	});

   	
   	
	$('input[type="text"]').focus(function() {
		$(this).removeClass('error');
	});
	
    $('.block__actions--edit').click(function() {
    	$('#size-row').val($(this).prev().val());
    	$('#layout').val(modules[$(this).prev().val()].length);
    }); 

    $('#layout').change(function() {
		var row = $('#size-row').val();
		var cols = $(this).val();
		
		if(cols == 1) {
			if(modules[row][1].length > 0) {
				modulesInUse = $.grep(modulesInUse, function(value) {
		        	return value != modules[row][1];
		        });
		        $('.module-' + modules[row][1]).show();
			}
			modules[row].splice(1, 1);
			
			$('.module-row:eq(' + row + ')').find('.small-6:eq(1)').remove();
			$('.module-row:eq(' + row + ')').find('.small-6:eq(0)').removeClass('small-6').addClass('small-12');
		} else {
			modules[row][1] = '';
			$('.module-row:eq(' + row + ') > .small-12:eq(0)').find('.small-12:eq(0)').removeClass('small-12').addClass('small-6');
			$('.module-row:eq(' + row + ') > .small-12:eq(0) > .row > .small-6:eq(0)').after('<div class="small-6 columns"><div class="block__add">'
 				+ '<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">' 
 				+ '<span class="fi-plus"></span></button></div></div>');
		}

		$.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]})
			.done(function() { 
				$('#gr-modal-size').foundation('close');
			});
    });

    $('body').delegate('.block__add--button', 'click', function() {
    	var col;
    	if($(this).parent().parent().hasClass('small-12') || $(this).parent().parent().next().hasClass('small-6')) {
        	col = 0;
    	} else {
        	col = 1;
    	}
		var row = $(this).parent().parent().parent().parent().children().eq(0).children().eq(0).val();
		$("#newModulCol").val(col);
		$("#newModulRow").val(row);
    });

    $('body').delegate('.module__delete', 'click', function() {
    	var col;
    	if($(this).parent().parent().parent().hasClass('small-12') || $(this).parent().parent().parent().next().hasClass('small-6')) {
        	col = 0;
    	} else {
        	col= 1;
    	}
		var row = $(this).parent().parent().parent().parent().parent().children().eq(0).children().eq(0).val();
		var moduleId = modules[row][col];
		
		modules[row][col] = ''; 
		$.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]});
		
        $(this).parent().parent().html('<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">' 
			+ '<span class="fi-plus"></span></button>');

        modulesInUse = $.grep(modulesInUse, function(value) {
        	return value != moduleId;
        });
        $('.module-' + moduleId).show();
    });

    $('.chooseModule').click(function() {
		var row = $("#newModulRow").val();
		var col = $("#newModulCol").val();
		var moduleId = $(this).children('.newModulId').val();

		modules[row][col] = moduleId; 
		
        $('.module-row').eq(row).children().eq(0).children().eq(1).children().eq(col).children().eq(0).html(
        	'<div class="module">' +
            '    <button class="module__edit" data-open="gr-modal-' + moduleId + '">' +
            '        <span class="fi-pencil"></span>' +
            '    </button>' +
            '    <span class="module__title">' + moduleNames[moduleId] + '</span>' +
            '    <button class="module__delete" href="#">' +
            '        <span class="fi-trash"></span>' +
            '    </button>' +
            '</div>');

        modulesInUse.push(moduleId);
        $('.module-' + moduleId).hide();

        $.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]})
		.done(function() { 
			$('#gr-modal-add').foundation('close');
			return false;
		});
    });

</script>
</body>
</html>