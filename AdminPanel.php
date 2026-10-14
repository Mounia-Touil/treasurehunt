<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TreasureHunt</title>
    <link rel="stylesheet" href="style.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .sidebar {
            flex: 1;
            max-width: 250px;
            background-color: #f9f9f9;
            border: 2px solid #b87333;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
        }
        .main-content {
            flex: 3;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
        }
        #adminChart {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<header>
    <img src="treasure-hunter.png" alt="Treasure Hunt Banner" class="banner">
    <div class="header-content">
        <div class="header-left">
            <img src="Logo.png" alt="Treasure Hunt Logo" class="logo">
            <h1>TreasureHunt - Admin Panel</h1>
        </div>
        <div id="cart-icon">
            <a href="logout.html"> Logout</a>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="TreasureHunt.php" class="nav-button">Home</a></li>
            <li><a href="AdminPanel.php" class="nav-button active">Admin Dashboard</a></li>
            <li><a href="Help.html" class="nav-button">Help</a></li>
            <li><a href="Contact.html" class="nav-button">Contact</a></li>
        </ul>
    </nav>
</header>

<button id="scroll-top">⬆</button>

<div class="dashboard-container">
    <div class="sidebar">
        <h3>Admin Tools</h3>
        <ul>
            <li><a href="purchase_report.php">Purchases by Category & Date</a></li>
            <li><a href="expire_old_auctions.php" class="nav-button"> Process Expired Auctions</a></li>
            <li><a href="report.php"> Items Currently on Sale</a></li>
            <li><a href="table_dump.php?table=Users"> Dump Users Table</a></li>
            <li><a href="table_dump.php?table=Items"> Dump Items Table</a></li>
        </ul>
    </div>

    <div class="main-content">
        <section id="dashboard">
            <h2> Admin Dashboard Overview</h2>
            <ul>
                <li><strong>Total Registered Users:</strong> <span id="stat-users">...</span></li>
                <li><strong>Items Currently on Sale:</strong> <span id="stat-items">...</span></li>
                <li><strong>Total Bids Placed:</strong> <span id="stat-bids">...</span></li>
                <li><strong>Bids Placed Today:</strong> <span id="stat-bids-today">...</span></li>
                <li><strong>New Users Today:</strong> <span id="stat-users-today">...</span></li>
            </ul>
        </section>

        <section id="chart">
            <h3>System Snapshot</h3>
            <canvas id="adminChart" width="600" height="300" style="display: block; margin: 0 auto;"></canvas>
        </section>

        <section>
            <h3>Full Table Viewer</h3>
            <form action="table_dump.php" method="get">
                <label><input type="checkbox" name="export" value="1"> Export as CSV</label><br><br>

                <label for="table">Choose a table to view:</label>
                <select name="table" id="table">
                    <option value="Users">Users</option>
                    <option value="Items">Items</option>
                    <option value="Purchase">Purchase</option>
                    <option value="Bids">Bids</option>
                    <option value="Categories">Categories</option>
                    <option value="Admins">Admins</option>
                </select><br><br>

                <label for="search">Search keyword (optional):</label>
                <input type="text" name="search" id="search" placeholder="Search term...">

                <input type="submit" value="View Table">
            </form>
        </section>
    </div>
</div>

<footer>
    <p>&copy; 2025 TreasureHunt | <a href="Contact.html">Contact</a> | <a href="Help.html">Help</a></p>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetch('admin_stats.php')
            .then(res => res.json())
            .then(data => {
                console.log(" admin_stats.php data:", data);

                document.getElementById('stat-users').textContent = data.users;
                document.getElementById('stat-items').textContent = data.items;
                document.getElementById('stat-bids').textContent = data.bids;
                if (data.bids_today !== undefined) {
                    document.getElementById('stat-bids-today').textContent = data.bids_today;
                }
                if (data.users_today !== undefined) {
                    document.getElementById('stat-users-today').textContent = data.users_today;
                }

                const canvas = document.getElementById('adminChart');
                const ctx = canvas ? canvas.getContext('2d') : null;

                if (!ctx) {
                    console.error(" Could not get canvas context.");
                    return;
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Users', 'Items on Sale', 'Bids', 'Bids Today', 'Users Today'],
                        datasets: [{
                            label: 'System Stats',
                            data: [data.users, data.items, data.bids, data.bids_today ?? 0, data.users_today ?? 0],
                            backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#FF5722'],
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: '📊 Current System Snapshot',
                                font: { size: 18 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error(" Failed to fetch stats:", err));
    });
</script>

</body>
</html>
