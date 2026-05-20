  <?php
  session_start();
  $user=$_SESSION['user'];
  $fullname=$_SESSION['fullname'];
if(!isset($_SESSION['user'])){
  header('location:login.php');
}
else
{
  $userprofile=$fullname;
}

include('connection.php');

if (isset($_POST['deactivate']) && isset($_POST['plate_no'])) {
    $plate_no = trim($_POST['plate_no']);

    // Escape input to be safe
    $plate_no_esc = mysqli_real_escape_string($con, $plate_no);

    $updateSql = "UPDATE vehicles SET status = 'Inactive' WHERE plate_no = '$plate_no_esc'";
    $result = mysqli_query($con, $updateSql);

    if (!$result) {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($con) . "</div>";
    } else {
        // Redirect after update to prevent resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<html lang="en">
  <head>
    <title>Anne|Traffic Sign Detection System</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- external css-->
    <link rel="stylesheet" href="mystyle.css"/>

   <!-- jqueryAPI----->
   <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

<!--sweetalert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  </head>

  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <nav id="sidebar">
        <ul class="list-unstyled mt-4 mb-5 px-3">
          <li class="active">
            <a href="index.php"><span class="fa fa-home"></span> Home</a>
          </li>
          <li >
            <a href="uploads.html"><span class="fa fa-file"></span> Uploads </a>
          </li>
          <li>
            <a href="analytics.php"><span class="fa fa-bar-chart"></span> Analytics</a>
          </li>
          <li>
            <a href="settings.php"><span class="fa fa-gear"></span> Settings</a>
          </li>
          <li>
            <a href="https://www.mdpi.com/2227-7390/12/2/297"><span class="fa fa-info-circle"></span> Help</a>
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
            <h6> Traffic Sign Detection System</h6>
          </div>

          <div class="d-flex align-items-center gap-3">
            <a class="text-reset" href="#" title="Traffic Signs">
            <i class="fa fa-ban"></i></a>

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
                    >1</span
                  >
                </span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#">Latest</a></li>
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
                <li><a class="dropdown-item" href="#"><?php echo $userprofile;  ?></a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Main content -->
        <div class="main-content"> 
          <div class="dashboard-grid">
            <!-- Camera feed card -->
            <div class="card">
              <h5>Camera Feed</h5>
              <div id="videoWrapper">
                <video id="video" autoplay muted playsinline></video>
                <canvas id="overlay"></canvas>
              </div>
            </div>

            <!-- Controls card -->
            <div class="card">
              <h5>Controls</h5>
              <div id="output">Waiting to start detection...</div>
              <div class="buttons">
                <button id="startBtn">Start Detection</button>
                <button id="stopBtn" disabled>Stop Detection</button>
                <button id="toggleSoundBtn">Sound: OFF</button>
                <button id="toggleModeBtn">Dark Mode</button>
                 <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                 <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM3 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2m10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM2.906 5.189a.51.51 0 0 0 .497.731c.91-.073 3.35-.17 4.597-.17s3.688.097 4.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 11.691 3H4.309a.5.5 0 0 0-.447.276L2.906 5.19Z"/>
                </svg> Add Car
                </button>

<?php
$sql = "SELECT * FROM vehicles";
$query3 = mysqli_query($con, $sql);

if (mysqli_num_rows($query3) > 0) {
    echo "
    <div class='table-responsive mt-3'>
        <table class='table table-hover border rounded shadow-sm'>
            <thead class='bg-primary text-light'>
                <tr class='bg-primary'>
                    <th><i class='bi bi-upc-scan'></i> Plate Number</th>
                    <th><i class='bi bi-activity'></i> Status</th>
                    <th><i class='bi bi-gear-fill'></i> Action</th>
                </tr>
            </thead>
            <tbody>
    ";

    while ($row = mysqli_fetch_assoc($query3)) {
        $plate = htmlspecialchars($row['plate_no']);
        $status = htmlspecialchars($row['status']);

        // Display badge
        $statusBadge = $status === "Active"
            ? "<span class='badge bg-success'>Active</span>"
            : "<span class='badge bg-secondary'>Inactive</span>";

        // Disable button if already inactive
        $buttonDisabled = $status === "Inactive" ? "disabled" : "";

        echo "
        <tr>
            <td><i class='bi bi-car-front-fill text-primary'></i> <strong>$plate</strong></td>
            <td>$statusBadge</td>
            <td>
                <form method='POST' style='display:inline-block;'>
                    <input type='hidden' name='plate_no' value='$plate'>
                    <button type='submit' name='deactivate' class='btn btn-sm btn-danger' $buttonDisabled>
                        <i class='bi bi-x-circle'></i> Deactivate
                    </button>
                </form>
            </td>
        </tr>";
    }

    echo "</tbody></table></div>";
} else {
    echo "<div class='alert alert-warning mt-3'>No vehicles registered yet.</div>";
}
?>

              </div>
            </div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="exampleModalLabel">Car Information</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Begin Form -->
      <form method="POST">
        <div class="modal-body">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="plate" id="search" placeholder="Enter plate number" required />
                <label for="search">Plate Number</label>
            </div>

            <div class="alert alert-success p-2 rounded-2 text-dark" id="disp" style="display: none;"></div>
        </div>

        <div class="modal-footer">
            <input type="submit" class="btn btn-success" value="Activate" name="activate"/>
        </div>
      </form>
      <!-- End Form -->

    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    $("#search").keyup(function(){
        var btn = $(this).val();

        if(btn != ""){
            $.ajax({
                url: "apilivesearch.php",
                method: "POST",
                data: { btn: btn },
                success: function(data){
                    $("#disp").html(data).show();
                }
            });
        } else {
            $("#disp").hide();
        }
    });
});
</script>

