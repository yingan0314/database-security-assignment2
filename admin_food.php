<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: menu.php");
    exit();
}

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {

    sqlsrv_query($conn,
        "DELETE FROM menu WHERE food_id = ?",
        [$_GET['delete']]
    );

    header("Location: admin_food.php");
    exit();
}

/* =========================
   UPDATE FOOD
========================= */
if (isset($_POST['update'])) {

    $id = $_POST['food_id'];
    $name = $_POST['food_name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $category = $_POST['category'];

    /* check if new image uploaded */
    if (!empty($_FILES['image']['name'])) {

        $imgName = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        $path = "images/" . time() . "_" . $imgName;
        move_uploaded_file($tmp, $path);

    } else {
        $path = $_POST['old_image'];
    }

    sqlsrv_query($conn,
        "UPDATE menu SET food_name=?, price=?, description=?, image=?, category=? WHERE food_id=?",
        [$name, $price, $desc, $path, $category, $id]
    );

    header("Location: admin_food.php");
    exit();
}

/* =========================
   ADD FOOD
========================= */
if (isset($_POST['add'])) {

    $name = $_POST['food_name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $category = $_POST['category'];

    $imgName = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $path = "images/" . time() . "_" . $imgName;
    move_uploaded_file($tmp, $path);

    sqlsrv_query($conn,
        "INSERT INTO menu (food_name, price, description, image, category)
         VALUES (?, ?, ?, ?, ?)",
        [$name, $price, $desc, $path, $category]
    );

    header("Location: admin_food.php");
    exit();
}

/* =========================
   EDIT DATA (LOAD)
========================= */
$editItem = null;

if (isset($_GET['edit'])) {

    $stmt = sqlsrv_query($conn,
        "SELECT * FROM menu WHERE food_id = ?",
        [$_GET['edit']]
    );

    $editItem = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
}

/* =========================
   FILTER
========================= */
$category = $_GET['category'] ?? 'all';

if ($category == 'all') {
    $stmt = sqlsrv_query($conn, "SELECT * FROM menu ORDER BY food_id DESC");
} else {
    $stmt = sqlsrv_query($conn,
        "SELECT * FROM menu WHERE category=? ORDER BY food_id DESC",
        [$category]
    );
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Food</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background: linear-gradient(135deg, #1f1c2c, #928dab);
    color:white;
}

.topbar{
    background: rgba(0,0,0,0.25);
    padding:18px;
    display:flex;
    justify-content:space-between;
}

.container{
    width:95%;
    max-width:1100px;
    margin:30px auto;
}

.form-box{
    background: rgba(255,255,255,0.12);
    padding:20px;
    border-radius:15px;
    margin-bottom:20px;
}

input, select{
    width:100%;
    padding:10px;
    margin:6px 0;
    border-radius:8px;
    border:none;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#2ed573;
    color:white;
    cursor:pointer;
}

/* GRID */
.grid{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
}

.card{
    width:240px;
    background: rgba(255,255,255,0.12);
    border-radius:15px;
    overflow:hidden;
}

.card img{
    width:100%;
    height:130px;
    object-fit:cover;
}

.info{ padding:12px; }

.btn{
    display:inline-block;
    margin-top:6px;
    padding:5px 8px;
    border-radius:6px;
    text-decoration:none;
    color:white;
}

.edit{ background:#ffa502; }
.delete{ background:#ff4757; }
</style>
</head>

<body>

<div class="topbar">
    <div>🍔 Food Admin</div>
</div>

<div class="container">

<!-- ADD / EDIT FORM -->
<div class="form-box">

<?php if ($editItem) { ?>

<h3>Update Food</h3>

<form method="POST" enctype="multipart/form-data">

    <input type="hidden" name="food_id" value="<?= $editItem['food_id'] ?>">
    <input type="hidden" name="old_image" value="<?= $editItem['image'] ?>">

    <input name="food_name" value="<?= $editItem['food_name'] ?>" required>
    <input name="price" value="<?= $editItem['price'] ?>" required>
    <input name="description" value="<?= $editItem['description'] ?>" required>

    <select name="category">
        <option value="rice" <?= $editItem['category']=='rice'?'selected':'' ?>>Rice</option>
        <option value="noodle" <?= $editItem['category']=='noodle'?'selected':'' ?>>Noodle</option>
        <option value="western" <?= $editItem['category']=='western'?'selected':'' ?>>Western</option>
        <option value="drink" <?= $editItem['category']=='drink'?'selected':'' ?>>Drink</option>
    </select>

    <input type="file" name="image">

    <button name="update">Update</button>
</form>

<?php } else { ?>

<h3>Add Food</h3>

<form method="POST" enctype="multipart/form-data">

    <input name="food_name" placeholder="Food Name" required>
    <input name="price" placeholder="Price" required>
    <input name="description" placeholder="Description" required>

    <select name="category">
        <option value="rice">Rice</option>
        <option value="noodle">Noodle</option>
        <option value="western">Western</option>
        <option value="drink">Drink</option>
    </select>

    <input type="file" name="image" required>

    <button name="add">Add</button>
</form>

<?php } ?>

</div>

<!-- LIST -->
<div class="grid">

<?php while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) { ?>

<div class="card">

    <img src="<?= $row['image'] ?>">

    <div class="info">
        <b><?= $row['food_name'] ?></b><br>
        RM <?= $row['price'] ?><br>
        <?= $row['category'] ?><br>

        <a class="btn edit" href="admin_food.php?edit=<?= $row['food_id'] ?>">Edit</a>
        <a class="btn delete" href="admin_food.php?delete=<?= $row['food_id'] ?>">Delete</a>
    </div>

</div>

<?php } ?>

</div>

</div>

</body>
</html>