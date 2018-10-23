document.addEventListener('DOMContentLoaded', function () {
    // Create a token or display an error when the form is submitted.
    let form = document.getElementById('payment-form');
    if (form) {
        let button = form.querySelector('button[type=submit]');

        form.addEventListener('submit', function(event) {
            // Disable the submit button to prevent repeated clicks:
            button.disabled = true;

            event.preventDefault();

            let firstName = form.querySelector('input[name=first_name]').value;
            let lastName = form.querySelector('input[name=last_name]').value;

            let ownerInfo = {
                owner: {
                  name: firstName + ' ' + lastName,
                  address: {
                    line1: form.querySelector('input[name=address]').value,
                    city: form.querySelector('input[name=city]').value,
                    postal_code: form.querySelector('input[name=postal_code]').value,
                    country: form.querySelector('select[name=country] option:checked').value,
                  },
                  email: form.querySelector('input[name=email]').value,
                },
            };
            stripe.createSource(card, ownerInfo).then(function(result) {
                if (result.error) {
                    // Inform the customer that there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    button.disabled = false; // Re-enable submission

                } else {
                    // Send the source to your server.
                    stripeSourceHandler(result.source);
                }
            });
        });
    }

    function stripeSourceHandler(source) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeSource');
        hiddenInput.setAttribute('value', source.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
    }
});