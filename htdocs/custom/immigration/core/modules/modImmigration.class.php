<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019-2020  Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2023 SuperAdmin
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
 * 	\defgroup   immigration     Module Immigration
 *  \brief      Immigration module descriptor.
 *
 *  \file       htdocs/immigration/core/modules/modImmigration.class.php
 *  \ingroup    immigration
 *  \brief      Description and activation file for module Immigration
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/PowerERPModules.class.php';

/**
 *  Description and activation class for module Immigration
 */
class modImmigration extends PowerERPModules
{
	/**
	 * Constructor. Define names, constants, directories, boxes, permissions
	 *
	 * @param DoliDB $db Database handler
	 */
	public function __construct($db)
	{
		global $langs, $conf;
		$this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> PowerERP for list of used modules id).
		$this->numero = 600000; // TODO Go on page https://wiki.powererp.org/index.php/List_of_modules_id to reserve an id number for your module

		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'immigration';

		// Family can be 'base' (core modules),'crm','financial','hr','projects','products','ecm','technic' (transverse modules),'interface' (link with external tools),'other','...'
		// It is used to group modules by family in module setup page
		$this->family = "other";

		// Module position in the family on 2 digits ('01', '10', '20', ...)
		$this->module_position = '90';

		// Gives the possibility for the module, to provide his own family info and position of this family (Overwrite $this->family and $this->module_position. Avoid this)
		//$this->familyinfo = array('myownfamily' => array('position' => '01', 'label' => $langs->trans("MyOwnFamily")));
		// Module label (no space allowed), used if translation string 'ModuleImmigrationName' not found (Immigration is name of module).
		$this->name = preg_replace('/^mod/i', '', get_class($this));

		// Module description, used if translation string 'ModuleImmigrationDesc' not found (Immigration is name of module).
		$this->description = "ImmigrationDescription";
		// Used only if file README.md and README-LL.md not found.
		$this->descriptionlong = "ImmigrationDescription";

		// Author
		$this->editor_name = 'Editor name';
		$this->editor_url = 'https://www.example.com';

		// Possible values for version are: 'development', 'experimental', 'powererp', 'powererp_deprecated' or a version string like 'x.y.z'
		$this->version = '1.0';
		// Url to the file with your last numberversion of this module
		//$this->url_last_version = 'http://www.example.com/versionmodule.txt';

