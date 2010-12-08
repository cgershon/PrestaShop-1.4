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

class LinkCore
{
	/** @var boolean Rewriting activation */
	private $allow;
	private $url;
	static $cache = array('page' => array());

	/**
	  * Constructor (initialization only)
	  */
	public function __construct()
	{
		$this->allow = (int)(Configuration::get('PS_REWRITING_SETTINGS'));
		$this->url = $_SERVER['SCRIPT_NAME'];
	}

	/**
	  * Return the correct link for product/category/supplier/manufacturer
	  *
	  * @param mixed $id_OBJ Can be either the object or the ID only
	  * @param string $alias Friendly URL (only if $id_OBJ is the object)
	  * @return string link
	  */
	public function getProductLink($id_product, $alias = NULL, $category = NULL, $ean13 = NULL, $id_lang = NULL)
	{
		if (!isset($this->allow)) $this->allow = 0;
		if (is_object($id_product))
			return ($this->allow == 1)?(_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(($id_product->category != 'home' AND !empty($id_product->category)) ? $id_product->category.'/' : '').(int)($id_product->id).'-'.$id_product->link_rewrite.($id_product->ean13 ? '-'.$id_product->ean13 : '').'.html') :
			(_PS_BASE_URL_.__PS_BASE_URI__.'product.php?id_product='.(int)($id_product->id));
		elseif ($alias)
			return ($this->allow == 1)?(_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(($category AND $category != 'home') ? ($category.'/') : '').(int)($id_product).'-'.$alias.($ean13 ? '-'.$ean13 : '').'.html') :
			(_PS_BASE_URL_.__PS_BASE_URI__.'product.php?id_product='.(int)($id_product));
		else
			return _PS_BASE_URL_.__PS_BASE_URI__.'product.php?id_product='.(int)($id_product);
	}

