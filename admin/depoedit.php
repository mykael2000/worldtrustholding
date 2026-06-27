<?php
include "includes/header.php";
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader

require '../dash/PHPMailer-master/src/PHPMailer.php';
require '../dash/PHPMailer-master/src/Exception.php';
require '../dash/PHPMailer-master/src/SMTP.php';
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

$depoid = $_GET['id'];
$sqleu = "SELECT * FROM deposits WHERE id='$depoid'";
$queryeu = mysqli_query($conn, $sqleu);
$usereu = mysqli_fetch_assoc($queryeu);
$message = "";

$userid = $usereu['client_id'];
$sqlSu = "SELECT * FROM clients WHERE id='$userid'";
$querySu = mysqli_query($conn, $sqlSu);
$userSu = mysqli_fetch_assoc($querySu);
$firstname = $userSu['first_name'];
$email = $userSu['email'];
if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $paid_via = $_POST['paid_via'];
    $amount = $_POST['amount'];
    $created_at = $_POST['created_at'];
    $plan = $_POST['plan'];
    $sqlup = "UPDATE deposits set status='$status', paid_via ='$paid_via', amount='$amount', plan='$plan', created_at='$created_at' WHERE id='$depoid'";
    $queryup = mysqli_query($conn, $sqlup);

    if ($status == "completed") {

        $newbal = $userSu['total_balance'] + $usereu['amount'];
        $sqlcoin = "UPDATE clients set total_balance ='$newbal' WHERE id='$userid'";
        $querycoin = mysqli_query($conn, $sqlcoin);
        try {
            //Server settings
            $mail->SMTPDebug = 0; //Enable verbose debug output
            $mail->isSMTP(); //Send using SMTP
            $mail->Host = 'mail.checta.com'; //Set the SMTP server to send through
            $mail->SMTPAuth = true; //Enable SMTP authentication
            $mail->Username = 'support@checta.com'; //SMTP username
            $mail->Password = 'trSHF@Hdo!'; //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
            $mail->Port = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('support@checta.com', 'Support');
            $mail->addAddress($email); //Add a recipient               //Name is optional

            $mail->addCC('support@checta.com');

            //Content
            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = 'Successful Deposit';
            $mail->Body = '<html><head></head></head>
<body style="background-color: #1e2024; padding: 45px;">
    <div>
        <img style="position:relative; left:35%;" src="https://checta.com/dash/dashboard/logo.png">
        <h3 style="color: black;">Mail From support@checta.com - Successful Registration</h3>
    </div>
    <div style="color: #ffff;"><hr/>
        <h3>Dear ' . $firstname . '</h3>
        <p>Your deposit has been added, kindly sign in to view your balance</p>

        <a style="background-color:#060c39;color:#ffff; padding:15px; text-decoration:none;border-radius: 10px;font-size: 20px;" href="https://checta.com/dash/dashboard/auth/signin.php" class="btn btn primary">Sign in</a>

        <h5>Note : the details in this email should not be disclosed to anyone</h5>

    </div><hr/>
        <div style="background-color: white; color: black;">
            <h3 style="color: black;">support@Checta<sup>TM</sup> </h3>
        </div>

</body></html>

';

            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    $message = '<div class="alert alert-success d-flex align-items-center" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#check-circle-fill" />
            </svg>
            <div>Status Updated Successfully</div>
        </div>';
}
?>
<!-- Right side column. Contains the navbar and content of the page -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Checta Users List

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Home</a></li>
            <li class="#">users</li>
            <li class="active">edit</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Edit User</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->
                    <form action="" method="post" role="form">
                        <?php echo $message; ?>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Username</label>
                                <input type="email" class="form-control" id="exampleInputEmail1"
                                    value="<?php echo $usereu['username']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputbtc">Transaction ID</label>
                                <input type="text" name="tranx_id" class="form-control" id="exampleInputbtc"
                                    placeholder="Enter amount" value="<?php echo $usereu['tranx_id']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputeth">Payment method</label>
                                <input type="text" name="paid_via" class="form-control" id="exampleInputeth"
                                    placeholder="Enter method" value="<?php echo $usereu['paid_via']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputusdt">Amount</label>
                                <input type="number" name="amount" class="form-control" id="exampleInputusdt"
                                    placeholder="Enter amount" value="<?php echo $usereu['amount']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputltc">Deposit Status</label>
                                <select name="status" class="form-control">
                                    <option value="<?php echo $usereu['status']; ?>"><?php echo $usereu['status']; ?>
                                    </option>
                                    <option value="completed">Completed
                                    </option>
                                    <option value="pending">pending
                                    </option>
                                    <option value="failed">Failed
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputltc">Plan</label>
                                <input type="text" name="plan" class="form-control" id="exampleInputltc"
                                    placeholder="Enter Plan" value="<?php echo $usereu['plan']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputusdt">Date of transaction</label>
                                <input type="text" name="created_at" class="form-control" id="exampleInputusdt"
                                    value="<?php echo $usereu['created_at']; ?>">
                            </div>


                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div><!-- /.box -->



            </div>
            <!--/.col (left) -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php
include "includes/footer.php";

?>