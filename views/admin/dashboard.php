<?php
$locationModel = new Location();
$contentModel = new Content();
$stats = $locationModel->getStats();
Auth::startSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f7fa}.header{background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);color:white;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}.header-content{max-width:1400px;margin:0 auto;display:flex;justify-content:space-between;align-items:center}h1{font-size:1.8em}.logout{color:white;text-decoration:none;padding:8px 16px;background:rgba(255,255,255,0.2);border-radius:6px}.container{max-width:1400px;margin:20px auto;padding:0 20px}.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px}.stat-card{background:white;padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1)}.stat-card h3{color:#666;font-size:0.9em;margin-bottom:10px;text-transform:uppercase}.stat-card .number{font-size:2em;font-weight:bold;color:#1e3a8a}.tabs{display:flex;gap:10px;margin-bottom:20px;border-bottom:2px solid #e5e7eb}.tab{padding:12px 24px;cursor:pointer;border:none;background:none;font-size:16px;color:#666;border-bottom:3px solid transparent;transition:all 0.3s}.tab.active{color:#3b82f6;border-bottom-color:#3b82f6;font-weight:600}.content-section{display:none;background:white;border-radius:12px;padding:30px;box-shadow:0 2px 8px rgba(0,0,0,0.1)}.content-section.active{display:block}#map{height:600px;border-radius:8px;margin-bottom:30px}table{width:100%;border-collapse:collapse}th,td{padding:12px;text-align:left;border-bottom:1px solid #e5e7eb}th{background:#f9fafb;font-weight:600;color:#374151}.btn{padding:8px 16px;background:#3b82f6;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;margin:2px}.btn-danger{background:#ef4444}.form-group{margin-bottom:20px}label{display:block;margin-bottom:8px;font-weight:500;color:#374151}input,select,textarea{width:100%;padding:10px;border:2px solid #e5e7eb;border-radius:6px;font-size:14px}textarea{resize:vertical;min-height:80px}.success{padding:12px;background:#d1fae5;color:#065f46;border-radius:6px;margin-bottom:20px}.error{padding:12px;background:#fee;color:#991b1b;border-radius:6px;margin-bottom:20px}</style>
</head>
<body>
<div class="header">
<div class="header-content">
<h1>📊 Admin Dashboard</h1>
<a href="/manage/logout" class="logout">Logout</a>
</div>
</div>
<div class="container">
<?php if (isset($_SESSION['success'])): ?>
<div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; if (isset($_SESSION['error'])): ?>
<div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<div class="stats">
<div class="stat-card"><h3>Total Locations</h3><div class="number"><?php echo number_format($stats['total']); ?></div></div>
<div class="stat-card"><h3>Last 24 Hours</h3><div class="number"><?php echo number_format($stats['last_24h']); ?></div></div>
<div class="stat-card"><h3>GPS Locations</h3><div class="number"><?php echo number_format($stats['gps_locations']); ?></div></div>
<div class="stat-card"><h3>IP Locations</h3><div class="number"><?php echo number_format($stats['ip_locations']); ?></div></div>
</div>
<div class="tabs">
<button class="tab active" onclick="showTab('map')">📍 Map View</button>
<button class="tab" onclick="showTab('locations')">📋 Locations</button>
<button class="tab" onclick="showTab('content')">📦 Content</button>
<button class="tab" onclick="showTab('create')">➕ Create Content</button>
</div>
<div id="map-section" class="content-section active"><div id="map"></div></div>
<div id="locations-section" class="content-section">
<table>
<thead><tr><th>Slug</th><th>Type</th><th>Coordinates</th><th>Location</th><th>IP</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($locationModel->getAll(100) as $loc): ?>
<tr>
<td><?php echo htmlspecialchars($loc['slug']); ?></td>
<td><?php echo $loc['location_type'] === 'gps' ? '📍 GPS' : '🌐 IP'; ?></td>
<td><?php echo number_format($loc['latitude'], 6); ?>, <?php echo number_format($loc['longitude'], 6); ?></td>
<td><?php echo $loc['city'] ? htmlspecialchars($loc['city'] . ', ' . $loc['country']) : '-'; ?></td>
<td><?php echo htmlspecialchars($loc['ip_address']); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($loc['created_at'])); ?></td>
<td><a href="https://www.google.com/maps?q=<?php echo $loc['latitude']; ?>,<?php echo $loc['longitude']; ?>" target="_blank" class="btn">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div id="content-section" class="content-section">
<table>
<thead><tr><th>Title</th><th>Type</th><th>Views</th><th>Expires</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($contentModel->getAll() as $item): ?>
<tr>
<td><?php echo htmlspecialchars($item['title']); ?></td>
<td><?php echo $item['content_type']; ?></td>
<td><?php echo number_format($item['view_count']); ?></td>
<td><?php echo $item['expires_at'] ? date('M j, Y', strtotime($item['expires_at'])) : 'Never'; ?></td>
<td>
<a href="/content/<?php echo $item['slug']; ?>" target="_blank" class="btn">View</a>
<form method="POST" action="/manage/content/delete/<?php echo $item['id']; ?>" style="display:inline;">
<button type="submit" class="btn btn-danger" onclick="return confirm('Delete?')">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div id="create-section" class="content-section">
<form method="POST" action="/manage/content/create" enctype="multipart/form-data">
<div class="form-group"><label>Title</label><input type="text" name="title" required></div>
<div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
<div class="form-group"><label>Content Type</label><select name="content_type" id="content_type" onchange="toggleContentType()" required><option value="file">File Upload</option><option value="link">External Link</option></select></div>
<div class="form-group" id="file_upload"><label>Upload File</label><input type="file" name="file" accept=".jpg,.jpeg,.png,.gif,.mp4,.webm,.pdf"></div>
<div class="form-group" id="link_input" style="display:none;"><label>External URL</label><input type="url" name="url"></div>
<div class="form-group"><label>Expires In (Days)</label><input type="number" name="expiry_days" value="7" min="1" max="7"></div>
<button type="submit" class="btn">Create Content</button>
</form>
</div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
function showTab(tabName){document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));document.querySelectorAll('.content-section').forEach(s=>s.classList.remove('active'));event.target.classList.add('active');document.getElementById(tabName+'-section').classList.add('active');}
function toggleContentType(){const type=document.getElementById('content_type').value;document.getElementById('file_upload').style.display=type==='file'?'block':'none';document.getElementById('link_input').style.display=type==='link'?'block':'none';}
const map=L.map('map').setView([0,0],2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(map);
const markers=L.markerClusterGroup();
fetch('/api/locations?api_key=<?php echo Config::get('api_key'); ?>')
.then(res=>res.json())
.then(data=>{
data.data.forEach(loc=>{
const marker=L.marker([loc.latitude,loc.longitude]);
marker.bindPopup('<strong>Slug:</strong> '+loc.slug+'<br><strong>Type:</strong> '+loc.location_type+'<br><strong>IP:</strong> '+loc.ip_address+'<br><strong>Time:</strong> '+new Date(loc.created_at).toLocaleString());
markers.addLayer(marker);
});
map.addLayer(markers);
if(data.data.length>0)map.fitBounds(markers.getBounds());
});
</script>
</body>
</html>
