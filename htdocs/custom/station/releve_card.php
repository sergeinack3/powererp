<?php
/* Copyright (C) 2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *   	\file       releve_card.php
 *		\ingroup    station
 *		\brief      Page to create/edit/view releve
 */

//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');				// Do not create database handler $db
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');				// Do not load object $user
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');				// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');				// Do not load object $langs
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');		// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');		// Do not check injection attack on POST parameters
//if (! defined('NOCSRFCHECK'))              define('NOCSRFCHECK', '1');				// Do not check CSRF attack (test on referer + on token).
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');				// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');				// Do not check style html tag into posted data
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');				// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');				// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  	// Do not load ajax.lib.php library
//if (! defined("NOLOGIN"))                  define("NOLOGIN", '1');					// If this page is public (can be called outside logged session). This include the NOIPCHECK too.
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');					// Do not check IP defined into conf $powererp_main_restrict_ip
//if (! defined("MAIN_LANG_DEFAULT"))        define('MAIN_LANG_DEFAULT', 'auto');					// Force lang to a particular value
//if (! defined("MAIN_AUTHENTICATION_MODE")) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined("NOREDIRECTBYMAINTOLOGIN"))  define('NOREDIRECTBYMAINTOLOGIN', 1);		// The main.inc.php does not make a redirect if not logged, instead show simple error message
//if (! defined("FORCECSP"))                 define('FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');		// Force use of CSRF protection with tokens even for GET
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');				// Disable browser notification
//if (! defined('NOSESSION'))     		     define('NOSESSION', '1');				    // Disable session

