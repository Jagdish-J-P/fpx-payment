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
            <h2>CSR Generation Form</h2>
        </div>

        <form class="needs-validation" novalidate method="POST" action="{{ route('fpx.payment.auth.request') }}">
            @csrf
            <input type="hidden" name="flow" value="01" />
            <input type="hidden" name="reference_id" value="{{ uniqid() }}" />
            <input type="hidden" name="datetime" value="{{ now() }}" />
            {{ implode(',', $errors->all()) }}
            <div class="row">
                <div class="col-md-4 order-md-2 mb-4">
                    <div class="border p-3 mb-3 rounded">
                        <h4>Information</h4>
                        <div id="csr-panel">
                            Fill the form and click on Generate to Generate your CSR.
                        </div>
                    </div>
                </div>
                <div class="col-md-8 order-md-1">
                    <div class="border p-3 mb-3 rounded">
                        <h4 class="mb-3">Certificate details</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="common_name">Common name</label>
                                <input type="text" class="form-control" id="common_name" name="common_name"
                                    placeholder="Enter common name(Exchange ID)"
                                    value="{{ $exchangeId = Config::get('fpx.exchange_id', '') }}"
                                    {{ !empty($exchangeId) ? 'readonly' : '' }} required>
                                <div class="invalid-feedback">
                                    Valid common name is required.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="organization">Organization</label>
                            <input type="name" class="form-control" id="organization" name="organization"
                                placeholder="Enter organization name" required>
                            <div class="invalid-feedback">
                                Please enter a valid organization.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="org_unit">Organizational Unit</label>
                            <input type="text" class="form-control" id="org_unit" name="org_unit"
                                placeholder="Enter Department e.g. IT" required>
                            <div class="invalid-feedback">
                                Please enter a valid department.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter city"
                                required>
                            <div class="invalid-feedback">
                                Please enter a valid city.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="state">State / Province</label>
                            <input type="text" class="form-control" id="state" name="state"
                                placeholder="Enter state / province" required>
                            <div class="invalid-feedback">
                                Please enter a valid state / province.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="country">Country</label>
                            {!! Form::select('country', [null => 'Select Country'] + $countries, null, ['class' => 'form-control', 'id' => 'country', 'required' => '']) !!}
                            <div class="invalid-feedback">
                                Please enter a valid country.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="key_size">Key Size</label>
                            <select name="key_size" id="key_size" class="form-control">
                                <option value="2048" selected>RSA 2048 (recommended)</option>
                                <option value="4096">RSA 4096</option>
                                <option value="p256">P-256 (elliptic curve)</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select key size
                            </div>
                        </div>
                        <button class="btn btn-primary btn-lg btn-block" type="submit">GENERATE</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="d-none">
        <div class="csrDescription" id="descriptionFor_common_name">
            <b>Common Name</b> (Exchange ID)<br><br>Please enter Exchange ID you received from FPX.
        </div>
        <div class="csrDescription" id="descriptionFor_org_unit">
            <b>Department</b> (optional)<br><br>You can leave this field blank. This is the <br>department within your
            organization that you want <br>to appear on the certificate. It will be listed in the certificate's subject
            as Organizational Unit, or "OU".<br><br>Common examples: <i>IT</i>,<br> <i>Web Administration</i>,<br>
            <i>Web Security</i>,
            or <i>Marketing</i>
        </div>
        <div class="csrDescription" id="descriptionFor_city">
            <b>City</b><br><br>The city where your organization is legally located.
        </div>
        <div class="csrDescription" id="descriptionFor_state">
            <b>State or Province</b><br><br>The state or province where your organization is legally located.
        </div>
        <div class="csrDescription" id="descriptionFor_country">
            <b>Country</b><br><br>We guessed your country based on your IP address, but if we guessed wrong, please
            choose the correct country. If your country does not appear in this list, there is a chance we cannot issue
            certificates to organizations in your country.
        </div>
        <div class="csrDescription" id="descriptionFor_organization">
            <b>Organization name</b><br><br>The exact legal name of your organization, (e.g., <i>DigiCert,
                Inc.</i>)<br><br>If you do not have a legal registered organization name, you should enter your own full
            name here.
        </div>
        <div class="csrDescription" id="descriptionFor_key_size">
            <b>Key</b><br><br>RSA Key sizes smaller than 2048 are considered unsecure.
        </div>
        <div class="csrDescription" id="descriptionFor_infotext">
            Now just copy and paste this command into a terminal session on your server. Your CSR and Private key will
            be written to
            ###FILE###.csr and ###FILE###.key respectively.
        </div>
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
                        event.preventDefault();
                        event.stopPropagation();
                        if (form.checkValidity() === false) {

                        } else {
                            var exchangeId = common_name.value.trim().toUpperCase();
                            var req =
                                'openssl req -new -newkey rsa:' + key_size.value +
                                ' -nodes -out ' + exchangeId +
                                '.csr -keyout ' +
                                exchangeId + '.key -subj "/C=' +
                                country.value +
                                '/ST=' + state.value + '/L=' + city.value + '/O=' +
                                organization.value + '/OU=' + org_unit.value +
                                '/CN=' + exchangeId + '"';

                            $("#csr-panel").html($("#descriptionFor_infotext").html()
                                    .replaceAll('###FILE###', common_name.value
                                        .toUpperCase()))
                                .append(
                                    "<textarea readonly class='form-control' style='min-height: 200px;'>" +
                                    req +
                                    "</textarea>");
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        $("input,select").on('focus', function() {
            var id = $(this).prop('id');
            if ($("#descriptionFor_" + id).length)
                $("#csr-panel").html($("#descriptionFor_" + id).html());
        })
    </script>
</body>

</html>
