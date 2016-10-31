<?php 
include('glancrConfig.php');

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
  <title><?php echo _('module overview');?></title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <link rel="stylesheet" href="bower_components/foundation-icon-fonts/foundation-icons.css" media="screen" title="no title" charset="utf-8">
     <style type="text/css">
  .error {
  	border: 2px solid red;
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
$modules_content = scandir(GLANCR_ROOT .'/modules');

if (!empty(getConfigValue('module_updates'))) {
    $updates_available = unserialize(getConfigValue('module_updates'));
} else {
    $updates_available = [];
}

if (!empty(getConfigValue('system_updates'))) {
    $system_updates_available = getConfigValue('system_updates');
} else {
    $system_updates_available = null;
}

$modules_available = [];

foreach($modules_content as $file) {
	if(is_dir(GLANCR_ROOT .'/modules/' . $file) && $file != '.' && $file != '..') {
		$modules_available[] = $file;
	}
}

if (!file_exists(GLANCR_ROOT .'/config/modules_enabled')) {
    $defaults = file(GLANCR_ROOT .'/config/modules_enabled.default');
    $fp = fopen(GLANCR_ROOT .'/config/modules_enabled', 'w');
    fwrite($fp, implode("", $defaults));
}
$modules_enabled = file(GLANCR_ROOT .'/config/modules_enabled');

?>
<script type="text/javascript">
var modules = [];
var moduleNames = {};
var modulesInUse = [];
<?php 
for($i = 0; $i < 6; $i++) {
    //@FIXME: This throws "undefined offset" notices for full-width modules.
	$modules = explode("\t", substr($modules_enabled[$i], 0, -1));
	echo  "modules[$i] = [];\n";
	foreach ($modules AS $key => $module) {
		echo "modules[$i][$key] = '$module';\n";
		echo "modulesInUse.push('$module');\n";
	}
}

foreach($modules_available as $module_available) {
	setGetTextDomain(GLANCR_ROOT ."/modules/$module_available/locale");
	
	echo "moduleNames['" . $module_available . "'] = '" . _($module_available . '_title') . "';\n";
}

setGetTextDomain(GLANCR_ROOT ."/locale");
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
                <?php
                // Output update button if there are updates available for an installed module. Module names are passed to JS in bottom script tag.
                if(sizeof($updates_available) > 0) : ?>
                    <div id="update-notification">
                        <p><?php echo _("We made some changes.") ?></p>
                        <p><button class="button" onclick='updateModules(<?php echo json_encode($updates_available); ?>)'><?php echo _("Update modules now") ?></button></p>
                    </div>
                    <?php
                endif;
                if(!empty($system_updates_available)) : ?>
                    <div id="system-update-notification">
                        <p><?php echo _("There's a system update available for your Glancr: Version $system_updates_available") ?></p>
                        <p><button class="button" onclick='updateSystem()'><?php echo _("Update system now") ?></button></p>
                <?php
                    endif;
                ?>
            </div>
        </div>  
<?php 
for($i = 0; $i < 6; $i++) {
    //@FIXME: This throws "undefined offset" notices for full-width modules.
	$modules = explode("\t", substr($modules_enabled[$i], 0, -1));
?>     
        <div class="row module-row">
            <div class="small-12 columns">
                <div class="block__actions">
                    <!-- bearbeiten-Button für die gesamte Zeile -->
                    <input type="hidden" name="size-row" value="<?php echo $i;?>">
                    <button class="block__actions--edit" data-open="gr-modal-size">
                        <i class="fi-widget"></i>
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
			setGetTextDomain(GLANCR_ROOT ."/modules/" . $modules[0] . "/locale");
?>                        
                            <div class="module">
                                <button class="module__edit" data-open="gr-modal-<?php echo $modules[0];?>">
                                    <i class="fi-pencil"></i>
                                </button>
                                <span class="module__title"><?php echo _($modules[0] . '_title');?></span>
                                <button class="module__delete" href="#">
                                    <i class="fi-trash"></i>
                                </button>
                            </div>
<?php 
		} else {
?>              
							<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">
                                <i class="fi-plus"></i>
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
		setGetTextDomain(GLANCR_ROOT ."/modules/$nextModule/locale");
		
?>                        
                            <div class="module">
                                <button class="module__edit" data-open="gr-modal-<?php echo $nextModule;?>">
                                    <i class="fi-pencil"></i>
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
                                <i class="fi-plus"></i>
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
setGetTextDomain(GLANCR_ROOT ."/locale");
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

            <div class="moduleManager">
                <a href="https://glancr.de/module" target="_blank" class="button"><?php echo _("find modules") ?></a>
                <button type="button" id="uploadModule" class="button" onclick="openZipUpload()"><?php echo _("install modules") ?>
                </button>
                <input type="file" id="moduleZip" name="moduleZip" accept="application/zip" class="uploadModule__button">
                <div class="uploadModule__error" id="fileError"></div>
            </div>
            <div class="row small-up-1">
            <input type="hidden" name="newModulCol" id="newModulCol" value="-1">
           	<input type="hidden" name="newModulRow" id="newModulRow" value="-1">
                        	
 <?php 
 foreach ($modules_available AS $module_available) { 
 	setGetTextDomain(GLANCR_ROOT ."/modules/$module_available/locale");
 ?>           
                <section class="column module-<?php echo $module_available;?> flex-row">
                    <div class="modulepicker__choosebox">
                        <a href="#" class="chooseModule">
                            <input type="hidden" name="newModulId" class="newModulId" value="<?php echo $module_available;?>">
                            <div class="small-3 columns modulepicker__imgwrap">
                                <img width="100" height="100" src="../modules/<?php echo $module_available;?>/assets/icon.svg" alt="logo" />
                            </div>
                            <div class="small-9 columns">
                                <h6><?php echo _($module_available . '_title');?></h6>
                                <p><?php echo _($module_available . '_description');?></p>
                            </div>
                        </a>
                    </div>

                    <div class="modulepicker__deletebox">
                        <a href="#" data-deleteModule="<?php echo $module_available ?>">
                            <i class="fi-trash"></i>
                            <input type="hidden" name="deleteModuleId" class="deleteModuleId" value="<?php echo $module_available;?>" />
                        </a>
                    </div>
                    <div class="modulepicker__confirmdelete" data-deleteModule-confirmbox="<?php echo $module_available ?>">
                        <div class="modulepicker__confirmdelete--inner">

                            <?php
                                setGetTextDomain(GLANCR_ROOT ."/locale");
                            ?>

                            <button type="button" class="alert button" data-deleteModule-cancel="<?php echo $module_available ?>"><?php echo _("cancel") ?><i class="fi-x"></i></button>
                            <button type="button" class="success button" data-deleteModule-confirm="<?php echo $module_available ?>"><?php echo _("delete") ?><i class="fi-check"></i></button>
                        </div>
                    </div>
                </section>
<?php 
 }
 ?>
            </div>
        </div>
<?php 
foreach ($modules_available AS $module_available) { 
	setGetTextDomain(GLANCR_ROOT ."/modules/$module_available/locale");
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

<script type="text/javascript" src="js/backend-scripts.js"></script>
<script type="text/javascript">
    $(document).foundation();

    // Generate localized strings for JS output.
    <?php
    setGetTextDomain(GLANCR_ROOT ."/locale");

    echo "var LOCALE = " . json_encode(
            [
                "deleteSuccess" => _("module deleted"),
                "notZip" => _('not a zip file'),
                "overwriteModule" => _('really overwrite module?'),
                "internalError" => _('there was a server error: '),
                "confirmModuleUpdates" => _('do you really want to update all modules?'),
                "confirmSystemUpdate" => _('do you really want to update mirr.OS?'),
                "updatingSystemMessage" => _('downloading and installing system update ...'),
                "updatesSuccess" => _('all updates were successful!')
            ]
        ) . ";"
    ?>
</script>
</body>
</html>