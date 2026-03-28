<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<?php include 'header.php';?>
<head>
  <meta charset="UTF-8">
  <title>Our Trainers - Smart Campus</title>
  <link rel="stylesheet" href="style.css">

  <!-- Font Awesome for social icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
	.trainer-section {
  padding: 60px 20px;
  background-color: #f5f7fa;
  text-align: center;
}

.trainer-section .container {
  max-width: 1200px;
  margin: auto;
}

.section-title {
  font-size: 2.5rem;
  color: #004B87;
  margin-bottom: 10px;
}

.section-subtitle {
  font-size: 1.2rem;
  color: #555;
  margin-bottom: 40px;
}

.trainer-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  justify-content: center;
}

.trainer-card {
  background-color: #fff;
  border-radius: 15px;
  padding: 20px;
  max-width: 300px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: center;
  transition: transform 0.3s;
}

.trainer-card:hover {
  transform: translateY(-5px);
}

.trainer-card img {
  width: 100%;
  height: auto;
  border-radius: 50%;
  margin-bottom: 15px;
}

.trainer-card h3 {
  margin: 10px 0 5px;
  color: #004B87;
}

.trainer-title {
  font-weight: bold;
  color: #555;
  margin-bottom: 10px;
}

.trainer-socials a {
  display: inline-block;
  margin: 0 8px;
  color: #004B87;
  font-size: 1.2rem;
  transition: color 0.3s;
}

.trainer-socials a:hover {
  color: #0066cc;
}

  </style>
</head>
<body>

<section class="trainer-section">
  <div class="container">
    <h2 class="section-title">👨‍🏫 Our Trainers</h2>
    <p class="section-subtitle">Meet our experienced and passionate educators</p>

    <div class="trainer-grid">

      <!-- Trainer Card 1 -->
      <div class="trainer-card">
        <img src="assets/img/trainers/trainer-2.jpg" alt="Trainer Photo">
        <h3>Dr. Priya Desai</h3>
        <p class="trainer-title">Data Science Expert</p>
        <p>Specialized in Machine Learning, AI, and Python programming. 10+ years of teaching experience.</p>
        <div class="trainer-socials">
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="mailto:priya@smartcampus.edu.in"><i class="fas fa-envelope"></i></a>
        </div>
      </div>

      <!-- Trainer Card 2 -->
      <div class="trainer-card">
        <img src="assets/img/trainers/trainer-1.jpg" alt="Trainer Photo">
        <h3>Mr. Arjun Patel</h3>
        <p class="trainer-title">Full Stack Developer</p>
        <p>Expert in Java, React, PHP, and MySQL. Known for building smart campus solutions.</p>
        <div class="trainer-socials">
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="mailto:arjun@smartcampus.edu.in"><i class="fas fa-envelope"></i></a>
        </div>
      </div>

      <!-- Trainer Card 3 -->
      <div class="trainer-card">
        <img src="assets/img/trainers/trainer-2-2.jpg" alt="Trainer Photo">
        <h3>Ms. Kavita Nair</h3>
        <p class="trainer-title">Cybersecurity Specialist</p>
        <p>Experienced in ethical hacking, network security & teaching cybersecurity to students.</p>
        <div class="trainer-socials">
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="mailto:kavita@smartcampus.edu.in"><i class="fas fa-envelope"></i></a>
        </div>
      </div>

    </div>
  </div>
</section>

</body>
<?php include 'footer.php';?>
</html>
