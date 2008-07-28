<?php
/*
Plugin Name: AdvancedOptions
Plugin URI: http://www.mountaingrafix.at/
Description: Dieses kleine Plugin erweitert den Adminbereich des Wordpress - Blogs um einige versteckte Einstellungsmöglichkeiten. 
Version: 1.0
Author: MountainGrafix
Author URI: http://www.mountaingrafix.at/
*/

/* ***************************************************************************************************** */

/**
 * Initialisiert das Plugin bzw. die Optionwerte
 * 
 * @author Sascha Schoppengerd
 * @copyright MountainGrafix <http://www.mountaingrafix.at/>
 */
function MG_AdvancedOptionsInit() {
	
	// Wir prüfen, ob der Wert für die Versionsspeicherung
	// bereits in der Datenbank vorhanden ist
	$MG_DeaktivateVersionHistory = get_option('MG_DeaktivateVersionHistory');
	
	if ($MG_DeaktivateVersionHistory === false) {
		update_option('MG_DeaktivateVersionHistory', '0');
	}
	
	// Wir prüfen, ob der Wert für die max. Artikelanzahl
	// bereits in der Datenbank vorhanden ist
	$MG_MaxVersionHistoryFiles = get_option('MG_MaxVersionHistoryFiles');
	
	if ($MG_MaxVersionHistoryFiles === false) {
		update_option('MG_MaxVersionHistoryFiles', '3');
	}
	
	// Wir prüfen, ob der Wert für die Autosave-Einstellung
	// bereits in der Datenbank vorhanden ist
	$MG_DisableAutosave = get_option('MG_DisableAutosave');
	
	if ($MG_DisableAutosave === false) {
		update_option('MG_DisableAutosave', '0');
	}
}

/* ***************************************************************************************************** */

/**
 * Fügt einen zusätzlichen Menüpunkt im ACP ein
 * 
 * @author Sascha Schoppengerd
 * @copyright MountainGrafix <http://www.mountaingrafix.at/>
 */
function MG_AddMenu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', 'AdvancedOptions', 'AdvancedOptions', 0, basename(__FILE__), 'MG_PrintAdminHTML');
	}
}

/* ***************************************************************************************************** */

/**
 * Repräsentiert die zusätzliche Einstellungsseite im ACP
 * 
 * @author Sascha Schoppengerd
 * @copyright MountainGrafix <http://www.mountaingrafix.at/>
 */
function MG_PrintAdminHTML() { 
	
	if (isset($_POST['submit'])) {
		
		// Status für die Versionierung
		update_option('MG_DeaktivateVersionHistory', $_POST['MG_DeaktivateVersionHistory']);
		
		// max. Artikel
		$MG_MaxVersionHistoryFiles = intval(trim($_POST['MG_MaxVersionHistoryFiles']));
		
		if ($MG_MaxVersionHistoryFiles > 0) {
			update_option('MG_MaxVersionHistoryFiles', $MG_MaxVersionHistoryFiles);
		} else {
			update_option('MG_MaxVersionHistoryFiles', 3);
		}
		
		// Autosave
		update_option('MG_DisableAutosave', $_POST['MG_DisableAutosave']);
		
		?> 
		
		<div style="background-color:rgb(207, 235, 247);" id="message" class="updated fade"><p>Die Änderungen wurden erfolgreich gespeichert!</p></div> 
	
	<?php } ?>
	
	<div class="wrap">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=MG_AdvancedOptions.php" method="post">
			<h2>Erweiterte Einstellungen</h2>
			<table class="form-table">
			<tr valign="top">
				<th scope="row">Versionierung</th>
				<td><label for="MG_DeaktivateVersionHistory"><input name="MG_DeaktivateVersionHistory" type="checkbox" id="MG_DeaktivateVersionHistory" value="1" <?php checked('1', get_option('MG_DeaktivateVersionHistory')); ?> />&nbsp;Die Versionierung der einzelnen Artikel <u>deaktivieren</u></label></td>
			</tr>
			<tr valign="top">
				<th scope="row">max. Artikel</th>
				<td><input name="MG_MaxVersionHistoryFiles" type="text" id="MG_MaxVersionHistoryFiles" value="<?php form_option('MG_MaxVersionHistoryFiles'); ?>" maxlength="2" size="2" style="width:1.5em;" /> Artikel speichern</td>
			</tr>
			<tr valign="top">
				<th scope="row">Autosave</th>
				<td><label for="MG_DisableAutosave"><input name="MG_DisableAutosave" type="checkbox" id="MG_DisableAutosave" value="1" <?php checked('1', get_option('MG_DisableAutosave')); ?> />&nbsp;Automatisches speichern der einzelnen Artikel <u>deaktivieren</u></label></td>
			</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="submit" value="Änderungen speichern &raquo;" class="button" />
			</p>
		</form>
	</div>
	
	<?php
}

/* ***************************************************************************************************** */

/**
 * Deaktiviert die Autosave Funktion
 */
function MG_DisableAutosave() {
	wp_deregister_script('autosave');
}

/* ***************************************************************************************************** */

// ... jetzt noch alle Funktionen zuweisen
add_action('init', 'MG_AdvancedOptionsInit');
add_action('admin_menu', 'MG_AddMenu');

// Versionierung der Artikel deaktivieren?
if (!defined('WP_POST_REVISIONS') && get_option('MG_DeaktivateVersionHistory') == '1') {
	define('WP_POST_REVISIONS', false);
} 

// max. Anzahl von Artikeln, die für die Versionierung
// in der Datenbank gespeichert werden sollen
$MG_MaxVersionHistoryFiles = intval(get_option('MG_MaxVersionHistoryFiles'));
if (!defined('WP_POST_REVISIONS') && $MG_MaxVersionHistoryFiles > 0) {
	define('WP_POST_REVISIONS', $MG_MaxVersionHistoryFiles);
}

// Automatisches Speichern der Artikel deaktivieren?
if (get_option('MG_DisableAutosave') == '1') {
	add_action('wp_print_scripts', 'MG_DisableAutosave');
}
?>