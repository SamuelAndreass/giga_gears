# GigaGears - E-commerce Platform

## Overview
GigaGears is a Laravel 12-based e-commerce platform for technology and digital products. The application supports multiple user roles (admin, seller, customer) and includes features like product management, shopping cart, orders, and payments.

## Tech Stack
- **Backend**: Laravel 12 (PHP 8.4)
- **Frontend**: Vite, Tailwind CSS, Alpine.js
- **Database**: PostgreSQL (Replit managed)
- **Real-time**: Livewire 3.7
- **Authentication**: Laravel Fortify, Socialite (Google OAuth)

## Project Structure
- `/app` - Application logic (Controllers, Models, Livewire components)
- `/resources/views` - Blade templates
- `/routes` - Application routes
- `/database` - Migrations, seeders, factories
- `/public` - Public assets (images, CSS, JS)

## Development Setup (Replit)

### Environment Configuration
- Laravel server runs on port 5000 (0.0.0.0)
- Vite dev server runs on port 5173 (0.0.0.0)
- PostgreSQL database configured via environment variables
- Sessions stored in database

### Running the Application
The workflow runs:
1. Laravel server (port 5000)
2. Queue worker
3. Log tailing
4. Vite dev server (port 5173)

Command: `composer dev`

### Database
- **Seeded users**:
  - Admin: Check database
  - Seller: Check database
  - Customer: Check database

### Key Features
- Multi-role authentication (admin, seller, customer)
- Product catalog with categories
- Shopping cart and checkout
- Order management and tracking
- Seller analytics and dashboard
- Admin panel for managing users, products, and transactions
- Google OAuth integration
- Two-factor authentication support
- **Customer Communities/Forum** - Discussion platform for users to share experiences and ask questions

## Deployment (Replit)
- **Type**: Autoscale
- **Build**: Installs dependencies, builds assets, caches config
- **Run**: Serves Laravel on port 5000

## Recent Changes (Dec 8, 2024)
- Imported from GitHub repository
- Installed PHP 8.4 for compatibility
- Configured PostgreSQL database
- Updated Vite config for Replit (port 5173, no HMR host override)
- Modified composer dev script to run on 0.0.0.0:5000
- Set up deployment configuration for autoscale
- Built production assets
- **Added Customer Communities Feature** (Dec 8, 2024):
  - Database tables: forum_posts, forum_comments, forum_likes, forum_tags, forum_post_tags
  - Models: ForumPost, ForumComment, ForumLike, ForumTag
  - Controller: CommunityController with full CRUD + API endpoints
  - Views: community/index.blade.php (list), community/show.blade.php (detail)
  - Routes: /community, /community/{id}, /api/community/* endpoints
  - CSS: public/css/community-style.css
  - Seeder: ForumSeeder with demo posts and tags

## Customer Communities Feature
The forum feature allows customers to:
- View and create discussion posts
- Comment on posts (with nested replies)
- Like/unlike posts
- Filter posts by tags (Smartphones, Laptops, Audio, Gaming, Smart Home, etc.)
- Search posts by title/content
- Sort posts by newest, popular (views), or most liked

### Routes
- `GET /community` - Community index page
- `GET /community/{id}` - Post detail page
- `GET /api/community/posts` - Get posts (supports pagination, filtering, sorting)
- `POST /api/community/posts` - Create new post (authenticated)
- `POST /api/community/posts/{id}/like` - Toggle like (authenticated)
- `GET /api/community/posts/{id}/comments` - Get comments for a post
- `POST /api/community/comments` - Add comment (authenticated)
