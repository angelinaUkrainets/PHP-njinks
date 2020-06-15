<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("connection_database.php");
    $email = $_POST["email"];
    $password = $_POST['password'];

    function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    $image = bin2hex(openssl_random_pseudo_bytes(16));
    $imageName = "" . $image . ".jpg";
    $image = "uploads/" . $image . ".jpg";
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $image)) {
        echo "Файл корректен и был успешно загружен.\n";
    } else {
        echo "Возможная атака с помощью файловой загрузки!\n";
    }


    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $error = "Занадто слабкий пароль";
    } else {
        $error = "";

        $sql = "SELECT id FROM `users` AS u WHERE u.Email=? LIMIT 1";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$email]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $error = "Данний юзер вже зареєстрований";
        }
//        while ($row = $stmt->fetch()) {
//            if($row['email']==$email){
//                $error="Данний юзер вже зареєстрований";
//            }
//            //echo $row['email']."<br />\n";
//        }
        if ($error == "") {
            $image = bin2hex(openssl_random_pseudo_bytes(16)) . ".jpg";
            $path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $image;
            $target_dir = "uploads/";
            $img = $_POST['output'];
            if ($img == "") {
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk = 1;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
                if (isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if ($check !== false) {
                        if ($check[1] <= 300) {
                            $error = "Фото замале !";
                            $uploadOk = 0;
                        } elseif ($check[0] <= 300) {
                            $error = "Фото замале !";
                            $uploadOk = 0;
                        } else {
                            $uploadOk = 1;
                        }
                    } else {
                        $error = "File is not an image.";
                        $uploadOk = 0;
                    }
                }

// Check if file already exists
                if (file_exists($target_file)) {
                    $error = "Sorry, file already exists.";
                    $uploadOk = 0;
                }

                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    $error = "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                    && $imageFileType != "gif") {
                    $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
                if ($uploadOk == 0) {
                    //$error="Оберіть інше фото";
                } else {

                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $path)) {
                        $error = "";
                    } else {
                        $error = "Eroor";
                    }
                }
            }
            //echo "<script>alert('POST JS".$email."'); </script>";
            else {


                list(, $img) = explode(';', $img);
                list(, $img) = explode(',', $img);


                $img = base64_decode($img);
                $arr = getimagesizefromstring($img);
                if ($arr[0] > 300 && $arr[1] > 300) {
                    file_put_contents($path, $img);
                    $error = "";
                } else {
                    $error = "Оберіть інше фото";
                }
            }
            if ($error == "") {


                $sql = "INSERT INTO `users` (`Email`, `Password`, `Image`) VALUES (?, ?, ?);";
                $stmt = $dbh->prepare($sql);
                $stmt->execute([$email, $password, $image]);
                include_once("compressor.php");
                my_image_resize(200, 200, $path);
                echo '<script>window.location.href = "index.php";</script>';
            }
        }

    }
}
else{
    $email="";
    $password="";
    $error="";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
    <title>Document</title>
</head>
<body>
<?php include("navbar.php"); ?>
<div class="container">
    <div class="row">
        <h1 class="col-12 text-center">Реєстрація</h1>
    </div>
    <div class="row">
        <form class="col-12 " action="registration.php" method="post" enctype="multipart/form-data">
            <label class="offset-3 col-6 " style="color: #ff0000"><?php echo $error ?></label>
            <div class="offset-3 col-6 form-group">
                <label for="email">Електронна пошта</label>
                <input required type="email" class="form-control" id="email" name="email" value="<?php echo $email ?>"
                       aria-describedby="emailHelp">
            </div>
            <div class="offset-3 col-6 form-group">
                <label for="password">Пароль</label>
                <input required type="password" value="<?php echo $password ?>" class="form-control" id="password"
                       name="password">
            </div>
            <div class="offset-3 col-6 mb-3 input-group mt-2">
                <div class="input-group mb-3">

                    <div class="custom-file">
                        <input required type="file" onchange="loadFile(event)"  class="custom-file-input" name="fileToUpload" id="fileToUpload" aria-describedby="fileToUpload">
                        <label class="custom-file-label" for="fileToUpload">Choose file</label>
                    </div>
                </div>

            </div>
            <input type="hidden" id="h" name="output"/>
            <img id="output" src="uploads/default.png" class="offset-4" style="border-radius: 50%; height: 250px;width: 250px;"/>
            <script>
                var loadFile = function(event) {

                    //alert("t");
                    var output = document.getElementById('output');
                    output.src = URL.createObjectURL(event.target.files[0]);
                    output.onload = function() {
                        URL.revokeObjectURL(output.src) // free memory
                    }


                };
            </script>
            <div class="offset-3 form-group form-check">
                <input required type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Я буду ходити в магазин в масці</label>
            </div>
            <button type="submit" class="offset-8 btn btn-primary">Реєстрація</button>
        </form>
    </div>
</div>
<script src="node_modules/jquery/dist/jquery.min.js" />
<script src="node_modules/popper.js/dist/popper.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<?php include_once("services/cropper.php");?>

<?php include_once "scripts.php" ?>
<script src="node_modules/cropperjs/dist/cropper.min.js"></script>
<script>

    $(function() {

        let dialogCropper = $("#cropperModal");
        $("#fileToUpload").on("change", function() {
            //console.log("----select file------", this.files);
            //this.files;
            if (this.files && this.files.length) {
                let file = this.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    //cropper.destroy();
                    //$('#modalImg').attr('src', e.target.result);
                    dialogCropper.modal('show');
                    cropper.replace(e.target.result);

                }
                reader.readAsDataURL(file);

            }
        });

        const image = document.getElementById('modalImg');
        var lastValidCrop = null;
        const cropper = new Cropper(image, {
            aspectRatio: 1/1,
            viewMode: 1,
            autoCropArea: 1.5,
            crop(e) {
                var validCrop = true;
                if (e.detail.width < 300) validCrop = false;
                if (e.detail.height < 300) validCrop = false;

                if (validCrop) {
                    lastValidCrop = cropper.getData();
                    $("#crop_photo_x").val(e.detail.x);
                    $("#crop_photo_y").val(e.detail.y);
                    $("#crop_photo_width").val(e.detail.width);
                    $("#crop_photo_height").val(e.detail.height);
                } else {
                    cropper.setData(lastValidCrop);
                }
            },
        });

        $("#rotateImg").on("click",function (e) {
            cropper.rotate(90);
        });

        $("#croppImg").on("click", function (e) {
            e.preventDefault();

            var imgContent = cropper.getCroppedCanvas().toDataURL();


            $("#h").val(imgContent);
            $("#output").attr("src", imgContent);
            dialogCropper.modal('hide');
        });

    });

</script>
</body>
</html>
