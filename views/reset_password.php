<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Reset Password</h2>
        <form action="{{ url_for('reset_password', token=token) }}" method="post">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" name="password" placeholder="Enter your new password" required>
            <button type="submit" class="btn btn-primary mt-3">Reset Password</button>
        </form>
    </div>
</body>
</html>