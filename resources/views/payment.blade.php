<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css"
        integrity="undefined" crossorigin="anonymous">
    <link href="{{ asset('assets/FPX/css/form-validation.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="py-5 text-center">
            <h2>Checkout form</h2>
            <p class="lead">Below is an example form built entirely with Bootstrap's form controls to to demonstrate
                payment flow. Each required form group has a validation state that can be triggered by attempting to
                submit the form without completing it.</p>
        </div>

        <form class="needs-validation" novalidate method="POST" action="{{ route('fpx.payment.auth.request') }}">
            @csrf
            <input type="hidden" name="flow" value="01" />
            <input type="hidden" name="reference_id" value="{{ uniqid() }}" />

            {{ implode(',', $errors->all()) }}
            <div class="row">
                <div class="col-md-4 order-md-2 mb-4">
                    <div class="border p-3 mb-3 rounded">
                        <h4>Payment Details</h4>
                        <p class="mb-3 pt-1">Please select your payment details.</p>

                        <div class="alert alert-info">Minimum RM 1.00 and maximum RM 30,000.00</div>

                        <div class="row mb-3">
                            <div class="col-lg-6 col-sm-12">
                                <div class="custom-control custom-radio">
                                    <img src="{{ asset('assets/FPX/Images/fpx.svg') }}" height="64px">
                                    <input type="hidden" id="fpx" name="payment_mode" value="fpx">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="select_bank"></div>
                            <div class="col">
                                {!! Form::select('bank_id', [null => 'Select Bank'] + $banks->toArray(), null, ['class' => 'form-control', 'required' => '']) !!}
                                <input type="hidden" name="be_message" id="be_message">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <div class="custom-control custom-checkbox">
                                    <label class="custom-control-label" for="agree">By clicking on "proceed", you agree
                                        to
                                        the <a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp"
                                            target="_blank">terms and conditions</a> of FPX.</label>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-primary btn-lg btn-block" type="submit">Proceed</button>
                    </div>
                </div>
                <div class="col-md-8 order-md-1">
                    <div class="border p-3 mb-3 rounded">
                        <h4 class="mb-3">Billing details</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="customer_name">Buyer name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    placeholder="Enter buyer name" value="" required>
                                <div class="invalid-feedback">
                                    Valid buyer name is required.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount">Amount</label>
                            <input type="name" class="form-control" id="amount" name="amount" placeholder="1.00"
                                value="" required>
                            <div class="invalid-feedback">
                                Please enter a valid amount.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="customer_email">Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" value=""
                                placeholder="you@example.com" required>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="fpx_buyerTelephone">Telephone</label>
                            <input type="tel " class="form-control" id="fpx_buyerTelephone" name="fpx_buyerTelephone"
                                value="" placeholder="01XXXXXXXX" required>
                            <div class="invalid-feedback">
                                Please enter a valid telephone no.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="product_description">Description <span
                                    class="text-muted">(Optional)</span></label>
                            <textarea class="form-control" id="product_description" name="product_description"
                                placeholder="Enter Product Description" required></textarea>
                            <div class="invalid-feedback">
                                Please enter product description
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script>
        window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery-slim.min.js"><\/script>')
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" crossorigin="anonymous">
    </script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';

            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');

                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>

</html>
