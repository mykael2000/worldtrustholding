<?php
include "includes/header.php";

$sqldepo = "SELECT * FROM clients";
$querydepo = mysqli_query($conn, $sqldepo);
?>
<!-- Right side column. Contains the navbar and content of the page -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Worldtrustholding KYC List

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Home</a></li>
            <li class="active">kyc</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">KYC</h3>
                        <div class="box-tools">
                            <div class="input-group">
                                <input type="text" name="table_search" class="form-control input-sm pull-right"
                                    style="width: 150px;" placeholder="Search" />
                                <div class="input-group-btn">
                                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <tr>


                                <th>Email</th>

                                <th>Username</th>
                                <th>Address Document Type</th>
                                <th>Address Document</th>
                                <th>Identity Document Type</th>
                                <th>Identity Document</th>

                            </tr>
                            <?php while ($depo = mysqli_fetch_assoc($querydepo)) {?>
                            <tr>
                                <td><?php echo $depo['email']; ?></td>

                                <td><?php echo $depo['username']; ?></td>
                                <td><?php echo $depo['address_doc_type']; ?></td>
                                <td><a href="../dash/uploads/<?php echo $depo['address_doc']; ?>" target="__blank"
                                        class="btn btn-block btn-success btn-xs">Download</a></td>
                                <td><?php echo $depo['identity']; ?></td>
                                <td><a href="../dash/uploads/<?php echo $depo['kyc']; ?>" target="__blank"
                                        class="btn btn-block btn-success btn-xs">Download</a></td>



                            </tr>
                            <?php }?>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php
include "includes/footer.php";

?>