		// Key used in llx_const table to save module status enabled/disabled (where IMMIGRATION is value of property name of module in uppercase)
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);

		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		// To use a supported fa-xxx css style of font awesome, use this->picto='xxx'
		$this->picto = 'object_procedures@immigration';

		// Define some features supported by module (triggers, login, substitutions, menus, css, etc...)
		$this->module_parts = array(
			// Set this to 1 if module has its own trigger directory (core/triggers)
			'triggers' => 0,
			// Set this to 1 if module has its own login method file (core/login)
			'login' => 0,
			// Set this to 1 if module has its own substitution function file (core/substitutions)
			'substitutions' => 0,
			// Set this to 1 if module has its own menus handler directory (core/menus)
			'menus' => 0,
			// Set this to 1 if module overwrite template dir (core/tpl)
			'tpl' => 0,
			// Set this to 1 if module has its own barcode directory (core/modules/barcode)
			'barcode' => 0,
			// Set this to 1 if module has its own models directory (core/modules/xxx)
			'models' => 1,
			// Set this to 1 if module has its own printing directory (core/modules/printing)
			'printing' => 0,
			// Set this to 1 if module has its own theme directory (theme)
			'theme' => 0,
			// Set this to relative path of css file if module has its own css file
			'css' => array(
				//    '/immigration/css/immigration.css.php',
			),
			// Set this to relative path of js file if module must load a js on all pages
			'js' => array(
				//   '/immigration/js/immigration.js.php',
			),
			// Set here all hooks context managed by module. To find available hook context, make a "grep -r '>initHooks(' *" on source code. You can also set hook context to 'all'
			'hooks' => array(
				//   'data' => array(
				//       'hookcontext1',
				//       'hookcontext2',
				//   ),
				//   'entity' => '0',
			),
			// Set this to 1 if features of module are opened to external users
			'moduleforexternal' => 0,
		);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/immigration/temp","/immigration/subdir");
		$this->dirs = array("/immigration/temp");

		// Config pages. Put here list of php page, stored into immigration/admin directory, to use to setup module.
		$this->config_page_url = array("setup.php@immigration");

		// Dependencies
		// A condition to hide module
		$this->hidden = false;
		// List of module class names as string that must be enabled if this module is enabled. Example: array('always1'=>'modModuleToEnable1','always2'=>'modModuleToEnable2', 'FR1'=>'modModuleToEnableFR'...)
		$this->depends = array();
		$this->requiredby = array(); // List of module class names as string to disable if this one is disabled. Example: array('modModuleToDisable1', ...)
		$this->conflictwith = array(); // List of module class names as string this module is in conflict with. Example: array('modModuleToDisable1', ...)

		// The language file dedicated to your module
		$this->langfiles = array("immigration@immigration");

		// Prerequisites
		$this->phpmin = array(5, 6); // Minimum version of PHP required by module
		$this->need_powererp_version = array(11, -3); // Minimum version of PowerERP required by module

		// Messages at activation
		$this->warnings_activation = array(); // Warning to show when we activate module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		$this->warnings_activation_ext = array(); // Warning to show when we activate an external module. array('always'='text') or array('FR'='textfr','MX'='textmx'...)
		//$this->automatic_activation = array('FR'=>'ImmigrationWasAutomaticallyActivatedBecauseOfYourCountryChoice');
		//$this->always_enabled = true;								// If true, can't be disabled

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(1 => array('IMMIGRATION_MYNEWCONST1', 'chaine', 'myvalue', 'This is a constant to add', 1),
		//                             2 => array('IMMIGRATION_MYNEWCONST2', 'chaine', 'myvalue', 'This is another constant to add', 0, 'current', 1)
		// );
		$this->const = array();

		// Some keys to add into the overwriting translation tables
		/*$this->overwrite_translation = array(
			'en_US:ParentCompany'=>'Parent company or reseller',
			'fr_FR:ParentCompany'=>'Maison mère ou revendeur'
		)*/

		if (!isset($conf->immigration) || !isset($conf->immigration->enabled)) {
			$conf->immigration = new stdClass();
			$conf->immigration->enabled = 0;
		}

		// Array to add new pages in new tabs
		$this->tabs = array();
		// Example:
		// $this->tabs[] = array('data'=>'objecttype:+tabname1:Title1:mylangfile@immigration:$user->rights->immigration->read:/immigration/mynewtab1.php?id=__ID__');  					// To add a new tab identified by code tabname1
		// $this->tabs[] = array('data'=>'objecttype:+tabname2:SUBSTITUTION_Title2:mylangfile@immigration:$user->rights->othermodule->read:/immigration/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2. Label will be result of calling all substitution functions on 'Title2' key.
		// $this->tabs[] = array('data'=>'objecttype:-tabname:NU:conditiontoremove');                                                     										// To remove an existing tab identified by code tabname
		//
		// Where objecttype can be
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
		// 'contact'          to add a tab in contact view
		// 'contract'         to add a tab in contract view
		// 'group'            to add a tab in group view
		// 'intervention'     to add a tab in intervention view
		// 'invoice'          to add a tab in customer invoice view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'member'           to add a tab in fundation member view
		// 'opensurveypoll'	  to add a tab in opensurvey poll view
		// 'order'            to add a tab in customer order view
		// 'order_supplier'   to add a tab in supplier order view
		// 'payment'		  to add a tab in payment view
		// 'payment_supplier' to add a tab in supplier payment view
		// 'product'          to add a tab in product view
		// 'propal'           to add a tab in propal view
		// 'project'          to add a tab in project view
		// 'stock'            to add a tab in stock view
		// 'thirdparty'       to add a tab in third party view
		// 'user'             to add a tab in user view

		// Dictionaries
		$this->dictionaries = array();
		/* Example: 
		$this->dictionaries=array(
			'langs'=>'immigration@immigration',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array("table1", "table2", "table3"),
			// Label of tables
			'tablib'=>array("Table1", "Table2", "Table3"),
			// Request to select fields
			'tabsql'=>array('SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table1 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table2 as f', 'SELECT f.rowid as rowid, f.code, f.label, f.active FROM '.MAIN_DB_PREFIX.'table3 as f'),
			// Sort order
			'tabsqlsort'=>array("label ASC", "label ASC", "label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label", "code,label", "code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label", "code,label", "code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid", "rowid", "rowid"),
			// Condition to show each dictionary
			'tabcond'=>array($conf->immigration->enabled, $conf->immigration->enabled, $conf->immigration->enabled),
			// Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
			'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip'), ...),
		);
		*/

		$this->dictionaries=array(
			'langs'=>'immigration@immigration',
			// List of tables we want to see into dictonnary editor
			'tabname'=>array("c_immigrations_documents"),
			// Label of tables
			'tablib'=>array("Documentation d'immigration"),
			// Request to select fields
			'tabsql'=>array('SELECT d.rowid as rowid, d.code, d.label, d.active FROM '.MAIN_DB_PREFIX.'c_immigrations_documents as d'),
			// Sort order
			'tabsqlsort'=>array("label ASC"),
			// List of fields (result of select to show dictionary)
			'tabfield'=>array("code,label"),
			// List of fields (list of fields to edit a record)
			'tabfieldvalue'=>array("code,label"),
			// List of fields (list of fields for insert)
			'tabfieldinsert'=>array("code,label"),
			// Name of columns with primary key (try to always name it 'rowid')
			'tabrowid'=>array("rowid"),
			// Condition to show each dictionary
			'tabcond'=>array($conf->immigration->enabled),
			// Tooltip for every fields of dictionaries: DO NOT PUT AN EMPTY ARRAY
			'tabhelp'=>array(array('code'=>$langs->trans('CodeTooltipHelp'), 'field2' => 'field2tooltip')),
		);

		// Boxes/Widgets
		// Add here list of php file(s) stored in immigration/core/boxes that contains a class to show a widget.
		$this->boxes = array(
			//  0 => array(
			//      'file' => 'immigrationwidget1.php@immigration',
			//      'note' => 'Widget provided by Immigration',
			//      'enabledbydefaulton' => 'Home',
			//  ),
			//  ...
		);

		// Cronjobs (List of cron jobs entries to add when module is enabled)
		// unit_frequency must be 60 for minute, 3600 for hour, 86400 for day, 604800 for week
		$this->cronjobs = array(
			//  0 => array(
			//      'label' => 'MyJob label',
			//      'jobtype' => 'method',
			//      'class' => '/immigration/class/procedures.class.php',
			//      'objectname' => 'Procedures',
			//      'method' => 'doScheduledJob',
			//      'parameters' => '',
			//      'comment' => 'Comment',
			//      'frequency' => 2,
			//      'unitfrequency' => 3600,
			//      'status' => 0,
			//      'test' => '$conf->immigration->enabled',
			//      'priority' => 50,
			//  ),
		);
		// Example: $this->cronjobs=array(
		//    0=>array('label'=>'My label', 'jobtype'=>'method', 'class'=>'/dir/class/file.class.php', 'objectname'=>'MyClass', 'method'=>'myMethod', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>2, 'unitfrequency'=>3600, 'status'=>0, 'test'=>'$conf->immigration->enabled', 'priority'=>50),
		//    1=>array('label'=>'My label', 'jobtype'=>'command', 'command'=>'', 'parameters'=>'param1, param2', 'comment'=>'Comment', 'frequency'=>1, 'unitfrequency'=>3600*24, 'status'=>0, 'test'=>'$conf->immigration->enabled', 'priority'=>50)
		// );

		// Permissions provided by this module
		$this->rights = array();
		$r = 0;
		// Add here entries to declare new permissions
		/* BEGIN MODULEBUILDER PERMISSIONS */

		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Configure_immigration'; // Permission label
		$this->rights[$r][4] = 'procedures';
		$this->rights[$r][5] = 'configure'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->delete)
		$r++;

		// Procedures
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read_procedures'; // Permission label
		$this->rights[$r][4] = 'procedures';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->read)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update_procedures'; // Permission label
		$this->rights[$r][4] = 'procedures';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->write)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete_procedures'; // Permission label
		$this->rights[$r][4] = 'procedures';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->delete)
		$r++;
		
		// Categories procedures
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read_cat_procedures'; // Permission label
		$this->rights[$r][4] = 'cat_procedures';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->read)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update_cat_procedures'; // Permission label
		$this->rights[$r][4] = 'cat_procedures';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->write)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete_cat_procedures'; // Permission label
		$this->rights[$r][4] = 'cat_procedures';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->delete)
		$r++;

		// step procedures
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Read_step_procedures'; // Permission label
		$this->rights[$r][4] = 'step_procedures';
		$this->rights[$r][5] = 'read'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->read)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Create/Update_step_procedures'; // Permission label
		$this->rights[$r][4] = 'step_procedures';
		$this->rights[$r][5] = 'write'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->write)
		$r++;
		$this->rights[$r][0] = $this->numero . sprintf("%02d", $r + 1); // Permission id (must not be already used)
		$this->rights[$r][1] = 'Delete_step_procedures'; // Permission label
		$this->rights[$r][4] = 'step_procedures';
		$this->rights[$r][5] = 'delete'; // In php code, permission will be checked by test if ($user->rights->immigration->procedures->delete)
		$r++;
		/* END MODULEBUILDER PERMISSIONS */

		// Main menu entries to add
		$this->menu = array();
		$r = 0;
		// Add here entries to declare new menus
		/* BEGIN MODULEBUILDER TOPMENU */
		$this->menu[$r++] = array(
			'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'top', // This is a Top menu entry
			'titre'=>'ModuleImmigrationName',
			'prefix' => img_picto('', 'procedures@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'immigration',
			'leftmenu'=>'immigration',
			'url'=>'/immigration/immigrationindex.php',
			'langs'=>'immigration@immigration', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000 + $r,
			'enabled'=>'$conf->immigration->enabled', // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled.
			'perms'=>'1', // Use 'perms'=>'$user->rights->immigration->procedures->read' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2, // 0=Menu for internal users, 1=external users, 2=both
		);
		/* END MODULEBUILDER TOPMENU */
		/* BEGIN MODULEBUILDER LEFTMENU PROCEDURES
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=immigration',      // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',                          // This is a Left menu entry
			'titre'=>'Procedures',
			'prefix' => img_picto('', $this->picto, 'class="paddingright pictofixedwidth valignmiddle"'),
			'mainmenu'=>'immigration',
			'leftmenu'=>'procedures',
			'url'=>'/immigration/immigrationindex.php',
			'langs'=>'immigration@immigration',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->immigration->enabled',  // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled.
			'perms'=>'$user->rights->immigration->procedures->read',			                // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=procedures',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'List_Procedures',
			'mainmenu'=>'immigration',
			'leftmenu'=>'immigration_procedures_list',
			'url'=>'/immigration/procedures_list.php',
			'langs'=>'immigration@immigration',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->immigration->enabled',  // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->immigration->procedures->read',			                // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		$this->menu[$r++]=array(
			'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=procedures',	    // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
			'type'=>'left',			                // This is a Left menu entry
			'titre'=>'New_Procedures',
			'mainmenu'=>'immigration',
			'leftmenu'=>'immigration_procedures_new',
			'url'=>'/immigration/procedures_card.php?action=create',
			'langs'=>'immigration@immigration',	        // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
			'position'=>1000+$r,
			'enabled'=>'$conf->immigration->enabled',  // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
			'perms'=>'$user->rights->immigration->procedures->write',			                // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
			'target'=>'',
			'user'=>2,				                // 0=Menu for internal users, 1=external users, 2=both
		);
		*/

		// Tableau de bord
		$this->menu[$r++]=array(
            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'fk_menu'=>'fk_mainmenu=immigration',
            // This is a Left menu entry
            'type'=>'left',
            'titre'=>'Dashboard',
			'prefix' => img_picto('', 'object_dashboard@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
            'mainmenu'=>'immigration',
            'leftmenu'=>'immigration_dashboard',
            'url'=>'/immigration/immigration_dashboard.php',
            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'langs'=>'immigration@immigration',
            'position'=>1100+$r,
            // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'enabled'=>'$conf->immigration->enabled',
            // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
            'perms'=>'1',
            'target'=>'',
            // 0=Menu for internal users, 1=external users, 2=both
            'user'=>2,
        );

		// Configurations
		$this->menu[$r++]=array(
            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'fk_menu'=>'fk_mainmenu=immigration',
            // This is a Left menu entry
            'type'=>'left',
            'titre'=>'Settings',
			'prefix' => img_picto('', 'object_configuration@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
            'mainmenu'=>'immigration',
            'leftmenu'=>'immigration_configuration',
            // 'url'=>'#',
            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'langs'=>'immigration@immigration',
            'position'=>1100+$r,
            // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'enabled'=>'$user->rights->immigration->procedures->configure',
            // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
            'perms'=>'1',
            'target'=>'',
            // 0=Menu for internal users, 1=external users, 2=both
            'user'=>2,
        );
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_configuration',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'ListDocuments',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_configuration',
				'url'=>'/admin/dict.php?mainmenu=home',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2,
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_configuration',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'ListCat_Procedures',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_categories',
				'url'=>'/immigration/cat_procedures_list.php',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_configuration',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'StepProcedure',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_step',
				'url'=>'/immigration/step_procedures_list.php',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);

		// Procedures 
		$this->menu[$r++]=array(
            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'fk_menu'=>'fk_mainmenu=immigration',
            // This is a Left menu entry
            'type'=>'left',
            'titre'=>'Procedures',
			'prefix' => img_picto('', 'object_documents@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
            'mainmenu'=>'immigration',
            'leftmenu'=>'immigration_procedures',
            // 'url'=>'#',
            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'langs'=>'immigration@immigration',
            'position'=>1100+$r,
            // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'enabled'=>'$conf->immigration->enabled',
            // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
            'perms'=>'1',
            'target'=>'',
            // 0=Menu for internal users, 1=external users, 2=both
            'user'=>2,
        );
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_procedures',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'ListProcedures',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_procedures',
				'url'=>'/immigration/procedures_list.php',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2,
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_procedures',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'NewProcedures',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_procedures',
				'url'=>'/immigration/procedures_card.php?action=create',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);


		// Suivi
		$this->menu[$r++]=array(
            // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'fk_menu'=>'fk_mainmenu=immigration',
            // This is a Left menu entry
            'type'=>'left',
            'titre'=>'Tracking',
			'prefix' => img_picto('', 'object_suivis@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
            'mainmenu'=>'immigration',
            'leftmenu'=>'immigration_suivi',
            // 'url'=>'#',
            // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'langs'=>'immigration@immigration',
            'position'=>1100+$r,
            // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
            'enabled'=>'$conf->immigration->enabled',
            // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
            'perms'=>'1',
            'target'=>'',
            // 0=Menu for internal users, 1=external users, 2=both
            'user'=>2,
        );
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_suivi',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'Student',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_suivi_student',
				'url'=>'/immigration/tracking_list.php?type=2',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_suivi',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'Temporary',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_suivi_temporary',
				'url'=>'/immigration/tracking_list.php?type=3',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_suivi',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'Qualified',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_suivi_qualified',
				'url'=>'/immigration/tracking_list.php?type=4',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);
			$this->menu[$r++]=array(
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_suivi',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'Visitor',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_suivi_visitor',
				'url'=>'/immigration/tracking_list.php?type=1',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);
			$this->menu[$r++]=array(
				
				// '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
				'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_suivi',
				// This is a Left menu entry
				'type'=>'left',
				'titre'=>'Certified_TW',
				'mainmenu'=>'immigration',
				'leftmenu'=>'sub_immigration_suivi_ctw',
				'url'=>'/immigration/tracking_list.php?type=5',
				// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
				'langs'=>'immigration@immigration',
				'position'=>1100+$r,
				// Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
				'enabled'=>'$conf->immigration->enabled',
				// Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
				'perms'=>'1',
				'target'=>'',
				// 0=Menu for internal users, 1=external users, 2=both
				'user'=>2
			);

		// $this->menu[$r++]=array(
        //     // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
        //     'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_categories',
        //     // This is a Left menu entry
        //     'type'=>'left',
        //     'titre'=>'List Cat_Procedures',
		// 	'prefix' => img_picto('', 'object_navigation@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
        //     'mainmenu'=>'immigration',
        //     'leftmenu'=>'sub_immigration_categories',
        //     'url'=>'/immigration/procedures_list.php',
        //     // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
        //     'langs'=>'immigration@immigration',
        //     'position'=>1100+$r,
        //     // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
        //     'enabled'=>'$conf->immigration->enabled',
        //     // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
        //     'perms'=>'1',
        //     'target'=>'',
        //     // 0=Menu for internal users, 1=external users, 2=both
        //     'user'=>2,
        // );
        // $this->menu[$r++]=array(
        //     // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
        //     'fk_menu'=>'fk_mainmenu=immigration,fk_leftmenu=immigration_categories',
        //     // This is a Left menu entry
        //     'type'=>'left',
        //     'titre'=>'New Cat_Procedures',
		// 	'prefix' => img_picto('', 'object_navigation@immigration', 'class="paddingright pictofixedwidth valignmiddle"'),
        //     'mainmenu'=>'immigration',
        //     'leftmenu'=>'sub_immigration_categories',
        //     'url'=>'/immigration/procedures_card.php?action=create',
        //     // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
        //     'langs'=>'immigration@immigration',
        //     'position'=>1100+$r,
        //     // Define condition to show or hide menu entry. Use '$conf->immigration->enabled' if entry must be visible if module is enabled. Use '$leftmenu==\'system\'' to show if leftmenu system is selected.
        //     'enabled'=>'$conf->immigration->enabled',
        //     // Use 'perms'=>'$user->rights->immigration->level1->level2' if you want your menu with a permission rules
        //     'perms'=>'1',
        //     'target'=>'',
        //     // 0=Menu for internal users, 1=external users, 2=both
        //     'user'=>2
        // );

		/* END MODULEBUILDER LEFTMENU PROCEDURES */
		// Exports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER EXPORT PROCEDURES */
		/*
		$langs->load("immigration@immigration");
		$this->export_code[$r]=$this->rights_class.'_'.$r;
		$this->export_label[$r]='ProceduresLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->export_icon[$r]='procedures@immigration';
		// Define $this->export_fields_array, $this->export_TypeFields_array and $this->export_entities_array
		$keyforclass = 'Procedures'; $keyforclassfile='/immigration/class/procedures.class.php'; $keyforelement='procedures@immigration';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		//$this->export_fields_array[$r]['t.fieldtoadd']='FieldToAdd'; $this->export_TypeFields_array[$r]['t.fieldtoadd']='Text';
		//unset($this->export_fields_array[$r]['t.fieldtoremove']);
		//$keyforclass = 'ProceduresLine'; $keyforclassfile='/immigration/class/procedures.class.php'; $keyforelement='proceduresline@immigration'; $keyforalias='tl';
		//include DOL_DOCUMENT_ROOT.'/core/commonfieldsinexport.inc.php';
		$keyforselect='procedures'; $keyforaliasextra='extra'; $keyforelement='procedures@immigration';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$keyforselect='proceduresline'; $keyforaliasextra='extraline'; $keyforelement='proceduresline@immigration';
		//include DOL_DOCUMENT_ROOT.'/core/extrafieldsinexport.inc.php';
		//$this->export_dependencies_array[$r] = array('proceduresline'=>array('tl.rowid','tl.ref')); // To force to activate one or several fields if we select some fields that need same (like to select a unique key if we ask a field of a child to avoid the DISTINCT to discard them, or for computed field than need several other fields)
		//$this->export_special_array[$r] = array('t.field'=>'...');
		//$this->export_examplevalues_array[$r] = array('t.field'=>'Example');
		//$this->export_help_array[$r] = array('t.field'=>'FieldDescHelp');
		$this->export_sql_start[$r]='SELECT DISTINCT ';
		$this->export_sql_end[$r]  =' FROM '.MAIN_DB_PREFIX.'procedures as t';
		//$this->export_sql_end[$r]  =' LEFT JOIN '.MAIN_DB_PREFIX.'procedures_line as tl ON tl.fk_procedures = t.rowid';
		$this->export_sql_end[$r] .=' WHERE 1 = 1';
		$this->export_sql_end[$r] .=' AND t.entity IN ('.getEntity('procedures').')';
		$r++; */
		/* END MODULEBUILDER EXPORT PROCEDURES */

		// Imports profiles provided by this module
		$r = 1;
		/* BEGIN MODULEBUILDER IMPORT PROCEDURES */
		/*
		$langs->load("immigration@immigration");
		$this->import_code[$r]=$this->rights_class.'_'.$r;
		$this->import_label[$r]='ProceduresLines';	// Translation key (used only if key ExportDataset_xxx_z not found)
		$this->import_icon[$r]='procedures@immigration';
		$this->import_tables_array[$r] = array('t' => MAIN_DB_PREFIX.'immigration_procedures', 'extra' => MAIN_DB_PREFIX.'immigration_procedures_extrafields');
		$this->import_tables_creator_array[$r] = array('t' => 'fk_user_author'); // Fields to store import user id
		$import_sample = array();
		$keyforclass = 'Procedures'; $keyforclassfile='/immigration/class/procedures.class.php'; $keyforelement='procedures@immigration';
		include DOL_DOCUMENT_ROOT.'/core/commonfieldsinimport.inc.php';
		$import_extrafield_sample = array();
		$keyforselect='procedures'; $keyforaliasextra='extra'; $keyforelement='procedures@immigration';
		include DOL_DOCUMENT_ROOT.'/core/extrafieldsinimport.inc.php';
		$this->import_fieldshidden_array[$r] = array('extra.fk_object' => 'lastrowid-'.MAIN_DB_PREFIX.'immigration_procedures');
		$this->import_regex_array[$r] = array();
		$this->import_examplevalues_array[$r] = array_merge($import_sample, $import_extrafield_sample);
		$this->import_updatekeys_array[$r] = array('t.ref' => 'Ref');
		$this->import_convertvalue_array[$r] = array(
			't.ref' => array(
				'rule'=>'getrefifauto',
				'class'=>(empty($conf->global->IMMIGRATION_PROCEDURES_ADDON) ? 'mod_procedures_standard' : $conf->global->IMMIGRATION_PROCEDURES_ADDON),
				'path'=>"/core/modules/commande/".(empty($conf->global->IMMIGRATION_PROCEDURES_ADDON) ? 'mod_procedures_standard' : $conf->global->IMMIGRATION_PROCEDURES_ADDON).'.php'
				'classobject'=>'Procedures',
				'pathobject'=>'/immigration/class/procedures.class.php',
			),
			't.fk_soc' => array('rule' => 'fetchidfromref', 'file' => '/societe/class/societe.class.php', 'class' => 'Societe', 'method' => 'fetch', 'element' => 'ThirdParty'),
			't.fk_user_valid' => array('rule' => 'fetchidfromref', 'file' => '/user/class/user.class.php', 'class' => 'User', 'method' => 'fetch', 'element' => 'user'),
			't.fk_mode_reglement' => array('rule' => 'fetchidfromcodeorlabel', 'file' => '/compta/paiement/class/cpaiement.class.php', 'class' => 'Cpaiement', 'method' => 'fetch', 'element' => 'cpayment'),
		);
		$r++; */
		/* END MODULEBUILDER IMPORT PROCEDURES */
	}

	/**
	 *  Function called when module is enabled.
	 *  The init function add constants, boxes, permissions and menus (defined in constructor) into PowerERP database.
	 *  It also creates data directories
	 *
	 *  @param      string  $options    Options when enabling module ('', 'noboxes')
	 *  @return     int             	1 if OK, 0 if KO
	 */
	public function init($options = '')
	{
		global $conf, $langs;

		//$result = $this->_load_tables('/install/mysql/', 'immigration');
		$result = $this->_load_tables('/immigration/sql/');
		if ($result < 0) {
			return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')
		}

		// Create extrafields during init
		include_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafields = new ExtraFields($this->db);
		$result1=$extrafields->addExtraField('immigration_categorie_procedure', "Procedure", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Etudiant','code2'=>'Travailleur qualifie','code3'=>'Visiteur','code4'=>'Travailleur temporaire')), 1,'', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');
		//$result1=$extrafields->addExtraField('immigration_myattr1', "New Attr 1 label", 'boolean', 1,  3, 'thirdparty',   0, 0, '', '', 1, '', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');
		//$result2=$extrafields->addExtraField('immigration_myattr2', "New Attr 2 label", 'varchar', 1, 10, 'project',      0, 0, '', '', 1, '', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');
		//$result3=$extrafields->addExtraField('immigration_myattr3', "New Attr 3 label", 'varchar', 1, 10, 'bank_account', 0, 0, '', '', 1, '', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');
		//$result4=$extrafields->addExtraField('immigration_myattr4', "New Attr 4 label", 'select',  1,  3, 'thirdparty',   0, 1, '', array('options'=>array('code1'=>'Val1','code2'=>'Val2','code3'=>'Val3')), 1,'', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');
		//$result5=$extrafields->addExtraField('immigration_myattr5', "New Attr 5 label", 'text',    1, 10, 'user',         0, 0, '', '', 1, '', 0, 0, '', '', 'immigration@immigration', '$conf->immigration->enabled');

		// Permissions
		$this->remove($options);

		$sql = array();

		// Document templates
		$moduledir = dol_sanitizeFileName('immigration');
		$myTmpObjects = array();
		$myTmpObjects['Procedures'] = array('includerefgeneration'=>0, 'includedocgeneration'=>0);

		foreach ($myTmpObjects as $myTmpObjectKey => $myTmpObjectArray) {
			if ($myTmpObjectKey == 'Procedures') {
				continue;
			}
			if ($myTmpObjectArray['includerefgeneration']) {
				$src = DOL_DOCUMENT_ROOT.'/install/doctemplates/'.$moduledir.'/template_proceduress.odt';
				$dirodt = DOL_DATA_ROOT.'/doctemplates/'.$moduledir;
				$dest = $dirodt.'/template_proceduress.odt';

				if (file_exists($src) && !file_exists($dest)) {
					require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
					dol_mkdir($dirodt);
					$result = dol_copy($src, $dest, 0, 0);
					if ($result < 0) {
						$langs->load("errors");
						$this->error = $langs->trans('ErrorFailToCopyFile', $src, $dest);
						return 0;
					}
				}

				$sql = array_merge($sql, array(
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'standard_".strtolower($myTmpObjectKey)."' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('standard_".strtolower($myTmpObjectKey)."', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")",
					"DELETE FROM ".MAIN_DB_PREFIX."document_model WHERE nom = 'generic_".strtolower($myTmpObjectKey)."_odt' AND type = '".$this->db->escape(strtolower($myTmpObjectKey))."' AND entity = ".((int) $conf->entity),
					"INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES('generic_".strtolower($myTmpObjectKey)."_odt', '".$this->db->escape(strtolower($myTmpObjectKey))."', ".((int) $conf->entity).")"
				));
			}
		}

		return $this->_init($sql, $options);
	}

	/**
	 *  Function called when module is disabled.
	 *  Remove from database constants, boxes and permissions from PowerERP database.
	 *  Data directories are not deleted
	 *
	 *  @param      string	$options    Options when enabling module ('', 'noboxes')
	 *  @return     int                 1 if OK, 0 if KO
	 */
	public function remove($options = '')
	{
		$sql = array();
		return $this->_remove($sql, $options);
	}
}
