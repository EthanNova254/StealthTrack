<?php Auth::startSession(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}.container{background:white;border-radius:20px;padding:40px;max-width:400px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3)}h1{font-size:2em;margin-bottom:30px;color:#1e3a8a;text-align:center}.input-group{margin-bottom:20px}label{display:block;margin-bottom:8px;font-weight:500;color:#444}input{width:100%;padding:12px;border:2px solid #e0e0e0;border-radius:8px;font-size:16px}input:focus{outline:none;border-color:#3b82f6}button{width:100%;padding:14px;background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);color:white;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer}.error{padding:12px;background:#fee;color:#991b1b;border-radius:8px;margin-bottom:20px}</style>
</head>
<body>
<div class="container">
<h1>🔐 Admin Login</h1>
<?php if (isset($_SESSION['error'])): ?>
<div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<form method="POST" action="/manage/login">
<div class="input-group"><label for="password">Password</label><input type="password" id="password" name="password" required autofocus></div>
<button type="submit">Login</button>
</form>
</div>
</body>
</html>
