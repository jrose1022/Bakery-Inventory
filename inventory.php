<?php
session_start();
require_once "dbConfig.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['action'])) {

    if ($_POST['action'] == 'add_pastry') {
        $stmt = $pdo->prepare("INSERT INTO pastries (name, quantity, added_by) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['qty'],
            $_SESSION['user_id']
        ]);
        echo "success";
        exit();
    }

    if ($_POST['action'] == 'add_ingredient') {
        $stmt = $pdo->prepare("INSERT INTO ingredients (name, quantity, added_by) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['qty'],
            $_SESSION['user_id']
        ]);
        echo "success";
        exit();
    }

    if ($_POST['action'] == 'delete_pastry') {
    $stmt = $pdo->prepare("DELETE FROM pastries WHERE pastry_id = ?");
    $stmt->execute([$_POST['id']]);
    echo "deleted";
    exit();
}

if ($_POST['action'] == 'delete_ingredient') {
    $stmt = $pdo->prepare("DELETE FROM ingredients WHERE ingredient_id = ?");
    $stmt->execute([$_POST['id']]);
    echo "deleted";
    exit();
}

if ($_POST['action'] == 'edit_pastry') {
    $stmt = $pdo->prepare("UPDATE pastries SET name=?, quantity=? WHERE pastry_id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['qty'],
        $_POST['id']
    ]);
    echo "updated";
    exit();
}

if ($_POST['action'] == 'edit_ingredient') {
    $stmt = $pdo->prepare("UPDATE ingredients SET name=?, quantity=? WHERE ingredient_id=?");
    $stmt->execute([
        $_POST['name'],
        $_POST['qty'],
        $_POST['id']
    ]);
    echo "updated";
    exit();
}
}

$pastries = $pdo->query("
    SELECT pastries.*, users.username 
    FROM pastries 
    JOIN users ON pastries.added_by = users.user_id
    ORDER BY pastry_id DESC
")->fetchAll();

$ingredients = $pdo->query("
    SELECT ingredients.*, users.username 
    FROM ingredients 
    JOIN users ON ingredients.added_by = users.user_id
    ORDER BY ingredient_id DESC
")->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sweet Crumbs Bakery</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          heading: ['Playfair Display', 'serif'],
          body: ['Poppins', 'sans-serif']
        }
      }
    }
  }
</script>

</head>

<body class="font-body bg-pink-50 text-gray-800">

<nav class="bg-white/80 backdrop-blur-md p-4 flex justify-between shadow-md">
  <img src="img/logo.png" class="h-10">
  <div class="space-x-6">

    <a href="/inventory.php" class="text-pink-700 font-semibold">Inventory</a>
    <a href="logout.php" class="text-pink-500">Log Out</a>
  </div>
</nav>

