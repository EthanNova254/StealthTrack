# 📍 Location Tracker & Content Delivery Service

A production-ready, geo-restricted content delivery system with intelligent location tracking, IP-based fallback, and comprehensive admin dashboard.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)
![Docker](https://img.shields.io/badge/Docker-ready-blue.svg)

## ✨ Features

### 🌍 **Smart Location Tracking**
- **GPS-based tracking** with high accuracy (5-50m)
- **IP-based fallback** when GPS is denied (city-level accuracy)
- **Automatic retry mechanism** with user-friendly prompts
- **Geo-restriction messaging** to encourage location sharing
- Tracks: coordinates, IP, user agent, city, region, country

### 📦 **Content Management**
- Share images, videos, GIFs, PDFs, and external links
- Auto-expiring content (1-7 days)
- View counter and analytics
- Secure file upload (10MB limit)
- Direct file serving with proper MIME types

### 📊 **Admin Dashboard**
- 🗺️ **Interactive Leaflet.js map** with clustering
- 📈 Real-time statistics (total, 24h, GPS vs IP locations)
- 📋 Full data management (view, search, delete)
- ➕ Content creation interface
- 🔐 Password-protected access

### 🔒 **Security Features**
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- CSRF protection (session validation)
- Secure file upload validation
- API key authentication
- HttpOnly session cookies
- Security headers (CSP, X-Frame-Options, etc.)

### 🚀 **Production Ready**
- Docker containerization
- Multi-platform deployment (Koyeb, Render, Railway, Heroku)
- Health check endpoints
- Automatic data cleanup (60-hour retention)
- Optimized database with indexing
- CDN-compatible asset serving

## 📋 Requirements

- PHP 8.2 or higher
- SQLite3 extension
- Apache/Nginx with mod_rewrite
- Docker (optional, recommended)

## 🚀 Quick Start

### Option 1: Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/yourusername/location-tracker.git
cd location-tracker

# Set environment variables
cp .env.example .env
nano .env  # Edit ADMIN_PASSWORD and API_KEY

# Build and run
docker-compose up -d

# Access at http://localhost:8080
```

### Option 2: Manual Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/location-tracker.git
cd location-tracker

# Set permissions
mkdir -p data uploads
chmod 755 data uploads

# Configure environment
cp .env.example .env
nano .env

# Set up web server (Apache example)
sudo cp location-tracker.conf /etc/apache2/sites-available/
sudo a2ensite location-tracker
sudo systemctl reload apache2
```

## 🔧 Configuration

### Environment Variables

Create a `.env` file or set these environment variables:

```bash
# Required
ADMIN_PASSWORD=your_secure_password_here
API_KEY=your_secure_api_key_here

# Optional (for better IP geolocation)
IPINFO_TOKEN=your_ipinfo_token_here
```

### Get IP Geolocation Token (Optional)

For more accurate IP-based location:
1. Sign up at [ipinfo.io](https://ipinfo.io)
2. Get free token (50,000 requests/month)
3. Add to `.env` file

**Without token**: Falls back to ip-api.com (free, 45 req/min)

## 📁 Project Structure

```
location-tracker/
├── index.php              # Main entry point & router
├── config.php             # Configuration & database
├── models.php             # Data models (Location, Content)
├── auth.php               # Authentication system
├── utils.php              # Utility functions
├── router.php             # Request routing
├── views/
│   ├── home.php          # Landing page
│   ├── track.php         # Location capture with fallback
│   ├── content.php       # Content display
│   ├── 404.php           # Not found page
│   └── admin/
│       ├── login.php     # Admin login
│       └── dashboard.php # Admin dashboard with map
├── data/                 # SQLite database (auto-created)
├── uploads/              # Uploaded files (auto-created)
├── .htaccess            # Apache configuration
├── Dockerfile           # Docker configuration
├── docker-compose.yml   # Docker Compose setup
├── .env.example         # Environment template
└── README.md            # This file
```

## 🎯 Usage

### Creating a Tracking Link

1. Visit the homepage
2. Enter custom slug (optional) or use auto-generated
3. Click "Generate Tracking Link"
4. Share the link: `/track/{slug}`

### User Experience (Enhanced)

When users visit a tracking link:

1. **Geo-restriction message displayed** explaining content is location-restricted
2. **Privacy information shown** (60-hour retention, temporary access)
3. User clicks "Share Location to Access"
4. **GPS location requested** (high accuracy)
   - ✅ If approved → Access granted immediately
   - ❌ If denied → Automatic IP-based fallback
5. **IP fallback verification** (city-level)
   - Shows: "Detected location: City, Country"
   - Access granted based on IP geolocation
6. **Retry button** available if both fail

### Creating Content

1. Login to admin panel: `/manage`
2. Navigate to "Create Content" tab
3. Fill in details:
   - Title & description
   - Choose file upload or external link
   - Set expiry (1-7 days)
4. Submit and get shareable link
5. Share: `/content/{slug}`

## 🔐 Admin Dashboard

### Access

- URL: `/manage`
- Default password: `change_this_password_123` (**CHANGE THIS!**)

### Features

1. **Map View** 📍
   - Interactive Leaflet.js map
   - Marker clustering for performance
   - Click markers for details
   - Distinguishes GPS vs IP locations

2. **Locations Tab** 📋
   - View all tracked locations
   - Shows: slug, coordinates, IP, timestamp
   - Location type indicator (GPS/IP)
   - City/country info for IP-based
   - Direct Google Maps links

3. **Content Tab** 📦
   - List all content items
   - View count statistics
   - Expiry dates
   - Quick access and delete

4. **Create Content** ➕
   - Upload files or add links
   - Set custom expiry
   - Get instant shareable link

## 🌐 API Documentation

### Authentication

All API requests require authentication via `X-API-KEY` header:

```bash
curl -H "X-API-KEY: your_api_key" https://your-domain.com/api/stats
```

Or query parameter:
```bash
curl https://your-domain.com/api/stats?api_key=your_api_key
```

### Endpoints

#### `GET /api/locations`

Get all tracked locations with pagination.

**Parameters:**
- `page` (optional): Page number (default: 1)
- `api_key` (required): API key

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "slug": "abc123",
      "latitude": 40.7128,
      "longitude": -74.0060,
      "ip_address": "192.168.1.1",
      "location_type": "gps",
      "city": "New York",
      "country": "US",
      "accuracy": "high",
      "created_at": "2025-01-15 10:30:00"
    }
  ],
  "page": 1,
  "total": 100,
  "pages": 2
}
```

#### `POST /api/track`

Track a new location (used internally by tracking page).

**Request Body:**
```json
{
  "slug": "unique-slug",
  "latitude": 40.7128,
  "longitude": -74.0060
}
```

**Response (GPS):**
```json
{
  "success": true,
  "message": "Location tracked (GPS)",
  "type": "gps"
}
```

**Response (IP fallback):**
```json
{
  "success": true,
  "message": "Location tracked (IP-based)",
  "type": "ip",
  "city": "New York",
  "country": "United States"
}
```

#### `GET /api/content`

Get all content items.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "slug": "content-slug",
      "title": "My Content",
      "content_type": "file",
      "view_count": 42,
      "expires_at": "2025-01-22 10:30:00"
    }
  ]
}
```