// Load Powererp environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formprojet.class.php';
dol_include_once('/station/class/releve.class.php');
dol_include_once('/station/lib/station_releve.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("station@station", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'relevecard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Releve($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->station->dir_output . '/temp/massgeneration/' . $user->id;
$hookmanager->initHooks(array('relevecard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch optionals attributes and labels
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
	if (GETPOST('search_' . $key, 'alpha')) {
		$search[$key] = GETPOST('search_' . $key, 'alpha');
	}
}

if (empty($action) && empty($id) && empty($ref)) {
	$action = 'view';
}

// Load object
include DOL_DOCUMENT_ROOT . '/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 1;
if ($enablepermissioncheck) {
	$permissiontoread = $user->rights->station->releve->read;
	$permissiontoadd = $user->rights->station->releve->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = $user->rights->station->releve->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
	$permissionnote = $user->rights->station->releve->write; // Used by the include of actions_setnotes.inc.php
	$permissiondellink = $user->rights->station->releve->write; // Used by the include of actions_dellink.inc.php
	$permissiontosendapprobation = $user->rights->station->releve->send_approbation; // Used by the include of actions_dellink.inc.php
	$permissiontoapprove = $user->rights->station->releve->approved; // Used by the include of actions_dellink.inc.php
	$permissiontodesapprove = $user->rights->station->releve->desapproved; // Used by the include of actions_dellink.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
	$permissiontodelete = 1;
	$permissionnote = 1;
	$permissiondellink = 1;
	$permissiontosendapprobation = 1;
	$permissiontodesapprove = 1;
	$permissiontoapprove = 1;
}

$upload_dir = $conf->station->multidir_output[isset($object->entity) ? $object->entity : 1] . '/releve';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (isset($object->status) && ($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->element, $object->id, $object->table_element, '', 'fk_soc', 'rowid', $isdraft);
if (empty($conf->station->enabled)) accessforbidden();
if (!$permissiontoread) accessforbidden();


/*
* Actions
*/
if (isset($_POST['index_releve'])) {
	$index_fin = $_POST['index_releve'];
	if ($index_fin >= $object->index_debut) {
		$object->getclos($index_fin);
		$object->status = $object::STATUS_CLOS;
		$verif = $object->clos($user);
		print '<script type="text/JavaScript"> document.location.replace("' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '"); </script>';
	} else {
		$errorMsg1 = $langs->trans('error_clos_quart');
		setEventMessages($errorMsg1, '', 'errors');
	}
}

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
	setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
	$error = 0;

	$backurlforlist = dol_buildpath('/station/releve_list.php', 1);

	if (empty($backtopage) || ($cancel && empty($id))) {
		if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
			if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
				$backtopage = $backurlforlist;
			} else {
				$backtopage = dol_buildpath('/station/releve_card.php', 1) . '?id=' . ((!empty($id) && $id > 0) ? $id : '__ID__');
			}
		}
	}

	$triggermodname = 'STATION_RELEVE_MODIFY'; // Name of trigger action code to execute when we modify record

	// Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
	include DOL_DOCUMENT_ROOT . '/core/actions_addupdatedelete.inc.php';

	// Actions when linking object each other
	include DOL_DOCUMENT_ROOT . '/core/actions_dellink.inc.php';

	// Actions when printing a doc from card
	include DOL_DOCUMENT_ROOT . '/core/actions_printing.inc.php';

	// Action to move up and down lines of object
	//include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

	// Action to build doc
	include DOL_DOCUMENT_ROOT . '/core/actions_builddoc.inc.php';

	if ($action == 'set_thirdparty' && $permissiontoadd) {
		$object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
	}
	if ($action == 'classin' && $permissiontoadd) {
		$object->setProject(GETPOST('projectid', 'int'));
	}

	// Actions to send emails
	$triggersendname = 'STATION_RELEVE_SENTBYMAIL';
	$autocopy = 'MAIN_MAIL_AUTOCOPY_RELEVE_TO';
	$trackid = 'releve' . $object->id;
	include DOL_DOCUMENT_ROOT . '/core/actions_sendmails.inc.php';
}



/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Releve");
$help_url = '';
llxHeader('', $title, $help_url);

// Example : Adding jquery code
// print '<script type="text/javascript">
// jQuery(document).ready(function() {
// 	function init_myfunc()
// 	{
// 		jQuery("#myid").removeAttr(\'disabled\');
// 		jQuery("#myid").attr(\'disabled\',\'disabled\');
// 	}
// 	init_myfunc();
// 	jQuery("#mybutton").click(function() {
// 		init_myfunc();
// 	});
// });
// </script>';


// Part to create
if ($action == 'create') {
	if (empty($permissiontoadd)) {
		accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
		exit;
	}

	print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Releve")), '', 'object_' . $object->picto);

	print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
	print '<input type="hidden" name="token" value="' . newToken() . '">';
	print '<input type="hidden" name="action" value="add">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
	}

	print dol_get_fiche_head(array(), '');

	// Set some default values
	//if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

	print '<table class="border centpercent tableforfieldcreate">' . "\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT . '/custom/station/tpl/commonfields_add.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_add.tpl.php';

	$sqlGetPom = "SELECT pom.rowid, pom.station FROM " . MAIN_DB_PREFIX . "station_pompiste pom";
	$sqlGetPom .= " JOIN " . MAIN_DB_PREFIX . "user u ON pom.ref = u.login";
	$sqlGetPom .= " WHERE u.rowid = " . $user->id;
	$resqlGetPom = $db->query($sqlGetPom);
	$pomid = $db->fetch_object($resqlGetPom);

	$sqlGetPompe = "SELECT pompe.rowid, pompe.ref FROM " . MAIN_DB_PREFIX . "station_pompe pompe";
	$sqlGetPompe .= " JOIN " . MAIN_DB_PREFIX . "station_cuve cu ON pompe.cuve = cu.rowid";
	$sqlGetPompe .= " JOIN " . MAIN_DB_PREFIX . "station_stations s ON cu.station = s.rowid";
	$sqlGetPompe .= " WHERE s.rowid = " . $pomid->station;
	$resqlGetPompe = $db->query($sqlGetPompe);
	// $pompeArr = array();
	while ($pompeid = $db->fetch_object($resqlGetPompe)) {
		$rowid = $pompeid->rowid;
		$ref = $pompeid->ref;
		$pompeArr[$rowid] = $ref;
	}

	print ' <tr class="field_pompe">
				<td class="titlefieldcreate fieldrequired">' . $langs->trans('Pompe') . '</td>
				<td class="valuefieldcreate">' . $form->selectarray("pompe", $pompeArr, $rowid) . '</td>
			</tr>';

	print ' <tr class="field_pompiste">
				<td class="titlefieldcreate fieldrequired">' . $langs->trans('Pompiste') . '</td>
				<td class="valuefieldcreate">' . $form->selectForForms("Pompiste:custom/station/class/pompiste.class.php ::status = 1 AND rowid = " . (int) $pomid->rowid, 'pompiste', (int) $pomid->rowid, '', '', '', '', '', '', 0) . '</td>
			</tr>';

	print ' <tr class="field_station">
				<td class="titlefieldcreate fieldrequired">' . $langs->trans('Station') . '</td>
				<td class="valuefieldcreate">' . $form->selectForForms("Stations:custom/station/class/stations.class.php ::status = 1 AND rowid = " . (int) $pomid->station, 'station', (int) $pomid->station, '', '', '', '', '', '', 0) . '</td>
			</tr>';



	print '</table>' . "\n";

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel("Create");

	print '</form>';
	// dol_set_focus('input[name="station"]');
}