<!-- HERO -->
<section class="max-w-7xl mx-auto px-6 py-10">
  <div class="rounded-[2rem] bg-white shadow-lg border border-pink-100 p-8 mb-10 grid lg:grid-cols-3 gap-6">

        <div>
            <p class="text-sm uppercase tracking-widest text-pink-400">Inventory system</p>
            <h2 class="mt-3 text-3xl font-heading font-bold text-pink-700">Sweet Crumbs Inventory</h2>
            <p class="mt-4 text-gray-600">
                Track your bakery items, manage stock, and monitor your desserts easily.
            </p>
        </div>

        <div class="bg-pink-50 rounded-3xl p-6 border border-pink-100">
            <p class="text-sm text-pink-400 uppercase">Current Pastry Inventory</p>
            <p id="totalItems" class="text-2xl font-bold text-pink-700 mt-2">2 items</p>

            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-white rounded-2xl p-4 border border-pink-100">
                <p class="text-sm text-gray-500">In stock</p>
                <p id="inStock" class="text-xl font-bold text-pink-700">1</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-pink-100">
                <p class="text-sm text-gray-500">Out of stock</p>
                <p id="outStock" class="text-xl font-bold text-red-400">1</p>
                </div>
            </div>
        </div>
        <div class="bg-pink-50 rounded-3xl p-6 border border-pink-100">
            <p class="text-sm text-pink-400 uppercase">Current Ingredients Inventory</p>
            <p id="totalItems" class="text-2xl font-bold text-pink-700 mt-2">2 items</p>

            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="bg-white rounded-2xl p-4 border border-pink-100">
                <p class="text-sm text-gray-500">In stock</p>
                <p id="inStock" class="text-xl font-bold text-pink-700">1</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-pink-100">
                <p class="text-sm text-gray-500">Out of stock</p>
                <p id="outStock" class="text-xl font-bold text-red-400">1</p>
                </div>
            </div>
        </div>

    </div>


  <div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-3xl shadow-lg">
      <h3 class="text-center font-bold text-pink-700 mb-4">Pastry Inventory</h3>

      <div class="flex gap-3 mb-4">
        <input id="pastryName" placeholder="Pastry name"
          class="flex-1 border rounded-xl px-4 py-2">

        <input id="pastryQty" type="number" value="1"
          class="w-20 border rounded-xl px-2">

        <button onclick="addPastry()" class="bg-pink-600 text-white px-4 rounded-xl">
          Add
        </button>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-pink-100 text-pink-700">
          <tr>
            <th class="p-2 text-left">Item</th>
            <th class="p-2 text-left">Qty</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Added By</th>
            <th class="p-2 text-left">Last Updated</th>
            <th class="p-2 text-left">Action</th>
          </tr>
        </thead>
                <tbody id="pastries">
        <?php foreach ($pastries as $row): ?>
        <tr>
          <td class="p-2"><?= $row['name'] ?></td>
          <td class="p-2"><?= $row['quantity'] ?></td>
          <td class="p-2 <?= $row['quantity'] > 0 ? 'text-green-600' : 'text-red-400' ?>">
            <?= $row['quantity'] > 0 ? 'In stock' : 'Out of stock' ?>
          </td>
          <td class="p-2"><?= $row['username'] ?></td>
          <td class="p-2"><?= $row['last_updated'] ?></td>

          <td class="p-2 space-x-2">
            <button onclick="openModal('pastry', <?= $row['pastry_id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', <?= $row['quantity'] ?>)"
              class="bg-yellow-400 text-white px-3 py-1 rounded-lg hover:bg-yellow-500">
              Edit
            </button>

            <button onclick="deletePastry(<?= $row['pastry_id'] ?>)"
              class="bg-red-400 text-white px-3 py-1 rounded-lg hover:bg-red-500">
              Delete
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-lg">
      <h3 class="text-center font-bold text-pink-700 mb-4">Ingredients</h3>

      <div class="flex gap-3 mb-4">
        <input id="ingredientName" placeholder="Ingredient name"
          class="flex-1 border rounded-xl px-4 py-2">

        <input id="ingredientQty" type="number" value="1"
          class="w-20 border rounded-xl px-2">

        <button onclick="addIngredient()" class="bg-pink-600 text-white px-4 rounded-xl">
          Add
        </button>
      </div>

      <table class="w-full text-sm">
        <thead class="bg-pink-100 text-pink-700">
          <tr>
            <th class="p-2 text-left">Item</th>
            <th class="p-2 text-left">Qty</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-left">Added By</th>
            <th class="p-2 text-left">Last Updated</th>
            <th class="p-2 text-left">Action</th>
          </tr>
        </thead>
        <tbody id="ingredients">
        <?php foreach ($ingredients as $row): ?>
       <tr>
  <td class="p-2"><?= $row['name'] ?></td>
  <td class="p-2"><?= $row['quantity'] ?></td>
  <td class="p-2 <?= $row['quantity'] > 0 ? 'text-green-600' : 'text-red-400' ?>">
    <?= $row['quantity'] > 0 ? 'In stock' : 'Out of stock' ?>
  </td>
  <td class="p-2"><?= $row['username'] ?></td>
  <td class="p-2"><?= $row['last_updated'] ?></td>

  <td class="p-2 flex space-x-2">
  <button onclick="openModal('ingredient', <?= $row['ingredient_id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', <?= $row['quantity'] ?>)"
    class="bg-yellow-400 text-white px-3 py-1 rounded-lg hover:bg-yellow-500">
    Edit
  </button>

  <button onclick="deleteIngredient(<?= $row['ingredient_id'] ?>)"
    class="bg-red-400 text-white px-3 py-1 rounded-lg hover:bg-red-500">
    Delete
  </button>
</td>
</tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</section>

<div class="relative -mb-1 rotate-180">
  <svg viewBox="0 0 1440 120" class="w-full h-[100px]" preserveAspectRatio="none">
    <path fill="#fff"
      d="M0,64L60,85.3C120,107,240,149,360,138.7C480,128,600,64,720,53.3C840,43,960,85,1080,106.7C1200,128,1320,128,1380,128L1440,128L1440,0L0,0Z">
    </path>
  </svg>
</div>

