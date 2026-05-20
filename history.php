<?php
session_start();
 $fullname=$_SESSION['fullname'];
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}
$userEmail = $_SESSION['user'];

include('connection.php');
include('remainder_mail.php');

// Fetch analytics history
$query = "SELECT * FROM history WHERE user_email = ? ORDER BY created_at DESC";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$analytics = $result->fetch_all(MYSQLI_ASSOC);

// Prepare data for visualizations
$signFrequencyData = [];
$locationData = [];
$timelineData = [];
$dailyPatterns = array_fill(0, 24, 0);
$weeklyPatterns = array_fill(0, 7, 0);

foreach ($analytics as $row) {
    $data = json_decode($row['analytics_data'], true);
    $timestamp = strtotime($row['created_at']);
    
    // For sign frequency
    if ($data && isset($data['signFrequency'])) {
        foreach ($data['signFrequency'] as $sign) {
            if (!isset($signFrequencyData[$sign['sign']])) {
                $signFrequencyData[$sign['sign']] = 0;
            }
            $signFrequencyData[$sign['sign']] += $sign['count'];
        }
    }
    
    // For locations
    if ($row['latitude'] && $row['longitude']) {
        $locationData[] = [
            'lat' => $row['latitude'],
            'lng' => $row['longitude'],
            'date' => date('Y-m-d H:i', $timestamp),
            'count' => isset($data['signFrequency']) ? count($data['signFrequency']) : 1
        ];
    }
    
    // For timeline
    $timelineData[] = [
        'date' => date('Y-m-d H:i', $timestamp),
        'count' => isset($data['signFrequency']) ? count($data['signFrequency']) : 0,
        'total' => isset($data['signFrequency']) ? array_sum(array_column($data['signFrequency'], 'count')) : 0
    ];
    
    // For daily patterns
    $hour = date('G', $timestamp);
    $dailyPatterns[$hour] += isset($data['signFrequency']) ? count($data['signFrequency']) : 0;
    
    // For weekly patterns
    $dayOfWeek = date('w', $timestamp);
    $weeklyPatterns[$dayOfWeek] += isset($data['signFrequency']) ? count($data['signFrequency']) : 0;
}

// Sort sign frequency data
arsort($signFrequencyData);
$topSigns = array_slice($signFrequencyData, 0, 5, true);
$otherSigns = array_slice($signFrequencyData, 5, null, true);
$otherCount = array_sum($otherSigns);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detection History Analytics</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet" />
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
      /* Layout CSS - matches analytics.php style */
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

      /* Enhanced Dashboard Styles */
      .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
      }
      
      .card {
        background: #222;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
        color: #eee;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      body.light-mode .card {
        background: #fff;
        color: #222;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }
      
      .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      }
      
      .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
      }
      
      body.light-mode .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.1);
      }
      
      .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
      }
      
      .stat-card {
        text-align: center;
        padding: 1.5rem;
      }
      
      .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
      }
      
      .stat-card .stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
      }
      
      .heatmap-container {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
      }
      
      .timeline-container {
        height: 350px;
        width: 100%;
      }
      
      .badge-pill {
        margin-right: 5px;
        margin-bottom: 5px;
      }
      
      .map-icon {
        cursor: pointer;
        color: #0d6efd;
        font-weight: bold;
        font-size: 1.2rem;
        transition: transform 0.2s;
      }
      
      .map-icon:hover {
        transform: scale(1.2);
      }
      
      .dataTables_wrapper {
        background: #333;
        padding: 1rem;
        border-radius: 8px;
      }
      
      body.light-mode .dataTables_wrapper {
        background: #f8f9fa;
      }
      
      /* Animation for cards */
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      .card {
        animation: fadeIn 0.5s ease-out forwards;
      }
      
      .card:nth-child(1) { animation-delay: 0.1s; }
      .card:nth-child(2) { animation-delay: 0.2s; }
      .card:nth-child(3) { animation-delay: 0.3s; }
      .card:nth-child(4) { animation-delay: 0.4s; }

#themeToggle {
  color: #000000; /* dark text */
  border: 1px solid #ccc;
  font-size: 1.2rem;
  padding: 0.3em 0.6em;
  border-radius: 4px;
}
body.dark-mode #themeToggle {
  color: #fefefe;
  background-color: #222;
  border-color: #444;
}

