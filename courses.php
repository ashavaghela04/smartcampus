<?php
session_start();
$pageTitle = "Courses";
$activePage = "courses";
 include 'head.php'; 
 include 'header.php';
include 'db/db.php';

// ✅ Connect to DB
$pdo = Database::getInstance();

// ✅ Fetch courses
$stmt = $pdo->prepare("SELECT id, title, description, category, price, image, trainer FROM courses ORDER BY id DESC");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main id="main">

  <!-- Page Title -->
  <section class="page-title">
    <div class="heading text-center">
      <h1>Our Courses</h1>
      <p>Explore a wide range of courses designed for students and professionals.</p>
</div>
  </section><!-- End Page Title -->

  <!-- ======= Courses Section ======= -->
  <section class="courses section">
    <div class="container">

      <div class="row gy-4">
        <?php if (!empty($courses)): ?>
          <?php foreach ($courses as $course): ?>
            <div class="col-lg-4 col-md-6 d-flex">
              <div class="course-item">
                <img src="<?php echo !empty($course['image']) ? 'uploads/courses/' . htmlspecialchars($course['image']) : 'assets/img/course-default.jpg'; ?>" 
                     class="img-fluid" alt="<?php echo htmlspecialchars($course['title']); ?>">

                <div class="course-content">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="category"><?php echo htmlspecialchars($course['category']); ?></span>
                    <span class="price">₹<?php echo number_format($course['price'], 2); ?></span>
                  </div>

                  <h3>
                    <a href="course-details.php?id=<?php echo $course['id']; ?>">
                      <?php echo htmlspecialchars($course['title']); ?>
                    </a>
                  </h3>
                  <p class="description"><?php echo substr(htmlspecialchars($course['description']), 0, 100) . '...'; ?></p>

                  <div class="trainer d-flex justify-content-between align-items-center">
                    <div class="trainer-profile d-flex align-items-center">
                      <img src="assets/img/faculty.png" class="img-fluid" alt="">
                      <a href="#" class="trainer-link"><?php echo htmlspecialchars($course['trainer']); ?></a>
                    </div>
                    <div class="trainer-rank d-flex align-items-center">
                      <i class="bi bi-people user-icon"></i>&nbsp;120
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Course Item -->
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">No courses available at the moment.</p>
        <?php endif; ?>
      </div>

    </div>
  </section><!-- End Courses Section -->

</main><!-- End #main -->

<?php include 'footer.php'; ?>
