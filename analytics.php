  <?php
  session_start();
  $fullname=$_SESSION['fullname'];
  $user=$_SESSION['user'];
if(!isset($_SESSION['user'])){
  header('location:login.php');
}
else
{
  $userprofile=$user;
}

include('connection.php');
?>

<html lang="en">
  <head>
    <title>Traffic Sign Analytics</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
      /* Layout CSS - matches your existing style */
      body,
      html {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #222;
        color: #eee;
        transition: background-color 0.3s ease, color 0.3s ease;
      }

      body.light-mode {
        background-color: #f0f0f0;
        color: #222;
      }

      .wrapper {
        display: flex;
        height: 100vh;
        overflow: hidden;
      }

      #sidebar {
        width: 220px;
        background: #111;
        color: #eee;
        flex-shrink: 0;
        padding-top: 40px;
        transition: all 0.3s ease;
      }

      #sidebar.collapsed {
        margin-left: -220px;
      }

      body.light-mode #sidebar {
        background: #007bff;
        color: #fff;
      }

      #sidebar ul.list-unstyled {
        padding-left: 0;
      }

      #sidebar ul li {
        padding: 15px 30px;
        font-weight: normal;
      }

      #sidebar ul li.active,
      #sidebar ul li:hover {
        background: #007bff;
        cursor: pointer;
      }

      #sidebar ul li a {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
      }

      #sidebar ul li a .fa {
        margin-right: 12px;
        font-size: 16px;
      }

      #sidebar .footer {
        position: absolute;
        bottom: 0;
        width: 220px;
        padding: 10px 30px;
        font-size: 0.8rem;
        background: rgba(0, 0, 0, 0.2);
      }
      body.light-mode #sidebar .footer {
        background: rgba(255, 255, 255, 0.2);
        color: white;
      }

      #content-area {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        height: 100vh;
        background: inherit;
        color: inherit;
        padding: 0 20px 20px 20px;
      }

      .main-navbar {
        display: flex;
        align-items: center;
        background: #111;
        color: #eee;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.7);
        user-select: none;
        justify-content: space-between;
      }

      body.light-mode .main-navbar {
        background: #007bff;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
      }

      .toggle-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: inherit;
        cursor: pointer;
      }

      .main-navbar > div:last-child {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .main-navbar a.text-reset {
        color: inherit;
        font-size: 1.3rem;
        position: relative;
      }

      .badge.bg-danger {
        position: absolute;
        top: -5px;
        right: -10px;
        font-size: 0.6rem;
        padding: 0.2em 0.35em;
      }

      .dropdown-menu {
        min-width: 150px;
      }

      .main-content {
        flex-grow: 1;
        margin-top: 1rem;
        overflow-y: auto;
      }

      .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        max-width: 1200px;
        margin: 0 auto;
      }

      @media (max-width: 767px) {
        .dashboard-grid {
          grid-template-columns: 1fr;
        }
      }

      .card {
        background: #222;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
        color: #eee;
        display: flex;
        flex-direction: column;
      }

      body.light-mode .card {
        background: #fff;
        color: #222;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }

      .card h5 {
        margin-bottom: 1rem;
      }

      /* Chart containers */
      .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
      }

      /* Heatmap styles */
      #heatmap {
        height: 300px;
        width: 100%;
        border-radius: 8px;
      }

      /* Analytics table */
      .analytics-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
      }

      .analytics-table th, 
      .analytics-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #444;
      }

      body.light-mode .analytics-table th,
      body.light-mode .analytics-table td {
        border-bottom: 1px solid #ddd;
      }

      .analytics-table th {
        background-color: #333;
      }

      body.light-mode .analytics-table th {
        background-color: #f0f0f0;
      }

      /* Time distribution chart */
      #timeDistributionChart {
        max-height: 300px;
      }
      /* Theme toggle button */
#themeToggle {
  border: none;
  background: transparent;
  color: inherit;
  font-size: 1.2rem;
  margin-right: 0.5rem;
}

/* Chart adjustments for light mode */
body.light-mode .chartjs-render-monitor {
  color: #333 !important;
}

body.light-mode .chartjs-grid line {
  stroke: rgba(0, 0, 0, 0.1) !important;
}

