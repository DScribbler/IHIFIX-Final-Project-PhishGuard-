<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PhishGuard - Awareness & Protection</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin:0;
      padding:0;
      line-height:1.6;
      background:#fdfdfd;
      color:#333;
    }
    header {
      background: linear-gradient(135deg, #2c3e50, #34495e);
      color:#fff;
      padding:60px 20px;
      text-align:center;
      animation: fadeInDown 1.2s ease;
    }
    header h1 {
      margin:0;
      font-size:2.8rem;
      font-weight:600;
    }
    header p {
      margin:10px 0 20px;
      font-size:1.2rem;
      opacity:0.9;
    }
    .btn {
      background:#e67e22;
      color:#fff;
      padding:12px 24px;
      text-decoration:none;
      border-radius:30px;
      font-weight:bold;
      transition: background 0.3s, transform 0.2s;
    }
    .btn:hover {
      background:#d35400;
      transform: scale(1.05);
    }

    section {
      padding:60px 20px;
      max-width:1200px;
      margin:auto;
    }

    h2 {
      text-align:center;
      font-size:2rem;
      margin-bottom:40px;
      color:#2c3e50;
      position: relative;
    }
    h2::after {
      content:"";
      display:block;
      width:60px;
      height:3px;
      background:#e67e22;
      margin:10px auto 0;
      border-radius:2px;
    }

    .features {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap:25px;
    }
    .card {
      background:#fff;
      padding:30px 20px;
      border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
      text-align:center;
      transition: transform 0.3s, box-shadow 0.3s;
      animation: fadeInUp 1s ease;
      cursor:pointer;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow:0 8px 20px rgba(0,0,0,0.15);
    }
    .card i {
      font-size:2.5rem;
      color:#e67e22;
      margin-bottom:15px;
    }
    .card h3 {
      margin-bottom:10px;
      font-size:1.3rem;
      color:#2c3e50;
    }

    .about {
      text-align:center;
      max-width:800px;
      margin:auto;
      font-size:1.1rem;
      color:#555;
      animation: fadeIn 1.5s ease;
    }

    footer {
      background:#2c3e50;
      color:#fff;
      text-align:center;
      padding:20px;
      margin-top:40px;
      font-size:0.9rem;
    }

    /* Modal Styles */
    .modal {
      display:none;
      position:fixed;
      z-index:1000;
      left:0;
      top:0;
      width:100%;
      height:100%;
      overflow:auto;
      background: rgba(0,0,0,0.7);
      animation: fadeIn 0.3s ease;
    }
    .modal-content {
      background:#fff;
      margin:10% auto;
      padding:30px;
      border-radius:12px;
      max-width:600px;
      box-shadow:0 8px 20px rgba(0,0,0,0.2);
      animation: slideIn 0.4s ease;
      text-align:center;
    }
    .modal-content h3 {
      margin-top:0;
      color:#2c3e50;
    }
    .modal-text {
      opacity:0;
      display:inline-block;
      white-space:pre-line;
    }
    .close {
      color:#aaa;
      float:right;
      font-size:24px;
      font-weight:bold;
      cursor:pointer;
    }
    .close:hover {
      color:#e74c3c;
    }

    /* Animations */
    @keyframes fadeInDown {
      from { opacity:0; transform: translateY(-30px); }
      to { opacity:1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
      from { opacity:0; transform: translateY(30px); }
      to { opacity:1; transform: translateY(0); }
    }
    @keyframes fadeIn {
      from { opacity:0; }
      to { opacity:1; }
    }
    @keyframes slideIn {
      from { transform: scale(0.9); opacity:0; }
      to { transform: scale(1); opacity:1; }
    }
  </style>
</head>
<body>
  <header>
    <h1>Welcome to PhishGuard</h1>
    <p>Training and protecting organizations against phishing attacks with ease.</p>
    <a href="login.php" class="btn">Get Started</a>
  </header>

  <section>
    <h2>Our Features</h2>
    <div class="features">
      <div class="card" onclick="openModal('campaign')">
        <i class="fas fa-envelope-open-text"></i>
        <h3>Campaign Creator</h3>
        <p>Simulate phishing tests with customizable email and SMS templates.</p>
      </div>
      <div class="card" onclick="openModal('monitoring')">
        <i class="fas fa-user-shield"></i>
        <h3>Employee Monitoring</h3>
        <p>Track who opened emails, clicked links, or submitted data.</p>
      </div>
      <div class="card" onclick="openModal('analytics')">
        <i class="fas fa-chart-line"></i>
        <h3>Analytics Dashboard</h3>
        <p>View real-time statistics, risk scores, and awareness insights.</p>
      </div>
      <div class="card" onclick="openModal('template')">
        <i class="fas fa-flask"></i>
        <h3>Template Checker</h3>
        <p>Evaluate phishing realism before sending campaigns.</p>
      </div>
    </div>
  </section>

  <section>
    <h2>About Us</h2>
    <p class="about">
      PhishGuard is a cybersecurity awareness and simulation platform. 
      We help organizations identify high-risk employees, improve phishing awareness, 
      and strengthen security culture with smart training.
    </p>
  </section>

  <footer>
    <p>&copy; <?= date('Y') ?> PhishGuard. All Rights Reserved.</p>
  </footer>

  <!-- Modals -->
  <div id="modal-campaign" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('campaign')">&times;</span>
      <h3>Campaign Creator</h3>
      <p id="text-campaign" class="modal-text">
        Create realistic phishing campaigns to test employee awareness. 
        Customize email and SMS templates, schedule campaigns, 
        and analyze responses to identify vulnerabilities.
      </p>
    </div>
  </div>

  <div id="modal-monitoring" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('monitoring')">&times;</span>
      <h3>Employee Monitoring</h3>
      <p id="text-monitoring" class="modal-text">
        Monitor employee interactions with phishing simulations. 
        See who opened emails, clicked suspicious links, or entered sensitive data. 
        Track behavior trends to provide targeted awareness training.
      </p>
    </div>
  </div>

  <div id="modal-analytics" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('analytics')">&times;</span>
      <h3>Analytics Dashboard</h3>
      <p id="text-analytics" class="modal-text">
        Gain insights from powerful analytics dashboards. 
        Review campaign success rates, employee risk scores, and long-term trends 
        to measure improvement over time.
      </p>
    </div>
  </div>

  <div id="modal-template" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('template')">&times;</span>
      <h3>Template Checker</h3>
      <p id="text-template" class="modal-text">
        Test your phishing templates before deploying. 
        Ensure realism, detect weaknesses, and refine templates 
        to make training more effective and engaging.
      </p>
    </div>
  </div>

  <script>
    function openModal(id) {
      const modal = document.getElementById('modal-' + id);
      const textEl = document.getElementById('text-' + id);

      modal.style.display = 'block';

      // Typing effect for modal text
      let text = textEl.textContent.trim();
      textEl.textContent = "";
      textEl.style.opacity = 1;

      let i = 0;
      let speed = 25; // typing speed (ms)
      function typeWriter() {
        if (i < text.length) {
          textEl.textContent += text.charAt(i);
          i++;
          setTimeout(typeWriter, speed);
        }
      }
      typeWriter();
    }

    function closeModal(id) {
      document.getElementById('modal-' + id).style.display = 'none';
    }

    // Close modal when clicking outside content
    window.onclick = function(event) {
      document.querySelectorAll('.modal').forEach(modal => {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      });
    }
  </script>
</body>
</html>
