<?php
session_start();
include('db.php');

// አድሚን መሆኑን ማረጋገጥ
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$msg_type = "";

// ተማሪ መመዝገቢያ ፎርም ሲላክ
if (isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']);
    $fullname = trim($_POST['fullname']);
    $department = trim($_POST['department']);

    $sql = "INSERT INTO students (student_id, fullname, department) VALUES ('$student_id', '$fullname', '$department')";
    if ($conn->query($sql) === TRUE) {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$student_id', '123456', 'student')");
        $message = "ተማሪው በትክክል ተመዝግቧል!";
        $msg_type = "success";
    } else {
        $message = "ስህተት፦ " . $conn->error;
        $msg_type = "error";
    }
}

// ውጤት ማስገቢያ ፎርም ሲላክ
if (isset($_POST['add_result'])) {
    $student_id = trim($_POST['student_id']);
    $course_name = trim($_POST['course_name']);
    $test_score = $_POST['test_score'];
    $final_score = $_POST['final_score'];
    $total = $test_score + $final_score;

    $sql = "INSERT INTO results (student_id, course_name, test_score, final_score, total) 
            VALUES ('$student_id', '$course_name', '$test_score', '$final_score', '$total')";
    if ($conn->query($sql) === TRUE) {
        $message = "ውጤቱ በትክክል ተመዝግቧል!";
        $msg_type = "success";
    } else {
        $message = "ስህተት፦ " . $conn->error;
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Result System</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f0f2f5; color: #333; }
        
        /* Navbar */
        .navbar { background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar h2 { font-size: 22px; font-weight: 600; }
        .logout-btn { background: #ff4757; color: white; text-decoration: none; padding: 8px 18px; border-radius: 20px; font-weight: bold; transition: 0.3s; }
        .logout-btn:hover { background: #ff6b81; transform: translateY(-2px); }

        .main-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* Alert Message */
        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 25px; font-weight: 500; text-align: center; }
        .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Grid Layout */
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .card h3 { color: #1e3c72; margin-bottom: 18px; font-size: 18px; border-bottom: 2px solid #f0f2f5; padding-bottom: 8px; }

        /* Forms */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #555; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: #2a5298; box-shadow: 0 0 5px rgba(42, 82, 152, 0.2); }
        
        .btn-submit { width: 100%; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border: none; padding: 12px; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-blue { background: linear-gradient(135deg, #2193b0, #6dd5ed); }

        /* Full Width Search Card */
        .full-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-top: 30px; }
        .search-flex { display: flex; gap: 10px; margin-top: 10px; }
        .search-flex input { flex: 1; padding: 10px 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .search-btn { background: #2a5298; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: center; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background-color: #2a5298; color: white; font-weight: 600; }
        tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Admin Dashboard</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="main-container">

    <?php if ($message != ""): ?>
        <div class="alert <?php echo $msg_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="grid-container">
        <!-- 1. ተማሪ መመዝገቢያ -->
        <div class="card">
            <h3>አዲስ ተማሪ መመዝገቢያ</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" placeholder="ለምሳሌ፦ RU001" required>
                </div>
                <div class="form-group">
                    <label>ሙሉ ስም</label>
                    <input type="text" name="fullname" placeholder="የተማሪው ሙሉ ስም" required>
                </div>
                <div class="form-group">
                    <label>ዲፓርትመንት</label>
                    <input type="text" name="department" placeholder="ለምሳሌ፦ Software Engineering" required>
                </div>
                <button type="submit" name="add_student" class="btn-submit">ተማሪ መዝግብ</button>
            </form>
        </div>

        <!-- 2. ውጤት ማስገቢያ -->
        <div class="card">
            <h3>የተማሪ ውጤት ማስገቢያ</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" placeholder="ለምሳሌ፦ RU001" required>
                </div>
                <div class="form-group">
                    <label>የኮርስ ስም</label>
                    <input type="text" name="course_name" placeholder="ለምሳሌ፦ Java Programming" required>
                </div>
                <div class="form-group">
                    <label>የክፍል ስራ/ፈተና (ከ 50)</label>
                    <input type="number" step="0.01" name="test_score" placeholder="0 - 50" required>
                </div>
                <div class="form-group">
                    <label>የፋይናል ፈተና (ከ 50)</label>
                    <input type="number" step="0.01" name="final_score" placeholder="0 - 50" required>
                </div>
                <button type="submit" name="add_result" class="btn-submit btn-blue">ውጤት መዝግብ</button>
            </form>
        </div>
    </div>

    <!-- 3. ተማሪዎችን በስም ወይም በ ID መፈለጊያ ክፍል -->
    <div class="full-card">
        <h3 style="color: #1e3c72;">የተማሪዎችን ውጤት መፈለጊያ (Search)</h3>
        <form method="GET">
            <div class="search-flex">
                <input type="text" name="search" placeholder="የተማሪውን ስም ወይም ID እዚህ ይጻፉ..." value="<?php echo $_GET['search'] ?? ''; ?>">
                <button type="submit" class="search-btn">ፈልግ</button>
            </div>
        </form>

        <?php
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = $conn->real_escape_string(trim($_GET['search']));
            
            $s_query = "SELECT * FROM students WHERE student_id LIKE '%$search%' OR fullname LIKE '%$search%'";
            $s_result = $conn->query($s_query);

            if ($s_result->num_rows > 0) {
                while ($student = $s_result->fetch_assoc()) {
                    $sid = $student['student_id'];
                    echo "<div style='margin-top: 20px; background: #eef2f5; padding: 15px; border-radius: 8px;'>";
                    echo "<p style='font-size: 15px; font-weight: bold; color: #2a5298;'>ስም፦ {$student['fullname']} | ID: {$sid} | ዲፓርትመንት፦ {$student['department']}</p>";

                    $r_query = "SELECT * FROM results WHERE student_id = '$sid'";
                    $r_result = $conn->query($r_query);

                    if ($r_result->num_rows > 0) {
                        echo "<table>
                                <tr>
                                    <th>የኮርስ ስም</th>
                                    <th>ፈተና (ከ50)</th>
                                    <th>ፋይናል (ከ50)</th>
                                    <th>አጠቃላይ ድምር (ከ100)</th>
                                </tr>";
                        while ($res = $r_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$res['course_name']}</td>
                                    <td>{$res['test_score']}</td>
                                    <td>{$res['final_score']}</td>
                                    <td><b>{$res['total']}</b></td>
                                  </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p style='color: #777; margin-top: 8px;'>ለዚህ ተማሪ የተመዘገበ ውጤት የለም።</p>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p style='color: #d9534f; margin-top: 15px; font-weight: bold;'>ምንም ተማሪ አልተገኘም!</p>";
            }
        }
        ?>
    </div>

</div>

</body>
</html>