body.light-mode .chartjs-tooltip {
  background: #fff !important;
  color: #333 !important;
  border: 1px solid #ddd !important;
}

/* Leaflet map adjustments */
body.light-mode .leaflet-tile {
  filter: none !important;
}

body.light-mode .leaflet-container {
  background: #f8f9fa !important;
}

/* Table adjustments */
body.light-mode .analytics-table th {
  background-color: #f8f9fa !important;
  color: #333 !important;
}

body.light-mode .analytics-table td {
  color: #333 !important;
}

body.light-mode .analytics-table tr {
  border-bottom: 1px solid #dee2e6 !important;
}
/* Sign Statistics Table */
body:not(.light-mode) .analytics-table th,
body:not(.light-mode) .analytics-table td {
  color: #eee !important; /* Light text in dark mode */
}

body.light-mode .analytics-table th,
body.light-mode .analytics-table td {
  color: #333 !important; /* Dark text in light mode */
}
    </style>
  </head>

  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <nav id="sidebar">
        <ul class="list-unstyled mt-4 mb-5 px-3">
          <li>
            <a href="index.php"><span class="fa fa-home"></span> Home</a>
          </li>
          <li>
            <a href="uploads.html"><span class="fa fa-upload"></span> Uploads</a>
          </li>
          <li class="active">
            <a href="analytics.php"><span class="fa fa-bar-chart"></span> Analytics</a>
          </li>
          <li>
            <a href="history.php"><span class="fa fa-history"></span> History</a>
          </li>
          <li>
            <a href="settings.php"><span class="fa fa-gear"></span> Settings</a>
          </li>
          <li>
            <a href="https://www.mdpi.com/2227-7390/12/2/297"><span class="fa fa-question-circle"></span> Help</a>
          </li>
        </ul>
        <div class="footer px-3">
          <p class="small">
            &copy;
            <script>
              document.write(new Date().getFullYear());
            </script>
            All rights reserved | Designed by
            <a href="#" target="_blank" class="text-white">Nshutiyanjye</a>
          </p>
        </div>
      </nav>

      <!-- Content area -->
      <div id="content-area">
        <!-- Navbar -->
        <div class="main-navbar">
          <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
            <i class="fa fa-bars"></i>
          </button>

          <div>
            <h6>Traffic Sign Detection Analytics</h6>
          </div>

          <div class="d-flex align-items-center gap-3">
            <!-- Add this button before the notification dropdown -->
<button class="btn btn-sm btn-outline-secondary" id="themeToggle" title="Toggle dark mode">
  <i class="fa fa-moon"></i>
</button>
            <div class="dropdown">
              <a
                class="dropdown-toggle text-reset text-decoration-none"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                style="position: relative; padding-right: 1.5rem"
                title="Notifications"
              >
                <span style="position: relative; display: inline-block">
                  <i class="fa fa-bell"></i>
                  <span
                    class="badge bg-danger rounded-pill"
                    style="
                      position: absolute;
                      top: -5px;
                      right: -10px;
                      font-size: 0.6rem;
                    "
                    >3</span
                  >
                </span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">New data available</a></li>
                <li><a class="dropdown-item" href="#">System updated</a></li>
              </ul>
            </div>

            <div class="dropdown">
              <a
                class="dropdown-toggle d-flex align-items-center text-reset"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                </svg>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><?php echo  $fullname; ?></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
               <li><a id="logout-link" class="dropdown-item" href="logout.php">Logout</a></li>

              <script>
        document.addEventListener("DOMContentLoaded", () => {
      const logoutLink = document.getElementById("logout-link");

    if (logoutLink) {
      logoutLink.addEventListener("click", function (e) {
        e.preventDefault(); // Stop default link behavior

        // Send reset request to FastAPI
        fetch("http://localhost:8000/analytics?reset=true", {
          method: "GET",
          keepalive: true // Ensures request goes through even if page is unloading
        }).finally(() => {
          // Redirect to logout after reset
          window.location.href = logoutLink.href;
        });
      });
    }
  });
