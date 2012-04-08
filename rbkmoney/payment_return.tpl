
{if $status == 'ok'}
	<p class="txt-content">{l s='Your order on' mod='rbkmoney'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='rbkmoney'}
		<br /><br />
		<br /><br />{l s='An e-mail has been sent to you with this information.' mod='rbkmoney'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as we receive your payment.' mod='rbkmoney'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='rbkmoney'} <a href="{$link->getCMSLink('6', NULL)}#getInTouch">{l s='customer support' mod='rbkmoney'}</a>.
	</p>
{else}
	<p class="message_content warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='rbkmoney'} 
		<a href="{$link->getCMSLink('6', NULL)}#getInTouch">{l s='customer support' mod='rbkmoney'}</a>.
	</p>
{/if}
