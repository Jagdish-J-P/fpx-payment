<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.min.js" defer></script>
</head>

<body>
    <div class="font-sans antialiased text-gray-900">
        <form id="form" method="post" action="{{ $request->url }}" x-data x-init="$refs.submit.click()">
            <input type="hidden" name="fpx_msgType" value="{{ $request->type }}" />
            <input type="hidden" name="fpx_msgToken" value="{{ $request->flow }}" />
            <input type="hidden" name="fpx_sellerExId" value="{{ $request->exchangeId }}" />
            <input type="hidden" name="fpx_sellerId" value="{{ $request->sellerId }}" />
            <input type="hidden" name="fpx_sellerExOrderNo" value="{{ $request->id }}" />
            <input type="hidden" name="fpx_sellerTxnTime" value="{{ $request->timestamp }}" />
            <input type="hidden" name="fpx_sellerOrderNo" value="{{ $request->reference }}" />
            <input type="hidden" name="fpx_sellerBankCode" value="{{ $request->bankCode }}" />
            <input type="hidden" name="fpx_txnCurrency" value="{{ $request->currency }}" />
            <input type="hidden" name="fpx_txnAmount" value="{{ $request->amount }}" />
            <input type="hidden" name="fpx_buyerEmail" value="{{ $request->buyerEmail }}" />
            <input type="hidden" name="fpx_buyerName" value="{{ $request->buyerName }}" />
            <input type="hidden" name="fpx_buyerBankId" value="{{ $request->targetBankId }}" />
            <input type="hidden" name="fpx_productDesc" value="{{ $request->productDescription }}" />
            <input type="hidden" name="fpx_version" value="{{ $request->version }}" />
            <input type="hidden" name="fpx_checkSum" value="{{ $request->checkSum }}" />
            {{-- <input type=hidden value="" name="fpx_buyerBankBranch">
		<input type=hidden value="" name="fpx_buyerAccNo">
		<input type=hidden value="" name="fpx_buyerId">
		<input type=hidden value="" name="fpx_makerName">
		<input type=hidden value="" name="fpx_buyerIban"> --}}
            <input type="submit" value="Proceed with Payment" x-ref="submit" name="Submit" style=" display: none;">
        </form>
    </div>
</body>

</html>
