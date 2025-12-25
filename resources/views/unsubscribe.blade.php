<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Unsubscribe Form</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f9fafb;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .unsubscribe-container {
            background: #fff;
            padding: 2.5rem 3rem;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .unsubscribe-container h2 {
            font-size: 1.75rem;
        }

        .unsubscribe-container p {
            font-size: 0.95rem;
            color: #4b5563;
            margin-bottom: 1.25rem;
        }

        input[type="email"] {
            width: 100%;
            padding: 0.65rem 1rem;
            border: 1.5px solid #374151;
            border-radius: 8px;
            font-size: 1rem;
            outline-offset: 2px;
            transition: border-color 0.3s ease;
        }

        input[type="email"]::placeholder {
            color: #9ca3af;
            font-weight: 500;
        }

        input[type="email"]:focus {
            border-color: #ef4444;
            box-shadow: 0 0 6px rgba(239, 68, 68, 0.5);
        }

        button {
            margin-top: 1.5rem;
            width: 100%;
            background-color: #ef4444;
            border: none;
            padding: 10px;
            border-radius: 10px;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        button:hover,
        button:focus {
            background-color: #dc2626;
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.6);
            outline: none;
        }

        .message {
            margin-top: 1rem;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .message.success {
            color: #059669;
        }

        .message.error {
            color: #b91c1c;
        }
    </style>
</head>

<body>
    <div class="unsubscribe-container">
        <h2>Unsubscribe</h2>
        <p>Enter your email to unsubscribe from our list:</p>
        <form id="unsubscribeForm">
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
            <button type="submit">Unsubscribe</button>
        </form>
        <div class="message" id="formMessage"></div>
    </div>

    <script>
        const form = document.getElementById('unsubscribeForm');
        const emailInput = document.getElementById('email');
        const formMessage = document.getElementById('formMessage');
        const urlParams = new URLSearchParams(window.location.search);
        const emailParam = urlParams.get('email');

        if (emailParam) {
            emailInput.value = emailParam;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            formMessage.textContent = '';
            formMessage.className = 'message';

            const emailValue = emailInput.value.trim();

            if (!emailValue) {
                formMessage.textContent = 'Please enter your email.';
                formMessage.classList.add('error');
                emailInput.focus();
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailValue)) {
                formMessage.textContent = 'Please enter a valid email address.';
                formMessage.classList.add('error');
                emailInput.focus();
                return;
            }

            fetch('/unsubscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        email: emailValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        formMessage.textContent = 'You have been unsubscribed successfully.';
                        formMessage.classList.remove('error');
                        formMessage.classList.add('success');
                        form.reset();
                    } else {
                        if (data.errors && data.errors.email) {
                            formMessage.textContent = data.errors.email[0];
                        } else {
                            formMessage.textContent = 'Unsubscription failed.';
                        }
                        formMessage.classList.add('error');
                    }
                    setTimeout(() => {
                        formMessage.textContent = '';
                        formMessage.className = 'message';
                    }, 5000);
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    formMessage.textContent = 'Something went wrong. Please try again.';
                    formMessage.classList.add('error');
                });
        });
    </script>
</body>

</html>
