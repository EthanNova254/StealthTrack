<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Location Tracker</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}.container{background:white;border-radius:20px;padding:40px;max-width:500px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3)}h1{font-size:2em;margin-bottom:10px;color:#333}p{color:#666;margin-bottom:30px}.input-group{margin-bottom:20px}label{display:block;margin-bottom:8px;font-weight:500;color:#444}input{width:100%;padding:12px;border:2px solid #e0e0e0;border-radius:8px;font-size:16px;transition:border 0.3s}input:focus{outline:none;border-color:#667eea}button{width:100%;padding:14px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:transform 0.2s}button:hover{transform:translateY(-2px)}.result{margin-top:20px;padding:15px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px}.link{display:block;margin-top:10px;color:#667eea;word-break:break-all}.footer{margin-top:30px;text-align:center}.footer a{color:#667eea;text-decoration:none;font-weight:500}</style>
</head>
<body>
<div class="container">
<h1>📍 Location Tracker</h1>
<p>Create a unique tracking link to capture location data</p>
<div class="input-group">
<label for="slug">Custom Link ID (optional)</label>
<input type="text" id="slug" placeholder="Leave empty for random ID">
</div>
<button onclick="createLink()">Generate Tracking Link</button>
<div id="result"></div>
<div class="footer"><a href="/manage">Admin Dashboard</a></div>
</div>
<script>
function createLink(){const slug=document.getElementById('slug').value.trim()||generateSlug();const url=window.location.origin+'/track/'+slug;document.getElementById('result').innerHTML='<div class="result"><strong>✓ Link Created!</strong><a href="'+url+'" class="link" target="_blank">'+url+'</a></div>';}
function generateSlug(){const chars='abcdefghijklmnopqrstuvwxyz0123456789';let slug='';for(let i=0;i<8;i++){slug+=chars[Math.floor(Math.random()*chars.length)];}return slug;}
</script>
</body>
</html>
