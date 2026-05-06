<?php

session_start();
require_once "dbConfig.php";

if (isset($_SESSION['user_id'])) {
    header("Location: inventory.php");
    exit();
}

if (isset($_POST['signup'])) {

    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $username = $_POST['signup_username'];
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        $signup_error = "Username already exists";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fname, $lname, $username, $password]);

        $signup_success = "Account created successfully!";
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            header("Location: inventory.php");
            exit();

        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "User not found";
    }
}
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

<body class="font-body overflow-x-hidden bg-white">


<nav class="bg-white/80 backdrop-blur-md text-gray-800 p-4 flex items-center justify-between shadow-md">
  <img src="img/logo.png" class="h-10 w-auto">

  <div class="flex space-x-6 text-sm md:text-base">  
    <a href="#" class="text-pink-700 font-semibold">Home</a>
  </div>
</nav>

<section class="bg-pink-50 py-16 px-8">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center">

    <video class="w-full h-[300px] md:h-[400px] object-cover rounded-3xl shadow-lg" autoplay muted loop>
      <source src="img/Cake.mp4">
    </video>

    <div>
      <h1 class="text-4xl md:text-5xl font-heading font-bold text-pink-700 mb-4">
        Deliver You A Blissful Dessert in Every Bite
      </h1>

      <p class="text-gray-600 mb-6">
        Freshly baked cakes, pastries, and desserts made with love for every sweet craving.
      </p>

      <button onclick="openLoginModal()" 
        class="bg-pink-700 text-white px-6 py-2 rounded-full hover:bg-pink-400 transition">
        Login
      </button>
    </div>
  </div>
</section>

<section class="py-16 text-center">
  <h2 class="text-3xl md:text-4xl font-heading font-bold mb-10">
    Check Out Our <span class="text-pink-600">Best Sellers</span>
  </h2>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">

    <div class="group">
      <img src="img/cake.jpg" class="w-40 h-40 md:w-48 md:h-48 object-cover mx-auto rounded-2xl group-hover:scale-110 transition">
      <p class="mt-2 text-pink-600 opacity-0 group-hover:opacity-100">Strawberry Cake</p>
    </div>

    <div class="group">
      <img src="img/cake2.jpg" class="w-40 h-40 md:w-48 md:h-48 object-cover mx-auto rounded-2xl group-hover:scale-110 transition">
      <p class="mt-2 text-pink-600 opacity-0 group-hover:opacity-100">Vanilla Cake</p>
    </div>

    <div class="group">
      <img src="img/cake3.jpg" class="w-40 h-40 md:w-48 md:h-48 object-cover mx-auto rounded-2xl group-hover:scale-110 transition">
      <p class="mt-2 text-pink-600 opacity-0 group-hover:opacity-100">Buttercream Cake</p>
    </div>

    <div class="group">
      <img src="img/cake4.jpg" class="w-40 h-40 md:w-48 md:h-48 object-cover mx-auto rounded-2xl group-hover:scale-110 transition">
      <p class="mt-2 text-pink-600 opacity-0 group-hover:opacity-100">Butterfly Cake</p>
    </div>
  </div>
</section>

<div class="relative -mb-1 rotate-180">
  <svg viewBox="0 0 1440 120" class="w-full h-[100px]" preserveAspectRatio="none">
    <path fill="#fdf2f8"
      d="M0,64L60,85.3C120,107,240,149,360,138.7C480,128,600,64,720,53.3C840,43,960,85,1080,106.7C1200,128,1320,128,1380,128L1440,128L1440,0L0,0Z">
    </path>
  </svg>
</div>

<footer class="bg-pink-50 text-center py-12 px-6">

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

<div id="loginModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">

  <div class="bg-pink-700 rounded-3xl p-6 w-full max-w-md relative">

    <button onclick="closeLoginModal()" class="absolute top-2 right-4 text-white text-xl">&times;</button>

    <h2 class="text-white text-center mb-3 font-bold">Login</h2>

    <form method="POST" class="bg-[#fdf2f8] rounded-2xl p-6">

      <?php if (isset($error)): ?>
        <p class="text-red-500 text-center mb-3"><?= $error ?></p>
      <?php endif; ?>

      <input type="text" name="username" placeholder="Username"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <input type="password" name="password" placeholder="Password"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <button name="login" type="submit"
        class="w-full bg-pink-600 text-white py-2 rounded-lg hover:bg-pink-800">
        Login
      </button>

      <p class="text-center text-sm mt-4">
        Don’t have an account?
        <button type="button" onclick="switchToSignup()" class="text-pink-600 font-semibold">
          Sign up
        </button>
      </p>

    </form>
  </div>
</div>


<div id="signupModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 z-50">

  <div class="bg-pink-700 rounded-3xl p-6 w-full max-w-md relative">

    <button onclick="closeSignupModal()" class="absolute top-2 right-4 text-white text-xl">&times;</button>

    <h2 class="text-white text-center mb-3 font-bold">Create Account</h2>

    <form method="POST" class="bg-[#fdf2f8] rounded-2xl p-6">

      <?php if (isset($signup_error)): ?>
        <p class="text-red-500 text-center mb-3"><?= $signup_error ?></p>
      <?php endif; ?>

      <?php if (isset($signup_success)): ?>
        <p class="text-green-600 text-center mb-3"><?= $signup_success ?></p>
      <?php endif; ?>

      <input type="text" name="first_name" placeholder="First Name"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <input type="text" name="last_name" placeholder="Last Name"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <input type="text" name="signup_username" placeholder="Username"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <input type="password" name="signup_password" placeholder="Password"
        class="w-full mb-4 px-4 py-2 border rounded-lg" required>

      <button name="signup" type="submit"
        class="w-full bg-pink-600 text-white py-2 rounded-lg hover:bg-pink-800">
        Sign Up
      </button>

      <p class="text-center text-sm mt-4">
        Already have an account?
        <button type="button" onclick="switchToLogin()" class="text-pink-600 font-semibold">
          Login
        </button>
      </p>

    </form>
  </div>
</div>

<script>
function openLoginModal() {
  document.getElementById("loginModal").classList.remove("hidden");
  document.getElementById("loginModal").classList.add("flex");
}

function closeLoginModal() {
  document.getElementById("loginModal").classList.add("hidden");
}

function openSignupModal() {
  document.getElementById("signupModal").classList.remove("hidden");
  document.getElementById("signupModal").classList.add("flex");
}

function closeSignupModal() {
  document.getElementById("signupModal").classList.add("hidden");
}

function switchToSignup() {
  closeLoginModal();
  openSignupModal();
}

function switchToLogin() {
  closeSignupModal();
  openLoginModal();
}
</script>

<!-- AUTO OPEN MODALS -->
<?php if (isset($error)): ?>
<script>openLoginModal();</script>
<?php endif; ?>

<?php if (isset($signup_error) || isset($signup_success)): ?>
<script>openSignupModal();</script>
<?php endif; ?>

</body>
</html>