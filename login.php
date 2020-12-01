<?php
session_start();
$error="";
if (isset($_SESSION['user'])) {
    header('Location:user/user_dashboard.php');
}
if (isset($_SESSION['admin'])) {
    header('Location:admin/admin_dashboard.php');
}
if (isset($_POST['submit'])) {
    include 'admin/tbl_user.php';
    $tbluser=new tblUser();
    $username=$_POST['username'];
    $password=$_POST['password'];
    $password=md5($password);
    $logindata=$tbluser->login($username,$password);
    if ($logindata!=false) {
        while($row=$logindata->fetch_assoc()) {
            if ($row['isblock']==1) {
                $cookie_username='username';
                $cookie_value = $row['user_name'];
                setcookie($cookie_username, $cookie_value, time() + (86400 * 30), "/");
                if ($row['is_admin']==0) {
                    $_SESSION['user']=array($row['user_name'],$row['user_id']);
                    if (isset($_SESSION['booking'])) {
                        include_once 'admin/tbl_ride.php';
                        $tblride=new tblRide();
                        $userid=$_SESSION['user'][1];
                        $pickup=$_SESSION['booking'][0];
                        $drop=$_SESSION['booking'][1];
                        $cabtype=$_SESSION['booking'][2];
                        $totaldistance=$_SESSION['booking'][3];
                        $luggage=$_SESSION['booking'][4];
                        $fare=$_SESSION['booking'][5];
                        $tblride->insertData($pickup,$drop,$cabtype,$totaldistance,$luggage,$fare,$userid);
                        header('location:user/user_dashboard.php');
                    }
                    else {
                        header('location:user/user_dashboard.php');
                    }
                    
                } elseif ($row['is_admin']==1) {
                    $_SESSION['admin']=array($row['user_name'],$row['user_id']);
                    header('location:admin/admin_dashboard.php');
                }
            }
            else {
                $error="Kindly wait for the admin to grant you access";
            }
        }
    } else {
        $error="username or password is incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="user/style.css">
</head>
<body>
    <header class="signup-header">
        <nav>
            <a href="index.php"><h4><span class='logo-color'>Ced</span>Cab</h4></a>
            <a href="signup.php">Sign up</a>
            <a href="index.php">CalculateFare</a>
            <a href="javascript:void(0)" class="signup-right">ContactUs</a>
            <a href="javascript:void(0)" class="signup-right">AboutUs</a>
        </nav>
    </header>
    <div class="signup-form">
        <form action="login.php" method="post">
            <label for="username">Username</label>
            <div>
                <input type="text" name="username" <?php if (isset($_COOKIE['username'])) echo 'value="'.$_COOKIE["username"].'"'; ?>id="username" placeholder="username.." required>
            </div>
            <label for="password">Password</label>
            <div>
                <input type="password" name="password" id="password" placeholder="password.." required>
            </div>
            <div>
                <input type="submit" value="LOG IN" name="submit" id="submit">
            </div>
        </form>
    </div>
    <div class="error warning-message"><?php echo $error; ?></div>
    <div class="page-content"></div>
    <div class="footer-dashboard">
        <footer class="container-fluid footer-bg-color">
            <div class='row margin-row'>
                <div class="col-lg-4 text-center icon-color">
                    <i class="fa fa-instagram" aria-hidden="true"></i>
                    <i class="fa fa-facebook-square" aria-hidden="true"></i>
                    <i class="fa fa-twitter-square" aria-hidden="true"></i>
                    <i class="fa fa-linkedin-square" aria-hidden="true"></i>
                </div>
                <div class="col-lg-4 text-center">
                    <div id="middle-footer">Copyright &#169; <span class='logo-color'>Ced</span>Cab</div> 
                    <div>2016-2020</div>
                </div>
                <div class="col-lg-4 text-center">
                    <a href="javascript:void(0)" class="btn btn-link text-white">Disclaimer</a>
                    <a href="javascript:void(0)" class="btn btn-link text-white">About Us</a>
                    <a href="javascript:void(0)" class="btn btn-link text-white">Contact Us</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>