<?php
include "includes/header.php";

$BTC = 1;
$sqlBTC = "SELECT * FROM wallet WHERE id = '$BTC'";
$queryBTC = mysqli_query($conn, $sqlBTC);
$getdetailsBTC = mysqli_fetch_assoc($queryBTC);
$message = "";

if (isset($_POST['save'])) {

    $btcadd = $_POST['btc'];
    if ($_FILES["btcqr"]["error"] === UPLOAD_ERR_OK) {
        $file_name = $_FILES["btcqr"]["name"];
        $file_tmp = $_FILES["btcqr"]["tmp_name"];

        // Specify upload directory
        $upload_dir = "../dash/user-area/address/";

        // Move the uploaded file to the specified directory
        move_uploaded_file($file_tmp, $upload_dir . $file_name);

        // Insert file name into the database
        $sql = "UPDATE wallet set qrcode = '$file_name', address = '$btcadd' WHERE id = '$BTC'";
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
            <div>BTC Wallet changed successfully</div>
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
                                <label class="form-label">BTC Address</label>
                                <input type="text" class="form-control" value="<?php echo $getdetailsBTC['address']; ?>"
                                    name="btc">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Enter qr code</label>
                                <input type="file" class="form-control" name="btcqr">
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