/* Optional: When in dark mode, change color */
body.dark-mode #themeToggle {
  color: #eee; /* light color for dark background */
}

/* Dark mode specific styles */
body:not(.light-mode) .apexcharts-grid line {
  stroke: rgba(255, 255, 255, 0.1);
}

body:not(.light-mode) .apexcharts-tooltip {
  background: #333;
  color: #fff;
  border: 1px solid #444;
}

/* Leaflet map tiles for dark mode */
body:not(.light-mode) .leaflet-tile {
  filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
}

body:not(.light-mode) .leaflet-container {
  background: #222;
}

/* DataTables pagination buttons */
body.light-mode .page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
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
            <a href="uploads.php"><span class="fa fa-upload"></span> Uploads</a>
          </li>
          <li>
            <a href="analytics.php"><span class="fa fa-bar-chart"></span> Analytics</a>
          </li>
          <li class="active">
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
            <h6>Traffic Sign Detection History</h6>
          </div>

          <div class="d-flex align-items-center gap-3">
                        <!-- Add this button before the notification dropdown -->
           <button  id="themeToggle" title="Toggle dark mode">
            <i class="fa fa-moon-o"></i>
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
                <li><?php echo $fullname; ?></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <div class="main-content">
          <?php if (count($analytics) > 0): ?>
          
          <!-- Summary Stats Row -->
          <div class="dashboard-grid">
            <div class="card stat-card">
              <div class="stat-value"><?= count($analytics) ?></div>
              <div class="stat-label">Total Sessions</div>
              <div class="text-muted small mt-2">Last: <?= date('M j, Y', strtotime($analytics[0]['created_at'])) ?></div>
            </div>
            
            <div class="card stat-card">
              <div class="stat-value"><?= array_sum($signFrequencyData) ?></div>
              <div class="stat-label">Total Signs Detected</div>
              <div class="text-muted small mt-2"><?= count($signFrequencyData) ?> unique types</div>
            </div>
            
            <div class="card stat-card">
              <div class="stat-value"><?= count($locationData) ?></div>
              <div class="stat-label">Geotagged Sessions</div>
              <div class="text-muted small mt-2"><?= round(count($locationData)/count($analytics)*100) ?>% of all sessions</div>
            </div>
            
            <div class="card stat-card">
              <div class="stat-value"><?= round(array_sum($signFrequencyData)/count($analytics), 1) ?></div>
              <div class="stat-label">Avg Signs/Session</div>
              <div class="text-muted small mt-2">Peak: <?= max(array_column($timelineData, 'total')) ?> signs</div>
            </div>
          </div>
          
          <!-- Visualization Row -->
          <div class="dashboard-grid">
            <div class="card">
              <div class="card-header">
                <h5>Top Detected Signs</h5>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa fa-filter"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="updateSignsChart('top5')">Top 5</a></li>
                    <li><a class="dropdown-item" href="#" onclick="updateSignsChart('top10')">Top 10</a></li>
                    <li><a class="dropdown-item" href="#" onclick="updateSignsChart('all')">All Signs</a></li>
                  </ul>
                </div>
              </div>
              <div class="chart-container">
                <canvas id="signsChart"></canvas>
              </div>
            </div>
            
            <div class="card">
              <div class="card-header">
                <h5>Detection Timeline</h5>
              </div>
              <div class="timeline-container">
                <div id="timelineChart"></div>
              </div>
            </div>
          </div>
          
          <!-- Second Visualization Row -->
          <div class="dashboard-grid">
            
            
            <div class="card">
              <div class="card-header">
                <h5>Detection Patterns</h5>
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-secondary active" onclick="updatePatternsChart('hourly')">Hourly</button>
                  <button class="btn btn-outline-secondary" onclick="updatePatternsChart('daily')">Daily</button>
                </div>
              </div>
              <div class="chart-container">
                <canvas id="patternsChart"></canvas>
              </div>
            </div>
          </div>
          
          <!-- History Table -->
          <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>Session History</h5>
              <div>
                <button id="exportCSV" class="btn btn-sm btn-outline-primary me-2">
                  <i class="fa fa-download"></i> CSV
                </button>
                <button id="exportPDF" class="btn btn-sm btn-outline-danger">
                  <i class="fa fa-file-pdf-o"></i> PDF
                </button>
              </div>
            </div>
            <div class="card-body">
              <table id="historyTable" class="table table-hover" style="width:100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Signs Detected</th>
                    <th>Total Signs</th>
                    <th>Location</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($analytics as $index => $row): 
                    $data = json_decode($row['analytics_data'], true);
                    $signsList = [];
                    $totalSigns = 0;
                    $uniqueSigns = 0;
                    
                    if ($data && isset($data['signFrequency'])) {
                      foreach ($data['signFrequency'] as $sign) {
                        $signsList[] = htmlspecialchars($sign['sign']) . " ({$sign['count']})";
                        $totalSigns += $sign['count'];
                      }
                      $uniqueSigns = count($data['signFrequency']);
                    }
                    
                    $lat = $row['latitude'];
                    $lng = $row['longitude'];
                    $hasLocation = ($lat !== null && $lng !== null && $lat !== '' && $lng !== '');
                  ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                      <div class="d-flex flex-wrap">
                        <?php if ($signsList): ?>
                          <?php foreach (array_slice($signsList, 0, 3) as $signItem): ?>
                            <span class="badge bg-primary rounded-pill me-1 mb-1"><?= $signItem ?></span>
                          <?php endforeach; ?>
                          <?php if (count($signsList) > 3): ?>
                            <span class="badge bg-secondary rounded-pill me-1 mb-1">+<?= count($signsList) - 3 ?> more</span>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="text-muted">No signs</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td><?= $totalSigns ?></td>
                    <td class="text-center">
                      <?php if ($hasLocation): ?>
                        <span class="map-icon" 
                              data-lat="<?= htmlspecialchars($lat) ?>" 
                              data-lng="<?= htmlspecialchars($lng) ?>" 
                              data-date="<?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at']))) ?>"
                              title="View location">
                          <i class="fa fa-map-marker"></i>
                        </span>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td><a href='analytics.php'>
                      <button class="btn btn-sm btn-outline-info" onclick="viewSessionDetails(<?= $row['id'] ?>)">
                        <i class="fa fa-eye"></i> Details
                      </button>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <?php else: ?>
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="fa fa-history fa-4x mb-4 text-muted"></i>
                <h4>No detection history found</h4>
                <p class="text-muted">Your detection sessions will appear here once you start using the system.</p>
                <a href="uploads.html" class="btn btn-primary mt-3">
                  <i class="fa fa-upload me-2"></i> Upload Images to Get Started
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Session Details Modal -->
    <div class="modal fade" id="sessionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Session Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="sessionDetails">
            <!-- Content loaded via AJAX -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Location Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="mapModalLabel">Detection Location</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-0">
            <div id="modalMap" style="height: 400px;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>