<footer class="bg-white text-center py-12 px-6">

  <h3 class="text-2xl md:text-3xl font-bold text-pink-700">
    Sweet Crumbs Bakery
  </h3>

  <p class="text-gray-600 mt-2 text-sm md:text-base">
    Baked with love, served with sweetness
  </p>

  <div class="mt-6 space-y-2 text-gray-700 text-sm">
    <p class="flex justify-center items-center space-x-2">
      <i class="fa-solid fa-phone text-pink-500"></i>
      <span>0912-345-6789</span>
    </p>

    <p class="flex justify-center items-center space-x-2">
      <i class="fa-solid fa-envelope text-pink-500"></i>
      <span>sweetcrumbs@email.com</span>
    </p>

    <p class="flex justify-center items-center space-x-2">
      <i class="fa-solid fa-location-dot text-pink-500"></i>
      <span>Imus, Cavite</span>
    </p>
  </div>

  <div class="flex justify-center space-x-5 mt-6 text-xl">
    <a href="#" class="text-pink-500 hover:text-pink-700 hover:scale-110 transition">
      <i class="fa-brands fa-facebook"></i>
    </a>

    <a href="#" class="text-pink-500 hover:text-pink-700 hover:scale-110 transition">
      <i class="fa-brands fa-instagram"></i>
    </a>

    <a href="#" class="text-pink-500 hover:text-pink-700 hover:scale-110 transition">
      <i class="fa-brands fa-tiktok"></i>
    </a>
  </div>

  <p class="text-gray-400 text-xs mt-8">
    © 2026 Sweet Crumbs Bakery. All rights reserved.
  </p>

</footer>

<div id="editModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
  <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-xl">

    <h3 class="text-xl font-bold text-pink-700 mb-4">Edit Item</h3>

    <input type="hidden" id="editId">
    <input type="hidden" id="editType">

    <div class="space-y-3">
      <input id="editName" class="w-full border rounded-xl px-4 py-2" placeholder="Name">
      <input id="editQty" type="number" class="w-full border rounded-xl px-4 py-2" placeholder="Quantity">
    </div>

    <div class="flex justify-end gap-3 mt-6">
      <button onclick="closeModal()" class="px-4 py-2 rounded-xl bg-gray-200">
        Cancel
      </button>

      <button onclick="saveEdit()" class="px-4 py-2 rounded-xl bg-pink-600 text-white">
        Save
      </button>
    </div>

  </div>
</div>

<script>
function getDate() {
  return new Date().toLocaleString();
}

function addPastry() {
  const name = document.getElementById("pastryName").value;
  const qty = document.getElementById("pastryQty").value;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=add_pastry&name=${name}&qty=${qty}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function addIngredient() {
  const name = document.getElementById("ingredientName").value;
  const qty = document.getElementById("ingredientQty").value;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=add_ingredient&name=${name}&qty=${qty}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function deletePastry(id) {
  if (!confirm("Delete this pastry?")) return;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=delete_pastry&id=${id}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function deleteIngredient(id) {
  if (!confirm("Delete this ingredient?")) return;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=delete_ingredient&id=${id}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function editPastry(id, name, qty) {
  const newName = prompt("Edit pastry name:", name);
  if (newName === null) return;

  const newQty = prompt("Edit quantity:", qty);
  if (newQty === null) return;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=edit_pastry&id=${id}&name=${encodeURIComponent(newName)}&qty=${newQty}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function editIngredient(id, name, qty) {
  const newName = prompt("Edit ingredient name:", name);
  if (newName === null) return;

  const newQty = prompt("Edit quantity:", qty);
  if (newQty === null) return;

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=edit_ingredient&id=${id}&name=${encodeURIComponent(newName)}&qty=${newQty}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

function openModal(type, id, name, qty) {
  document.getElementById("editModal").classList.remove("hidden");
  document.getElementById("editModal").classList.add("flex");

  document.getElementById("editId").value = id;
  document.getElementById("editType").value = type;
  document.getElementById("editName").value = name;
  document.getElementById("editQty").value = qty;
}

function closeModal() {
  document.getElementById("editModal").classList.add("hidden");
  document.getElementById("editModal").classList.remove("flex");
}

function saveEdit() {
  const id = document.getElementById("editId").value;
  const type = document.getElementById("editType").value;
  const name = document.getElementById("editName").value;
  const qty = document.getElementById("editQty").value;

  let action = type === "pastry" ? "edit_pastry" : "edit_ingredient";

  fetch("", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `action=${action}&id=${id}&name=${encodeURIComponent(name)}&qty=${qty}`
  })
  .then(res => res.text())
  .then(() => location.reload());
}

</script>

</body>
</html>