#### `GET /api/stats`

Get system statistics.

**Response:**
```json
{
  "locations": {
    "total": 1250,
    "last_24h": 45,
    "gps_locations": 980,
    "ip_locations": 270
  },
  "content_items": 23
}
```

#### `GET /health`

Health check endpoint (no authentication required).

**Response:**
```json
{
  "status": "healthy",
  "timestamp": 1705315800
}
```

## 🚢 Deployment

### Docker Deployment

```bash
# Production deployment
docker-compose -f docker-compose.prod.yml up -d

# View logs
docker-compose logs -f

# Stop service
docker-compose down
```

### Cloud Platforms

#### Koyeb

```bash
koyeb app create location-tracker
koyeb service create web \
  --app location-tracker \
  --git github.com/yourusername/location-tracker \
  --env ADMIN_PASSWORD=your_password \
  --env API_KEY=your_api_key
```

#### Render

1. Connect GitHub repository
2. Select "Docker" environment
3. Add environment variables in dashboard
4. Deploy

#### Railway

```bash
railway init
railway variables set ADMIN_PASSWORD=your_password
railway variables set API_KEY=your_api_key
railway up
```

#### Heroku

```bash
heroku create location-tracker
heroku config:set ADMIN_PASSWORD=your_password
heroku config:set API_KEY=your_api_key
git push heroku main
```