// Theme management
function initTheme() {
  const savedTheme = localStorage.getItem('theme') || 'dark';
  const isLight = savedTheme === 'light';
  
  // Set initial state
  if (isLight) {
    document.body.classList.add('light-mode');
    $('#themeToggle').html('<i class="fa fa-sun"></i>');
  } else {
    document.body.classList.remove('light-mode');
    $('#themeToggle').html('<i class="fa fa-moon"></i>');
  }
  
  // Update charts if they exist
  updateChartsTheme(isLight);
}

function toggleTheme() {
  const isLight = document.body.classList.toggle('light-mode');
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
  
  // Update icon
  $('#themeToggle').html(isLight ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>');
  
  // Update charts
  updateChartsTheme(isLight);
  
  // Refresh map if it exists
  if (heatmap) {
    setTimeout(() => heatmap.invalidateSize(), 100);
  }
}

function updateChartsTheme(isLight) {
  const bgColor = isLight ? '#fff' : '#222';
  const textColor = isLight ? '#222' : '#eee';
  const gridColor = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
  
  // Update Chart.js charts
  if (signsChart) {
    signsChart.options.plugins.legend.labels.color = textColor;
    signsChart.update();
  }
  
  if (patternsChart) {
    patternsChart.options.scales.x.grid.color = gridColor;
    patternsChart.options.scales.y.grid.color = gridColor;
    patternsChart.options.scales.x.ticks.color = textColor;
    patternsChart.options.scales.y.ticks.color = textColor;
    patternsChart.update();
  }
  
  // Update ApexCharts
  if (timelineChart) {
    timelineChart.updateOptions({
      theme: {
        mode: isLight ? 'light' : 'dark'
      },
      chart: {
        background: bgColor
      },
      grid: {
        borderColor: gridColor
      },
      xaxis: {
        labels: {
          style: {
            colors: textColor
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: textColor
          }
        }
      },
      legend: {
        labels: {
          colors: textColor
        }
      }
    });
  }
}

    // Global variables
    let signsChart, patternsChart, timelineChart, heatmap, historyTable;
    let map, marker, heatmapLayer;
    
    // Initialize page
    $(document).ready(function() {
       
  // Initialize theme first
  initTheme();
  
  // Set up theme toggle
  $('#themeToggle').click(toggleTheme);
        // Initialize DataTable
        $('#historyTable').DataTable({
    dom: 'Bfrtip',
    buttons: [
        { extend: 'copy', className: 'btn btn-sm btn-outline-primary' },
        { extend: 'csv', className: 'btn btn-sm btn-outline-success' },
        { extend: 'excel', className: 'btn btn-sm btn-outline-success' },
        { extend: 'pdf', className: 'btn btn-sm btn-outline-danger' },
        { extend: 'print', className: 'btn btn-sm btn-outline-secondary' }
    ],

            responsive: true,
            order: [[1, 'desc']],
            pageLength: 10
        });
        
        // Initialize charts if data exists
        <?php if (count($analytics) > 0): ?>
            initSignsChart();
            initPatternsChart();
            initTimelineChart();
            initHeatmap();
            setupMapIcons();
        <?php endif; ?>
        
        // Export buttons
        $('#exportCSV').click(function() {
            historyTable.button('.buttons-csv').trigger();
        });
        
        $('#exportPDF').click(function() {
            historyTable.button('.buttons-pdf').trigger();
        });
    });
    
    // Initialize signs chart
    function initSignsChart() {
        const ctx = document.getElementById('signsChart').getContext('2d');
        
        const labels = <?= json_encode(array_keys($topSigns)) ?>;
        const data = <?= json_encode(array_values($topSigns)) ?>;
        
        signsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [...labels, 'Other'],
                datasets: [{
                    data: [...data, <?= $otherCount ?>],
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8AC24A', '#607D8B'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Update signs chart based on filter
    function updateSignsChart(filter) {
        const allLabels = <?= json_encode(array_keys($signFrequencyData)) ?>;
        const allData = <?= json_encode(array_values($signFrequencyData)) ?>;
        
        let labels, data;
        
        switch(filter) {
            case 'top5':
                labels = allLabels.slice(0, 5);
                data = allData.slice(0, 5);
                const otherCount = allData.slice(5).reduce((a, b) => a + b, 0);
                labels = [...labels, 'Other'];
                data = [...data, otherCount];
                break;
            case 'top10':
                labels = allLabels.slice(0, 10);
                data = allData.slice(0, 10);
                break;
            case 'all':
            default:
                labels = allLabels;
                data = allData;
                break;
        }
        
        signsChart.data.labels = labels;
        signsChart.data.datasets[0].data = data;
        signsChart.update();
    }
    
    // Initialize patterns chart
    function initPatternsChart() {
        const ctx = document.getElementById('patternsChart').getContext('2d');
        
        patternsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Array.from({length: 24}, (_, i) => `${i}:00`),
                datasets: [{
                    label: 'Detections by Hour',
                    data: <?= json_encode($dailyPatterns) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Detections'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hour of Day'
                        }
                    }
                }
            }
        });
    }
    
    // Update patterns chart
    function updatePatternsChart(type) {
        if (type === 'daily') {
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            patternsChart.data.labels = days;
            patternsChart.data.datasets[0].data = <?= json_encode($weeklyPatterns) ?>;
            patternsChart.data.datasets[0].label = 'Detections by Day';
            patternsChart.options.scales.x.title.text = 'Day of Week';
        } else {
            patternsChart.data.labels = Array.from({length: 24}, (_, i) => `${i}:00`);
            patternsChart.data.datasets[0].data = <?= json_encode($dailyPatterns) ?>;
            patternsChart.data.datasets[0].label = 'Detections by Hour';
            patternsChart.options.scales.x.title.text = 'Hour of Day';
        }
        
        patternsChart.update();
    }
    
    // Initialize timeline chart
    function initTimelineChart() {
        const timelineData = <?= json_encode($timelineData) ?>;
        
        timelineChart = new ApexCharts(document.querySelector("#timelineChart"), {
            series: [{
                name: 'Unique Signs',
                data: timelineData.map(item => item.count)
            }, {
                name: 'Total Signs',
                data: timelineData.map(item => item.total)
            }],
            chart: {
                height: '100%',
                type: 'area',
                toolbar: {
                    show: true,
                    tools: {
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                }
            },
            colors: ['#4BC0C0', '#FF6384'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: timelineData.map(item => new Date(item.date).getTime()),
                labels: {
                    formatter: function(value) {
                        return new Date(value).toLocaleDateString();
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy HH:mm'
                }
            },
            legend: {
                position: 'top'
            }
        });
        
        timelineChart.render();
    }
    
function initHeatmap() {
  const locations = <?= json_encode($locationData) ?>;
  const isLight = document.body.classList.contains('light-mode');
  
  setTimeout(() => {
    if (locations.length === 0) {
      document.getElementById('heatmap').innerHTML = '<div class="alert alert-info">No location data available</div>';
      return;
    }

    heatmap = L.map('heatmap').setView([locations[0].lat, locations[0].lng], 10);

    // Use different tile layer based on theme
    if (isLight) {
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(heatmap);
    } else {
      L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(heatmap);
    }

    const heatData = locations.map(loc => [loc.lat, loc.lng, loc.count]);

    heatmapLayer = L.heatLayer(heatData, {
      radius: 25,
      blur: 15,
      maxZoom: 17,
      gradient: {0.4: 'blue', 0.6: 'cyan', 0.7: 'lime', 0.8: 'yellow', 1.0: 'red'}
    }).addTo(heatmap);
  }, 200);
}

// Update the map modal setup
function setupMapIcons() {
  $('.map-icon').click(function() {
    const lat = parseFloat($(this).data('lat'));
    const lng = parseFloat($(this).data('lng'));
    const date = $(this).data('date');
    const isLight = document.body.classList.contains('light-mode');
    
    $('#mapModalLabel').text(`Detection Location - ${date}`);
    $('#mapModal').modal('show');
    
    // Initialize or update map
    if (!map) {
      map = L.map('modalMap').setView([lat, lng], 15);
      
      // Use appropriate tiles based on theme
      if (isLight) {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
      } else {
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
      }
      
      marker = L.marker([lat, lng]).addTo(map)
        .bindPopup(`<b>Detection Location</b><br>${date}`);
    } else {
      map.setView([lat, lng], 15);
      marker.setLatLng([lat, lng]).setPopupContent(`<b>Detection Location</b><br>${date}`);
    }
  });
}

    
    // Setup map icon click handlers
    function setupMapIcons() {
        $('.map-icon').click(function() {
            const lat = parseFloat($(this).data('lat'));
            const lng = parseFloat($(this).data('lng'));
            const date = $(this).data('date');
            
            $('#mapModalLabel').text(`Detection Location - ${date}`);
            $('#mapModal').modal('show');
            
            // Initialize or update map
            if (!map) {
                map = L.map('modalMap').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<b>Detection Location</b><br>${date}`);
            } else {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]).setPopupContent(`<b>Detection Location</b><br>${date}`);
            }
        });
    }
    
    // View session details
    function viewSessionDetails(sessionId) {
        $.ajax({
            url: 'get_session_details.php',
            method: 'POST',
            data: { id: sessionId },
            beforeSend: function() {
                $('#sessionDetails').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function(response) {
                $('#sessionDetails').html(response);
                $('#sessionModal').modal('show');
            },
            error: function() {
                $('#sessionDetails').html('<div class="alert alert-danger">Failed to load session details</div>');
            }
        });
    }
    

    
    // Toggle sidebar
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
    
    // Clean up map when modal closes
    $('#mapModal').on('hidden.bs.modal', function() {
        if (map) {
            map.remove();
            map = null;
            marker = null;
        }
    });
    </script>
</body>
</html>