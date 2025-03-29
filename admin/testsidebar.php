<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>

    <style>
        #sidebar {
            width: 15%;
            height: 100%;
            float: left;
        }

        #main-content {
            width: 85%;
            height: 100%;
            float: right;
        }
    </style>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <script>
        // Function to toggle the sidebar and adjust main content width
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            // Toggle the sidebar visibility
            sidebar.classList.toggle('hidden');

            // Toggle the margin on the main content to shrink or expand
            if (sidebar.classList.contains('hidden')) {
                mainContent.classList.remove('ml-64');  // Expand content when sidebar is hidden
            } else {
                mainContent.classList.add('ml-64');     // Shrink content when sidebar is visible
            }
        }
    </script> -->
</head>

<body class="flex">

    <!-- Sidebar -->
    <div id="sidebar" class="bg-gray-800 text-white w-64 h-screen p-5 fixed left-0 top-0 hidden md:block">
        <h2 class="text-2xl font-bold text-center text-white mb-8">Admin Dashboard</h2>
        <ul>
            <li class="mb-4"><a href="#" class="text-white hover:bg-gray-700 p-2 rounded">Dashboard</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:bg-gray-700 p-2 rounded">Manage Users</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:bg-gray-700 p-2 rounded">Transactions</a></li>
            <li class="mb-4"><a href="#" class="text-white hover:bg-gray-700 p-2 rounded">Settings</a></li>
            <li><a href="#" class="text-white hover:bg-gray-700 p-2 rounded">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="flex-1 p-5 transition-all ml-64">
        <!-- Hamburger Icon Button for Small Screens -->
        <!-- <button class=" text-white bg-blue-500 p-2 rounded" onclick="toggleSidebar()">☰</button> -->

        <!-- Main Content Area -->
        <h1 class="text-3xl font-semibold">Welcome to the Dashboard</h1>
        <p class="mt-4">Here is where the content goes.</p>
    </div>

</body>

</html>