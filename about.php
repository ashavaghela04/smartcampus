<!DOCTYPE html>
<html lang="en">

<?php include 'head.php'; ?>
<?php include 'header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

<main class="about-page">

    <section class="about-hero">
        <div class="container text-center">
            <h1 data-aos="fade-down" style="color:white">About SmartCampus</h1>
            <p data-aos="fade-up" data-aos-delay="200">Empowering Education Through Technology</p>
        </div>
    </section>

    <section class="who-we-are">
        <div class="container text-center">
            <h2 data-aos="zoom-in">Who We Are</h2>
            <p data-aos="fade-up">
                SmartCampus is a cutting-edge platform designed to revolutionize campus management and student engagement. Our mission is to connect students, faculty, and administration with seamless digital solutions that simplify academic life.
            </p>
        </div>
    </section>

    <section class="mission-vision">
        <div class="card" data-aos="flip-left">
            <h3>Our Mission</h3>
            <p>To provide innovative digital solutions that empower students and faculty to achieve excellence in education.</p>
        </div>
        <div class="card" data-aos="flip-right">
            <h3>Our Vision</h3>
            <p>To become the leading smart campus platform, creating a connected and efficient academic ecosystem worldwide.</p>
        </div>
    </section>

    <section class="team">
        <div class="container">
            <h2>Our Faculty</h2>
            <div class="team-grid">

                <?php
                // ✅ Connect to DB
                require_once __DIR__ . '/db/db.php';
                $pdo = Database::getInstance();

                // ✅ Fetch only approved faculty
                $stmt = $pdo->prepare("SELECT fname, lname, department, photo 
                                     FROM faculty 
                                     WHERE approved = 1");
                $stmt->execute();
                $facultyMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($facultyMembers) {
                    foreach ($facultyMembers as $faculty) {
                        // Full name
                        $fullName = htmlspecialchars($faculty['fname'] . " " . $faculty['lname']);

                        // Department
                        $department = htmlspecialchars($faculty['department']);

                        // Default info since no description field
                        $deptInfo = "Faculty member from the $department Department.";

                        // Photo path
                        $photo = !empty($faculty['photo']) ? 'forms/' . htmlspecialchars($faculty['photo']) : 'assets/images/faculty.png';

                ?>

                        <div class="team-member">
                            <img src="<?php echo $photo; ?>" alt="<?php echo $fullName; ?>">
                            <h3><?php echo $fullName; ?></h3>
                            <p class="department"><strong><?php echo $department; ?></strong></p>
                            <p class="dept-info"><?php echo $deptInfo; ?></p>
                        </div>

                <?php
                    }
                } else {
                    echo "<p style='text-align:center; color:#666;'>No approved faculty available at the moment.</p>";
                }
                ?>

            </div>
        </div>
    </section>


    <section class="stats">
        <h2>Our Achievements</h2>
        <div class="stats-grid">
            <div class="stat" data-aos="fade-up">
                <h3>500+</h3>
                <p>Students Enrolled</p>
            </div>
            <div class="stat" data-aos="fade-up" data-aos-delay="200">
                <h3>50+</h3>
                <p>Expert Trainers</p>
            </div>
            <div class="stat" data-aos="fade-up" data-aos-delay="400">
                <h3>100+</h3>
                <p>Courses Offered</p>
            </div>
            <div class="stat" data-aos="fade-up" data-aos-delay="600">
                <h3>10+</h3>
                <p>Years of Excellence</p>
            </div>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>

</html>