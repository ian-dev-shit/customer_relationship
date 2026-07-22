
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CargoNet — Customer Dashboard</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            freight: { 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 900: '#0c4a6e', 950: '#082f49' },
            cargo: { amber: '#f59e0b', slate: '#1e293b', dark: '#0f172a', panel: '#111827' }
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-950 text-slate-100 antialiased">

<div id="dashboard-view" data-role="customer" class="min-h-screen bg-slate-950">