## 🔒 Security Best Practices

### Before Production

- [ ] Change default admin password
- [ ] Generate strong API key (32+ characters)
- [ ] Enable HTTPS (use Let's Encrypt)
- [ ] Configure firewall rules
- [ ] Set up rate limiting
- [ ] Regular security updates
- [ ] Enable error logging
- [ ] Set up database backups
- [ ] Configure CSP headers
- [ ] Review file upload limits

### Recommended Headers (in production)

```apache
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
Header always set X-Content-Type-Options "nosniff"
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
```

## 🛠️ Maintenance

### Database Cleanup

Automatic cleanup runs on every request, removing:
- Locations older than 60 hours
- Expired content
- Old session data

### Manual Cleanup

```bash
docker-compose exec web php -r "
require 'config.php';
Database::getInstance()->cleanup();
echo 'Cleanup completed\n';
"
```

### Backup

```bash
# Backup database
docker-compose exec web cp /var/www/html/data/tracker.db /var/www/html/data/backup.db

# Download backup
docker cp $(docker-compose ps -q web):/var/www/html/data/tracker.db ./backup-$(date +%Y%m%d).db

# Backup uploads
tar -czf uploads-backup-$(date +%Y%m%d).tar.gz uploads/
```

## 🐛 Troubleshooting

### Issue: GPS location not working

**Solution:**
- Ensure HTTPS is enabled (required for geolocation API)
- Check browser permissions
- Verify device has GPS enabled
- System will automatically fall back to IP-based location

### Issue: IP geolocation not working

**Solution:**
1. Check if `file_get_contents()` can make external requests
2. Add IPInfo token for better reliability
3. Verify ip-api.com is not blocked by firewall

### Issue: Database permission denied

```bash
docker-compose exec web chown -R www-data:www-data data/
docker-compose restart
```

### Issue: File upload fails

```bash
docker-compose exec web chown -R www-data:www-data uploads/
docker-compose exec web chmod 755 uploads/
```

### Issue: Map not loading

- Check browser console for errors
- Verify internet connection (needs OpenStreetMap tiles)
- Check CSP headers aren't blocking external resources

## 📊 Performance

### Database Optimization

- Indexed columns: `slug`, `created_at`, `location_type`
- Automatic cleanup prevents database bloat
- SQLite is sufficient for <100k records/day

### Caching

```apache
# Add to .htaccess for better performance
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 1 day"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
</IfModule>
```

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Leaflet.js](https://leafletjs.com/) - Interactive maps
- [OpenStreetMap](https://www.openstreetmap.org/) - Map tiles
- [IPInfo.io](https://ipinfo.io/) - IP geolocation
- [ip-api.com](https://ip-api.com/) - Free IP geolocation fallback

## 📞 Support

- 📧 Email: support@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/yourusername/location-tracker/issues)
- 📖 Documentation: This README

## 🗺️ Roadmap

- [ ] Multi-language support
- [ ] Email notifications for admins
- [ ] Webhook integrations
- [ ] Custom geo-fencing rules
- [ ] Export data to CSV/JSON
- [ ] Two-factor authentication
- [ ] Mobile app (React Native)
- [ ] Real-time dashboard updates

---

**Built with ❤️ for location-based content delivery**

*Star ⭐ this repo if you find it useful!*
