<!DOCTYPE html>
<html lang="en">
<?php include 'head.php'; ?>
<?php include 'header.php';?>
<head>
  
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #eef2f7, #f8fbff);
      color: #333;
    }
    .search-bar {
      padding: 10px 14px;
      border-radius: 10px;
      border: none;
      width: 260px;
      font-size: 1rem;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .section-title {
      text-align: center;
      font-size: 2.8rem;
      margin: 50px 0 30px;
      color: #1e3a8a;
      position: relative;
    }

    .section-title::after {
      content: '';
      display: block;
      width: 80px;
      height: 4px;
      background: #3b82f6;
      margin: 10px auto 0;
      border-radius: 4px;
    }

    .event-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
      padding: 0 6%;
      margin-bottom: 80px;
    }

    .event-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border: 1px solid #e2e8f0;
    }

    .event-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.1);
    }

    .event-content {
      padding: 24px;
    }

    .event-content h3 {
      font-size: 1.5rem;
      color: #1e40af;
      margin-bottom: 12px;
    }

    .event-info {
      font-size: 1rem;
      color: #6b7280;
      margin-bottom: 10px;
    }

    .countdown {
      font-size: 0.9rem;
      color: #f59e0b;
      margin-bottom: 16px;
    }

    .event-content button {
      padding: 12px 20px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .event-content button:hover {
      background-color: #1d4ed8;
    }

    
  </style>
</head>
<body>

 
  

  <!-- Event Section -->
  <section class="event-section">
    <div class="container">
      <h2 class="section-title">Upcoming Events</h2>
      <div class="event-grid" id="eventGrid">
        <!-- Static Events -->
        <div class="event-card">
          <div class="event-content">
		  
            <h3>Tech Symposium 2025</h3>
            <p class="event-info">Date: August 12, 2025 | Venue: Main Auditorium</p>
            <p class="event-info">Explore the latest innovations in AI, IoT, and Cloud Computing.</p>
            <p class="countdown">Starts in: 20 days</p>
            <button>Learn More</button>
          </div>
        </div>
        <div class="event-card">
          <div class="event-content">
            <h3>Cultural Fest</h3>
            <p class="event-info">Date: September 5, 2025 | Venue: Open Ground</p>
            <p class="event-info">A celebration of talent, music, and culture from across the campus.</p>
            <p class="countdown">Starts in: 44 days</p>
            <button>Learn More</button>
          </div>
        </div>
        <div class="event-card">
          <div class="event-content">
            <h3>Startup Pitch Day</h3>
            <p class="event-info">Date: October 1, 2025 | Venue: Innovation Lab</p>
            <p class="event-info">Pitch your startup idea to leading investors and win funding.</p>
            <p class="countdown">Starts in: 70 days</p>
            <button>Learn More</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php';?>

</body>
</html>
