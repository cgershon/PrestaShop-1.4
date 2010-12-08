{*
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
*}

{literal}
<script type="text/javascript">
<!-- 
    document.write(unescape("%3Cscript src='" + ((document.location.protocol == 'https:') ? 'https://' : 'http://') + "api.treepodia.com/video/Treepodia.js' type='text/javascript'%3E%3C/script%3E"));

	
	function sendProductToTreepodia(rel)
	{
		var reg = new RegExp('^ajax_id_product_([\\d+]*)$', 'g');
		var res = reg.exec(rel);
		
		if (res.lenght > 2)
			Treepodia.getProduct('{/literal}{$account_id}{literal}', res[1]).logAddToCart();
	}
	
	$(document).ready(function()
	{
		$('.ajax_add_to_cart_button').mouseup(function(){
			sendProductToTreepodia($(this).attr('rel'));
			return false;
		});
		
		//for product page 'add' button...
		$('body#product p#add_to_cart input').mouseup(function(){
			sendProductToTreepodia($(this).attr('rel'));
			return false;
		});
	});
// -->
</script>
{/literal}