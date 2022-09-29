<?php

$conn = mysqli_connect("localhost","root","","db_sekolah");


$id = $_GET["id"];

$query = "DELETE FROM tb_siswa WHERE id=$id";
mysqli_query($conn,$query);


if(mysqli_affected_rows($conn)>0){
    echo "<script>
    alert('data berhasil dihapus');
    document.location.href = 'index.php';
</script>";
}else{
    echo "<script>
    alert('data gagal ditambahkan');
    document.location.href = 'index.php';
</script>";
}

?>