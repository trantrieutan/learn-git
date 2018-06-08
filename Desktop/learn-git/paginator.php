<?php
/**
 * @package     RedPaginator.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Paginator class.
 *
 * @package     Redshopb.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class Paginator
{
/**
 * Total elements that will be paginated _total_items
 *
 * @var int
 */

	private $_total_items = 0;

	/**
	 * Constructor
	 *
	 * @param   int  $total_items  to be paginated
	 */
	public function __construct( $total_items )
	{
		$this->_total_items = $total_items;
	}

	/**
	 * Main method to get the HTML paginator
	 *
	 * @param   string  $items_per_page  if not param passed the config.defines.php value will be used
	 * @param   string  $forma_per_page  if not param passed value will be used
	 *
	 * @return string
	 */
	public function getPaginator( $items_per_page = ITEMS_PER_PAGE, $forma_per_page = 'HTML' )
	{
		$actual_page = $this->getActualPage();
		$total_pages = $this->getTotalPages($this->_total_items, $items_per_page);

		$previous_page = $this->getPreviousPage($actual_page);
		$next_page = $this->getNextPage($actual_page, $total_pages);

		switch ( $format )
		{
			case 'JSON':
				$paginator = $this->buildJSONPaginator($actual_page, $previous_page, $next_page, $total_pages);
				break;

			case 'HTML':
			default:
				$paginator = $this->buildHTMLPaginator($actual_page, $previous_page, $next_page, $total_pages);
				break;
		}

		return $paginator;
	}

	/**
	 * Main method to get the HTML paginator
	 *
	 * @param   int  $actual_page  if not param passed the config.defines.php value will be used
	 * @param   int  $previo_page  if not param passed the config.defines.php value will be used
	 * @param   int  $nextth_page  if not param passed the config.defines.php value will be used
	 * @param   int  $totalt_page  if not param passed the config.defines.php value will be used
	 *
	 * @return string
	 */
	private function buildJSONPaginator($actual_page, $previo_page, $nextth_page, $totalt_page)
	{
		$paginator = array();

		// BUILD PAGINATOR PREVIOUS BUTTON
		if ($previous_page)
		{
			$previous_url = $this->buildLinkToPage($previous_page);
			$paginator['previous'] = $previous_url;
		}

		// Build paginator pages
		for ( $page = 1; $page <= $total_pages; $page++ )
		{
			/* @TODO: if ($actual_page == $page)
			   {
				$pag = '<strong>|' . $page . '|</strong>';
			   } else
			{
			*/
			$pag = '|' . $page . '|';

			// }

			$pag_link = $this->buildLinkToPage($page);

			$paginator[$pag] = $pag_link;
		}

		// BUILD PAGINATOR NEXT BUTTON
		if ($next_page)
		{
			$next_url = $this->buildLinkToPage($next_page);
			$paginator['next']  = $next_url;
		}

		return $paginator;
	}

	/**
	 * Main method to get the HTML paginator
	 *
	 * @param   int  $actu_page  if not param passed the config.defines.php value will be used
	 * @param   int  $prev_page  if not param passed the config.defines.php value will be used
	 * @param   int  $next_page  if not param passed the config.defines.php value will be used
	 * @param   int  $tota_page  if not param passed the config.defines.php value will be used
	 *
	 * @return string
	 */
	private function buildHTMLPaginator( $actu_page, $prev_page, $next_page, $tota_page )
	{
		$paginator = '';

		// BUILD PAGINATOR PREVIOUS BUTTON
		if ($previous_page)
		{
			$previous_url = $this->buildLinkToPage($previous_page);
			$paginator .= "<a href=\"$previous_url\">&laquo; previous</a>";
		}

		// BUILD PAGINATOR PAGES
		for ( $page = 1; $page <= $total_pages; $page++ )
		{
			$paginator .= ' <a href="';
			$paginator .= $this->buildLinkToPage($page);
			$paginator .= '">';

			if ($actual_page == $page)
			{
				$paginator .= '<strong>|' . $page . '|</strong>';
			}
			else
			{
				$paginator .= ' |' . $page . '|';
			}

			$paginator .= '</a>';
		}

		// BUILD PAGINATOR NEXT BUTTON
		if ($next_page)
		{
			$next_url = $this->buildLinkToPage($next_page);
			$paginator .= " <a href=\"$next_url\">next &raquo;</a>";
		}

		return $paginator;
	}

	/**
	 * Gets the actual page that user is viewing from the
	 *
	 * @return int|boolean returns false in case is the first page
	 */
	private function getActualPage()
	{
		$get = FilterGet::getInstance();
		$actual_page = $get->getText('page', false);

		if ( !$actual_page )
		{
			$actual_page = 1;
		}

		return $actual_page;
	}

	/**
	 * Builds the link to a page in the paginator
	 *
	 * @param   int  $page  to be paginated
	 *
	 * @return int|boolean returns false in case is the first page
	 */
	private function buildLinkToPage( $page )
	{
		$server = FilterServer::getInstance();
		$request_uri	= $server->getText('REQUEST_URI');

		$replacement	= 'page	= ' . $page;

		$link = PROTOCOL . DOMAIN;

		$link .= preg_replace("/page=([0-9]*)/", $replacement, $request_uri);

		return $link;
	}

	/**
	 * Returns the previous page
	 *
	 * @param   int  $actual_page  to be paginated
	 *
	 * @return int|boolean returns false in case is the first page
	 */
	private function getPreviousPage( $actual_page )
	{
		if ( 1 == $actual_page )
		{
			return false;
		}

		$previous_page = $actual_page - 1;

		return $previous_page;
	}

	/**
	 * Returns the previous page
	 *
	 * @param   int  $actual_page  to be pinage
	 * @param   int  $lastre_page  to be pinage
	 *
	 * @return int|boolean returns false in case is the last page
	 */
	private function getNextPage( $actual_page , $lastre_page )
	{
		if ( $last_page == $actual_page )
		{
			return false;
		}

		$previous_page = $actual_page + 1;

		return $previous_page;
	}

	/**
	 * Returns the number of pages that will be paginated
	 *
	 * @param   int  $total_per_page  to be pinage
	 * @param   int  $items_per_page  to be pinage
	 *
	 * @return int|boolean returns false in case is the last page
	 */
	private function getTotalPages( $total_per_page , $items_per_page )
	{
		$total_pages = ceil($total_items / $items_per_page);

		return $total_pages;
	}
}