	public function getCategoryLink($id_category, $alias = NULL, $id_lang = NULL)
	{
		if (is_object($id_category))
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_category->id).'-'.$id_category->link_rewrite) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'category.php?id_category='.(int)($id_category->id));
		if ($alias)
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_category).'-'.$alias) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'category.php?id_category='.(int)($id_category));
		return _PS_BASE_URL_.__PS_BASE_URI__.'category.php?id_category='.(int)($id_category);
	}

	public function getCMSCategoryLink($id_category, $alias = NULL, $id_lang = NULL)
	{
		if (is_object($id_category))
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).'content/category/'.(int)($id_category->id).'-'.$id_category->link_rewrite) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'cms.php?id_cms_category='.(int)($id_category->id));
		if ($alias)
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).'content/category/'.(int)($id_category).'-'.$alias) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'cms.php?id_cms_category='.(int)($id_category));
		return _PS_BASE_URL_.__PS_BASE_URI__.'cms.php?id_cms_category='.(int)($id_category);
	}

	public function getCMSLink($cms, $alias = null, $ssl = false, $id_lang = NULL)
	{
		$base = _PS_BASE_URL_;
		if ($ssl)
			$base = Tools::getHttpHost(true);
	
		if (is_object($cms))
		{
			return ($this->allow == 1) ? 
				($base.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).'content/'.(int)($cms->id).'-'.$cms->link_rewrite) :
				($base.__PS_BASE_URI__.'cms.php?id_cms='.(int)($cms->id));
		}
		
		if ($alias)
			return ($this->allow == 1) ? ($base.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).'content/'.(int)($cms).'-'.$alias) :
			($base.__PS_BASE_URI__.'cms.php?id_cms='.(int)($cms));
		return $base.__PS_BASE_URI__.'cms.php?id_cms='.(int)($cms);
	}

	public function getSupplierLink($id_supplier, $alias = NULL, $id_lang = NULL)
	{
		if (is_object($id_supplier))
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_supplier->id).'__'.$id_supplier->link_rewrite) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'supplier.php?id_supplier='.(int)($id_supplier->id));
		if ($alias)
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_supplier).'__'.$alias) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'supplier.php?id_supplier='.(int)($id_supplier));
		return _PS_BASE_URL_.__PS_BASE_URI__.'supplier.php?id_supplier='.(int)($id_supplier);
	}

	public function getManufacturerLink($id_manufacturer, $alias = NULL, $id_lang = NULL)
	{
		if (is_object($id_manufacturer))
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_manufacturer->id).'_'.$id_manufacturer->link_rewrite) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'manufacturer.php?id_manufacturer='.(int)($id_manufacturer->id));
		if ($alias)
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_manufacturer).'_'.$alias) :
			(_PS_BASE_URL_.__PS_BASE_URI__.'manufacturer.php?id_manufacturer='.(int)($id_manufacturer));
		return _PS_BASE_URL_.__PS_BASE_URI__.'manufacturer.php?id_manufacturer='.(int)($id_manufacturer);
	}

	public function getCustomLink($id_custom, $page, $prefix = '~', $alias = NULL, $id_lang = NULL)
	{
		if (is_object($id_custom))
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_custom->id).$prefix.$id_custom->link_rewrite) :
			(_PS_BASE_URL_.__PS_BASE_URI__.$page.'?id_custom='.(int)($id_custom->id));
		if ($alias)
			return ($this->allow == 1) ? (_PS_BASE_URL_.__PS_BASE_URI__.$this->getLangLink((int)($id_lang)).(int)($id_custom).$prefix.$alias) :
			(_PS_BASE_URL_.__PS_BASE_URI__.$page.'?id_custom='.(int)($id_custom));
		return _PS_BASE_URL_.__PS_BASE_URI__.$page.'?id_custom='.(int)($id_custom);
	}

	public function getImageLink($name, $ids, $type = NULL)
	{
		global $protocol;
		if ($this->allow == 1)
			$uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').'/'.$name.'.jpg';
		else
			$uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').'.jpg';
		return $protocol.Tools::getMediaServer($uri_path).$uri_path;
	}
	
	public function getMediaLink($filepath)
	{
		global $protocol;
		return $protocol.Tools::getMediaServer($filepath).$filepath;
	}
	
	public function getPageLink($filename, $ssl = false, $id_lang = NULL)
	{
		if($id_lang == NULL)
		{
			global $cookie;
			$id_lang = (int)($cookie->id_lang);
		}
		$base = $ssl ? Tools::getHttpHost(true) : _PS_BASE_URL_ ;
		if (array_key_exists($filename.'_'.$id_lang, self::$cache['page']))
			$uri_path = self::$cache['page'][$filename.'_'.$id_lang];
		else
		{
			if ($this->allow == 1)
			{
				$pagename = substr($filename, 0, -4);
				$url_rewrite = Db::getInstance()->getValue('
				SELECT url_rewrite
				FROM `'._DB_PREFIX_.'meta` m
				LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta)
				WHERE id_lang = '.(int)($id_lang).' AND `page` = \''.pSQL($pagename).'\'');
				$uri_path = $url_rewrite ? $this->getLangLink((int)($id_lang)).$url_rewrite : $filename;
			}
			else
				$uri_path = $filename;
			self::$cache['page'][$filename.'_'.$id_lang] = $uri_path;
		}
		return $base.__PS_BASE_URI__.$uri_path;
	}

	public function getCatImageLink($name, $id_category, $type = null)
	{
		return ($this->allow == 1) ? (__PS_BASE_URI__.$id_category.($type ? '-'.$type : '').'/'.$name.'.jpg') : (_THEME_CAT_DIR_.$id_category.($type ? '-'.$type : '').'.jpg');
	}


	/**
	  * Create link after language change
	  *
	  * @param integer $id_lang Language ID
	  * @return string link
	  */
	public function getLanguageLink($id_lang)
	{
		$matches = array();
		$request = $_SERVER['REQUEST_URI'];
		preg_match('#/lang-([a-z]{2})/([^\?]*).*$#', $request, $matches);
		if ($matches)
		{
			$current_iso = $matches[1];
			$rewrite = $matches[2];
			$url_rewrite = Meta::getEquivalentUrlRewrite($id_lang, Language::getIdByIso($current_iso), $rewrite);
			$request = str_replace($rewrite, $url_rewrite, $request);
		}
		if ($this->allow == 1)
			return __PS_BASE_URI__.'lang-'.Language::getIsoById($id_lang).'/'.substr(preg_replace('#/lang-([a-z]{2})/#', '/', $request), strlen(__PS_BASE_URI__));
		else
			return $this->getUrlWith('id_lang', (int)($id_lang));
	}

	public function getLanguageLinkAdmin($id_lang)
	{
		return $this->getUrlWith('id_lang', (int)($id_lang));
	}

	public function getUrlWith($key, $val)
	{
		$n = 0;
		$url = htmlentities($this->url, ENT_QUOTES, 'UTF-8');
		
		foreach ($_GET as $k => $value)
			// adminlang is an hand-written param in BO
			if ($k != 'adminlang')
				if (!is_array($value) AND $k != $key AND Tools::isSubmit($k))
					$url .= ((!$n++) ? '?' : '&amp;').urlencode($k).($value ? '='.urlencode($value) : '');
		
		return $url.($n ? '&amp;' : '?').urlencode($key).'='.urlencode($val);
	}

	public function goPage($url, $p)
	{
		return $url.($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&amp;').'p='.(int)($p));
	}

	public function getPaginationLink($type, $id_object, $nb = FALSE, $sort = FALSE, $pagination = FALSE, $array = FALSE)
	{
		if ($type AND $id_object)
			$url = $this->{'get'.$type.'Link'}($id_object, NULL);
		else
			$url = $this->url;
		$vars = (!$array ? '' : array());
		$varsNb = array('n', 'search_query');
		$varsSort = array('orderby', 'orderway');
		$varsPagination = array('p');
		$varsVarious = array('search_query');

		$n = 0;
		foreach ($_GET AS $k => $value)
			if ($k != 'id_'.$type)
			{
				if (Configuration::get('PS_REWRITING_SETTINGS') AND ($k == 'isolang' OR $k == 'id_lang'))
					continue;
				$ifNb = (!$nb OR ($nb AND !in_array($k, $varsNb)));
				$ifSort = (!$sort OR ($sort AND !in_array($k, $varsSort)));
				$ifPagination = (!$pagination OR ($pagination AND !in_array($k, $varsPagination)));
				if ($ifNb AND $ifSort AND $ifPagination AND !is_array($value))
					!$array ? ($vars .= ((!$n++ AND ($this->allow == 1 OR $url == $this->url)) ? '?' : '&').urlencode($k).'='.urlencode($value)) : ($vars[urlencode($k)] = urlencode($value));
			}
		if (!$array)
			return $url.$vars;
		$vars['requestUrl'] = $url;
		if ($type AND $id_object)
			$vars['id_'.$type] = (is_object($id_object) ? (int)($id_object->id) : (int)($id_object));
		return $vars;
	}

	public function addSortDetails($url, $orderby, $orderway)
	{
		return $url.(!strstr($url, '?') ? '?' : '&').'orderby='.urlencode($orderby).'&orderway='.urlencode($orderway);
	}
	
	private function getLangLink($id_lang = NULL)
	{
		if (!$id_lang)
		{
			global $cookie;
			$id_lang = (int)($cookie->id_lang);
		}
		
		if (!$this->allow)
			return NULL;
		return 'lang-'.Language::getIsoById((int)($id_lang)).'/';
	}
	
		public function preloadPageLinks()
		{
			global $cookie, $iso;
			if ($this->allow)
			{
				$specific_pages = Db::getInstance()->ExecuteS('
					SELECT url_rewrite, page
					FROM `'._DB_PREFIX_.'meta` m
					LEFT JOIN `'._DB_PREFIX_.'meta_lang` ml ON (m.id_meta = ml.id_meta)
					WHERE id_lang = '.(int)($cookie->id_lang));
				foreach ($specific_pages as $specific_page)
					self::$cache['page'][$specific_page['page'].'.php'] = 'lang-'.$iso.'/'.$specific_page['url_rewrite'];
			}
		}
}