<?php
if (isset($_POST['activate'])) {

    $plate = trim($_POST['plate']);
    $activate_status = "Active";
    $deactivate_status = "Inactive";

    if (!empty($plate)) {
        $sql = "INSERT INTO vehicles VALUES('', '$plate', '$activate_status')";
        $query = mysqli_query($con, $sql);

        if ($query) {
            echo "<script>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Activated',
                    text: 'Successfully activated vehicle status.',
                    showConfirmButton: false,
                    timer: 3000
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Database Error',
                    text: 'Failed to activate vehicle.',
                    showConfirmButton: false,
                    timer: 3000
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Missing Plate',
                text: 'Please enter a valid plate number.',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
    }
}
?>

            <!-- Logs card -->
            <div class="card">
              <h5>Detection Logs</h5>
              <div id="logPanel" aria-live="polite" aria-atomic="true">
                <div id="logPanel-content">
                  <strong>Detection logs:</strong><br/>
                </div>
              </div>
            </div>

            <!-- Map card -->
            <div class="card">
              <h5>Map View</h5>
              <div id="map"></div>
            </div>
          </div>
        </div>

       
      </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // Sidebar toggle functionality
      function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
      }

      // Collapse sidebar by default on small screens
      window.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth < 768) {
          document.getElementById('sidebar').classList.add('collapsed');
        }
      });

      window.addEventListener('resize', () => {
        if (window.innerWidth < 768) {
          document.getElementById('sidebar').classList.add('collapsed');
        } else {
          document.getElementById('sidebar').classList.remove('collapsed');
        }
      });

      // Traffic Detection Functionality
      const video = document.getElementById('video');
      const overlay = document.getElementById('overlay');
      const output = document.getElementById('output');
      const startBtn = document.getElementById('startBtn');
      const stopBtn = document.getElementById('stopBtn');
      const toggleSoundBtn = document.getElementById('toggleSoundBtn');
      const toggleModeBtn = document.getElementById('toggleModeBtn');
      const logPanel = document.getElementById('logPanel-content');

      let stream = null;
      let detectionInterval = null;
      const ctx = overlay.getContext('2d');
      const modelEndpoint = 'http://localhost:8000/detect/';  //  backend URL

      let soundEnabled = false;
      let lastSpokenSign = null;
      let lastSpokenTime = 0;
      const SPEECH_COOLDOWN = 3000; // 3 seconds cooldown for same sign

      // Setup map
      let map = L.map('map').setView([0, 0], 2);
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
      
      let userMarker = null;
      let accuracyCircle = null;
      let currentUserLatLng = null;
      let detectedSignMarkers = [];

      // Update user location on map
      function updateLocation(position) {
        const { latitude, longitude, accuracy } = position.coords;
        currentUserLatLng = [latitude, longitude];
        
        if (!userMarker) {
          userMarker = L.marker(currentUserLatLng).addTo(map);
          accuracyCircle = L.circle(currentUserLatLng, { radius: accuracy }).addTo(map);
          map.setView(currentUserLatLng, 15);
        } else {
          userMarker.setLatLng(currentUserLatLng);
          accuracyCircle.setLatLng(currentUserLatLng);
          accuracyCircle.setRadius(accuracy);
        }
        
        // Adjust map view to ensure marker is visible
        map.fitBounds(accuracyCircle.getBounds());
      }

      navigator.geolocation.watchPosition(
        updateLocation,
        (err) => {
          console.warn('Geolocation error:', err.message);
          logDetection('Geolocation error: ' + err.message);
        },
        { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
      );

      //get camera connected to the pc
      navigator.mediaDevices.enumerateDevices().then(devices => {
        devices.forEach(device => {
          if (device.kind === 'videoinput') {
            console.log(`${device.label} - ${device.deviceId}`);
          }
        });
      });
        
        //interal camera access
        /*
              async function startVideo() {
                if (!stream) {
                  try {
                    stream = await navigator.mediaDevices.getUserMedia({ 
                      video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                      }, 
                      audio: false 
                    });
                    video.srcObject = stream;
                    
                    await new Promise((resolve) => {
                      video.onloadedmetadata = resolve;
                    });
                    
                    overlay.width = video.videoWidth;
                    overlay.height = video.videoHeight;
                    
                    const videoWrapper = document.getElementById('videoWrapper');
                    const aspectRatio = video.videoHeight / video.videoWidth;
                    videoWrapper.style.minHeight = `${videoWrapper.clientWidth * aspectRatio}px`;
                    
                  } catch (err) {
                    alert('Camera access denied or not available.');
                    console.error(err);
                    logDetection('Camera error: ' + err.message);
                  }
                }
              }
        */


    //C170 external camera 
   async function startVideo() {
  if (!stream) {
    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
      const videoDevices = devices.filter(device => device.kind === 'videoinput');

      // Find Logitech C170 by label
      const externalCam = videoDevices.find(device =>
        device.label.includes('C170') || device.label.includes('Logitech')
      );

      if (!externalCam) {
        alert('Logitech C170 camera not found. Using default camera.');
      }

      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          deviceId: externalCam ? { exact: externalCam.deviceId } : undefined,
          width: { ideal: 1280 },
          height: { ideal: 720 }
        },
        audio: false
      });

      video.srcObject = stream;

      await new Promise((resolve) => {
        video.onloadedmetadata = resolve;
      });

      overlay.width = video.videoWidth;
      overlay.height = video.videoHeight;

      const videoWrapper = document.getElementById('videoWrapper');
      const aspectRatio = video.videoHeight / video.videoWidth;
      videoWrapper.style.minHeight = `${videoWrapper.clientWidth * aspectRatio}px`;

    } catch (err) {
      alert('Camera access denied or not available.');
      console.error(err);
      logDetection('Camera error: ' + err.message);
    }
  }
}


      function estimateDistance(bbox) {
        const [x1, y1, x2, y2] = bbox;
        const boxHeight = y2 - y1;
        const maxHeight = overlay.height;
        const minDistance = 2;    // meters, close
        const maxDistance = 50;   // meters, far
        const distance = maxDistance - (boxHeight / maxHeight) * (maxDistance - minDistance);
        return distance.toFixed(1);
      }

         function speak(text) {
  if (!soundEnabled || !('speechSynthesis' in window)) return;

  const now = Date.now();

  // If same sign and cooldown hasn’t passed, skip speaking
  if (text === lastSpokenSign && (now - lastSpokenTime) < SPEECH_COOLDOWN) {
    return;
  }

  // Do not interrupt if something is already being spoken
  if (window.speechSynthesis.speaking) {
    return; // just wait until current speech ends naturally
  }

  const utterance = new SpeechSynthesisUtterance(text);
  utterance.rate = 1;

  window.speechSynthesis.speak(utterance);

  lastSpokenSign = text;
  lastSpokenTime = now;
}


      function logDetection(text) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.innerHTML = `[${timestamp}] ${text}`;
        logPanel.appendChild(logEntry);
        logPanel.scrollTop = logPanel.scrollHeight;

      }
      async function sendToInfluxDB(className, distance, lat, lon) {
  const influxUrl = 'https://us-east-1-1.aws.cloud2.influxdata.com/api/v2/write?org=c7997314c3509909&bucket=signs&precision=s';
  const token = 'RSLKkSM4FRa66kzI6NR62yyZfSxRqgu8FgBGR5HcHzoGr6kPQds9j9JBmvo1vnmc-f_Lsw0fZ_PgOH078w7IVw==';//influxdb api

  const timestamp = Math.floor(Date.now() / 1000); // in seconds
  const line = `detections,class=${className} distance=${distance},lat=${lat},lon=${lon} ${timestamp}`;

  console.log("Sending line to Influx:", line);
  console.log("POST URL:", influxUrl);

  try {
    const res = await fetch(influxUrl, {
      method: 'POST',
      headers: {
        'Authorization': `Token ${token}`,
        'Content-Type': 'text/plain'
      },
      body: line
    });

    if (!res.ok) {
      const errorText = await res.text();
      console.error("InfluxDB write failed:", errorText);
    } else {
      console.log("✅ InfluxDB write success");
    }
  } catch (err) {
    console.error("Failed to send to InfluxDB:", err);
  }
}


      async function detectFrame() {
        if (!stream) return;

        ctx.clearRect(0, 0, overlay.width, overlay.height);

        // Capture frame
        let tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        let tempCtx = tempCanvas.getContext('2d');
        tempCtx.drawImage(video, 0, 0, tempCanvas.width, tempCanvas.height);

        const blob = await new Promise((resolve) => tempCanvas.toBlob(resolve, 'image/jpeg', 0.8));
        const formData = new FormData();
        formData.append('file', blob, 'frame.jpg');

        try {
          const res = await fetch(modelEndpoint, { method: 'POST', body: formData });
          if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
          const data = await res.json();

          if (data.detections && data.detections.length > 0) {
            const warnings = data.warnings.join(', ') || 'Signs detected!';
            output.textContent = warnings;

            if (soundEnabled) {
              // Speak only the most important warning
              if (data.warnings.length > 0) {
                speak(data.warnings[0]);
              }
            }

            data.detections.forEach((det) => {
              const [x1, y1, x2, y2] = det.bbox;
              const dist = estimateDistance(det.bbox);

              ctx.strokeStyle = '#00FF00';
              ctx.lineWidth = 3;
              ctx.font = '20px Arial';
              ctx.fillStyle = '#00FF00';

              ctx.beginPath();
              ctx.rect(x1, y1, x2 - x1, y2 - y1);
              ctx.stroke();

              const label = `${det.class_name} (${dist} cm)`;
              ctx.fillText(label, x1 + 5, y1 + 25);

              logDetection(label);

              if (currentUserLatLng) {
              sendToInfluxDB(det.class_name, dist, currentUserLatLng[0], currentUserLatLng[1]);
              }


              if (currentUserLatLng) {
                const marker = L.marker(currentUserLatLng, {
                  title: det.class_name,
                  icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/565/565547.png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                  })
                }).addTo(map);
                marker.bindPopup(`${det.class_name} detected here`).openPopup();
                detectedSignMarkers.push(marker);
                
                const markerGroup = new L.featureGroup([...detectedSignMarkers, userMarker]);
                map.fitBounds(markerGroup.getBounds().pad(0.5));
              }
            });
          } else {
            output.textContent = 'No signs detected.';
            logDetection('No signs detected.');
          }
        } catch (error) {
          output.textContent = 'Error detecting signs.';
          console.error(error);
          logDetection('Detection error: ' + error.message);
        }
      }

      startBtn.onclick = async () => {
        await startVideo();
        startBtn.disabled = true;
        stopBtn.disabled = false;
        output.textContent = 'Detecting signs...';
        logDetection('Started detection');
        detectionInterval = setInterval(detectFrame, 1500);
      };

      stopBtn.onclick = () => {
        startBtn.disabled = false;
        stopBtn.disabled = true;
        output.textContent = 'Detection stopped.';
        logDetection('Stopped detection');
        clearInterval(detectionInterval);
        detectionInterval = null;

        ctx.clearRect(0, 0, overlay.width, overlay.height);

        if (stream) {
          stream.getTracks().forEach((track) => track.stop());
          stream = null;
          video.srcObject = null;
        }

        detectedSignMarkers.forEach((marker) => map.removeLayer(marker));
        detectedSignMarkers = [];
      };
      toggleSoundBtn.onclick = () => {
        soundEnabled = !soundEnabled;
        toggleSoundBtn.textContent = `Sound: ${soundEnabled ? 'ON' : 'OFF'}`;
        logDetection(`Sound ${soundEnabled ? 'enabled' : 'disabled'}`);
      };

      toggleModeBtn.onclick = () => {
        document.body.classList.toggle('light-mode');
        const isLight = document.body.classList.contains('light-mode');
        toggleModeBtn.textContent = isLight ? 'Dark Mode' : 'Light Mode';
        logDetection(`Switched to ${isLight ? 'light' : 'dark'} mode`);
      };

      // Initialize map properly after DOM is loaded
      window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
          map.invalidateSize();
        }, 100);
      });
    </script>
  </body>
</html>