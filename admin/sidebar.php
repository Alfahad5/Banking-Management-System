<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/sign-in.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .head {
            width: 100%;
            height: 10%;
        }

        .side {
            width: 15%;
            height: 100%;
            float: left;
        }

        .cont {
            width: 85%;
            height: 100%;
            float: right;
        }

        @media (max-width: 768px) {
            .side {
                width: 100%;
                height: auto;
            }

            .cont {
                width: 100%;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <div class="panel">
        <!-- Header -->
        <div class="head">
            <header class="text-gray-600 body-font">
                <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                    <a class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2"
                            class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                        <span class="ml-3 text-xl">Admin Panel</span>
                    </a>
                    <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                        <a class="mr-5 hover:text-gray-900">Home</a>
                        <a class="mr-5 hover:text-gray-900">About</a>
                        <a class="mr-5 hover:text-gray-900">Contact</a>
                        <button
                            class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0"
                            onclick="logOut()">
                            Log Out
                            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </nav>
                </div>
            </header>
        </div>

        <!-- Sidebar -->
        <div class="side bg-white shadow-lg">
            <div class="flex h-screen flex-col justify-between border-e">
                <div class="px-4 py-6">
                    <ul class="mt-6 space-y-1">
                        <li>
                            <h2 class="block rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium">Banking Management
                            </h2>
                        </li>

                        <li>
                            <a href="admin_dashboard.php"
                                class="flex items-center space-x-2 block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 5h18M3 12h18M3 19h18" />
                                </svg>
                                <span>Admin Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="user_manager.php"
                                class="flex items-center space-x-2 block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                    <path
                                        d="M16 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-8 0c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" />
                                </svg>
                                <span>Manage Users</span>
                            </a>
                        </li>

                        <li>
                            <a href="account_manager.php"
                                class="flex items-center space-x-2 block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 110 20 10 10 0 010-20z" />
                                </svg>
                                <span>Manage Accounts</span>
                            </a>
                        </li>

                        <li>
                            <a href="transaction_history.php"
                                class="flex items-center space-x-2 block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 3l-7 7-7-7" />
                                </svg>
                                <span>Transaction History</span>
                            </a>
                        </li>

                        <li>
                            <button onclick="logOut()"
                                class="flex items-center space-x-2 block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 2l-7 7 7 7" />
                                </svg>
                                <span>Log Out</span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="cont">
            <!-- Your main content goes here -->
        </div>
    </div>

    <script>
        function logOut() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "../logout.php?logout=true";
            }
        }
    </script>

</body>

</html>