# 📄 About Us Page - Database-Driven Content

## Overview
The About Us page now fetches all content from the database, making it easy to update and manage content without editing code. All sections, titles, and content are stored in the `about_us_content` table.

---

## ✅ What Was Implemented

### Database Table
- **Table Name**: `about_us_content`
- **Purpose**: Store all About Us page content sections
- **Features**: Section ordering, active/inactive toggle, timestamps

### Content Sections
1. **Hero** - Main introduction
2. **Mission** - Company mission statement
3. **Vision** - Company vision statement
4. **Values** - Core values (multiple items)
5. **History** - Company history
6. **Team** - Team information
7. **Why Choose Us** - Key features/benefits
8. **Certifications** - Certifications and compliance

### Dynamic Page
- **Fetches from DB** - All content loaded from database
- **Flexible Layout** - Supports multiple section types
- **Image Support** - Optional images for each section
- **Ordering** - Sections display in custom order
- **Active/Inactive** - Toggle sections on/off

---

## 📁 Files Created/Modified

### Created Files:
1. **`used/about_us.sql`** - Database schema and default content
2. **`install_about_us.php`** - Installation script
3. **`ABOUT_US_DATABASE_FEATURE.md`** - This documentation

### Modified Files:
1. **`pages/about.php`** - Updated to fetch from database
2. **`css/about.css`** - Added new section styles

---

## 🗄️ Database Schema

### Table: `about_us_content`

```sql
CREATE TABLE about_us_content (
    content_id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(100) NOT NULL UNIQUE,
    section_title VARCHAR(255) NOT NULL,
    section_content TEXT NOT NULL,
    section_image VARCHAR(255) DEFAULT NULL,
    section_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Fields:
- **content_id** - Unique identifier
- **section_name** - Internal name (e.g., 'hero', 'mission')
- **section_title** - Display title (e.g., 'Our Mission')
- **section_content** - Main content (supports multiple paragraphs)
- **section_image** - Optional image path
- **section_order** - Display order (1, 2, 3...)
- **is_active** - Show/hide section (1 = active, 0 = inactive)
- **created_at** - Creation timestamp
- **updated_at** - Last update timestamp

### Indexes:
- `idx_section_order` - Fast ordering
- `idx_is_active` - Filter active sections
- `idx_section_name` - Quick lookups

---

## 🎯 Default Content Sections

### 1. Hero Section
- **Title**: "About REDY-MED"
- **Content**: Company introduction
- **Order**: 1

### 2. Mission Section
- **Title**: "Our Mission"
- **Content**: Mission statement
- **Order**: 2

### 3. Vision Section
- **Title**: "Our Vision"
- **Content**: Vision statement
- **Order**: 3

### 4. Values Section
- **Title**: "Our Core Values"
- **Content**: Multiple values (Quality, Integrity, Innovation, etc.)
- **Format**: `Title: Description` (one per line)
- **Order**: 4

### 5. History Section
- **Title**: "Our History"
- **Content**: Company history
- **Order**: 5

### 6. Team Section
- **Title**: "Our Team"
- **Content**: Team description
- **Order**: 6

### 7. Why Choose Us Section
- **Title**: "Why Choose REDY-MED?"
- **Content**: Multiple features (one per line)
- **Format**: `Feature: Description`
- **Order**: 7

### 8. Certifications Section
- **Title**: "Certifications & Compliance"
- **Content**: Certification details
- **Order**: 8

---

## 🔧 How It Works

### 1. Installation
```bash
# Run installation script
http://localhost/vita/install_about_us.php
```

### 2. Database Query
```php
// Fetch all active sections
$content_query = "SELECT * FROM about_us_content 
                  WHERE is_active = 1 
                  ORDER BY section_order ASC";
```

### 3. Content Display
```php
// Get content for specific section
function getContent($sections, $section_name, $field = 'section_content') {
    return isset($sections[$section_name]) 
        ? $sections[$section_name][$field] 
        : '';
}
```

### 4. Dynamic Rendering
- **Hero Section**: Large intro with image
- **Mission/Vision**: Alternating left/right layout
- **Values**: Grid of cards with icons
- **Features**: Grid of feature items
- **History/Certifications**: Full-width text sections

---

## 📝 Usage

### For Administrators:

#### Update Content via Database
```sql
-- Update mission statement
UPDATE about_us_content 
SET section_content = 'New mission statement here...'
WHERE section_name = 'mission';

