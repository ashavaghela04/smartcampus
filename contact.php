<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<?php include 'header.php';?>


<head>
  <meta charset="UTF-8">
  <title>Contact Us - Smart Campus</title>
  <link rel="stylesheet" href="style.css">

  <!-- Font Awesome CDN for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<section class="contact-section">
  <div class="contact-container">
    <h2 class="section-title">📬 Contact Us</h2>
    <p class="section-subtitle">We’d love to hear from you! Reach out via any method below.</p>

    <div class="contact-box">
      <div class="contact-left">
        <h3><i class="fas fa-info-circle"></i> Get in Touch</h3>
        <ul class="contact-info-list">
          <li><i class="fas fa-map-marker-alt"></i> Smart Campus, Knowledge Avenue, EduCity, India</li>
          <li><i class="fas fa-phone-alt"></i> +91 98765 43210</li>
          <li><i class="fas fa-envelope"></i> info@smartcampus.edu.in</li>
          <li><i class="fas fa-clock"></i> Mon - Fri, 9:00AM - 5:00PM</li>
        </ul>

        <div class="social-buttons">
          <a href="tel:+919876543210" class="icon-btn"><i class="fas fa-phone"></i></a>
          <a href="mailto:info@smartcampus.edu.in" class="icon-btn"><i class="fas fa-envelope"></i></a>
          <a href="https://maps.google.com" target="_blank" class="icon-btn"><i class="fas fa-map-marked-alt"></i></a>
        </div>
      </div>

      <div class="contact-right">
        <form action="send_message.php" method="POST" class="contact-form">
          <input type="text" name="name" placeholder="Your Name" required>
          <input type="email" name="email" placeholder="Your Email" required>
          <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
          <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
      </div>
    </div>

    <div class="map-container">
      <h3><i class="fas fa-map"></i> Our Location</h3>
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3782.048084989031!2d73.84751931535784!3d18.52043098473202!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2c08962b486c5%3A0xf1f72e527ec8e6d!2sShivaji%20Nagar%2C%20Pune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1621940836352!5m2!1sen!2sin"
        width="100%" 
        height="300" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy">
      </iframe>
    </div>
  </div>
</section>

</body>


<?php include 'footer.php';?>

</html>
