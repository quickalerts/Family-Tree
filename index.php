<?php
include('database-connection.php');

// Fetch family members from the database
$sql = "SELECT * FROM family_members";
$result = $conn->query($sql);

$members = [];
$relationships = [];

// Fetch all members
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $members[$id] = [
            "id" => (string)$id,
            "data" => [
                "first name" => $row['first_name'],
                "last name" => $row['last_name'],
                "birthday" => date('d-m-Y', strtotime($row['birthday'])),
                "avatar" => $row['avatar'],
                "gender" => $row['gender'],
                "Gender" => $row['gender'] === 'M' ? 'Male' : 'Female'
            ],
            "rels" => [
                "father" => $row['father_id'] ? (string)$row['father_id'] : null,
                "mother" => $row['mother_id'] ? (string)$row['mother_id'] : null,
                "spouses" => $row['spouse_id'] ? [(string)$row['spouse_id']] : [],
                "children" => []
            ]
        ];

        // Initialize relationships
        if (!isset($relationships[$row['father_id']])) {
            $relationships[$row['father_id']] = ['children' => []];
        }
        if (!isset($relationships[$row['mother_id']])) {
            $relationships[$row['mother_id']] = ['children' => []];
        }
        if (!isset($relationships[$row['spouse_id']])) {
            $relationships[$row['spouse_id']] = ['spouses' => []];
        }

        // Add relationships
        if ($row['father_id']) {
            $relationships[$row['father_id']]['children'][] = $id;
        }
        if ($row['mother_id']) {
            $relationships[$row['mother_id']]['children'][] = $id;
        }
        if ($row['spouse_id']) {
            $relationships[$row['spouse_id']]['spouses'][] = $id;
        }
    }
}

// Populate children and spouses in members
foreach ($members as $id => &$member) {
    if (isset($relationships[$id])) {
        $member['rels']['children'] = $relationships[$id]['children'] ?? [];
        $member['rels']['spouses'] = $relationships[$id]['spouses'] ?? [];
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>My Family Chart</title>
  <link rel="stylesheet" href="navbar.css">
  <script src="https://unpkg.com/d3@6"></script>
  <script src="https://unpkg.com/family-chart"></script>
  <link rel="stylesheet" href="./family-chart.css">
</head>
<body>
  <nav class="navbar">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="member_form.php">Add Member</a></li>
      <li><a href="about.php">About</a></li>
    </ul>
  </nav>
  <div id="FamilyChart" class="f3"></div>
  <script type="module">
    const store = f3.createStore({
        data: <?php echo json_encode(array_values($members)); ?>,
        node_separation: 250,
        level_separation: 150
    }),
    view = f3.d3AnimationView({
        store,
        cont: document.querySelector("#FamilyChart")
    }),
    Card = f3.elements.Card({
        store,
        svg: view.svg,
        card_dim: {w:220,h:70,text_x:75,text_y:15,img_w:60,img_h:60,img_x:5,img_y:5},
        card_display: [d => `${d.data['first name'] || ''} ${d.data['last name'] || ''}`,d => `${d.data['birthday'] || ''}`],
        mini_tree: true,
        link_break: false
    });

    view.setCard(Card);
    store.setOnUpdate(props => view.update(props || {}));
    store.update.tree({initial: true});
  </script>
</body>
</html>
