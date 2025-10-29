<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css"> 
</head>
<body>
    <?php include '../include/header.php'; ?>

<!-- Page Hero -->
 
<section class="page-hero">
    <div>
        <h1>Contact Us</h1>
        <p>We're Here to Help You</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="contact-container">
        <!-- Contact Info Cards -->
        <div class="contact-info-grid">
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Visit Us</h3>
                <p>123 Medical Center Drive<br>Healthcare City, HC 12345</p>
            </div>
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <h3>Call Us</h3>
                <p>+1 (555) 123-4567<br>Mon - Fri, 8AM - 6PM</p>
            </div>
            <div class="info-card">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Us</h3>
                <p>info@medsupply.com<br>support@medsupply.com</p>
            </div>
        </div>

        <!-- Contact Form & Map -->
        <div class="contact-content">
            <div class="contact-form-wrapper">
                <h2>Send Us a Message</h2>
                <p class="form-description">Have a question or need assistance? Fill out the form below and we'll get back to you as soon as possible.</p>
                
                <form class="contact-form" action="process-contact.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <div class="map-wrapper">
                <h2>Find Us Here</h2>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.019277383895!2d-122.41941548468208!3d37.77492977975903!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085809c6c8f4459%3A0xb10ed6d9b5050fa5!2sTwitter%20HQ!5e0!3m2!1sen!2sus!4v1580424239119!5m2!1sen!2sus" 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        style="border:0;" 
                        allowfullscreen="">
                    </iframe>
                </div>
                <div class="business-hours">
                    <h3><i class="fas fa-clock"></i> Business Hours</h3>
                    <div class="hours-grid">
                        <div class="hour-row">
                            <span class="day">Monday - Friday</span>
                            <span class="time">8:00 AM - 6:00 PM</span>
                        </div>
                        <div class="hour-row">
                            <span class="day">Saturday</span>
                            <span class="time">9:00 AM - 4:00 PM</span>
                        </div>
                        <div class="hour-row">
                            <span class="day">Sunday</span>
                            <span class="time">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Need Immediate Assistance?</h2>
        <p>Our customer support team is ready to help you with any urgent inquiries</p>
        <a href="tel:+15551234567" class="btn btn-primary">
            <i class="fas fa-phone-alt"></i> Call Now
        </a>
    </div>
</section>

<?php include '../include/footer.php'; ?>
</body>
</html>