</script>

              </ul>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <div class="main-content"> 
          <div class="dashboard-grid">
            <!-- Sign Frequency Card -->
            <div class="card">
              <h5>Most Frequent Signs</h5>
              <div class="chart-container">
                <canvas id="signFrequencyChart"></canvas>
              </div>
            </div>

            <!-- Detection Locations Card -->
            <div class="card">
              <h5>Detection Locations</h5>
              <div id="heatmap"></div>
            </div>

            <!-- Time Distribution Card -->
            <div class="card">
              <h5>Detection Time Distribution</h5>
              <div class="chart-container">
                <canvas id="timeDistributionChart"></canvas>
              </div>
            </div>

            <!-- Sign Statistics Card -->
            <div class="card">
              <h5>Sign Detection Statistics</h5>
              <table class="analytics-table">
                <thead>
                  <tr>
                    <th>Sign Type</th>
                    <th>Count</th>
                    <th>% of Total</th>
                    <th>Last Detected</th>
                  </tr>
                </thead>
                <tbody id="signStatsBody">
                  <!-- Will be populated by JavaScript -->
                </tbody>
              </table>
            </div>

            <!-- Hourly Activity Card -->
            <div class="card">
              <h5>Hourly Detection Activity</h5>
              <div class="chart-container">
                <canvas id="hourlyActivityChart"></canvas>
              </div>
            </div>

            <!-- Rare Signs Card -->
            <div class="card">
              <h5>Rarely Detected Signs</h5>
              <div class="chart-container">
                <canvas id="rareSignsChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Heatmap plugin -->
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
  sessionStorage.setItem('email', "<?php echo $userprofile; ?>");
</script>


    <script>
    // Theme management
function initTheme() {
  const savedTheme = localStorage.getItem('theme') || 'dark';
  const isLight = savedTheme === 'light';
  const themeToggle = document.getElementById('themeToggle');
  
  // Set initial state
  if (isLight) {
    document.body.classList.add('light-mode');
    themeToggle.innerHTML = '<i class="fa fa-sun"></i>';
  } else {
    document.body.classList.remove('light-mode');
    themeToggle.innerHTML = '<i class="fa fa-moon"></i>';
  }
  
  // Update charts if they exist
  updateChartsTheme(isLight);
}

function toggleTheme() {
  const isLight = document.body.classList.toggle('light-mode');
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
  const themeToggle = document.getElementById('themeToggle');
  
  // Update icon
  themeToggle.innerHTML = isLight ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>';
  
  // Update charts
  updateChartsTheme(isLight);
  
  // Refresh map if it exists
  if (heatmapMap) {
    setTimeout(() => heatmapMap.invalidateSize(), 100);
  }
}

function updateChartsTheme(isLight) {
  const textColor = isLight ? '#333' : '#eee';
  const gridColor = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
  
  // Update all charts
  const charts = [signFrequencyChart, timeDistributionChart, hourlyActivityChart, rareSignsChart];
  
  charts.forEach(chart => {
    if (chart) {
      chart.options.scales.x.ticks.color = textColor;
      chart.options.scales.y.ticks.color = textColor;
      chart.options.scales.x.grid.color = gridColor;
      chart.options.scales.y.grid.color = gridColor;
      chart.options.plugins.legend.labels.color = textColor;
      chart.update();
    }
  });
}

// Update getChartOptions function
function getChartOptions(type) {
  const isLight = document.body.classList.contains('light-mode');
  const textColor = isLight ? '#333' : '#eee';
  const gridColor = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
  
  return {
    responsive: true, 
    maintainAspectRatio: false,
    scales: {
      y: { 
        beginAtZero: true, 
        ticks: { color: textColor }, 
        grid: { color: gridColor } 
      },
      x: { 
        ticks: { color: textColor }, 
        grid: { color: gridColor } 
      }
    },
    plugins: { 
      legend: { 
        labels: { color: textColor } 
      } 
    }
  };
}
// Declare global variables for charts and map
let signFrequencyChart, timeDistributionChart, hourlyActivityChart, rareSignsChart;
let heatmapMap, heatmapLayer;

// Sidebar toggle functionality
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('collapsed');
}

