<?php
session_start();
include('db.php');

// ተማሪ መሆኑን ማረጋገጥ
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="am">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Result Lookup</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f4f6f9; color: #333; min-height: 100vh; }
        
        /* Navbar */
        .navbar { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .navbar h2 { font-size: 20px; font-weight: 600; }
        .logout-btn { background: #ff4757; color: white; text-decoration: none; padding: 8px 18px; border-radius: 20px; font-weight: bold; transition: 0.3s; }
        .logout-btn:hover { background: #ff6b81; transform: translateY(-2px); }

        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }

        /* Card Form */
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .card h3 { color: #2c5364; margin-bottom: 20px; font-size: 20px; text-align: center; font-weight: 600; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #555; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px; outline: none; transition: 0.3s; }
        .form-group input:focus { border-color: #2c5364; box-shadow: 0 0 6px rgba(44, 83, 100, 0.25); }

        .btn-search { width: 100%; background: linear-gradient(135deg, #00b4db, #0083b0); color: white; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { opacity: 0.95; transform: translateY(-1px); }

        /* Result Box */
        .result-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); margin-top: 25px; }
        .student-info { background: #eef5f8; padding: 15px; border-radius: 8px; margin-bottom: 15px; color: #2c5364; border-left: 5px solid #0083b0; }
        .student-info h4 { font-size: 16px; }

        /* Table Design */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: center; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background-color: #0083b0; color: white; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        .error-msg { color: #d9534f; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

<div class="navbar">
    <h2>የተማሪ ውጤት ማረጋገጫ ገጽ</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">
    <div class="card">
        <h3>ውጤት ለመመልከት መረጃዎን ያስገቡ</h3>
        <form method="POST">
            <div class="form-group">
                <label>የተማሪ ስም (Student Name)</label>
                <input type="text" name="student_name" placeholder="ሙሉ ስም ያስገቡ..." required value="<?php echo $_POST['student_name'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="student_id" placeholder="Student ID (ለምሳሌ፦ RU001)..." required value="<?php echo $_POST['student_id'] ?? ''; ?>">
            </div>
            <button type="submit" name="check_result" class="btn-search">ውጤት አሳይ</button>
        </form>
    </div>

    <?php
    if (isset($_POST['check_result'])) {
        $input_name = $conn->real_escape_string(trim($_POST['student_name']));
        $input_id = $conn->real_escape_string(trim($_POST['student_id']));

        // ስሙ እና ID ቱ ከዳታቤዝ ጋር መዛመዳቸውን ማረጋገጥ
        $query = "SELECT * FROM students WHERE student_id = '$input_id' AND fullname LIKE '%$input_name%'";
        $result = $conn->query($query);

        echo "<div class='result-card'>";
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();
            $sid = $student['student_id'];

            echo "<div class='student-info'>";
            echo "<h4>ስም፦ {$student['fullname']} | ID: {$sid} | ዲፓርትመንት፦ {$student['department']}</h4>";
            echo "</div>";

            // ውጤቶችን ማምጣት
            $res_query = "SELECT * FROM results WHERE student_id = '$sid'";
            $res_result = $conn->query($res_query);

            if ($res_result->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>የኮርስ ስም</th>
                            <th>ፈተና (ከ 50)</th>
                            <th>ፋይናል (ከ 50)</th>
                            <th>አጠቃላይ ድምር (ከ 100)</th>
                        </tr>";
                while ($row = $res_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['course_name']}</td>
                            <td>{$row['test_score']}</td>
                            <td>{$row['final_score']}</td>
                            <td><b>{$row['total']}</b></td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: #777; text-align:center;'>ለዚህ ተማሪ የተመዘገበ ውጤት የለም።</p>";
            }
        } else {
            echo "<p class='error-msg'>ያስገቡት ስም እና Student ID አልተዛመዱም ወይም አልተገኙም!</p>";
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>