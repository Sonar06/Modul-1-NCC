<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN </title>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(150deg, #0e0d0d,#540c0c, #0e0d0d) ;
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
            text-align: center;
        }

        .title {
            color: #ffffff;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .label {
            display: block;
            color: white;
            font-size: 20px;
            font-weight: 700;
            text-align: left;
        }

        input {
            width: 94%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
            background: #e39595;
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        input::placeholder {
            color: #ffffff;
        }

        select {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
            background: #e39595;
            color: #ffffff;
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 20px;
        }

       
        
        .forgot {
            margin-top: -15px;
            font-size: 12px;
            color: #eada25;
            text-align: right;
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
            margin-top: 5px;
            margin-bottom: 20px;
            
        }

        .btn:hover {
            background: rgba(109, 105, 105, 0.2);
        }

    </style>
</head>

<body>

<div class="container">
    <div class="card">

        <h1 class="title">SIGN UP</h1>

        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal_pass"){
                echo "<div class='alert'>Password dan konfirmasi tidak cocok!</div>";
            } else if($_GET['pesan'] == "gagal_user"){
                echo "<div class='alert'>Username sudah digunakan!</div>";
            } else if($_GET['pesan'] == "sukses"){
                echo "<div class='alert' style='background:#dcfce7; color:#166534;'>Pendaftaran berhasil! Silakan Login.</div>";
            }
        }
        ?>
        <form action="../private/proses_daftar.php" method="POST">
    
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirm" placeholder="Retype password" required>
            <select name="role" required>
                <option value="" disabled selected>Role</option>
                <option value="user">User</option>
                <option value="admin">Editor</option>
            </select>
            <a href="index.php"><button type="submit" class="btn daftar">Sign Up</button></a>

        </form>
       
    </div>
</div>

</body>
</html>