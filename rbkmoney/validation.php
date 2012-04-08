<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7734 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/rbkmoney.php');

$rbk = new RBKMoney();

//IMPL FOR SPECIAL LINK
$custSpecialOrder = Tools::getValue("rbkorderid", false);
$spOrder = 0;
if ($custSpecialOrder !== false) {
	$spOrder = new OrderCore($custSpecialOrder);
	$custCart = new CartCore($spOrder->id_cart);
	if($spOrder->id_customer == $cookie->id_customer) {
		$customer_id = $spOrder->id_customer;
		$rbk->currentOrder == $custSpecialOrder;
	}
}

if ($customer_id == 0 OR !$rbk->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'rbkmoney')
	{
		$authorized = true;
		break;
	}
if (!$authorized)
	die(Tools::displayError('This payment method is not available.'));

$rbkreturn = ToolsCore::getValue("return","error");
$orderstate = Configuration::get('RBK_OS_WAITING');
if($rbkreturn != "success" && ValidateCore::isLoadedObject($spOrder)) {
	$orderstate = Configuration::get('RBK_OS_FAILURE');
	$spOrder->setCurrentState($orderstate);
	$spOrder->update();
}



$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.(int)$spOrder->id_cart.'&id_module='.(int)$rbk->id.'&id_order='.$rbk->currentOrder.'&key='.$customer->secure_key);

