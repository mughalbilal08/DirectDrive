 <!-- Header -->
 <header class="header">
            <div class="menu-icon" onclick="openSidebar()">
                <span class="material-icons-outlined">menu</span>
            </div>
            <div class="header-right">
                <label style="margin-left: 35px;"><?php echo $_SESSION['name'];?></label> 
            </div>
        </header>
        <!-- End Header -->

        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-title">
                <div class="sidebar-brand">
                    Concord Transport
                </div>
                <span class="material-icons-outlined" onclick="closeSidebar()">close</span>
            </div>
            <ul class="sidebar-list">
                <li class="sidebar-list-item">
                    <a href="driverdashboard.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-list-item">
                    <a href="driverNotifications.php">
                        <i class='bx bx-grid-alt'></i>
                        <span class="links_name">Notifications</span>
                    </a>
                </li>
                
                <!-- ADD Section -->
                <li class="sidebar-section" onclick="toggleSection('add-section')">
                    <i class='bx bxs-plus-circle'></i>
                    <span class="links_name">ADD</span>
                </li>
                <ul id="add-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="add_ride_bydriver.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Add Ride</span>
                        </a>
                    </li>
                    <li class="sidebar-list-item">
                        <a href="add_payment_bydriver.php">
                            <i class='bx bxs-car'></i>
                            <span class="links_name">Add Expenses</span>
                        </a>
                    </li>
                </ul>

                <!-- VIEW Section -->
                <li class="sidebar-section" onclick="toggleSection('view-section')">
                    <i class='bx bxs-show'></i>
                    <span class="links_name">VIEW</span>
                </li>
                <ul id="view-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="driverincomereport.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Trip List</span>
                        </a>
                    </li>
                    
                </ul>

                <!-- REPORT Section -->
                <li class="sidebar-section" onclick="toggleSection('report-section')">
                    <i class='bx bxs-report'></i>
                    <span class="links_name">REPORT</span>
                </li>
                <ul id="report-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="driverexpensereport.php">
                            <i class='bx bxs-report'></i>
                            <span class="links_name">Expenses Report</span> 
                        </a>
                    </li>
                   
                </ul>

                <!-- CASH Section -->
                <li class="sidebar-section" onclick="toggleSection('cash-section')">
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="links_name">CASH</span>
                </li>
                <ul id="cash-section" class="sidebar-sublist">
                    <li class="sidebar-list-item">
                        <a href="driverDeposits.php">
                            <i class='bx bxs-user'></i>
                            <span class="links_name">Deposits</span>
                        </a>
                    </li>
                   
                </ul>

                <li class="sidebar-list-item">
                    <a href="view_users.php?logout">
                        <i class='bx bx-log-out'></i>
                        <span class="links_name">Logout</span>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- End Sidebar -->

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2>DRIVER DASHBOARD</h2>
            </div>

            <div class="main-cards">


            
            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h2>Add Rides</h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>
            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128663;</div>
                    <h3>Completed Rides</h3>
                    <h2><?php echo $numOfCompletedRides; ?></h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>
            <a href="add_ride_bydriver.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128661;</div>
                    <h3>Pending Rides</h3>
                    <h2><?php echo $numOfIncompleteRides; ?></h2>
                </div>
                <div class="card-popup">Add Rides</div>
            </a>

            <a href="driverexpensereport.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128176;</div>
                    <h3>Total Expenses</h3>
                    <h2><?php echo $totalExpense; ?></h2>
                </div>
                <div class="card-popup">Add Expenses</div>
            </a>

            <a href="driverincomereport.php" class="myCard">
                <div class="cardInner">
                    <div class="card-icon">&#128178;</div>
                    <h3>Total Income</h3>
                    <h2><?php echo $totalIncome; ?></h2>
                </div>
                <div class="card-popup">Trip List</div>
            </a>
               
            </div>