<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css"
        integrity="undefined" crossorigin="anonymous">
    <link href="{{ asset('assets/vendor/fpx-payment/css/form-validation.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="py-5 text-center">
            <h2>Checkout form</h2>
            <p class="lead">Below is an example form built entirely with Bootstrap's form controls to to demonstrate
                payment flow. Each required form group has a validation state that can be triggered by attempting to
                submit the form without completing it.</p>
        </div>

        <form class="needs-validation" novalidate method="POST" action="{{ route('fpx.payment.auth.enquiry') }}">
            @csrf
            <input type="hidden" name="response_format" value="{{ $response_format }}" />

            {{ implode(',', $errors->all()) }}
            <div class="row">
                <div class="col-md-8 order-md-1">
                    <div class="border p-3 mb-3 rounded">
                        <h4 class="mb-3">Payment Status details</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="reference_id">Order Reference Id</label>
                                <input type="text" class="form-control" id="reference_id" name="reference_id"
                                    placeholder="Enter Order Reference Id" value="{{ $test ? '60ee9d5ecd875' : '' }}" required>
                                <div class="invalid-feedback">
                                    Valid order reference id is required.
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Proceed</button>
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
