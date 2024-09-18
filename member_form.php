<?php
include('database-connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    // Handle optional ID fields
    $father_id = !empty($_POST['father_id']) ? intval($_POST['father_id']) : "NULL";
    $mother_id = !empty($_POST['mother_id']) ? intval($_POST['mother_id']) : "NULL";
    $spouse_id = !empty($_POST['spouse_id']) ? intval($_POST['spouse_id']) : "NULL";

    // Handle avatar upload
    $avatar = null;
    if (!empty($_FILES['avatar']['name'])) {
        $target_dir = "uploads/";
        $avatar = $target_dir . basename($_FILES["avatar"]["name"]);
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $avatar)) {
            // File successfully uploaded
        } else {
            // File upload failed
            $avatar = null;
        }
    }

    // Prepare SQL query
    $sql = "INSERT INTO family_members (first_name, last_name, birthday, gender, father_id, mother_id, spouse_id, avatar) 
            VALUES ('$first_name', '$last_name', '$birthday', '$gender', $father_id, $mother_id, $spouse_id, '$avatar')";

    // Execute SQL query
    if ($conn->query($sql) === TRUE) {
        echo "<p>New family member added successfully</p>";
    } else {
        echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Family Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"],
        select,
        input[type="file"] {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Family Member</h1>
        <form action="member_form.php" method="post" enctype="multipart/form-data">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="date" name="birthday" required>
            <select name="gender" required>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            <input type="number" name="father_id" placeholder="Father ID (Optional)">
            <input type="number" name="mother_id" placeholder="Mother ID (Optional)">
            <input type="number" name="spouse_id" placeholder="Spouse ID (Optional)">
            <input type="file" name="avatar">
            <button type="submit">Add Member</button>
        </form>
    </div>
</body>
</html>
