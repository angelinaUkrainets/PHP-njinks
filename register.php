<?php
if($_SERVER["REQUEST_METHOD"]=="POST")
{
    include("connection_database.php");
    $email = $_POST["email"];
    $password = $_POST['password'];
    //$file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));
    //$file = $_POST['image'];
    // $file = $_POST['file'];
    //    echo "<script>console.log($file)</script>";
    $guid = bin2hex(openssl_random_pseudo_bytes(16));
    $fileName = "".guid.".jpg";
    $path = "Images/".fileName;

    if(move_uploaded_file($_FILES['file']['tmp_name'])){
        $error = 'image uploaded correctly';
    }
    else{
        $error = 'Something going wrong';
    }

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $error="Занадто слабкий пароль";
    }else{
        $error="";
        $sql = "SELECT u.Id FROM tbl_users AS u WHERE u.Email=? LIMIT 1";
        $stmt= $dbh->prepare($sql);
        $stmt->execute([$email]);
        if($row=$stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error="This user is already in db";
        }
    }
    if($error=="")
    {

        $sql = "INSERT INTO tbl_users (Email, Password, Image) VALUES (?, ?, ?,);";
        $stmt= $dbh->prepare($sql);
        $stmt->execute([$email, $password, $fileName]);
        header("Location:  /");
        exit();
    }
    //echo "<script>alert('POST JS".$email."'); </script>";
}
else{
    $email="";
    $password="";
    $error="";
    $file="";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
<?php include("navbar.php");?>
<div class="container">
    <div class="row">
        <h1 class="col-12 text-center">Реєстрація</h1>
    </div>
    <div class="row">
        <form class="col-12 needs-validation" method="post" enctype="multipart/form-data" novalidate>
            <label class="offset-3 col-6 " style="color: red"><?php echo $error ?></label>
            <div class="offset-3 col-6 form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"  value="<?php echo $email ?>" aria-describedby="emailHelp" required>
                <div class="valid-feedback" >
                    All is good
                </div>
            </div>
            <div class="offset-3 col-6 form-group">
                <label for="password">Password</label>
                <input required type="password" value="<?php echo $password ?>" class="form-control" id="password" name="password" required>
                <div class="valid-feedback" >
                    All is good
                </div>
            </div>
            <div class="offset-3 col-6 form-group">
                <label for="file">Image</label>
                <input required type="file" class="form-control" id="file" name="file">
                <div class="valid-feedback">
                    All is good
                </div>
            </div>
            <button type="submit" class="offset-8 btn btn-primary">Save</button>
        </form>
    </div>
</div>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
<?php include_once("scripts.php"); ?>
</body>
</html>