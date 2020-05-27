<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <?php include_once ("styles.php");?>
    <title>Document</title>
</head>
<body>
<?php
include_once("navbar.php");
include("connection_database.php");
$defaulimage = 'https://alumni.crg.eu/sites/default/files/default_images/default-picture_0_0.png';
$sql = "SELECT u.Id, u.Email FROM tbl_users AS u";
$stmt= $dbh->prepare($sql);
$stmt->execute();

?>

<div class="container">
    <div class="row">
        <h1>Овочі</h1>
        <table class="table table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Email</th>
                <th scope="col">Image</th>
            </tr>
            </thead>
            <tbody>
            <?php
            while($row=$stmt->fetch(PDO::FETCH_ASSOC))
            {
                ?>
                <tr>
                    <th scope="row">1</th>
                    <td> <?php echo $row['Email']; ?> </td>
                    <td><img style="width:5em" src="<?php echo $defaulimage;?>"></td>
                </tr>
                <?php
            }
            ?>

            </tbody>
        </table>

    </div>
</div>



<?php include_once("scripts.php"); ?>
</body>
</html>