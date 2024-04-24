<?php
require_once "config.php";
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
   case 'GET':
         if (!empty($_GET["nim"])&&!empty($_GET["kode_mk"])) {
            $student_grades = get_mhs($_GET["nim"],$_GET["kode_mk"]);
        }
         else
         {
            get_mhss();
         }
         break;
   case 'POST':
         insert_mhs();
         break;
   case 'PUT':
         if(!empty($_GET["nim"]&&!empty($_GET["kode_mk"]))){
            update_mhs($_GET["nim"], $_GET["kode_mk"]);
        }
         break;
   case 'DELETE':
         $id= $_GET["nim"];
         $kode= $_GET["kode_mk"];
         delete_mhs($id, $kode);
         break;
   default:
         header("HTTP/1.0 405 Method Not Allowed");
         break;
      break;
 }

   function get_mhss()
   {
      global $mysqli;
      $query = "SELECT mahasiswa.nim, mahasiswa.nama, mahasiswa.alamat, mahasiswa.tanggal_lahir,
                        matakuliah.kode_mk, matakuliah.nama_mk, matakuliah.sks,
                        perkuliahan.nilai
               FROM mahasiswa
               INNER JOIN perkuliahan ON mahasiswa.nim = perkuliahan.nim
               INNER JOIN matakuliah ON perkuliahan.kode_mk = matakuliah.kode_mk";
      $data = array();
      $result = $mysqli->query($query);
      while ($row = mysqli_fetch_assoc($result)) {
         $data[] = $row;
      }
      $response = array(
         'status' => 1,
         'message' => 'Get List Mahasiswa Successfully.',
         'data' => $data
      );
      header('Content-Type: application/json');
      echo json_encode($response);
   }
 
   function get_mhs($nim,$kode_mk)
   {
      global $mysqli;
      $query = "SELECT mahasiswa.nim, mahasiswa.nama, mahasiswa.alamat, mahasiswa.tanggal_lahir,
                        matakuliah.kode_mk, matakuliah.nama_mk, matakuliah.sks,
                        perkuliahan.nilai
                  FROM mahasiswa
                  INNER JOIN perkuliahan ON mahasiswa.nim = perkuliahan.nim
                  INNER JOIN matakuliah ON perkuliahan.kode_mk = matakuliah.kode_mk";

      if($nim != 0)
      {
         $query .= " WHERE perkuliahan.nim = '".$nim."' AND perkuliahan.kode_mk='".$kode_mk."'";
      }

      $data=array();
      $result=$mysqli->query($query);

      if ($result) {
         while($row=mysqli_fetch_object($result))
         {
               $data[]=$row;
         }
         $response=array(
               'status' => 1,
               'message' =>'Get Mahasiswa Successfully.',
               'data' => $data
         );
      } else {
         $response=array(
               'status' => 0,
               'message' =>'Error: Failed to execute query.'
         );
      }

      header('Content-Type: application/json');
      echo json_encode($response);
   }

   function insert_mhs() {
      global $mysqli;
      if(!empty($_POST["nim"])){
         $data=$_POST;
      }else{
         $data = json_decode(file_get_contents('php://input'), true);
      }

      $arrcheckpost = array('nim' => '','kode_mk' => '','nilai'=>'');
      $hitung = count(array_intersect_key($data, $arrcheckpost));
      if($hitung == count($arrcheckpost)){
         $query = "INSERT INTO perkuliahan (nim, kode_mk, nilai) VALUES ('$data[nim]', '$data[kode_mk]', $data[nilai])";
         $result=mysqli_query($mysqli,$query);
         if($result)
         {
               $response=array(
                  'status' => 1,
                  'message' =>'Mahasiswa Added Successfully.'
               );
            }
            else
            {
               $response=array(
                  'status' => 0,
                  'message' =>'Mahasiswa Addition Failed.'
               );
         }
      }else{
         $response=array(
               'status' => 0,
               'message' =>'Parameter Do Not Match'
         );
      }
      header('Content-Type: application/json');
      echo json_encode($response);
}

  function update_mhs($nim, $kode_mk)
   {
      global $mysqli;

      $input_data = file_get_contents("php://input");
      $put_data = json_decode($input_data, true);

      if (isset($put_data["nilai"])) {
         $nilai = $put_data["nilai"];
         $result = mysqli_query($mysqli, "UPDATE perkuliahan SET nilai='$nilai' WHERE nim='$nim' AND kode_mk='$kode_mk'");
         
         if ($result) {
               $response = array(
                  'status' => 1,
                  'message' => 'Nilai Mahasiswa Updated Successfully.'
               );
         } else {
               $response = array(
                  'status' => 0,
                  'message' => 'Nilai Mahasiswa Updation Failed.'
               );
         }
      } else {
         $response = array(
               'status' => 0,
               'message' => 'Data nilai tidak ditemukan dalam permintaan.'
         );
      }

      header('Content-Type: application/json');
      echo json_encode($response);
   }
 
   function delete_mhs($nim, $kode_mk)
   {
      global $mysqli;
      $query = "DELETE FROM perkuliahan WHERE nim = '$nim' AND kode_mk = '$kode_mk'";
      if(mysqli_query($mysqli, $query))
      {
         $response=array(
            'status' => 1,
            'message' =>'Mahasiswa Deleted Successfully.'
         );
      }
      else
      {
         $response=array(
            'status' => 0,
            'message' =>'Mahasiswa Deletion Failed.'
         );
      }
      header('Content-Type: application/json');
      echo json_encode($response);
   }
?> 
