# Group Management Setup Instructions

## Laravel Backend Setup for Groups Feature

### 1. Run Migrations

First, run the database migrations to create the groups and group_members tables:

```bash
cd house_and_job
php artisan migrate
```

### 2. Run Seeders (Optional)

To populate the database with sample groups data:

```bash
php artisan db:seed --class=GroupSeeder
```

Or run all seeders:

```bash
php artisan db:seed
```

### 3. Storage Configuration

Make sure the storage link is created for file uploads:

```bash
php artisan storage:link
```

### 4. API Endpoints

The following API endpoints are now available:

#### Public Endpoints (No Authentication Required)
- `GET /api/groups/public` - Get all groups (public access)

#### Authenticated Endpoints (Requires Bearer Token)
- `GET /api/groups` - Get all groups with filtering
- `GET /api/groups/{id}` - Get specific group details
- `POST /api/groups` - Create new group
- `PUT /api/groups/{id}` - Update group (creator only)
- `DELETE /api/groups/{id}` - Delete group (creator only)
- `POST /api/groups/{id}/join` - Join a group
- `POST /api/groups/{id}/leave` - Leave a group
- `GET /api/groups/joined` - Get user's joined groups
- `GET /api/groups/search?q={query}` - Search groups

### 5. Frontend Integration

The Flutter app is already configured to use these endpoints. Make sure to update the base URL in your Flutter app's API service:

```dart
// In lib/services/group_api_service.dart
static const String baseUrl = 'https://your-api-domain.com/api';
```

### 6. File Upload Configuration

The group banner image upload feature requires proper file storage configuration. Make sure your Laravel app has:

1. Proper storage disk configuration in `config/filesystems.php`
2. Storage link created with `php artisan storage:link`
3. Proper permissions for the storage directory

### 7. Testing the API

You can test the API endpoints using tools like Postman or curl:

```bash
# Get all groups
curl -X GET "http://your-domain.com/api/groups/public" \
  -H "Accept: application/json"

# Create a group (requires authentication)
curl -X POST "http://your-domain.com/api/groups" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Group",
    "description": "This is a test group",
    "category": "Technology",
    "meeting_type": "Online",
    "online_meeting_url": "https://zoom.us/j/123456789",
    "start_date": "2024-01-15",
    "start_time": "19:00",
    "end_time": "21:00",
    "timezone": "Eastern",
    "repeat": "Weekly",
    "admin_approval": true
  }'
```

### 8. Database Schema

The groups table includes the following fields:
- `id` - Primary key
- `title` - Group title
- `description` - Group description
- `category` - Group category
- `meeting_type` - In Person or Online
- `city`, `state`, `zip_code` - Location fields (for In Person meetings)
- `online_meeting_url` - Meeting URL (for Online meetings)
- `start_date`, `start_time`, `end_time` - Meeting schedule
- `timezone` - Meeting timezone
- `repeat` - Repeat frequency
- `group_banner_image` - Banner image path
- `admin_approval` - Admin approval required
- `created_by` - User who created the group
- `created_at`, `updated_at` - Timestamps

The group_members table includes:
- `id` - Primary key
- `group_id` - Foreign key to groups table
- `user_id` - Foreign key to users table
- `joined_at` - When the user joined
- `created_at`, `updated_at` - Timestamps

### 9. Features Included

✅ Complete CRUD operations for groups
✅ Group membership management
✅ File upload for banner images
✅ Search functionality
✅ Category filtering
✅ Meeting type support (In Person/Online)
✅ Repeat frequency support
✅ Admin approval system
✅ Proper validation and error handling
✅ Authentication and authorization
✅ Sample data seeder

### 10. Next Steps

1. Run the migrations and seeders
2. Test the API endpoints
3. Update the Flutter app's API base URL
4. Test the complete flow from frontend to backend
5. Deploy to production

The Groups feature is now fully integrated and ready to use!
