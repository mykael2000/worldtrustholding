<?php
include "includes/header.php";

// SQL query to get the total number of users
$sqlt = "SELECT COUNT(*) as total_users FROM users";

// Execute the query
$result = mysqli_query($conn, $sqlt);

if ($result) {
    // Fetch the result as an associative array
    $row = mysqli_fetch_assoc($result);

    // Get the total number of users
    $totalUsers = $row['total_users'];

    // Output the total number of users

} else {
    // Handle the query error
    echo "Error: " . mysqli_error($conn);
}


// Close the database connection
mysqli_close($conn);
?>
<!-- Right side column. Contains the navbar and content of the page -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        </div><!-- /.row -->
       
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php
include "includes/footer.php";

?>