-- Update section title
UPDATE about_us_content 
SET section_title = 'New Title'
WHERE section_name = 'hero';

-- Add image to section
UPDATE about_us_content 
SET section_image = 'images/about/new-image.jpg'
WHERE section_name = 'vision';
```

#### Reorder Sections
```sql
-- Change section order
UPDATE about_us_content 
SET section_order = 2
WHERE section_name = 'values';
```

#### Hide/Show Sections
```sql
-- Hide a section
UPDATE about_us_content 
SET is_active = 0
WHERE section_name = 'team';

-- Show a section
UPDATE about_us_content 
SET is_active = 1
WHERE section_name = 'team';
```

#### Add New Section
```sql
INSERT INTO about_us_content 
(section_name, section_title, section_content, section_order, is_active) 
VALUES 
('awards', 'Our Awards', 'Award content here...', 9, 1);
```

### For Developers:

#### Add New Section Type
1. **Add to database**:
```sql
INSERT INTO about_us_content (section_name, section_title, section_content, section_order) 
VALUES ('new_section', 'New Section', 'Content...', 10);
```

2. **Add to about.php**:
```php
<?php if (isset($content_sections['new_section'])): ?>
<section class="about-section">
    <h2><?php echo htmlspecialchars(getContent($content_sections, 'new_section', 'section_title')); ?></h2>
    <p><?php echo htmlspecialchars(getContent($content_sections, 'new_section', 'section_content')); ?></p>
</section>
<?php endif; ?>
```

---

## 🎨 Content Formatting

### Multiple Paragraphs
Separate paragraphs with newlines (`\n`):
```
First paragraph here.
Second paragraph here.
Third paragraph here.
```

### List Items (Values/Features)
Use format: `Title: Description`
```
Quality: We never compromise on quality.
Integrity: We conduct business with honesty.
Innovation: We embrace new technologies.
```

### Images
Store images in `images/about/` folder:
```
images/about/hero.jpg
images/about/mission.jpg
images/about/vision.jpg
```

---

## 💡 Benefits

### ✅ Easy Updates
- **No Code Changes** - Update content via database
- **Quick Edits** - Change text without touching files
- **Version Control** - Track changes with timestamps

### ✅ Flexibility
- **Reorder Sections** - Change display order easily
- **Toggle Visibility** - Show/hide sections
- **Add Sections** - Expand content anytime

### ✅ Maintainability
- **Centralized Content** - All content in one table
- **Consistent Structure** - Standard format for all sections
- **Easy Backup** - Export/import content easily

---

## 🐛 Troubleshooting

### Issue: Content not displaying
**Solution**: Check `is_active = 1` in database

### Issue: Wrong order
**Solution**: Update `section_order` values

### Issue: Images not showing
**Solution**: Verify image path exists in `images/about/` folder

### Issue: Blank sections
**Solution**: Check `section_content` is not empty

---

## 📊 Summary

The About Us page is now **database-driven** with:

✅ **8 default sections** - Hero, Mission, Vision, Values, etc.  
✅ **Easy updates** - Edit via database  
✅ **Flexible ordering** - Custom section order  
✅ **Toggle visibility** - Show/hide sections  
✅ **Image support** - Optional images per section  
✅ **Timestamps** - Track creation/updates  
✅ **Indexed** - Fast queries  
✅ **Responsive** - Mobile-friendly layout  

**Access the page at**: `http://localhost/vita/index.php?page=about`

---

## 🎉 Next Steps

### 1. Run Installation
```
http://localhost/vita/install_about_us.php
```

### 2. View Page
```
http://localhost/vita/index.php?page=about
```

### 3. Add Images (Optional)
- Create folder: `images/about/`
- Add images: `hero.jpg`, `mission.jpg`, etc.
- Update database with image paths

### 4. Customize Content
- Edit content via database
- Reorder sections
- Add new sections
- Toggle visibility

### 5. Create Admin Panel (Future)
- Build admin interface to manage content
- WYSIWYG editor for content
- Image upload functionality
- Drag-and-drop section ordering

---

**Enjoy your new database-driven About Us page! 📄✨**

