// Grafik Penulis
function renderAuthorChart(data) {
  const ctx = document.getElementById("authorChart").getContext("2d");

  // Siapkan data untuk chart
  const authors = data.map((item) => item.author_name);
  const articleCounts = data.map((item) => item.total_articles);

  // Warna berdasarkan kategori (contoh)
  const backgroundColors = data.map((item) => {
    const ratio = item.total_articles / Math.max(...articleCounts);
    return `rgba(54, 162, 235, ${0.5 + ratio * 0.5})`;
  });

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: authors,
      datasets: [
        {
          label: "Jumlah Artikel",
          data: articleCounts,
          backgroundColor: backgroundColors,
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Jumlah Artikel",
          },
        },
        x: {
          title: {
            display: true,
            text: "Nama Penulis",
          },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            afterLabel: function (context) {
              const index = context.dataIndex;
              return `Topik Teratas: ${data[index].top_category}`;
            },
          },
        },
      },
    },
  });
}

// Grafik Artikel
function renderArticleChart(data) {
  const ctx = document.getElementById("articleChart").getContext("2d");

  // Kelompokkan per bulan
  const monthlyData = {};
  data.forEach((article) => {
    const month = new Date(article.published_at).toLocaleString("default", {
      month: "short",
    });
    if (!monthlyData[month]) {
      monthlyData[month] = { count: 0, categories: {} };
    }
    monthlyData[month].count++;

    // Hitung kategori
    article.categories.split(",").forEach((cat) => {
      cat = cat.trim();
      if (cat) {
        monthlyData[month].categories[cat] =
          (monthlyData[month].categories[cat] || 0) + 1;
      }
    });
  });

  const months = Object.keys(monthlyData);
  const counts = months.map((m) => monthlyData[m].count);

  // Warna berdasarkan kategori dominan
  const backgroundColors = months.map((month) => {
    const topCategory =
      Object.entries(monthlyData[month].categories).sort(
        (a, b) => b[1] - a[1]
      )[0]?.[0] || "Lainnya";

    const colorMap = {
      Technology: "rgba(75, 192, 192, 0.7)",
      Business: "rgba(255, 159, 64, 0.7)",
      Health: "rgba(255, 99, 132, 0.7)",
      default: "rgba(153, 102, 255, 0.7)",
    };

    return colorMap[topCategory] || colorMap.default;
  });

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: months,
      datasets: [
        {
          label: "Jumlah Artikel",
          data: counts,
          backgroundColor: backgroundColors,
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Jumlah Artikel",
          },
        },
        x: {
          title: {
            display: true,
            text: "Bulan",
          },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            afterLabel: function (context) {
              const month = context.label;
              const categories = monthlyData[month].categories;
              const topCategories = Object.entries(categories)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 2)
                .map(([cat, count]) => `${cat} (${count})`)
                .join(", ");
              return `Top Kategori: ${topCategories}`;
            },
          },
        },
      },
    },
  });
}
