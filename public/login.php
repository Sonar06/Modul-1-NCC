<?php

    session_start();
    
    require_once __DIR__ . "/../config/db.php";

    
    //library google api
    require_once '../vendor/autoload.php';

    $Client_ID = 'Your-Client-ID.apps.googleusercontent.com';
    $Client_Secret = 'Your-Client-Secret';
    $Redirect_URI = 'Your-Redirect-URI/login.php';

    $client = new Google\Client();

    $client->setClientId($Client_ID);
    $client->setClientSecret($Client_Secret);
    $client->setRedirectUri($Redirect_URI);

    // echo $client->createAuthUrl();

    $client->addScope('email');
    $client->addScope('profile');

    $login_url = $client->createAuthUrl();

    if(isset($_GET['code'])){

        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        
        if(!isset ($token['error'])){
            $client->setAccessToken($token['access_token']);

            $service = new Google\Service\Oauth2($client);
            $profile = $service->userinfo->get();
            
            // echo "<pre>";
            //     print_r ($profile);  
            // echo "</pre>";

            // ambil data
            $g_name = $profile['name'];
            //$g_email = $profile['email'];
            $g_id = $profile['id'];

            $query_check = "SELECT * FROM users WHERE username = '$g_name'";
            $run_query_check = mysqli_query($conn, $query_check);
            $d = mysqli_fetch_array($run_query_check);

            if ($d){
                //user sudah terdaftar, buat session
                $_SESSION['user_id'] = $d['id'];
                $_SESSION['username'] = $d['username'];
                $_SESSION['role'] = $d['role'];
                $_SESSION['status'] = "login";

                //redirect ke halaman dashboard
                header("Location: index.php");
                exit();

            } else {
                $query_insert = "INSERT INTO users (username, password, role, g_auth) VALUES ('$g_name','', 'user', '$g_id')";
                $run_query_insert = mysqli_query($conn, $query_insert); 

                if ($run_query_insert) {
                    // Jika Insert Berhasil, langsung buat session (Auto Login)
                    
                    // Ambil ID yang baru saja dibuat
                    $new_id = mysqli_insert_id($conn);

                    $_SESSION['user_id'] = $new_id;
                    $_SESSION['username'] = $g_name;
                    //$_SESSION['role'] = 'user'; // Default role
                    $_SESSION['status'] = "login";

                    header("Location: index.php"); // Atau index.php sesuai kebutuhan
                    exit();
                } else {
                    echo "login gagal";
                }
            }

        } else {
            echo "Login Gagal";
        }
        // } else {
        //     //user belum terdaftar, redirect ke halaman pendaftaran
        //     header("Location: register.php?email=$email&name=$name");
        //     exit();
        // }
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOGIN</title>

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(150deg, #1b1a1a, #6c0a0a,  #21202036);
    }

    .container {
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .card {
        background: #f5f5f536;
        width: 420px;
        padding: 20px 40px;
        border-radius: 15px;
        border: none;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(22px);
        text-align: center;
    }

    .title {
        color: #ffffff;
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    input {
        width: 94%;
        padding: 12px;
        border: none;
        border-radius: 10px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
        background: #e39595;
        font-size: 14px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    input::placeholder {
        color: #ffffff;
    }

    .join-text {
        display: flex;
        align-items: center;
        gap: 3px;
        justify-content: flex-end;
        margin-top: -10px;
    }

    .join-text .forgot {
        margin: 0;
        padding-top: 3px;
        font-size: 12px;
        color: #600303;
        text-decoration: none;
    }

    .join-text .text {
        margin: 0;
        font-size: 12px;
        color: #600303;
    }

    .btn {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: none;
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2);
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        background: #cf1515;
        color: rgb(255, 255, 255);
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .btn:hover {
        background: #97101079;
    }

    .gLogin {
        margin-top: 20px;
        margin-bottom: 20px;
        text-align: center;
    }

    /* --- BAGIAN INI YANG DITAMBAHKAN UNTUK MENGECILKAN GAMBAR --- */
    .gLogin img {
        width: 40px; /* Ganti angka ini sesuai keinginan (misal: 30px atau 50px) */
        height: auto; /* Agar gambar tetap proporsional */
        transition: transform 0.3s ease; /* Efek halus saat di-hover (opsional) */
    }

    .gLogin img:hover {
        transform: scale(1.1);
    }
</style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1 class="title">LOGIN</h1>

            <form action="../private/proses_login.php" method="POST">
                
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>       
                
                <?php 
                if(isset($_GET['pesan'])){
                    if($_GET['pesan'] == "gagal"){
                        echo "<div class='alert' style='color:white; font-weight:semi-bold;'>Username atau Password salah!</div>";
                    }
                }
                ?>
                <button type="submit" class="btn">Login</button>
                
                <div class="join-text">
                    <span class="text">Belum punya akun?</span>
                    <a href="daftar.php" class="forgot">Daftar</a>
                </div>

                <div class="gLogin">
                    <a href="<?php echo $login_url; ?>">
                        <img src="../assets/download-removebg-preview.png" alt="Google Sign-In Button">
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>