<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="/css/registration.css">
</head>
<body>

    <div class="registration-container">
    <form class="registration-form" method="POST" action="/register">
        @csrf  <!-- Include CSRF token for security -->
        <h2>Create Account</h2>

        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="input-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="password_confirmation" required>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="error">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        <button type="submit">Register</button>

        <p class="login-link">Already have an account? <a href="/">Login here</a></p>
    </form>
 
    </div>

</body>
</html>