// Initialize all charts
function initCharts() {
  const freqCtx = document.getElementById('signFrequencyChart').getContext('2d');
  signFrequencyChart = new Chart(freqCtx, {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Detection Count', data: [], backgroundColor: [], borderColor: [], borderWidth: 1 }] },
    options: getChartOptions('bar')
  });

  const timeCtx = document.getElementById('timeDistributionChart').getContext('2d');
  timeDistributionChart = new Chart(timeCtx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Detections per hour', data: [], fill: true, backgroundColor: 'rgba(54, 162, 235, 0.2)', borderColor: 'rgba(54, 162, 235, 1)', tension: 0.4 }] },
    options: getChartOptions('line')
  });

  const hourlyCtx = document.getElementById('hourlyActivityChart').getContext('2d');
  hourlyActivityChart = new Chart(hourlyCtx, {
    type: 'bar',
    data: { labels: [], datasets: [{ label: 'Detections', data: [], backgroundColor: 'rgba(75, 192, 192, 0.7)', borderColor: 'rgba(75, 192, 192, 1)', borderWidth: 1 }] },
    options: getChartOptions('bar')
  });

  const rareCtx = document.getElementById('rareSignsChart').getContext('2d');
  rareSignsChart = new Chart(rareCtx, {
    type: 'doughnut',
    data: { labels: [], datasets: [{ label: 'Detection Count', data: [], backgroundColor: [], borderColor: [], borderWidth: 1 }] },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'right', labels: { color: '#eee' } } }
    }
  });
}

function getChartOptions(type) {
  return {
    responsive: true, maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: true, ticks: { color: '#eee' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
      x: { ticks: { color: '#eee' }, grid: { color: 'rgba(255, 255, 255, 0.1)' } }
    },
    plugins: { legend: { labels: { color: '#eee' } } }
  };
}

// Initialize heatmap
function initHeatmap() {
  const isLight = document.body.classList.contains('light-mode');
  heatmapMap = L.map('heatmap').setView([0, 0], 2);
  
  // Use different tile layer based on theme
  if (isLight) {
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(heatmapMap);
  } else {
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(heatmapMap);
  }

  heatmapLayer = L.heatLayer([], { radius: 25, blur: 15, maxZoom: 17 }).addTo(heatmapMap);
}

// Update heatmap with new data
function updateHeatmap(locations) {
  if (!locations || locations.length === 0) return;
  const heatData = locations.map(p => [p[0], p[1], p[2] * 100]);
  heatmapLayer.setLatLngs(heatData);
  const avgLat = locations.reduce((sum, p) => sum + p[0], 0) / locations.length;
  const avgLng = locations.reduce((sum, p) => sum + p[1], 0) / locations.length;
  heatmapMap.setView([avgLat, avgLng], 10);
}

