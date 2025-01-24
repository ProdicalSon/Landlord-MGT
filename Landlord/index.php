<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="icon" href="assets/icons/logoX.png"> 
    <title>Landlord Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <header class="logo-container">
            <img src="assets/icons/logoX.png" alt="Kisii Online BNB Logo"> <!-- Corrected path -->
            <h5>Kisii Online BNB</h5>
        </header>

        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li><a href="index.html" class="active"><img src="assets/icons/homeicon.png" alt=""> Dashboard</a></li> <!-- Ensure correct path -->
                <li class="dropdown">
                    <a href="#"><img src="assets/icons/propertyicon.png" alt=""> Properties</a>
                    <div class="dropdown-content">
                        <a href="addproperty.html">Add Property</a>
                        <a href="editlistings.html">Edit Listings</a>
                        <a href="managelocation.html">Manage Location</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#"><img src="assets/icons/manageroomicon.png" alt=""> Manage Rooms</a>
                    <div class="dropdown-content">
                        <a href="roomdetails.html">Room Details</a>
                        <a href="editrooms.html">Edit Rooms</a>
                    </div>
                </li>
                <!-- More Menu Items -->
                <li class="dropdown">
              <a href="#"><img src="assets/icons/tenantsicon.png" alt="">Tenants</a> 
              <div class="dropdown-content">
                <a href="viewtenants.html">View Tenants</a>
                <a href="tenantbookings.html">Tenant Bookings</a>
              </div>
            </li>
            <li class="dropdown">
              <a href="#"><img src="assets/icons/inquiriesicon.png" alt="">Inquiries</a>
              <div class="dropdown-content">
                <a href="inquiries.html">Inquiries</a>
                <a href="chat.html">Chat</a>
              
              </div>
            </li>
                <li><a href="payments.html"><img src="assets/icons/paymentsicon.png" alt=""> Payments</a></li>
                <li><a href="location.html"><img src="assets/icons/locationicon.png" alt=""> Location</a></li>
                <li><a href="announcements.html"><img src="assets/icons/announcementicon.png" alt=""> Announcements</a></li>
              <li><a href="reports.html"><img src="assets/icons/reporticon.png" alt="">Reports</a></li>
              <li><a href="profilesettings.html"><img src="assets/icons/profileicon.png" alt="">Profile Setting</a></li>
              <li><a href="notifications.html"><img src="assets/icons/notificationicon.png" alt="">Notifications</a></li>
              <li><a href="support.html"><img src="assets/icons/supporticon.png" alt="">Support</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <nav class="navbar">
                <div class="navbar-brand">KISII ONLINE BNB</div>
                
                <div class="login-image">
                    <li class="dropdown">
                        <a href="#"><img src="assets/icons/loginicon.png" alt="Login Icon"></a>
                        <div class="dropdown-content">
                            <a href="login.php">Login</a>
                            <a href="login.php">Sign Up</a> <!-- Added signup link -->
                        </div>
                    </li>    
                </div>
            </nav>

            <!-- Dashboard Body -->
            <section class="content">
                <h1>Welcome Back, User!</h1>
                <p>Overview of your properties</p>

                <div class="cards">
                   <!-- Active Properties Card -->
                      <div class="card" id="active-properties-card">
                        <h3>Active Properties</h3>
                        <p id="active-properties-count">5 Properties</p>
                        <button>View Details</button>
                      </div>


                    <div class="card">
                        <h3>Inquiries</h3>
                        <p>12 New Inquiries</p>
                        <button onclick="window.location.href='inquiries.html'">Check Now</button> 
                    </div>

                    <div class="card">
                        <h3>Current Tenants</h3>
                        <p>8 Tenants</p>
                        <button onclick="window.location.href='tenantbookings.html'">Manage Tenants</button> 
                    </div>

                    <div class="card">
                      <h3>Occupancy Rate</h3>
                      <p>Occupancy: <span id="occupancyPercentage">0%</span></p>
                      <div class="progress-bar">
                          <div id="progress" class="progress" style="width: 0%;"></div>
                      </div>
                      <!-- Button to simulate room occupancy -->
                  <button onclick="occupyRoom()" class="occupybtn">Occupy a Room</button>
                  <button onclick="vacateRoom()">Vacate a Room</button>
                  </div>
                  
                  

                    <div class="card">
                      <h3>Payments</h3>
                      <p>2 Pending Payments</p>
                      <div class="alert">
                          <p><strong>Alert:</strong> 2 tenants have not paid their rent.</p>
                      </div>
                    
                  </div>

                  <div class="card">
                    <h3>Announcements</h3>
                    <p>8 Announcements</p>
                    <button class="announcement">View Announcements</button>
                </div>
              
                
              
                  
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <img src="assets/icons/logoX.png" alt="Kisii Online BNB Logo"> <!-- Corrected path -->
        <h6>&copy; Algorithm-X Softwares. <br>All rights reserved</h6>
    </footer>

    <script src="script.js"></script> <!-- Ensure correct path -->
</body>
</html>
