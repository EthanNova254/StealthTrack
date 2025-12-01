<?php
$extension = $item['file_path'] ? pathinfo($item['file_path'], PATHINFO_EXTENSION) : '';
$isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
$isVideo = in_array($extension, ['mp4', 'webm']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($item['title']); ?></title>
<style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f5;min-height:100vh;padding:20px}.container{max-width:900px;margin:0 auto;background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1)}.header{padding:30px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white}h1{font-size:2em;margin-bottom:10px}.meta{opacity:0.9;font-size:0.9em}.content{padding:30px}.description{color:#666;margin-bottom:20px;line-height:1.6}img,video{max-width:100%;height:auto;border-radius:8px}.link-button{display:inline-block;padding:12px 24px;background:#667eea;color:white;text-decoration:none;border-radius:8px;margin-top:10px}.stats{padding:20px 30px;background:#f9fafb;border-top:1px solid #e5e7eb;color:#666;font-size:0.9em}</style>
</head>
<body>
<div class="container">
<div class="header">
<h1><?php echo htmlspecialchars($item['title']); ?></h1>
<div class="meta"><?php if ($item['expires_at']): ?>Expires: <?php echo date('M j, Y g:i A', strtotime($item['expires_at'])); ?><?php endif; ?></div>
</div>
<div class="content">
<?php if ($item['description']): ?><p class="description"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p><?php endif; ?>
<?php if ($item['file_path']): ?>
<?php if ($isImage): ?><img src="/serve/<?php echo $item['file_path']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
<?php elseif ($isVideo): ?><video controls><source src="/serve/<?php echo $item['file_path']; ?>" type="video/<?php echo $extension; ?>"></video>
<?php else: ?><a href="/serve/<?php echo $item['file_path']; ?>" class="link-button" download>📥 Download File</a><?php endif; ?>
<?php endif; ?>
<?php if ($item['external_url']): ?><a href="<?php echo htmlspecialchars($item['external_url']); ?>" class="link-button" target="_blank">🔗 Open Link</a><?php endif; ?>
</div>
<div class="stats">👁️ <?php echo number_format($item['view_count']); ?> views • Created <?php echo date('M j, Y', strtotime($item['created_at'])); ?></div>
</div>
</body>
</html>