// Populate sign statistics table
function populateSignStats(signStats) {
  const tbody = document.getElementById('signStatsBody');
  tbody.innerHTML = '';
  signStats.forEach(stat => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${stat.sign}</td>
      <td>${stat.count}</td>
      <td>${stat.percentage}%</td>
      <td>${stat.lastDetected}</td>
    `;
    tbody.appendChild(row);
  });
}

// Color generator
function generateColors(count, border = false) {
  const baseColors = ['255, 99, 132', '54, 162, 235', '255, 206, 86', '75, 192, 192', '153, 102, 255', '255, 159, 64', '199, 199, 199', '83, 102, 255', '201, 203, 207'];
  return Array.from({ length: count }, (_, i) => {
    const c = baseColors[i % baseColors.length];
    return border ? `rgba(${c}, 1)` : `rgba(${c}, 0.7)`;
  });
}

// Update chart content
function updateCharts(data) {
  if (!data) return;

  if (data.signFrequency) {
    signFrequencyChart.data.labels = data.signFrequency.map(i => i.sign);
    signFrequencyChart.data.datasets[0].data = data.signFrequency.map(i => i.count);
    signFrequencyChart.data.datasets[0].backgroundColor = generateColors(data.signFrequency.length);
    signFrequencyChart.data.datasets[0].borderColor = generateColors(data.signFrequency.length, true);
    signFrequencyChart.update();
  }

  if (data.timeDistribution) {
    timeDistributionChart.data.labels = data.timeDistribution.hours.map(h => `${h}:00`);
    timeDistributionChart.data.datasets[0].data = data.timeDistribution.counts;
    timeDistributionChart.update();

    hourlyActivityChart.data.labels = data.timeDistribution.hours.map(h => `${h}:00`);
    hourlyActivityChart.data.datasets[0].data = data.timeDistribution.counts;
    hourlyActivityChart.update();
  }

  if (data.rareSigns) {
    rareSignsChart.data.labels = data.rareSigns.map(i => i.sign);
    rareSignsChart.data.datasets[0].data = data.rareSigns.map(i => i.count);
    rareSignsChart.data.datasets[0].backgroundColor = generateColors(data.rareSigns.length);
    rareSignsChart.data.datasets[0].borderColor = generateColors(data.rareSigns.length, true);
    rareSignsChart.update();
  }

  if (data.locations) updateHeatmap(data.locations);
  if (data.signStats) populateSignStats(data.signStats);
}

// Save analytics to DB
let lastSavedData = null;

// Save analytics to DB
async function saveAnalyticsToDB(data) {
  try {
    // Get email from sessionStorage
    const userEmail = sessionStorage.getItem('email');
    if (!userEmail) {
      console.warn("User email not found in sessionStorage. Skipping save.");
      return;
    }

    // Check if data has changed
    const currentData = JSON.stringify(data);
    if (currentData === lastSavedData) {
      console.log('Duplicate data, skipping save.');
      return;
    }

    // Store current data to compare next time
    lastSavedData = currentData;

    // Prepare payload and send request
    const payload = {
      user_email: userEmail,
      analytics: data
    };

    const res = await fetch('http://localhost/trafficSignDetection/save_analytics.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    if (!res.ok) throw new Error(`Failed to save analytics: ${res.statusText}`);
    const result = await res.json();
    console.log(result.message);

  } catch (error) {
    console.error('Failed to save analytics:', error);
  }
}

// Fetch data from API and save
async function fetchData() {
  try {
    let data;
    try {
      const response = await fetch('http://localhost:8000/analytics');
      if (!response.ok) throw new Error('Network response was not ok');
      data = await response.json();
    } catch (err) {
      console.warn('API error, using mock data:', err);
      data = getMockData();
    }

    updateCharts(data);
    await saveAnalyticsToDB(data);

  } catch (error) {
    console.error('Failed to fetch and update:', error);
  }
}

// Mock data fallback
function getMockData() {
  const signTypes = ["Stop", "Yield", "Speed Limit 30", "Speed Limit 50", "Speed Limit 70", "No Entry", "Pedestrian Crossing", "School Zone", "Roundabout", "No Parking"];
  const signFrequency = signTypes.map(sign => ({ sign, count: Math.floor(Math.random() * 100) + 10 }));
  const totalDetections = signFrequency.reduce((sum, item) => sum + item.count, 0);
  const signStats = signFrequency.map(item => ({ sign: item.sign, count: item.count, percentage: ((item.count / totalDetections) * 100).toFixed(1), lastDetected: new Date(Date.now() - Math.random() * 604800000).toLocaleString() }));
  const timeDistribution = { hours: [...Array(24).keys()], counts: Array.from({ length: 24 }, () => Math.floor(Math.random() * 50) + 5) };
  const rareSigns = signTypes.slice(6).map(sign => ({ sign, count: Math.floor(Math.random() * 10) + 1 })).sort((a, b) => a.count - b.count);
  const locations = Array.from({ length: 50 }, () => {
    const lat = 51.505 + (Math.random() * 0.1 - 0.05);
    const lng = -0.09 + (Math.random() * 0.1 - 0.05);
    return [lat, lng, Math.random()];
  });

  return { signFrequency, timeDistribution, rareSigns, locations, signStats };
}

// On load init
document.addEventListener('DOMContentLoaded', () => {
  // Initialize theme first
  initTheme();
  
  // Set up theme toggle
  document.getElementById('themeToggle').addEventListener('click', toggleTheme);
  
  // Rest of your initialization code
  if (window.innerWidth < 768) document.getElementById('sidebar').classList.add('collapsed');
  initCharts();
  initHeatmap();
  fetchData();
  setInterval(fetchData, 10000);
});

// Responsive sidebar
window.addEventListener('resize', () => {
  const sidebar = document.getElementById('sidebar');
  if (window.innerWidth < 768) sidebar.classList.add('collapsed');
  else sidebar.classList.remove('collapsed');
});
</script>

  </body>
</html>