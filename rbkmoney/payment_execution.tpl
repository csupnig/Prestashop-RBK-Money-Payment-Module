{*
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
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="message_content txt-content">
<h3 class="txt-title">{l s='Summary and payment' mod='rbkmoney'}</h3>

{capture name=path}{l s='RBK payment' mod='rbkmoney'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="warning message_content">{l s='Your shopping cart is empty.'}</p>
{else}


<form action="https://rbkmoney.ru/acceptpurchase.aspx" method="post" class="rbk_payment">
	<div class="txt-content">
		<div class="order_information">
			<p>
				<img src="{$this_path}img/icon/{$preference}.png" alt="{l s='Payment method via RBK Money' mod='rbkmoney'}" class="rbk_icon"/>
				<br/><br />
				{l s='Your Order has been placed:' mod='rbkmoney'}
			</p>
			<p>
				- {l s='Your Order ID is:' mod='rbkmoney'} <span>{$orderId}</span>
			</p>
			<p>
				- {l s='The total amount of your order is' mod='rbkmoney'}
				<span id="amount" class="price">{displayPrice price=$total}</span>
				{if $use_taxes == 1}
					{l s='(tax incl.)' mod='rbkmoney'}
				{/if}
			</p>
			<p>
				<b>{l s='An E-Mail with this information has been sent to you! Please proceed to RBK Monkey to complete the payment...' mod='rbkmoney'}.
			</b>
		</p>
		</div>
	   <div class="clear"></div>
	   <p class="hidden">			
		   <input type="hidden" name="preference" value="{$preference}">

		   <input type="hidden" name="eshopId" value="{$eshopid}"> 			
		   <input type="hidden" name="orderId" value="{$orderId}"> 			
		   <input type="hidden" name="recipientAmount" value="{$total}"> 			
		   <input type="hidden" name="recipientCurrency" value="{$currencyISO}"> 			
		   <input type="hidden" value="{$this_path_ssl}validation.php?return=success&rbkorderid={$orderId}" name="successUrl"> 			
		   <input type="hidden" value="{$this_path_ssl}validation.php?return=error&rbkorderid={$orderId}" name="failUrl"> 			
		   <input type="hidden" value="{$customer_email}" name="user_email"> 			
		   <input type="hidden" name="serviceName" value="{l s='Applerel Order ID:' mod='rbkmoney'}{$orderId}">
	   </p>
   </div>
	<div class="cart_navigation clearfix">
		<input type="hidden" class="hidden" name="step" value="2" />
		<input type="hidden" name="back" value="{$back}" />
		
		<div class="cart_navigation_next txt-title">
			<input style="padding:0 !important;" type="submit" name="submit" value="{l s='Proceed to RBK Money' mod='rbkmoney'}" />
		</div>
		
		
	</div>
</form>
{/if}
</div>
