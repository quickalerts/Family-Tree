<?php
include('database-connection.php');

$sql = "SELECT * FROM family_members";
$result = $conn->query($sql);

$family_data = [];
while ($row = $result->fetch_assoc()) {
    $family_data[] = [
        'id' => $row['id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'birthday' => $row['birthday'],
        'gender' => $row['gender'],
        'father_id' => $row['father_id'],
        'mother_id' => $row['mother_id'],
        'spouse_id' => $row['spouse_id'],
        'avatar' => $row['avatar']
    ];
}

echo json_encode($family_data);
?>
