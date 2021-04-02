<?php

/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class ActViewSectorform extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	protected $canSave;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->canSave = $this->get('CanSave');
		$this->form		= $this->get('Form');

		// ACT Params 
		$this->params  = $app->getParams('com_act');
		$this->c3   = $this->params['color3grad'];
		$this->c4   = $this->params['color4grad'];
		$this->c5   = $this->params['color5grad'];
		$this->c6   = $this->params['color6grad'];
		$this->c7   = $this->params['color7grad'];
		$this->c8   = $this->params['color8grad'];
		$this->c9   = $this->params['color9grad'];
		$this->c10  = $this->params['color10grad'];
		$this->c11  = $this->params['color11grad'];
		$this->c12  = $this->params['color12grad'];
		
		// Params Routes-Planning
		$this->params_rp = $app->getParams('com_routes_planning');
		$this->record_should = $this->params_rp['record_should']; 						  // Soll Soll erfasst werden 0=nein 1=ja
		$this->record_sector_or_building = $this->params_rp['record_sector_or_building']; // Sollen die Sollwerte im Sektor oder Gebäude erfasst werden? 1=Gebäude 2=Sektor
		$this->record_type = $this->params_rp['record_type'];
		$this->grade_start_percent = $this->params_rp['grade_start_percent'];             // Prozentwerte - Niedrigster Schwierigkeitsgrad
		$this->grade_end_percent = $this->params_rp['grade_end_percent'];                 // Prozentwerte - Höchster  Schwierigkeitsgrad
		$this->grade_start_individually = $this->params_rp['grade_start_individually'];   // Einzelwerte - Niedrigster Schwierigkeitsgrad
		$this->grade_end_individually = $this->params_rp['grade_end_individually'];       // Einzelwerte - Höchster  Schwierigkeitsgrad


		// Alle Linien in diesem Sektor
		$this->lines = ActHelpersAct::getLinesFromSectorId($this->item->id);
		$this->total_lines_in_sektor =  count($this->lines); // Anzahl Linien im Sektor
		
		$this->total_max_routes = 0;
			foreach($this->lines AS $line) {
   			$this->total_max_routes += $line->maxroutes;// Max Routenanzahl gerechnet aus allen Linien in diesem Sektor
		}
		


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_ACT_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
