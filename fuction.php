<?php
$conn = mysqli_connect("localhost","root","","db_sekolah");
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result) ) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data){
    global $conn;
    $nama = htmlspecialchars($data["nama"]);
    $umur = htmlspecialchars($data["umur"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $tgllahir = htmlspecialchars($data["tgllahir"]);
    // $foto = htmlspecialchars($data["foto"]);
    $foto = upload();
    if(!$foto){
        return false;
    }

    $query = "INSERT INTO tb_siswa
                VALUES
              ('','$nama', '$umur', '$alamat','$tgllahir', '$foto') ";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);

}

function ubah($data){
    global $conn;
    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $umur = htmlspecialchars($data["umur"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $tgllahir = htmlspecialchars($data["tgllahir"]);
    // $foto = htmlspecialchars($data["foto"]);
    $fotoLama = htmlspecialchars($data["fotoLama"]);

    //cek
    if($_FILES['foto']['error']===UPLOAD_ERR_NO_FILE){
        $foto = $fotoLama;
    }else{
        $foto = upload();
    }

    $query = "UPDATE tb_siswa 
            SET nama = '$nama',
                umur = '$umur',
                alamat = '$alamat',
                tgllahir = '$tgllahir',
                foto = '$foto'
            WHERE id=$id   
            ";
    mysqli_query($conn,$query);

    return mysqli_affected_rows($conn);
}

function upload(){

    $namaFile = $_FILES['foto']['name'];
    $ukuranFile = $_FILES['foto']['size'];
    $error = $_FILES['foto']['error'];
    $tmpName = $_FILES['foto']['tmp_name'];

    // cek 
    if($error === UPLOAD_ERR_NO_FILE){
        echo "<script>
                alert('pilih gambar dulu!')
            </script>";
        return false;
    }
    $ekstensiGambarVld = ['jpg', 'jpeg', 'png', 'gif'];
    $ekstensiGambar = explode('.',$namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if(!in_array($ekstensiGambar,$ekstensiGambarVld)){
        echo "<script>
                alert('tolong upload gambar!')
            </script>";
        return false;
    };
    if($ukuranFile>1000000){
        echo "<script>
                alert('ukuran terlalu besar!')
            </script>";
    return false;
    }
    $namaFileBaru = date('Ymd');
    $namaFileBaru .= '_';
    $namaFileBaru .= $namaFile;
    move_uploaded_file($tmpName, 'image/' . $namaFileBaru);
    return $namaFileBaru;


}

function search($keyword){
    $query = "SELECT * FROM tb_siswa
                WHERE
              nama LIKE '%$keyword%' 
            ";
    return query($query);
}

function registrasi($data){
    global $conn;

    $username = strtolower(stripslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);


    if (empty(trim($username))) {
        echo "<script>
                alert('masukkan username dan password')    
                </script>";
        return ;
    }

    // cek username sudah ada atau belum
    $result = mysqli_query($conn, "SELECT username FROM tb_user 
         WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)){
        echo "<script>
                alert('username sudah terdaftar!')    
                </script>";
        return false;
    }

    // cek konfirmasi password
    if ($password !== $password2){
        echo "<script>
                alert('konfirmasi password tidak sesuai!')    
                </script>";
        return false;                
    }

    // enkripsi passwordn
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan userbaru ke database
    mysqli_query($conn, "INSERT INTO tb_user VALUES('', '$username', '$password')");
    return mysqli_affected_rows($conn);
}

?>