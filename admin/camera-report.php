<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0) {
    header('location:index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Camera Booking Report</title>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .table-container { max-width: 90%; margin: 20px auto; }
        .panel { border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .panel-heading { background-color: #2980b9 !important; color: white !important; font-size: 20px; font-weight: bold; text-transform: uppercase; border-radius: 10px 10px 0 0; }
        
        .table tbody tr:hover { background-color: #d6e9f9 !important; font-weight: bold; }
        .btn-action { font-weight: bold; font-size: 14px; }

     
        h3 {
            text-align: center;
            margin-bottom: 25px;
        }
        .table th {
            background-color: #2980b9 !important; color: white !important; 
        }
        .export-buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        .export-buttons button {
            margin-left: 10px;
        }
        
        @media print {
    body * {
        visibility: hidden;
    }
    .panel, .panel * {
        visibility: visible;
    }
    .panel form,
    .export-buttons,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }
    .panel {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 0;
        margin: 0;
    }

    table {
        width: 100%;
        font-size: 10px;
        border-collapse: collapse;
    }

    table thead th,
    table tbody td {
        padding: 4px !important;
        border: 1px solid #333 !important;
    }

    tr, td, th {
        page-break-inside: avoid !important;
    }

    h2.page-title, h5 {
        font-size: 14px;
        margin: 10px 0;
        text-align: center;
    }

    .badge {
        font-size: 9px !important;
        padding: 3px 5px !important;
    }
    .table-container {
    max-width: 100%;
    overflow-x: auto;  /* This allows horizontal scrolling */
    margin: 20px auto;
}

#reportTable {
    min-width: 1200px;  /* Adjust this width depending on your table's column count and content */
}
}
    </style>
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php');?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title text-center">Camera Bookings Report</h2>
                        <div class="panel panel-default table-container">
                            <div class="panel-heading">Generate Booking Report</div>
                            <div class="panel-body">

                            <!-- Filter Form -->
                            <form method="post">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <label>User</label>
                                        <select name="user" class="form-control">
                                            <option value="">All Users</option>
                                            <?php 
                                            $users = $dbh->query("SELECT DISTINCT FullName, EmailId FROM tblusers")->fetchAll(PDO::FETCH_OBJ);
                                            foreach($users as $user) {
                                                echo "<option value='{$user->EmailId}'>{$user->FullName}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Camera Brand</label>
                                        <select name="brand" class="form-control">
                                            <option value="">All Brands</option>
                                            <?php 
                                            $brands = $dbh->query("SELECT * FROM tblbrands")->fetchAll(PDO::FETCH_OBJ);
                                            foreach($brands as $brand) {
                                                echo "<option value='{$brand->id}'>{$brand->BrandName}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="">All</option>
        <option value="Pending Approval">Pending Approval</option>
        <option value="Awaiting Payment">Awaiting Payment</option>
        <option value="Paid and Confirmed">Paid and Confirmed</option>
        <option value="Cancelled">Cancelled</option>
    </select>
</div>
                                    <div class="col-md-2">
                                        <label>Date Type</label>
                                        <select name="date_type" class="form-control">
                                            <option value="PostingDate">Posting Date</option>
                                            <option value="FromDate">From Date</option>
                                            <option value="ToDate">To Date</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Quick Range</label>
                                        <select name="quick_range" onchange="updateDates(this.value)" class="form-control">
                                            <option value="">Custom</option>
                                            <option value="today">Today</option>
                                            <option value="7days">Last 7 Days</option>
                                            <option value="month">This Month</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-5">
                                        <label>From Date:</label>
                                        <input type="date" name="fromdate" id="fromdate" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <label>To Date:</label>
                                        <input type="date" name="todate" id="todate" class="form-control">
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <button type="submit" name="generate" class="btn btn-primary btn-block mt-2">Generate</button>
                                    </div>
                                </div>
                            </form>

                            <?php
                            if(isset($_POST['generate'])) {
                                $user = $_POST['user'];
                                $brand = $_POST['brand'];
                                $status = $_POST['status'];
                                $date_type = $_POST['date_type'] ?: 'PostingDate';
                                $from = $_POST['fromdate'];
                                $to = $_POST['todate'];

                                if (!$from) $from = date('Y-m-01');
                                if (!$to) $to = date('Y-m-d');

                                $where = " WHERE 1=1 ";
                                if($status !== '') $where .= " AND tblbooking.Status = :status";
                                if($user !== '') $where .= " AND tblusers.EmailId = :user";
                                if($brand !== '') $where .= " AND tblbrands.id = :brand";
                                if($from && $to) $where .= " AND DATE(tblbooking.$date_type) BETWEEN :from AND :to";

                                $sql = "SELECT tblusers.FullName, tblusers.EmailId, tblusers.ContactNo, 
               tblbrands.BrandName, tblcameras.VehiclesTitle, 
               tblbooking.BookingNumber, tblbooking.userEmail, tblbooking.VehicleId, 
               tblbooking.FromDate, tblbooking.ToDate, tblbooking.message, 
               tblbooking.Status, tblbooking.payment_id, tblbooking.amount_paid, 
               tblbooking.PostingDate, tblbooking.LastUpdationDate, 
               tblbooking.totalPrice, tblbooking.razorpay_order_id
        FROM tblbooking 
        JOIN tblcameras ON tblcameras.id = tblbooking.VehicleId
        JOIN tblbrands ON tblbrands.id = tblcameras.VehiclesBrand
        JOIN tblusers ON tblusers.EmailId = tblbooking.userEmail
        $where ORDER BY tblbooking.$date_type DESC";


                                $query = $dbh->prepare($sql);
                                if($status !== '') $query->bindParam(':status', $status, PDO::PARAM_STR);
                                if($user !== '') $query->bindParam(':user', $user, PDO::PARAM_STR);
                                if($brand !== '') $query->bindParam(':brand', $brand, PDO::PARAM_INT);
                                if($from && $to) {
                                    $query->bindParam(':from', $from);
                                    $query->bindParam(':to', $to);
                                }
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                            ?>

                            <h5 class="mb-3">Report From <strong><?php echo htmlentities($from); ?></strong> To <strong><?php echo htmlentities($to); ?></strong></h5>

                            <div class="export-buttons mb-3">
                                <button onclick="exportToPDF()" class="btn btn-danger">Export as PDF</button>
                                <button onclick="exportToExcel()" class="btn btn-success">Download Excel</button>
                                <button onclick="window.print()" class="btn btn-secondary">Print</button>
                            </div>

                            <table id="reportTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
        <th>#</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Booking No.</th>
        <th>Camera</th>
        <th>Status</th>
        <th>From</th>
        <th>To</th>
        <th>Posting Date</th>
        <th>Amount Paid</th>
       
    </tr>
                                </thead>
                                <tbody>
    <?php 
    if($query->rowCount() > 0){
        $cnt = 1;
        foreach($results as $row){ ?>
            <tr>
                <td><?php echo htmlentities($cnt++); ?></td>
                <td><?php echo htmlentities($row->EmailId); ?></td>
                <td><?php echo htmlentities($row->ContactNo); ?></td>
                <td><?php echo htmlentities($row->BookingNumber); ?></td>
                <td><?php echo htmlentities($row->BrandName . ' - ' . $row->VehiclesTitle); ?></td>
                
                <td>
                    <?php
                    if($row->Status == "Pending Approval") echo '<span class="badge bg-warning">Pending Approval</span>';
                    elseif($row->Status == "Awaiting Payment<") echo '<span class="badge bg-primary">Awaiting Payment</span>';
                    elseif($row->Status == "Paid and Confirmed") echo '<span class="badge bg-success">Paid and Confirmed</span>';
                    elseif($row->Status == "Cancelled") echo '<span class="badge bg-danger">Cancelled</span>';
                    else echo '<span class="badge bg-secondary">Unknown</span>';
                    ?>
                </td>
                       
                <td><?php echo htmlentities($row->FromDate); ?></td>
                <td><?php echo htmlentities($row->ToDate); ?></td>
                <td><?php echo htmlentities($row->PostingDate); ?></td>
                
                
                
                <td><?php echo htmlentities($row->amount_paid); ?></td>
                
            </tr>
        <?php } 
    } else { ?>
        <tr><td colspan="16" class="text-center">No bookings found for this filter.</td></tr>
    <?php } ?>
</tbody>
                            </table>
                            <?php } ?>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
$(document).ready(function() {
    $('#reportTable').DataTable();
});

function updateDates(val) {
    const today = new Date();
    let from = '', to = '';
    if (val === 'today') {
        from = to = today.toISOString().split('T')[0];
    } else if (val === '7days') {
        const prev = new Date(today);
        prev.setDate(today.getDate() - 6);
        from = prev.toISOString().split('T')[0];
        to = today.toISOString().split('T')[0];
    } else if (val === 'month') {
        const start = new Date(today.getFullYear(), today.getMonth(), 1);
        from = start.toISOString().split('T')[0];
        to = today.toISOString().split('T')[0];
    }
    if (from && to) {
        document.getElementById('fromdate').value = from;
        document.getElementById('todate').value = to;
    }
}

function exportToExcel() {
    const table = document.getElementById("reportTable");
    const wb = XLSX.utils.table_to_book(table, { sheet: "Bookings" });
    XLSX.writeFile(wb, "Camera_Bookings_Report.xlsx");
}


async function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape', 'pt', 'a4');

    doc.setFontSize(18);
    doc.text("Camera Bookings Report", 40, 40);

    const table = document.getElementById("reportTable");
    const headers = [];
    table.querySelectorAll("thead tr th").forEach(cell => headers.push(cell.textContent.trim()));

    const data = [];
    table.querySelectorAll("tbody tr").forEach(row => {
        const rowData = [];
        row.querySelectorAll("td").forEach(cell => rowData.push(cell.textContent.trim()));
        data.push(rowData);
    });

    doc.autoTable({
        head: [headers],
        body: data,
        startY: 60,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [41, 128, 185] },
        margin: { left: 40, right: 40 }
    });

    doc.save("Camera_Bookings_Report.pdf");
}

</script>

<script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>

</body>
</html>  