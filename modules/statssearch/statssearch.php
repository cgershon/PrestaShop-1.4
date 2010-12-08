<?php
/*
* 2007-2010 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Prestashop SA <contact@prestashop.com>
*  @copyright  2007-2010 Prestashop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class StatsSearch extends ModuleGraph
{
    private $_html = '';
	private $_query = '';
	private $_query2 = '';

    function __construct()
    {
        $this->name = 'statssearch';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		
		$this->_query = '
		SELECT ss.`keywords`, COUNT(TRIM(ss.`keywords`)) as occurences, MAX(results) as total
		FROM `'._DB_PREFIX_.'statssearch` ss
		WHERE ss.`date_add` BETWEEN ';
		$this->_query2 = '
		GROUP BY ss.`keywords`
		HAVING occurences > 1
		ORDER BY occurences DESC';

        parent::__construct();
		
        $this->displayName = $this->l('Shop search');
        $this->description = $this->l('Display which keywords have been searched by your visitors');
    }

	function install()
	{
		if (!parent::install() OR !$this->registerHook('search') OR !$this->registerHook('AdminStatsModules'))
			return false;
		return Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'statssearch` (
			id_statssearch INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			keywords VARCHAR(255) NOT NULL,
			results INT(6) NOT NULL DEFAULT 0,
			date_add DATETIME NOT NULL,
			PRIMARY KEY(id_statssearch)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
	}
	
    function uninstall()
    {
        if (!parent::uninstall())
			return false;
		return (Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'statssearch`'));
    }
	
	function hookSearch($params)
	{
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'statssearch` (`keywords`,`results`,`date_add`) VALUES (\''.pSQL($params['expr']).'\', '.(int)($params['total']).', NOW())');
	}
	
	function hookAdminStatsModules()
	{
		if (Tools::getValue('export'))
			$this->csvExport(array('type' => 'pie'));

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.ModuleGraph::getDateBetween().$this->_query2);
		$this->_html = '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>';
		$table = '<div style="overflow-y: scroll; height: 600px;">
		<table class="table" border="0" cellspacing="0" cellspacing="0">
		<thead>
			<tr>
				<th style="width:400px;">'.$this->l('keywords').'</th>
				<th style="width:50px; text-align: right">'.$this->l('Occurrences').'</th>
				<th style="width:50px; text-align: right">'.$this->l('Results').'</th>
			</tr>
		</thead><tbody>';

		foreach ($result as $row)
			$table .= '<tr>
				<td>'.$row['keywords'].'</td>
				<td style="text-align: right">'.$row['occurences'].'</td>
				<td style="text-align: right">'.$row['total'].'</td>
			</tr>';
		$table .= '</tbody></table></div>';
		
		if (sizeof($result))
			$this->_html .= '<center>'.ModuleGraph::engine(array('type' => 'pie')).'</center>
									<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p>
									<br class="clear" />'.$table;
		else
			$this->_html .= '<p><strong>'.$this->l('No keywords searched more than once found.').'</strong></p>';
		$this->_html .= '</fieldset>';
		return $this->_html;
	}
	
	protected function getData($layers)
	{
		$this->_titles['main'] = $this->l('10 first keywords');
		$totalResult = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.$this->getDate().$this->_query2);
		$total = 0;
		$total2 = 0;
		foreach ($totalResult as $totalRow)
			$total += $totalRow['occurences'];
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.$this->getDate().$this->_query2.' LIMIT 9');
		foreach ($result as $row)
		{
			if (!$row['occurences'])
				continue;
			$this->_legend[] = $row['keywords'];
			$this->_values[] = $row['occurences'];
			$total2 += $row['occurences'];
		}
		if ($total > $total2)
		{
			$this->_legend[] = $this->l('Others');
			$this->_values[] = $total - $total2;
		}
	}
}


