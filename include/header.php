<?php
session_start();
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script>
  function toggleDropdown(type) {
    const ids = {
      menu: "dropdownMenu",
      account: "dropdownAccount",
      cart: "dropdownCart"
    };

    // Close others
    for (const key in ids) {
      if (key !== type) {
        document.getElementById(ids[key]).classList.remove("show");
      }
    }

    // Toggle current
    document.getElementById(ids[type]).classList.toggle("show");
  }

  // Hide dropdown if clicked outside
  window.addEventListener("click", function(e) {
    if (!e.target.closest(".dropdown-wrapper")) {
      document.querySelectorAll('.custom-dropdown').forEach(dd => dd.classList.remove("show"));
    }
  });
</script>

<header>
  <nav class="navbar">
    <div class="left-section">
      <div class="logo">
        <img id="logo" src="/assets/logo/sakura.png" alt="Logo" width="40" height="40">
        <a href="/index.php">FLOREET</a>
      </div>
      <div class="divider"></div>
      <div class="location" title="Filter by Location">
        <img src="/assets/images/question_location.svg" alt="Location" height="24" width="24" onerror="this.onerror=null; this.src='../assets/images/question_location.svg';" >
      </div>
    </div>

    <form class="search-form" action="#" method="GET">
      <input type="text" placeholder="Search flowers..." name="q">
      <button type="submit">🔍</button>
    </form>

    <section class="menu-bar">
      <!-- Cart Dropdown -->
      <div class="dropdown-wrapper" style="position: relative;">
        <button onclick="toggleDropdown('cart')" class="btn primary">
          <i class="bi bi-basket-fill" style="font-size: 1.2rem;"></i>
        </button>
        <div class="custom-dropdown" id="dropdownCart">
          <a href="#">Offers</a>
          <a href="#">view Cart</a>
        </div>
      </div>

      <!-- Account Dropdown -->
      <div class="dropdown-wrapper" style="position: relative;">
        <div style="display: flex; flex-direction: column; align-items: center;">
          <button onclick="toggleDropdown('account')" class="btn primary">
            <i class="bi bi-person-fill" style="font-size: 1.2rem;"></i>
          </button>
        </div>

        <div class="custom-dropdown" id="dropdownAccount">
          <?php if (!isset($_SESSION['first_name'])): ?>
            <a href="/subpages/login.php">Login</a>
          <?php else: ?>
            <a href="profile.php">My Profile</a>
          <?php endif; ?>

          <a href="orders.php">Orders</a>

          <?php if (isset($_SESSION['first_name'])): ?>
            <a href="/backend/logout.php">Logout</a>
          <?php endif; ?>
        </div>
      </div>



      <!-- Menu Dropdown -->
      <div class="dropdown-wrapper" style="position: relative;">
        <button onclick="toggleDropdown('menu')" class="btn primary">
          <i class="bi bi-list" style="font-size: 1.2rem;"></i>
        </button>
        <div class="custom-dropdown" id="dropdownMenu">
          <a href="/index.php">Home</a>
          <a href="#">Shop by Color</a>
          <a href="#">Contact</a>
        </div>
      </div>
     
    </section>
     <div class="greeting-label">
        Hi, <?= htmlspecialchars($firstName) ?>
      </div>
  </nav>
</header>

<style>
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: center;
    padding: 1rem 2rem;
    background-color: #7A9E7E;
    color: white;
    backdrop-filter: blur(12px);
    border-radius: 20px;
    margin: 1rem 3rem 2rem 3rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 1000;
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .left-section {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .logo a {
    font-size: 1.5rem;
    font-weight: 700;
    text-decoration: none;
    color: white;
  }

  .divider {
    width: 1px;
    height: 30px;
    background-color: #D9CFC1;
  }

  .location img {
    vertical-align: middle;
  }

  .search-form {
    display: flex;
    align-items: center;
    background-color: #fff;
    border-radius: 30px;
    padding: 0.3rem 0.8rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    transition: box-shadow 0.3s ease;
  }

  .search-form input {
    border: none;
    outline: none;
    padding: 0.5rem 0.8rem;
    border-radius: 30px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
  }

  .search-form button {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #7A9E7E;
    padding: 0 0.4rem;
  }

  .dropdown-wrapper {
    position: relative;
    display: inline-block;
    margin-left: 0.5rem;
  }

  .btn.primary {
    background-color: transparent;
    color: white;
    padding: 0.4rem 0.6rem;
    border: 1px solid #ffffff44;
    border-radius: 12px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }

  .btn.primary:hover {
    background-color: #ffffff22;
  }

  .custom-dropdown {
    position: absolute;
    top: 155%;
    right: 0;
    background-color: #FAF6F0;
    color: #333;
    border-radius: 10px;
    padding: 0.5rem 1rem;
    width: max-content;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
    font-size: 0.95rem;
    z-index: 100;
    display: none;
    flex-direction: column;
    gap: 0.25rem;
    transition: all 0.2s ease-in-out;
  }

  .custom-dropdown.show {
    display: flex;
  }

  .custom-dropdown a {
    padding: 0.75rem 1rem;
    color: #333;
    text-decoration: none;
    transition: background-color 0.2s ease;
    border-radius: 6px;
  }

  .custom-dropdown a:hover {
    background-color: #F6CED8;
    color: #333;
  }

  .greeting-label {
    margin-top: 0.3rem;
    font-size: 0.8rem;
    color: white;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
  }
</style>