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
		<button type="submit" {{ $attributes->merge(['class' => 'border-indigo-600 bg-indigo-600 focus:ring-indigo-400 hover:bg-indigo-400 text-white transition-all duration-300 transform ease-in hover:-translate-y-0.5 focus:outline-none hover:shadow-btn-black-200 focus:ring-4 focus:ring-gray-300 focus:ring-opacity-50 hover:opacity-75 focus:ring-4 focus:ring-opacity-50 focus:shadow-btn-black-100-inset shadow-btn-black-100 px-4 py-1 text-sm rounded-md font-semibold']) }}>{{ $title ?? 'Pay' }}</button>
	</div>
</form>
