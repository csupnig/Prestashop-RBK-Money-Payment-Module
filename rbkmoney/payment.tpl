<form action="/modules/rbkmoney/payment.php" method="post"
        class="rbk_payment">

<!-- <form action="https://rbkmoney.ru/acceptpurchase.aspx" method="post"
	class="rbk_payment"> -->




	<p class="payment_module">
		<button id="bankCard" value="bankCard" name="preference" class="txt-payment">
			<img alt="" src="/themes/applerel/img/custom/icon-creditCard.png"
				class="method_image"> {l s='Кредитная карта Visa / MasterCard'
			mod='rbkmoney'}
		</button>
	</p>
	<p class="payment_module">
		<button id="mobilestores" value="mobilestores" name="preference" class="txt-payment">
			<img alt="" src="/themes/applerel/img/custom/icon-mobileStore.png" class="method_image">
			{l s='Euroset' mod='rbkmoney'}
		</button>
	</p>
	<p class="payment_module">
		<button id="terminals" value="terminals" name="preference" class="txt-payment">
			<img alt="" src="/themes/applerel/img/custom/icon-terminal.png"
				class="method_image"> {l s='Payment Terminals' mod='rbkmoney'}
		</button>
	</p>
	<p class="payment_module">
		<button id="sberbank" value="sberbank" name="preference" class="txt-payment">
			<img alt="" src="/themes/applerel/img/custom/icon-bankTransfer.png"
				class="method_image"> {l s='Bank payment' mod='rbkmoney'}
		</button>
	</p>
	<p class="payment_module">
		<button id="inner" value="inner" name="preference" class="txt-payment">
			<img alt="" src="/themes/applerel/img/custom/icon-rbk.png"
				class="method_image"> {l s='С кошелька RBK Money'
			mod='rbkmoney'}
		</button>
	</p>

	<p class="hidden">
		<input type="hidden" name="eshopId" value="{$eshopid}"> <input
			type="hidden" name="orderId" value="{$cart_id}"> <input type="hidden"
			name="recipientAmount" value="{$total}"> <input type="hidden"
			name="recipientCurrency" value="{$currencyISO}"> <input type="hidden"
			value="{$this_path_ssl}validation.php?return=success"
			name="successUrl"> <input type="hidden"
			value="{$this_path_ssl}validation.php?return=error" name="failUrl"> <input
			type="hidden" value="{$customer_email}" name="user_email"> <input
			type="hidden" name="serviceName"
			value="{l s='Applerel Order ID:' mod='rbkmoney'}{$cart_id}">
	</p>

</form>
