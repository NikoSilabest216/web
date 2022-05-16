<?php
require_once("config.php");
require_once("auth.php");

if (isset($_POST['edit'])) {
    $namaFile = $_FILES['file']['name'];
    $namaSementara = $_FILES['file']['tmp_name'];

    // tentukan lokasi file akan dipindahkan
    $dirUpload = "./storage/";
    $ext = pathinfo($namaFile, PATHINFO_EXTENSION);
    $linkPhoto = $dirUpload . date("Y-m-d-H--i--sa").".".$ext;

    // pindahkan file
    $terupload = move_uploaded_file($namaSementara, $linkPhoto);

    if ($terupload) {
        $sql = "UPDATE users set username=:username, email=:email, name=:name, photo=:photo
        where id=:id";
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);


        $stmt = $db->prepare($sql);

        $params = array(
            ":username" => $username,
            ":email" => $email,
            ":name" => $name,
            ":photo" => $linkPhoto,
            ":id" => $_SESSION["user"]["id"]
        );


        // eksekusi query untuk menyimpan ke database
        $saved = $stmt->execute($params);

        // jika query simpan berhasil, maka user sudah terdaftar
        // maka alihkan ke halaman login
        if ($saved)
            $sql = "SELECT * FROM users WHERE id=:id";
        $stmt = $db->prepare($sql);

        // bind parameter ke query
        $params = array(
            ":id" => $_SESSION["user"]["id"]
        );

        $stmt->execute($params);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION["user"] = $user;
        header("Location: timeline.php");
    }

    if (isset($_POST['delete'])) {
        $sql = "DELETE FROM users 
    where id=:id";

        $stmt = $db->prepare($sql);

        $params = array(
            ":id" => $_SESSION["user"]["id"]
        );


        // eksekusi query untuk menyimpan ke database
        $saved = $stmt->execute($params);

        // jika query simpan berhasil, maka user sudah terdaftar
        // maka alihkan ke halaman login
        if ($saved)
            session_destroy();
        header("Location: index.php");
    } else {
        echo "Upload Gagal!";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register Pesbuk</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">

                <p>&larr; <a href="timeline.php">Back</a>


                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input class="form-control" type="text" name="name" placeholder="Nama kamu" value="<?php echo  $_SESSION["user"]["name"] ?>" />
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input class="form-control" type="text" name="username" placeholder="Username" value="<?php echo  $_SESSION["user"]["username"] ?>" />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="email" placeholder="Alamat Email" value="<?php echo  $_SESSION["user"]["email"] ?>" />
                    </div>

                    <input type="file" accept=".jpg,.png,.svg" class="btn btn-success btn-block" name="file" value="Upload" />
                    <input type="submit" class="btn btn-success btn-block" name="edit" value="Edit" />

                    <input type="submit" class="btn btn-success btn-block" name="delete" value="Delete" />


                </form>

            </div>

            <div class="col-md-6">
                <img class="img img-responsive" style="width: 700px;" src="img/gotham.png" />
            </div>

        </div>
    </div>

</body>

</html>