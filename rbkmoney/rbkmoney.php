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
*  @version  Release: $Revision: 8005 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class RBKMoney extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public  $chequeName;
	public  $address;

	public function __construct()
	{
		$this->name = 'rbkmoney';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'Chris';
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('RBKMONEY_ESHOPID'));
		if (isset($config['RBKMONEY_ESHOPID']))
			$this->eshopid = $config['RBKMONEY_ESHOPID'];
			
		parent::__construct();

		$this->displayName = $this->l('RBKMoney');
		$this->description = $this->l('Module for accepting payments by rbkmoney.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
		
		if (!isset($this->chequeName) OR !isset($this->address))
			$this->warning = $this->l('\'To the order of\' and address must be configured in order to use this module correctly.');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency set for this module');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
			
		//CREATE ORDER STATE WAITING FOR PAYMENT
		$orderState = new OrderState();
		$orderState->name = array();
		foreach (Language::getLanguages() AS $language)
		{
			$orderState->name[$language['id_lang']] = 'Awaiting RBK payment';
			$orderState->template[$language['id_lang']] = 'rbk';
		}
		$orderState->send_email = true;
		$orderState->color = '#FF9398';
		$orderState->hidden = false;
		$orderState->delivery = false;
		$orderState->logable = true;
		$orderState->invoice = false;
		if ($orderState->add())
			copy(dirname(__FILE__).'/osicon.gif', dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif');
		Configuration::updateValue('RBK_OS_WAITING', (int)$orderState->id);
		
		//CREATE ORDER STATE FAILURE
		$orderState = new OrderState();
		$orderState->name = array();
		foreach (Language::getLanguages() AS $language)
		{
			$orderState->name[$language['id_lang']] = 'RBK payment failure';
			$orderState->template[$language['id_lang']] = 'rbk';
		}
		$orderState->send_email = true;
		$orderState->color = '#FF0000';
		$orderState->hidden = false;
		$orderState->delivery = false;
		$orderState->logable = true;
		$orderState->invoice = false;
		if ($orderState->add())
		copy(dirname(__FILE__).'/osicon.gif', dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif');
		Configuration::updateValue('RBK_OS_FAILURE', (int)$orderState->id);
		
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('RBKMONEY_ESHOPID') OR !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			if (!Tools::getValue('eshopid'))
				$this->_postErrors[] = $this->l('\'E-ShopID\' field is required.');
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('RBKMONEY_ESHOPID', Tools::getValue('eshopid'));
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('OK').'" /> '.$this->l('Settings updated').'</div>';
	}

	private function _displayCheque()
	{
		$this->_html .= '<img src="../modules/rbkmoney/img/rbkm_logo.png" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by RBKMoney.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, the order status will change to \'Waiting for payment\'.').'<br />
		'.$this->l('Therefore, you will need to manually confirm the order as soon as you receive a the money to your RBK account.').'<br /><br /><br />';
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('E-ShopID').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify the E-ShopID for your RBK account.').'.<br /><br /></td></tr>
					<tr><td width="130" style="height: 35px;">'.$this->l('E-ShopID').'</td><td><input type="text" name="eshopid" value="'.Tools::htmlentitiesUTF8(Tools::getValue('eshopid', $this->eshopid)).'" style="width: 300px;" /></td></tr>
					<tr><td colspan="2" align="center"><br /><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displayCheque();
		$this->_displayForm();

		return $this->_html;
	}
	
	private function _getCurrencyISO($currencies, $customerCurrency) {
		$iso = "";
		foreach($currencies AS $cur) {
			if($cur["id_currency"] == $customerCurrency) {
				$iso = $cur["iso_code"];
			}
		}
		return $iso;
	}

	public function execPayment($cart)
	{
		if (!$this->active)
			return ;
		
		if (!$this->_checkCurrency($cart))
			Tools::redirectLink(__PS_BASE_URI__.'order.php');

		global $cookie, $smarty;
		
		//IMPL FOR SPECIAL LINK
		$custSpecialOrder = Tools::getValue("specialOrderId", false);
		if ($custSpecialOrder) {
			$custCart = CartCore::getCartByOrderId($custSpecialOrder);
			if($custCart->id_customer == $cookie->id_customer) {
				$cart = $custCart;
			}
		}
		
		//CHECK CART FOR EXISTENS OR IF THE ORDER HAS ALREADY BEEN PLACED
		if (!isset($cart->id) || !$cart->id) {
			//CART IS NOT SET, GET IT FROM THE LAST POSTED ORDER
			$cart = CartCore::getCartByOrderId($cookie->get("rbk_current_order"));
		}
		
		
		$customer = new Customer((int)$cart->id_customer);
		$currencies = $this->getCurrency((int)$cart->id_currency);
		//CREATE ORDER
		$orderstate = Configuration::get('RBK_OS_WAITING');
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		$cartOrder = OrderCore::getOrderByCartId($cart->id);
		//var_dump($cart);
		//echo "TEST: ".$cartOrder." cart".$cart->id;
		if($cartOrder === false ) {
			$mailVars = array("{totalAmount}"=>$total,
					'{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int)($cookie->id_lang), 1),
			);
			$this->validateOrder((int)$cart->id, $orderstate, $total, $this->displayName, NULL, $mailVars, (int)$cart->id_currency, false, $customer->secure_key);
		} else {
			$this->currentOrder = $cartOrder;
			
		}
		$cookie->set("rbk_current_order",$this->currentOrder);
		$smarty->assign(array(
			'orderId' => $this->currentOrder,
			'preference' => Tools::getValue("preference","inner"),
			'cart_id' => $cart->id,
			'customer_email' => $customer->email,
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $currencies,
			'currencyISO' => $this->_getCurrencyISO($currencies, $cart->id_currency),
			'total' => Tools::ps_round($total),
			'isoCode' => Language::getIsoById((int)($cookie->id_lang)),
			'eshopid' => $this->eshopid,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		
		return $this->display(__FILE__, 'payment_execution.tpl');
	}

	public function hookPayment($params)
	{
		
		if (!$this->active)
			return ;
		
		$cart = $params['cart'];
		if (!$this->_checkCurrency($cart))
			return ;

		global $cookie, $smarty;

		$customer = new Customer((int)$cart->id_customer);
		$currencies = $this->getCurrency((int)$cart->id_currency);
		
		$smarty->assign(array(
			'cart_id' => $cart->id,
			'customer_email' => $customer->email,
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $currencies,
			'currencyISO' => $this->_getCurrencyISO($currencies, $cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'isoCode' => Language::getIsoById((int)($cookie->id_lang)),
			'eshopid' => $this->eshopid,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		global $smarty;
		$state = $params['objOrder']->getCurrentState();
		if ($state == Configuration::get('RBK_OS_WAITING') OR $state == Configuration::get('PS_OS_OUTOFSTOCK'))
			$smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
				'eshopid' => $this->eshopid,
				'status' => 'ok',
				'id_order' => $params['objOrder']->id
			));
		else
			$smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}
	
	private function _checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);
		$currency_default = Configuration::get('PS_CURRENCY_DEFAULT');

		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}
}
