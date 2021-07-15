<form id="form" method="post" action="{{ route('fpx.payment.auth.request') }}">
	@csrf
	<input type="hidden" name="flow" value="01" />
	<input type="hidden" name="reference_id" value="{{ $referenceId }}" />
	<input type="hidden" name="datetime" value="{{ $datetime }}" />
	<input type="hidden" name="product_description" value="{{ $productDescription }}" />
	<input type="hidden" name="currency" value="MYR" />
	<input type="hidden" name="amount" value="{{ ($testMode ?? null) ? '1.00' : $amount }}" />
	<input type="hidden" name="customer_name" value="{{ $customerName }}" />
	<input type="hidden" name="customer_email" value="{{ $customerEmail }}" />

	{{ $slot }}

	<div class="flex w-full justify-end mt-5">
		<button type="submit" {{ $attributes }}>{{ $title ?? 'Pay' }}</button>
	</div>
</form>
