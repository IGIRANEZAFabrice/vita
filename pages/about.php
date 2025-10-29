<?php
// Fetch About Us content from database
require_once 'config/db.php';

// Get all active content sections ordered by section_order
$content_query = "SELECT * FROM about_us_content WHERE is_active = 1 ORDER BY section_order ASC";
$content_result = $conn->query($content_query);

// Store content in an array for easy access
$content_sections = [];
if ($content_result && $content_result->num_rows > 0) {
    while ($row = $content_result->fetch_assoc()) {
        $content_sections[$row['section_name']] = $row;
    }
}

// Helper function to get content
function getContent($sections, $section_name, $field = 'section_content', $default = '') {
    return isset($sections[$section_name]) ? $sections[$section_name][$field] : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - REDY-MED</title>
    <link rel="icon" type="image/png" href="logo/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
<?php include 'include/header.php'; ?>

<!-- Page Hero -->
<section class="page-hero">
    <div>
        <h1><?php echo htmlspecialchars(getContent($content_sections, 'hero', 'section_title', 'About Us')); ?></h1>
        <p><?php echo htmlspecialchars(substr(getContent($content_sections, 'hero', 'section_content', 'Your Trusted Medical Supply Partner'), 0, 100)); ?>...</p>
    </div>
</section>

<!-- Hero/Introduction Section -->
<?php if (isset($content_sections['hero'])): ?>
<section class="about-section">
    <div class="about-grid">
        <div class="about-image">
            <?php
            $hero_image = getContent($content_sections, 'hero', 'section_image');
            if ($hero_image && file_exists($hero_image)):
            ?>
                <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="<?php echo htmlspecialchars(getContent($content_sections, 'hero', 'section_title')); ?>">
            <?php else: ?>
                🩺
            <?php endif; ?>
        </div>
        <div class="about-content">
            <h2><?php echo htmlspecialchars(getContent($content_sections, 'hero', 'section_title')); ?></h2>
            <?php
            $hero_content = getContent($content_sections, 'hero', 'section_content');
            $paragraphs = explode("\n", $hero_content);
            foreach ($paragraphs as $paragraph) {
                if (trim($paragraph)) {
                    echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
                }
            }
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Mission Section -->
<?php if (isset($content_sections['mission'])): ?>
<section class="about-section alternate">
    <div class="about-grid reverse">
        <div class="about-content">
            <h2><?php echo htmlspecialchars(getContent($content_sections, 'mission', 'section_title')); ?></h2>
            <?php
            $mission_content = getContent($content_sections, 'mission', 'section_content');
            $paragraphs = explode("\n", $mission_content);
            foreach ($paragraphs as $paragraph) {
                if (trim($paragraph)) {
                    echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
                }
            }
            ?>
        </div>
        <div class="about-image">
            <?php
            $mission_image = getContent($content_sections, 'mission', 'section_image');
            if ($mission_image && file_exists($mission_image)):
            ?>
                <img src="<?php echo htmlspecialchars($mission_image); ?>" alt="<?php echo htmlspecialchars(getContent($content_sections, 'mission', 'section_title')); ?>">
            <?php else: ?>
                🎯
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Vision Section -->
<?php if (isset($content_sections['vision'])): ?>
<section class="about-section">
    <div class="about-grid">
        <div class="about-image">
            <?php
            $vision_image = getContent($content_sections, 'vision', 'section_image');
            if ($vision_image && file_exists($vision_image)):
            ?>
                <img src="<?php echo htmlspecialchars($vision_image); ?>" alt="<?php echo htmlspecialchars(getContent($content_sections, 'vision', 'section_title')); ?>">
            <?php else: ?>
                🔭
            <?php endif; ?>
        </div>
        <div class="about-content">
            <h2><?php echo htmlspecialchars(getContent($content_sections, 'vision', 'section_title')); ?></h2>
            <?php
            $vision_content = getContent($content_sections, 'vision', 'section_content');
            $paragraphs = explode("\n", $vision_content);
            foreach ($paragraphs as $paragraph) {
                if (trim($paragraph)) {
                    echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
                }
            }
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hospital"></i>
            </div>
            <div class="stat-number">500+</div>
            <div class="stat-label">Healthcare Facilities</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number">2000+</div>
            <div class="stat-label">Products Available</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">15K+</div>
            <div class="stat-label">Happy Clients</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-number">6+</div>
            <div class="stat-label">Years Experience</div>
        </div>
    </div>
</section>

<!-- Values Section -->
<?php if (isset($content_sections['values'])): ?>
<section class="values-section">
    <h2><?php echo htmlspecialchars(getContent($content_sections, 'values', 'section_title')); ?></h2>
    <div class="values-grid">
        <?php
        $values_content = getContent($content_sections, 'values', 'section_content');
        $values = explode("\n", $values_content);
        $icons = ['shield-alt', 'handshake', 'lightbulb', 'heart', 'star', 'check-circle'];
        $icon_index = 0;

        foreach ($values as $value) {
            $value = trim($value);
            if (empty($value)) continue;

            // Split by colon to get title and description
            $parts = explode(':', $value, 2);
            $title = isset($parts[0]) ? trim($parts[0]) : $value;
            $description = isset($parts[1]) ? trim($parts[1]) : '';

            $icon = $icons[$icon_index % count($icons)];
            $icon_index++;
        ?>
        <div class="value-card">
            <div class="value-icon">
                <i class="fas fa-<?php echo $icon; ?>"></i>
            </div>
            <h3><?php echo htmlspecialchars($title); ?></h3>
            <?php if ($description): ?>
                <p><?php echo htmlspecialchars($description); ?></p>
            <?php endif; ?>
        </div>
        <?php } ?>
    </div>
</section>
<?php endif; ?>

<!-- Why Choose Us Section -->
<?php if (isset($content_sections['why_choose_us'])): ?>
<section class="about-section">
    <div class="about-content-full">
        <h2><?php echo htmlspecialchars(getContent($content_sections, 'why_choose_us', 'section_title')); ?></h2>
        <div class="features-grid">
            <?php
            $features_content = getContent($content_sections, 'why_choose_us', 'section_content');
            $features = explode("\n", $features_content);
            $feature_icons = ['boxes', 'certificate', 'headset', 'dollar-sign', 'shipping-fast', 'tools'];
            $feature_icon_index = 0;

            foreach ($features as $feature) {
                $feature = trim($feature);
                if (empty($feature)) continue;

                // Split by colon to get title and description
                $parts = explode(':', $feature, 2);
                $title = isset($parts[0]) ? trim($parts[0]) : $feature;
                $description = isset($parts[1]) ? trim($parts[1]) : '';

                $icon = $feature_icons[$feature_icon_index % count($feature_icons)];
                $feature_icon_index++;
            ?>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-<?php echo $icon; ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($title); ?></h4>
                <?php if ($description): ?>
                    <p><?php echo htmlspecialchars($description); ?></p>
                <?php endif; ?>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- History Section -->
<?php if (isset($content_sections['history'])): ?>
<section class="about-section alternate">
    <div class="about-content-full">
        <h2><?php echo htmlspecialchars(getContent($content_sections, 'history', 'section_title')); ?></h2>
        <?php
        $history_content = getContent($content_sections, 'history', 'section_content');
        $paragraphs = explode("\n", $history_content);
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph)) {
                echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
            }
        }
        ?>
    </div>
</section>
<?php endif; ?>

<!-- Certifications Section -->
<?php if (isset($content_sections['certifications'])): ?>
<section class="about-section">
    <div class="about-content-full">
        <h2><?php echo htmlspecialchars(getContent($content_sections, 'certifications', 'section_title')); ?></h2>
        <?php
        $cert_content = getContent($content_sections, 'certifications', 'section_content');
        $paragraphs = explode("\n", $cert_content);
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph)) {
                echo '<p>' . nl2br(htmlspecialchars(trim($paragraph))) . '</p>';
            }
        }
        ?>
    </div>
</section>
<?php endif; ?>

<?php include 'include/footer.php'; ?>
</body>
</html>