// Part to edit record
if (($id || $ref) && $action == 'edit') {
	print load_fiche_titre($langs->trans("Releve"), '', 'object_' . $object->picto);

	print '<form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
	print '<input type="hidden" name="token" value="' . newToken() . '">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="' . $object->id . '">';
	if ($backtopage) {
		print '<input type="hidden" name="backtopage" value="' . $backtopage . '">';
	}
	if ($backtopageforcancel) {
		print '<input type="hidden" name="backtopageforcancel" value="' . $backtopageforcancel . '">';
	}

	print dol_get_fiche_head();

	print '<table class="border centpercent tableforfieldedit">' . "\n";

	// Common attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_edit.tpl.php';

	// Other attributes
	include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_edit.tpl.php';

	print '</table>';

	print dol_get_fiche_end();

	print $form->buttonsSaveCancel();

	print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
	$res = $object->fetch_optionals();

	$head = relevePrepareHead($object);
	print dol_get_fiche_head($head, 'card', $langs->trans("Releve"), -1, $object->picto);

	$formconfirm = '';

	// Confirmation to delete
	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteReleve'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
	}
	// Confirmation to delete line
	if ($action == 'deleteline') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id . '&lineid=' . $lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
	}
	// Clone confirmation
	if ($action == 'clone') {
		// Create an array for form
		$formquestion = array();
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
	}

	// Confirmation of action xxxx
	if ($action == 'xxx') {
		$formquestion = array();
		/*
		$forcecombo=0;
		if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
		$formquestion = array(
			// 'text' => $langs->trans("ConfirmClone"),
			// array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
			// array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
			// array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
		);
		*/
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
	}

	// Call Hook formConfirm
	$parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
	$reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
	if (empty($reshook)) {
		$formconfirm .= $hookmanager->resPrint;
	} elseif ($reshook > 0) {
		$formconfirm = $hookmanager->resPrint;
	}

	// Print form confirm
	print $formconfirm;


	// Object card
	// ------------------------------------------------------------

	// Common attributes
	//$keyforbreak='fieldkeytoswitchonsecondcolumn';	// We change column just before this field
	//unset($object->fields['fk_project']);				// Hide field already shown in banner
	//unset($object->fields['fk_soc']);					// Hide field already shown in banner
	if ($action == 'confirm_compare') {

		$sql_rel_ctrl = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'station_relevedecontrole WHERE quart = ' . $object->quart . ' AND pompe = ' . $object->pompe . ' AND pompiste = ' . $object->pompiste . ' AND only_date_creation = "' . date('Y-m-d', $object->only_date_creation) . '"';
		$resql_rel_ctrl = $db->query($sql_rel_ctrl);
		$rel_ctrl = $db->fetch_object($resql_rel_ctrl);

		// var_dump($rel_ctrl);
		// die;

		print '<div class="fichecenter">';
		print '<style>
					.red {
						background: #F7502F;
						color: #000;
					}
				</style>';

		print '<div class="fichehalfleft">';
		print '<div class="underbanner clearboth"></div>';
		print '<table class="border centpercent tableforfield">' . "\n";
		print '<tr class="liste_titre">
					<td class="center" colspan=2>' . $object->ref . '</td>
				</tr>';
		include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_view.tpl.php';
		print '</table>';
		print '</div>';

		if ($db->num_rows($resql_rel_ctrl) > 0) {
			# code...

			print '<div class="fichehalfright">';
			// print '<div class="underbanner clearboth"></div>';
			print '<table class="border centpercent tableforfield">' . "\n";
			print '<tbody>';
			print '<tr class="liste_titre">
					<td class="center" colspan=2>' . $rel_ctrl->ref . '</td>
				</tr>';
			$class = $rel_ctrl->index_debut == $object->index_debut ? "field_index_debut" : "field_index_debut red";
			print '<tr class="' . $class . '">
					<td class="titlefield fieldname_index_debut">' . $langs->trans('index_debut') . '</td>
					<td class="valuefield fieldname_index_debut">' . $rel_ctrl->index_debut . '</td>
				</tr>';
			$class = $rel_ctrl->index_fin == $object->index_fin ? "field_index_fin" : "field_index_fin red";
			print '<tr class="' . $class . '">
					<td class="titlefield fieldname_index_fin">' . $langs->trans('index_fin') . '</td>
					<td class="valuefield fieldname_index_fin">' . $rel_ctrl->index_fin . '</td>
				</tr>';
			$class = $rel_ctrl->qty == $object->qty ? "field_qty" : "field_qty red";
			print '<tr class="' . $class . '">
					<td class="titlefield fieldname_qty">' . $langs->trans('qty') . '</td>
					<td class="valuefield fieldname_qty">' . $rel_ctrl->qty . '</td>
				</tr>';
			$class = $rel_ctrl->vente == $object->vente ? "field_vente" : "field_vente red";
			print '<tr class="' . $class . '">
					<td class="titlefield fieldname_vente">' . $langs->trans('sell') . '</td>
					<td class="valuefield fieldname_vente">' . $rel_ctrl->vente . '</td>
				</tr>';
			$getQuart = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'station_quarts WHERE rowid = ' . $rel_ctrl->quart;
			$resQuart = $db->query($getQuart);
			$quart = $db->fetch_object($resQuart);
			print '<tr class="field_quart">
					<td class="titlefield fieldname_quart">' . $langs->trans('quart') . '</td>
					<td class="valuefield fieldname_quart">
						<a href="/powererp/htdocs/custom/station/quarts_card.php?id=' . $rel_ctrl->quart . '" class="classfortooltip">
							<img src="/powererp/htdocs/custom/station/img/object_quarts.png" class="paddingright classfortooltip">' . $quart->ref . '
						</a>
					</td>
				</tr>';
			$getpompe = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'station_pompe WHERE rowid = ' . $rel_ctrl->pompe;
			$respompe = $db->query($getpompe);
			$pompe = $db->fetch_object($respompe);
			print '<tr class="field_pompe">
					<td class="titlefield fieldname_pompe">' . $langs->trans('Pompe') . '</td>
					<td class="valuefield fieldname_pompe">
						<a href="/powererp/htdocs/custom/station/pompe_card.php?id=' . $rel_ctrl->pompe . '" class="classfortooltip">
							<img src="/powererp/htdocs/custom/station/img/object_pompe.png" class="paddingright classfortooltip">' . $pompe->ref . '
						</a>
					</td>
				</tr>';
			$getpompiste = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'station_pompiste WHERE rowid = ' . $rel_ctrl->pompiste;
			$respompiste = $db->query($getpompiste);
			$pompiste = $db->fetch_object($respompiste);
			print '<tr class="field_pompiste">
					<td class="titlefield fieldname_pompiste">' . $langs->trans('Pompiste') . '</td>
					<td class="valuefield fieldname_pompiste">
						<a href="/powererp/htdocs/custom/station/pompiste_card.php?id=' . $rel_ctrl->pompiste . '" class="classfortooltip">
							<img src="/powererp/htdocs/custom/station/img/object_pompiste.png" class="paddingright classfortooltip">
							' . $pompiste->ref . '
						</a>
					</td>
				</tr>';
			$getstation = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'station_stations WHERE rowid = ' . $rel_ctrl->station;
			$resstation = $db->query($getstation);
			$station = $db->fetch_object($resstation);
			print '<tr class="field_station">
					<td class="titlefield fieldname_station">
						<span style="padding: 0px; padding-right: 3px !important;">' . $langs->trans('Station') . '</span>
						<span class="classfortooltip" style="padding: 0px; padding: 0px; padding-right: 3px !important;" title="Specifier la station">
							<span class="fas fa-info-circle  em088 opacityhigh" style=" vertical-align: middle; cursor: help"></span>
						</span>
					</td>
					<td class="valuefield fieldname_station">
						<a href="/powererp/htdocs/custom/station/stations_card.php?id=' . $rel_ctrl->station . '" class="classfortooltip">
							<img src="/powererp/htdocs/custom/station/img/object_stations.png" class="paddingright classfortooltip">' . $station->ref . '
						</a>
					</td>
				</tr>';
			print '</table>';
			print '</div>';
		} else {
			print '<div class="fichehalfright">';
			print '		<div style="border:1px solid #E0E0E0;height:20rem;display:flex;justify-content:center;align-items:center;align-self:center;">
							<h2>' . $langs->trans('error_rel_ctrl') . '</h2>
						</div>	
					</div>';
		}
	} else {
		$linkback = '<a href="' . dol_buildpath('/station/releve_list.php', 1) . '?restore_lastsearch_values=1' . (!empty($socid) ? '&socid=' . $socid : '') . '">' . $langs->trans("BackToList") . '</a>';

		$morehtmlref = '<div class="refidno">';
		$morehtmlref .= '</div>';


		dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);



		print '<div class="fichecenter">';

		print '<div class="fichehalfleft">';
		print '<div class="underbanner clearboth"></div>';
		print '<table class="border centpercent tableforfield">' . "\n";
		include DOL_DOCUMENT_ROOT . '/core/tpl/commonfields_view.tpl.php';

		if ($object->index_fin == 0 || $object->index_fin == '') {
			print '<table border-collapse class="noborder paymenttable">';

			print_barre_liste($langs->trans("quart_clos"), $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'hourglass.png', 0, $newcardbutton, '', $limit, 0, 0, 1);
			print  '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $object->id . '" method="post" style="height:100px;background:#f2f2fece;display:flex;justify-content:space-evenly;align-items:center;">
					<div class="form__control" style="display:flex;justify-content: space-between;align-items:center;gap:2rem;">
						<label for="index_releve" style="font-size: 1.2rem;">' . $langs->trans("idx_releved") . '</label>
						<input
						style="
							padding: .5rem 1rem;
							background: white;
							border-radius: 0.5rem;
							color: black; 
						" 
						type="number" name="index_releve" id="index_releve">
						<button type="submit" name="" class="butAction">' . $langs->trans("Confirm") . '</button>
	
					</div>';
			print	'</form>';
			print "</table>";
		}

		// Other attributes. Fields from hook formObjectOptions and Extrafields.
		include DOL_DOCUMENT_ROOT . '/core/tpl/extrafields_view.tpl.php';

		print '</tr>';

		print '</table>';
		print '</div>';
		print '</div>';
	}

	print '<div class="clearboth"></div>';

	print dol_get_fiche_end();


	/*
	 * Lines
	 */

	if (!empty($object->table_element_line)) {
		// Show object lines
		$result = $object->getLinesArray();

		print '	<form name="addproduct" id="addproduct" action="' . $_SERVER["PHP_SELF"] . '?id=' . $object->id . (($action != 'editline') ? '' : '#line_' . GETPOST('lineid', 'int')) . '" method="POST">
		<input type="hidden" name="token" value="' . newToken() . '">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline') . '">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="page_y" value="">
		<input type="hidden" name="id" value="' . $object->id . '">
		';

		if (!empty($conf->use_javascript_ajax) && $object->status == 0) {
			include DOL_DOCUMENT_ROOT . '/core/tpl/ajaxrow.tpl.php';
		}

		print '<div class="div-table-responsive-no-min">';
		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
			print '<table id="tablelines" class="noborder noshadow" width="100%">';
		}

		if (!empty($object->lines)) {
			$object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
		}

		// Form to add new line
		if ($object->status == 0 && $permissiontoadd && $action != 'selectlines') {
			if ($action != 'editline') {
				// Add products/services form

				$parameters = array();
				$reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
				if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
				if (empty($reshook))
					$object->formAddObjectLine(1, $mysoc, $soc);
			}
		}

		if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
			print '</table>';
		}
		print '</div>';

		print "</form>\n";
	}


	// Buttons for actions

	if ($action == 'confirm_send') {
		$object->fetch($id);
		if ($object->status == $object::STATUS_CLOS) {
			$object->status = $object::STATUS_APPROVED;
			$verif = $object->approved($user);


			print '<script type="text/JavaScript"> location.reload(); </script>';

			// if ($verif > 0) {

			// 	$destinataire = new User($db);
			// 	$destinataire->fetch($object->fk_validator);
			// 	// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);

			// }
		}
	}

	// if ($action=='quart_clos')
	// {
	// 	$object->fetch($id);
	// 	if ($object->status == $object::STATUS_DRAFT)
	// 	{
	// 		$object->status = $object::STATUS_CLOS;
	// 		$verif = $object->clos($user);

	// 		print '<script type="text/JavaScript"> location.reload(); </script>';

	// 		// if ($verif > 0) {
	// 		// 	// To
	// 		// 	$destinataire = new User($db);
	// 		// 	$destinataire->fetch($object->fk_validator);
	// 		// 	// $emailTo = $destinataire->email;
	// 		// 	// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);


	// 		// }
	// 	}

	// }

	if ($action == 'confirm_refuse') {
		$object->fetch($id);
		if ($object->status == $object::STATUS_APPROVED) {
			$object->status = $object::STATUS_REFUSED;
			$verif = $object->deny($user);

			print '<script type="text/JavaScript"> location.reload(); </script>';

			// if ($verif > 0) {
			// 	// To
			// 	$destinataire = new User($db);
			// 	$destinataire->fetch($object->fk_validator);
			// 	// $emailTo = $destinataire->email;
			// 	// header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);


			// }
		}
	}




	if ($action != 'presend' && $action != 'editline') {
		global $user;
		// print_r($user);
		// echo $user->id;'


		print '<div class="tabsAction">' . "\n";
		$parameters = array();
		$reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
		if ($reshook < 0) {
			setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
		}

		if (empty($reshook)) {

			if ($action != 'confirm_compare') {
				print dolGetButtonAction($langs->trans('Compare'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=confirm_compare&token=' . newToken(), '', $permissiontoapprove);
			}

			//send to validation or modify
			if ($object->status == $object::STATUS_CLOS) { //&& $object->fk_user_creat == $user->id){
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					print dolGetButtonAction($langs->trans('send_approval'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=confirm_send&confirm=yes&token=' . newToken(), '', ($object->status == $object::STATUS_CLOS && $permissiontosendapprobation));
					// print dolGetButtonAction($langs->trans('Modify'), '', 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', ($object->status == $object::STATUS_CLOS && $permissiontoadd));
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}

			if ($object->status == $object::STATUS_APPROVED) { //&& $object->validate == $user->id) {
				if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
					// print dolGetButtonAction($langs->transnoentitiesnoconv('Approuver'), '', 'default', '', '', $permissiontoapprove);
					print dolGetButtonAction($langs->trans('Approve'), '', 'default', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=confirm_validate&confirm=yes&token=' . newToken(), '', ($object->status == $object::STATUS_APPROVED && $permissiontoapprove));
					print dolGetButtonAction($langs->trans('Refuse'), '', 'danger', $_SERVER['PHP_SELF'] . '?id=' . $object->id . '&action=confirm_refuse&token=' . newToken(), '', ($object->status == $object::STATUS_APPROVED && $permissiontodesapprove));
				} else {
					$langs->load("errors");
					print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
				}
			}


			// cloturer la releve
			// if($object->status == $object::STATUS_CLOS){ //&& $object->validate == $user->id) {
			// 	if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
			// 		print dolGetButtonAction($langs->transnoentitiesnoconv('quart_clos'), '', 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=quart_clos&confirm=yes&token='.newToken(), '', ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
			// 		print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));


			// 	} else {
			// 		$langs->load("errors");
			// 		print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
			// 	}
			// }

			// Delete (need delete permission, or if draft, just need create/modify permission)
			// if ($object->status == $object::STATUS_DRAFT){ //&& $object->fk_user_creat == $user->id){
			// 	if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
			// 		print dolGetButtonAction($langs->trans('Delete'), '', 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete || ($object->status == $object::STATUS_DRAFT && $permissiontoadd));
			// 	} else {
			// 		$langs->load("errors");
			// 		print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
			// 	}
			// }
		}
		print '</div>' . "\n";
	}


	// // Select mail models is same action as presend
	// if (GETPOST('modelselected')) {
	// 	$action = 'presend';
	// }

	// if ($action != 'presend') {
	// 	print '<div class="fichecenter"><div class="fichehalfleft">';
	// 	print '<a name="builddoc"></a>'; // ancre

	// 	$includedocgeneration = 0;

	// 	// Documents
	// 	if ($includedocgeneration) {
	// 		$objref = dol_sanitizeFileName($object->ref);
	// 		$relativepath = $objref.'/'.$objref.'.pdf';
	// 		$filedir = $conf->station->dir_output.'/'.$object->element.'/'.$objref;
	// 		$urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
	// 		$genallowed = $permissiontoread; // If you can read, you can build the PDF to read content
	// 		$delallowed = $permissiontoadd; // If you can create/edit, you can remove a file on card
	// 		print $formfile->showdocuments('station:Releve', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
	// 	}

	// 	// Show links to link elements
	// 	$linktoelem = $form->showLinkToObjectBlock($object, null, array('releve'));
	// 	$somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);


	// 	print '</div><div class="fichehalfright">';

	// 	$MAXEVENT = 10;

	// 	$morehtmlcenter = dolGetButtonTitle($langs->trans('SeeAll'), '', 'fa fa-list-alt imgforviewmode', dol_buildpath('/station/releve_agenda.php', 1).'?id='.$object->id);

	// 	// List of actions on element
	// 	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
	// 	$formactions = new FormActions($db);
	// 	$somethingshown = $formactions->showactions($object, $object->element.'@'.$object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlcenter);

	// 	print '</div></div>';
	// }

	//Select mail models is same action as presend
	if (GETPOST('modelselected')) {
		$action = 'presend';
	}

	// Presend form
	$modelmail = 'releve';
	$defaulttopic = 'InformationMessage';
	$diroutput = $conf->station->dir_output;
	$trackid = 'releve' . $object->id;

	include DOL_DOCUMENT_ROOT . '/core/tpl/card_presend.tpl.php';
}

// End of page
llxFooter();
$db->close();
