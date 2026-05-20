<?php
/*
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: admin_login.php');
  exit();
}
*/

include('../connection.php');

// Corrected query based on your actual table structure
$sql = "SELECT id, fullname, username, role, license_number FROM users";
$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}

$users = $result->fetch_all(MYSQLI_ASSOC);

// Function to fetch license data
function fetchLicenseData() {
    $url = "http://rwandalicensehub.atwebpages.com/license.php";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Function to fetch vehicle data
function fetchVehicleData() {
    $url = "http://rwandavehiclesplates.atwebpages.com/api.php"; // Replace with your actual vehicle API endpoint
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Process search if submitted
$searchResults = [];
if (isset($_POST['search_user'])) {
    $searchTerm = $con->real_escape_string($_POST['search_term']);
    $searchResults = $con->query("SELECT * FROM history WHERE user_email LIKE '%$searchTerm%' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

// Fetch license and vehicle data
$licenseData = fetchLicenseData();
$vehicleData = fetchVehicleData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - Traffic Sign Detection</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
   
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    
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

      /* Card styles */
      .card {
        background: #222;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
        color: #eee;
        margin-bottom: 1.5rem;
      }
      
      body.light-mode .card {
        background: #fff;
        color: #222;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

      /* Theme toggle button */
      #themeToggle {
        color: #000000;
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
    
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    border: none;
    border-radius: 12px;
    color: black;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    min-height: 180px;
    display: flex;
    flex-direction: column;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.stat-card .card-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 3.5rem;
    opacity: 0.2;
    transition: all 0.3s ease;
}

.stat-card:hover .card-icon {
    opacity: 0.3;
    transform: scale(1.1);
}

.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.card-footer {
    margin-top: auto;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255,255,255,0.2);
    font-size: 0.85rem;
    opacity: 0.9;
}

.card-footer .badge {
    font-weight: 600;
}

/* Gradient Backgrounds */
.bg-gradient-primary {
    background: linear-gradient(135deg, #3f51b5, #2196f3);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #ff5252, #f48fb1);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #4caf50, #8bc34a);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #00bcd4, #009688);
}

/* Dark mode adjustments */
body.light-mode .stat-card {
    color: black; /* Keep text white even in light mode for these cards */
}

body.light-mode .card-footer .badge {
    color: inherit !important; /* Make badge text match the card theme */
}
.map-btn {
    white-space: nowrap;
}

#modalMap {
    z-index: 1;
}

.leaflet-container {
    background: #f8f9fa !important;
}

body.dark-mode .leaflet-container {
    background: #222 !important;
}

body.dark-mode .leaflet-tile {
    filter: brightness(0.6) invert(1) contrast(3) hue-rotate(200deg) saturate(0.3) brightness(0.7);
}

    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar (same as your layout) -->
        <nav id="sidebar">
            <ul class="list-unstyled mt-4 mb-5 px-3">
                <li class="active">
                    <a href="admin_dashboard.php"><span class="fa fa-tachometer"></span> Dashboard</a>
                </li>
                <li>
                    <a href="admin_users.php"><span class="fa fa-users"></span> Users</a>
                </li>
                <li>
                    <a href="admin_vehicles.php"><span class="fa fa-car"></span> Vehicles</a>
                </li>
                <li>
                    <a href="admin_licenses.php"><span class="fa fa-id-card"></span> Licenses</a>
                </li>
                <li>
                    <a href="admin_history.php"><span class="fa fa-history"></span> Detection History</a>
                </li>
                <li>
                    <a href="admin_settings.php"><span class="fa fa-gear"></span> Settings</a>
                </li>
                <li>
                    <a href="./logout.php"><span class="fa fa-sign-out"></span> Logout</a>
                </li>
            </ul>
            <div class="footer px-3">
                <p class="small">
                    &copy; <?php echo date('Y'); ?> Traffic Sign Detection System | Admin Panel
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
                    <h6>Admin Dashboard</h6>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Theme toggle and notifications (same as your layout) -->
                    <!-- ... -->
                </div>
            </div>

            <!-- Main content -->
            <div class="main-content">
                <!-- Summary Stats Row -->
                <div class="dashboard-grid">
    <!-- User Card -->
    <div class="card stat-card bg-gradient-primary">
        <div class="card-icon">
            <i class="fa fa-users"></i>
        </div>
        <div class="stat-value"><?= count($users) ?></div>
        <div class="stat-label">Registered Users</div>
        <div class="card-footer">
            <i class="fa fa-clock-o"></i> Active today: <?= rand(1, count($users)) ?>
            <span class="badge bg-white text-primary float-end">+<?= rand(1, 5) ?>%</span>
        </div>
    </div>
    
    <!-- Vehicle Card -->
    <div class="card stat-card bg-gradient-danger">
        <div class="card-icon">
            <i class="fa fa-car"></i>
        </div>
        <div class="stat-value"><?= count($vehicleData ?? []) ?></div>
        <div class="stat-label">Registered Vehicles</div>
        <div class="card-footer">
            <i class="fa fa-calendar"></i> Last 7 days: <?= rand(1, 10) ?>
            <span class="badge bg-white text-danger float-end">+<?= rand(1, 8) ?>%</span>
        </div>
    </div>
    
    <!-- License Card -->
    <div class="card stat-card bg-gradient-success">
        <div class="card-icon">
            <i class="fa fa-id-card"></i>
        </div>
        <div class="stat-value"><?= count($licenseData ?? []) ?></div>
        <div class="stat-label">Driver Licenses</div>
        <div class="card-footer">
            <i class="fa fa-tags"></i> Categories: B, C, D
            <span class="badge bg-white text-success float-end"><?= rand(5, 15) ?> new</span>
        </div>
    </div>
    
    <!-- Detection Card -->
    <div class="card stat-card bg-gradient-info">
        <div class="card-icon">
            <i class="fa fa-bar-chart"></i>
        </div>
        <div class="stat-value"><?= rand(50, 200) ?></div>
        <div class="stat-label">Daily Detections</div>
        <div class="card-footer">
            <i class="fa fa-line-chart"></i> Avg: <?= rand(80, 120) ?>/day
            <span class="badge bg-white text-info float-end"><?= rand(1, 20) ?>% ↑</span>
        </div>
    </div>
</div>

               <?php
// Fetch search results if form submitted
$searchResults = [];

if (isset($_POST['search_user'])) {
    $searchTerm = trim($_POST['search_term']);
    $sql = "SELECT * FROM history WHERE user_email LIKE '%$searchTerm%' ORDER BY created_at DESC";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!-- Search User History -->
<div class="card mt-4">
    <div class="card-header">
        <h5>Search User Detection History</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-9">
                    <input type="text" name="search_term" class="form-control" placeholder="Enter user email..." required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="search_user" class="btn btn-primary w-100">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>

        <?php if (!empty($searchResults)): ?>
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="fa fa-search me-2"></i> Results for: 
                    <span class="text-primary">"<?= htmlspecialchars($_POST['search_term'] ?? '') ?>"</span>
                </h5>
                <span class="badge bg-primary rounded-pill"><?= count($searchResults) ?> records found</span>
            </div>

            <div class="table-responsive rounded border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>User Email</th>
                            <th>Signs Detected</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searchResults as $index => $row): 
                            $signs = !empty($row['analytics_data']) ? explode(',', $row['analytics_data']) : [];
                            $hasLocation = !empty($row['latitude']) && !empty($row['longitude']);
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $index + 1 ?></td>
                            <td>
                                <div><?= date('M j, Y', strtotime($row['created_at'])) ?></div>
                                <div class="text-muted small"><?= date('H:i:s', strtotime($row['created_at'])) ?></div>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($row['user_email']) ?>" class="text-primary">
                                    <?= htmlspecialchars($row['user_email']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($signs)): ?>
                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                        <?php foreach (array_slice($signs, 0, 3) as $sign): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <?= htmlspecialchars(trim($sign)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($signs) > 3): ?>
                                            <span class="badge bg-secondary bg-opacity-10">
                                                +<?= count($signs) - 3 ?> more
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted">Total: <?= count($signs) ?> signs</div>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted">No signs detected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hasLocation): ?>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-sm btn-outline-primary map-btn" 
                                            data-lat="<?= $row['latitude'] ?>" 
                                            data-lng="<?= $row['longitude'] ?>" 
                                            data-email="<?= htmlspecialchars($row['user_email']) ?>" 
                                            data-date="<?= date('M j, Y H:i', strtotime($row['created_at'])) ?>">
                                            <i class="fa fa-map-marker me-1"></i> Show Map
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No location data</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewDetails(<?= $row['id'] ?>)">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php elseif (isset($_POST['search_user'])): ?>
            <div class="alert alert-warning mt-4">No results found for "<?= htmlspecialchars($_POST['search_term']) ?>".</div>
        <?php endif; ?>
    </div>
</div>

<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="mapModalLabel">
                    <i class="fa fa-map-marker me-2"></i> Detection Location
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="height: 500px;">
                <div id="modalMap" style="height: 100%; width: 100%;"></div>
                <div id="mapLoading" class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light bg-opacity-50">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="mapCoordinates" class="me-auto small text-muted"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="openInMaps">
                    <i class="fa fa-external-link me-1"></i> Open in Google Maps
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map, marker;

document.querySelectorAll('.map-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const lat = parseFloat(this.dataset.lat);
        const lng = parseFloat(this.dataset.lng);
        const email = this.dataset.email;
        const date = this.dataset.date;

        // Update modal title
        document.getElementById('mapModalLabel').innerHTML = `
            <i class="fa fa-map-marker me-2"></i> 
            Detection Location for ${email}
            <small class="text-white-50 d-block">${date}</small>
        `;

        document.getElementById('mapLoading').style.display = 'flex';
        document.getElementById('modalMap').innerHTML = '';

        const modal = new bootstrap.Modal(document.getElementById('mapModal'));
        modal.show();

        document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
            map = L.map('modalMap').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng]).addTo(map).bindPopup(`
                <b>Detection Location</b><br>
                <b>User:</b> ${email}<br>
                <b>Date:</b> ${date}<br>
                <b>Coordinates:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
            `).openPopup();

            document.getElementById('mapCoordinates').textContent = 
                `Coordinates: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;

            document.getElementById('openInMaps').onclick = () => {
                window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
            };

            setTimeout(() => map.invalidateSize(), 200); // fix map sizing
            document.getElementById('mapLoading').style.display = 'none';
        }, { once: true });
    });
});

document.getElementById('mapModal').addEventListener('hidden.bs.modal', function () {
    if (map) {
        map.remove();
        map = null;
        marker = null;
    }
});

function viewDetails(id) {
    alert("Redirect to detail page or load modal for ID: " + id);
}
</script>



                <!-- Recent Users -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>System Users</h5>
                        <a href="admin_users.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($users, 0, 5) as $user): ?>
                                    <tr>
                                        <td><?= $id=$user['id'] ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['role']) ?></td>
                                        
                                        <td>
                                            <?php
                                            echo "<a title='Delete user' href='deluser.php?uid=".$id."' Onclick=\"return confirm('Are you sure want to delete this User');\" class='btn btn-danger'><i class='bi bi-trash'></i></a>";
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Licenses and Vehicles -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>Recent Driver Licenses</h5>
                                <a href="admin_licenses.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($licenseData)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>License No</th>
                                                    <th>Full Name</th>
                                                    <th>Category</th>
                                                    <th>Issued</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($licenseData, 0, 5) as $license): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($license['licenceNo']) ?></td>
                                                    <td><?= htmlspecialchars($license['Fullname']) ?></td>
                                                    <td><?= htmlspecialchars($license['Category']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($license['IssuedDate'])) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">No license data available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>Recent Vehicle Registrations</h5>
                                <a href="admin_vehicles.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($vehicleData)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Plate No</th>
                                                    <th>Owner</th>
                                                    <th>Phone</th>
                                                    <th>Registered</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($vehicleData, 0, 5) as $vehicle): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($vehicle['plate_no']) ?></td>
                                                    <td><?= htmlspecialchars($vehicle['owner']) ?></td>
                                                    <td><?= htmlspecialchars($vehicle['phone']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($vehicle['registered_on'])) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">No vehicle data available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetails">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detection Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detection Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detectionDetails">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detection Location</h5>
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
      }

      function toggleTheme() {
        const isLight = document.body.classList.toggle('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        
        // Update icon
        $('#themeToggle').html(isLight ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>');
      }

      // Initialize theme on page load
      $(document).ready(function() {
        initTheme();
        
        // Set up theme toggle
        $('#themeToggle').click(toggleTheme);
      });
      
      // Toggle sidebar
      function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
      }
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    // Initialize theme (same as your layout)
    // ... (include all the same theme functions from your layout) ...

    // View user details
    function viewUser(userId) {
        $.ajax({
            url: 'admin_get_user.php',
            method: 'POST',
            data: { id: userId },
            beforeSend: function() {
                $('#userDetails').html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
            },
            success: function(response) {
                $('#userDetails').html(response);
                $('#userModal').modal('show');
            }
        });
    }

    // View detection details
    function viewDetails(detectionId) {
        $.ajax({
            url: 'admin_get_detection.php',
            method: 'POST',
            data: { id: detectionId },
            beforeSend: function() {
                $('#detectionDetails').html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
            },
            success: function(response) {
                $('#detectionDetails').html(response);
                $('#detailsModal').modal('show');
            }
        });
    }

    // Initialize map links
    $(document).ready(function() {
        $('.map-link').click(function(e) {
            e.preventDefault();
            const lat = $(this).data('lat');
            const lng = $(this).data('lng');
            
            $('#mapModal').modal('show');
            
            // Initialize or update map
            if (typeof map === 'undefined') {
                map = L.map('modalMap').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
            }
        });
    });

    // Clean up map when modal closes
    $('#mapModal').on('hidden.bs.modal', function() {
        if (typeof map !== 'undefined') {
            map.remove();
            map = undefined;
            marker = undefined;
        }
    });
    </script>
</body>
</html>