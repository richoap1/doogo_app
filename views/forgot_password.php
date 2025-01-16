<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Forgot Password</h2>
        <form action="{{ url_for('forgot_password') }}" method="post">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
            <button type="submit" class="btn btn-primary mt-3">Send Reset Link</button>
        </form>
    </div>
</body>
</html>