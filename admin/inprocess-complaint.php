<?php
session_start();
include('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
} else {
    date_default_timezone_set('Asia/Manila'); 
    $currentTime = date('d-m-Y h:i:s A', time());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>In Process Complaints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="index, follow" />
    <meta name="theme-color" content="#ffffff">
    <link rel="shortcut icon" href="assets/images/ingat.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link href="assets/css/vendor.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/table.dataTable-th.css">
    <script src="assets/js/config.js"></script>
    <style>
        table.dataTable tbody tr:nth-child(2n+1) {
            background-color: rgba(238, 237, 235, 0.03);
        }
        table.dataTable tbody tr {
            background-color: rgba(255, 255, 255, 0.07);
        }
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate {
            color: inherit;
            margin-bottom: 20px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #aaa;
            border-radius: 3px;
            padding: 5px;
            background-color: #ffebcd05;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #0d0d0d26;
        }
        @media (min-width: 1200px) {
            .offset-xl-3 {
                margin-left: 22%;
                margin-top: 2%;
            }
        }
        .anonymous-text {
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
<?php include('include/header.php'); ?>
<?php include('include/sidebar.php'); ?>  

<div class="row">
    <div class="col-xl-9 offset-xl-3">
        <div class="page-title-box">
            <h4 class="mb-0">In Process</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Complaints</a></li>
                <li class="breadcrumb-item active">In Process</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">In Process</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="complaintsTable" class="table table-hover mb-0 table-centered">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-1">#</th>
                                <th class="py-1">Complainant</th>
                                <th class="py-1">Date Filed</th>
                                <th class="py-1">Status</th>
                                <th class="py-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $query = mysqli_query($conn, "
                            SELECT 
                                c.complaint_number, 
                                c.userId, 
                                c.registered_at, 
                                c.anonymous, 
                                u.firstname, 
                                u.middlename, 
                                u.lastname,
                                c.status
                            FROM tblcomplaints c 
                            LEFT JOIN users u ON u.id = c.userId 
                            WHERE c.status = 'In Progress' 
                            LIMIT 0, 25
                        ");

                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_array($query)) {
                                $date = new DateTime($row['registered_at']);
                                if ($row['anonymous'] == 1) {
                                    $complainant = '<span class="anonymous-text">Anonymous</span>';
                                } else {
                                    $name = trim(
                                        ($row['firstname'] ?? '') . 
                                        (!empty($row['middlename']) ? ' ' . $row['middlename'] : '') . 
                                        ' ' . ($row['lastname'] ?? '')
                                    );
                                    $complainant = $name ? htmlentities($name) : 'Unknown User';
                                }
                        ?>
                            <tr>
                                <td><?php echo htmlentities($row['complaint_number']); ?></td>
                                <td><?php echo $complainant; ?></td>
                                <td><?php echo $date->format('m/d/Y h:i A'); ?></td>
                                <td><?php echo htmlentities($row['status']); ?></td>
                                <td><a href="complaint-details.php?cid=<?php echo htmlentities($row['complaint_number']); ?>" class="btn btn-sm btn-primary">View Details</a></td>
                            </tr>
                        <?php
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">No complaints in progress.</td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


<?php include('include/footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="assets/js/vendor.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
    $(document).ready(function() {
        $('#complaintsTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "searching": true,
            "ordering": true,
            "info": true,
            "paging": true
        });
    });
</script>
</body>
</html>
<?php } ?>