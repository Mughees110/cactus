<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <form id="payment-form">
        <div id="card-element"></div>
        <button id="submit">Pay</button>
        <div id="error-message"></div>
    </form>

    <script>
        var stripe = Stripe('{{ config('services.stripe.key') }}');
        var elements = stripe.elements();
        var cardElement = elements.create('card');
        cardElement.mount('#card-element');

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {
                    document.getElementById('error-message').textContent = result.error.message;
                } else {
                    var token = result.token.id;
                    var amount = 5000; // Example amount in cents ($50.00)
                    console.log(token);
                    fetch('/pointsS/public/create-charge', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ token: token, amount: amount })
                    }).then(function(response) {
                        return response.json();
                    }).then(function(data) {
                        console.log(data);
                    });
                }
            });
        });
    </script>
</body>
</html>
