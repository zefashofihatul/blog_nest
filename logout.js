// Fungsi logout yang bisa dipanggil dari mana saja
function logout() {
  // Hapus data autentikasi dari localStorage
  localStorage.removeItem("authToken");
  localStorage.removeItem("user");

  // Panggil API logout
  fetch("api/logout.php", {
    method: "POST",
    credentials: "include",
  })
    .then(() => {
      // Redirect ke halaman login
      window.location.href = "../login.html";
    })
    .catch((error) => {
      console.error("Logout error:", error);
      window.location.href = "../login.html";
    });
}
