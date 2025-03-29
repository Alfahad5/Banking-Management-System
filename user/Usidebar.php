<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .head {
            width: 100%;
        }

        .side {
            width: 15%;
            height: 100%;
            float: left;
            background: linear-gradient(to bottom, #ffffff, #f3f4f6);
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .cont {
            width: 85%;
            padding: 20px;
            float: right;
        }

        @media (max-width: 768px) {
            .side {
                width: 100%;
                height: auto;
            }

            .cont {
                width: 100%;
                float: none;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="head bg-gray-100 shadow">
        <header class="text-gray-600 body-font">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                <a class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2"
                        class="w-10 h-10 text-white p-2 bg-blue-500 rounded-full" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    <span class="ml-3 text-xl">Banking Management</span>
                </a>
                <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                    <a class="mr-5 hover:text-gray-900">Home</a>
                    <a class="mr-5 hover:text-gray-900">About</a>
                    <a class="mr-5 hover:text-gray-900">Services</a>
                    <a class="mr-5 hover:text-gray-900">Contact</a>
                </nav>
                <button onclick="window.location.href='../logout.php?logout=true';"
                    class="inline-flex items-center bg-blue-500 text-white border-0 py-1 px-3 focus:outline-none hover:bg-blue-600 rounded text-base mt-4 md:mt-0">
                    Log Out
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </header>
    </div>

    <!-- Sidebar + Content -->
    <div class="panel">
        <!-- Sidebar -->
        <div class="side bg-gradient-to-b from-gray-50 to-gray-100 shadow-lg">
            <div class="flex flex-col justify-between h-full">
                <div class="px-4 py-6">
                    <h2 class="text-lg font-bold text-gray-700 mb-4">Navigation</h2>
                    <ul class="space-y-2">
                        <li>
                            <a href="user_dashboard.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-tachometer-alt mr-2 text-blue-500"></i>User Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="create_account.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-user-plus mr-2 text-blue-500"></i>Create Account
                            </a>
                        </li>
                        <li>
                            <a href="forget_password.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-key mr-2 text-blue-500"></i>Forget Password
                            </a>
                        </li>
                        <li>
                            <a href="deposit.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-credit-card mr-2 text-blue-500"></i>Deposit
                            </a>
                        </li>
                        <li>
                            <a href="withdraw.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-arrow-down mr-2 text-blue-500"></i>Withdraw
                            </a>
                        </li>
                        <li>
                            <a href="transaction_history.php"
                                class="block px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-blue-100 hover:text-blue-900">
                                <i class="fas fa-history mr-2 text-blue-500"></i>Transaction History
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="px-4 pb-6">
                    <a href="../logout.php?logout=true"
                        class="block w-full px-4 py-2 text-center text-white bg-red-500 hover:bg-red-600 rounded-lg">
                        Log Out
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="cont bg-gray-50 p-6">
            <h1 class="text-2xl font-bold text-gray-800">Welcome to the Banking Management System</h1>
            <p class="mt-4 text-gray-600">
                This is your dashboard where you can manage your account, view transactions, and more.
            </p>
        </div>
    </div>

</body>

</html>