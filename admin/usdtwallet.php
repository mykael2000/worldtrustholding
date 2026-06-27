<?php
include "includes/header.php";

$USDT = 3;
$sqlUSDT = "SELECT * FROM wallet WHERE id = '$USDT'";
$queryUSDT = mysqli_query($conn, $sqlUSDT);
$getdetailsUSDT = mysqli_fetch_assoc($queryUSDT);
$message = "";

if (isset($_POST['save'])) {

    $usdtadd = $_POST['usdt'];
    if ($_FILES["usdtqr"]["error"] === UPLOAD_ERR_OK) {
        $file_name = $_FILES["usdtqr"]["name"];
        $file_tmp = $_FILES["usdtqr"]["tmp_name"];

        // Specify upload directory
        $upload_dir = "../dash/user-area/address/";

        // Move the uploaded file to the specified directory
        move_uploaded_file($file_tmp, $upload_dir . $file_name);

        // Insert file name into the database
        $sql = "UPDATE wallet set qrcode = '$file_name', address = '$usdtadd' WHERE id = '$USDT'";
        if ($conn->query($sql) === true) {
            echo "File uploaded and record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        // header("location: editwallet.php");
    } else {
        echo "File upload error.";
    }

    $message = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#check-circle-fill" />
            </svg>
            <div>USDT Wallet changed successfully</div>
        </div>';

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

        <!-- Main row -->
        <div class="row">
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Add Bonus To a User</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form action="" method="post" role="form" enctype="multipart/form-data">
                        <?php echo $message; ?>
                        <div class="box-body">

                            <div class="form-group">
                                <label class="form-label">USDT Address</label>
                                <input type="text" class="form-control"
                                    value="<?php echo $getdetailsUSDT['address']; ?>" name="usdt">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Enter qr code</label>
                                <input type="file" class="form-control" name="usdtqr">
                            </div>


                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button name="save" type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div><!-- /.box -->



            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php
include "includes/footer.php";

?>