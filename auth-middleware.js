async function checkAuth(allowedRoles = []) {
  const authToken = localStorage.getItem("authToken");
  const user = JSON.parse(localStorage.getItem("user"));

  if (!authToken || !user) {
    redirectToLogin();
    return false;
  }

  try {
    const response = await fetch("./validate.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        user_id: user.id,
        token: authToken,
      }),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (!data.valid) {
      console.error("Validation failed:", data.error || "Invalid session");
      redirectToLogin();
      return false;
    }

    // Validasi role jika ada parameter allowedRoles
    if (allowedRoles.length > 0 && !allowedRoles.includes(data.role)) {
      console.error("Access denied: Invalid role");
      redirectToLogin();
      return false;
    }

    // Update user data in localStorage with role
    if (data.role) {
      const updatedUser = { ...user, role: data.role };
      localStorage.setItem("user", JSON.stringify(updatedUser));
    }

    return true;
  } catch (error) {
    console.error("Auth validation error:", error);
    redirectToLogin();
    return false;
  }
}

// Fungsi bantu untuk redirect
function redirectToLogin() {
  // Hapus semua data auth dari localStorage
  localStorage.removeItem("authToken");
  localStorage.removeItem("user");

  window.location.href = `/hybrid-editor/login.html`;
}
