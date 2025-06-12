document.addEventListener("DOMContentLoaded", function () {
  // Load data dashboard
  loadDashboardStats();
  loadCharts();
});

function loadDashboardStats() {
  fetch("author_stats.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("total-authors").textContent = data.data.length;
        const totalArticles = data.data.reduce(
          (sum, author) => sum + author.total_articles,
          0
        );
        document.getElementById("total-articles").textContent = totalArticles;
      }
    });

  // Hitung artikel bulan ini
  const currentMonth = new Date().getMonth() + 1;
  fetch(`article_stats.php?month=${currentMonth}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("monthly-articles").textContent =
          data.data.length;
      }
    });
}

function loadCharts() {
  // Load author chart
  fetch("author_stats.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        renderAuthorChart(data.data);
      }
    });

  // Load article chart
  fetch("article_stats.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        renderArticleChart(data.data);
      }
    });
}
