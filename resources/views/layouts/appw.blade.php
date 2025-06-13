<!DOCTYPE html>

<html lang="en">



<head>

    <meta charset="utf-8">

    <meta content="width=device-width, initial-scale=1.0" name="viewport">



    <title>Bi.HAWA</title>

    <meta content="" name="description">

    <meta content="" name="keywords">



    <link href="https://fonts.gstatic.com" rel="preconnect">

    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="https://code.jqueryui.com/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">



</head>



<body>



    <header id="header" class="header fixed-top d-flex align-items-center">



        <div class="d-flex align-items-center justify-content-between">

            <a href="{{ url('index.html') }}" class="logo d-flex align-items-center">

                <img src="#" alt="">

                <span class="d-none d-lg-block">Bi.HAWA</span>

            </a>

            <i class="bi bi-list toggle-sidebar-btn"></i>

        </div>

        <nav class="header-nav ms-auto">

            <ul class="d-flex align-items-center">



                <li class="nav-item dropdown pe-3">



                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">

                        <i class="bi bi-person-circle"></i>

                        <span class="d-none d-md-block dropdown-toggle ps-2">{{ Auth::user()->email}}</span>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">



                        <li>

                            <hr class="dropdown-divider">

                        </li>



                        <li>

                            <hr class="dropdown-divider">

                        </li>



                        <li>

                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('change-password.update') }}">

                                <i class="bi bi-gear"></i>

                                <span>change password</span>

                            </a>

                        </li>



                        <li>

                            <form action="{{ route('logout') }}" method="POST">

                                @csrf

                                <button type="submit" class="dropdown-item d-flex align-items-center">

                                    <i class="bi bi-box-arrow-right"></i>

                                    <span>Sign Out</span>

                                </button>

                            </form>

                        </li>

                    </ul>

                </li>

            </ul>

        </nav>

    </header>

    <aside id="sidebar" class="sidebar">



        <ul class="sidebar-nav" id="sidebar-nav">



            <li class="nav-item">

                <a class="nav-link" href="{{ route('dashboard.index') }}">

                    <i class="bi bi-grid"></i>

                    <span>Dashboard</span>

                </a>

            </li>



            <li class="nav-item">

                @if(Auth::check() && Auth::user()->role_id == 1)

                <a class="nav-link collapsed" data-bs-target="#report-nav" data-bs-toggle="collapse" href="#">

                    <i class="fas"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                @endif

                <ul id="report-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('reports.profit_loss') }}">

                            <i class="bi bi-circle"></i><span>profit/loss</span>

                        </a>

                    </li>

                    <li>

                        <a href="{{ route('alizeti.index') }}">

                            <i class="bi bi-circle"></i><span>Alizeti summary</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#expenses-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-explicit"></i> <span>Expenses</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="expenses-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('expenses.index') }}">

                            <i class="bi bi-circle"></i><span>Da hawa alzeti</span>

                        </a>

                        <a href="{{ route('expenses.index') }}">

                            <i class="bi bi-circle"></i><span>Wateja alizeti</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#categories-nav" data-bs-toggle="collapse" href="#">

                    <i class="fas fa-folder"></i> <span>Category</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="categories-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('categories.index') }}">

                            <i class="bi bi-circle"></i><span>Da hawa alzeti</span>

                        </a>

                        <a href="{{ route('categories.index') }}">

                            <i class="bi bi-circle"></i><span>Wateja alizeti</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#alzeti-ghafi-nav" data-bs-toggle="collapse" href="#">

                    <i class="fas fa-warehouse"></i><span>Alzeti</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="alzeti-ghafi-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('alizeti.index') }}">

                            <i class="bi bi-circle"></i><span>Da hawa alzeti</span>

                        </a>

                        <a href="{{ route('alizeti.summary') }}">

                            <i class="bi bi-circle"></i><span>record alizeti</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#uzalishaji-mafuta-nav" data-bs-toggle="collapse"
                    href="#">

                    <i class="bi bi-building-fill-gear"></i><span>Ukamuaji_Mafuta</span><i
                        class="bi bi-chevron-down ms-auto"></i>



                </a>

                <ul id="uzalishaji-mafuta-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('uzalishaji.index') }}">

                            <i class="bi bi-circle"></i><span>Da hawa</span>

                        </a>

                        <a href="{{ route('uzalishaji.index') }}">

                            <i class="bi bi-circle"></i><span>Wateja</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#uchujaji-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-funnel"></i><span>Uchujaji</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="uchujaji-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('uchujaji.index')}}">

                            <i class="bi bi-circle"></i><span>Da hawa</span>

                        </a>

                        <a href="{{ route('uchujaji.index')}}">

                            <i class="bi bi-circle"></i><span>Wateja</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#stocks-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-funnel"></i><span>Stock</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="stocks-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('stocks.index')}}">

                            <i class="bi bi-circle"></i><span>Da hawa</span>

                        </a>

                        <a href="{{ route('stocks.index')}}">

                            <i class="bi bi-circle"></i><span>Wateja</span>

                        </a>

                    </li>

                </ul>

            </li>



            <li class="nav-item">

                @if(Auth::check() && Auth::user()->role_id == 1)

                <a class="nav-link collapsed" data-bs-target="#stock-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-tags-fill"></i><span>Prices</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                @endif

                <ul id="stock-nav" class="nav-content collapse">

                    <li>

                        <a href="{{route('price.index')}}">

                            <i class="bi bi-circle"></i><span>Da hawa</span>

                        </a>

                        <a href="{{route('price.index')}}">

                            <i class="bi bi-circle"></i><span>Wateja</span>

                        </a>

                    </li>

                </ul>

            </li>

            <li class="nav-item">

                <a class="nav-link collapsed" data-bs-target="#mauzo-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-receipt-cutoff"></i><span>Mauzo</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                <ul id="mauzo-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('mauzo.index') }}">

                            <i class="bi bi-circle"></i><span>Mafuta</span>

                        </a>

                        <a href="{{ route('mauzo.index') }}">

                            <i class="bi bi-trash2-fill"></i><span>Mashudu</span>

                        </a>

                    </li>

                </ul>

            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#debt-loans-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-receipt-cutoff"></i><span>Credit-Loans</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="debt-loans-nav" class="nav-content collapse"> <li>
                        <a href="{{ route('customer_debits.index') }}">
                            <i class="bi bi-circle"></i><span>debits</span>
                        </a>
                        <a href="{{ route('customer_debit_payments.index') }}">
                            <i class="bi bi-trash2-fill"></i><span>debt_payment</span>
                        </a>
                        <a href="{{ route('loans.index') }}">
                            <i class="bi bi-circle"></i><span>loans</span>
                        </a>
                        <a href="{{ route('loan_payments.index') }}">
                            <i class="bi bi-trash2-fill"></i><span>Loans_payments</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#loans-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-receipt-cutoff"></i><span>Loans</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="loans-nav" class="nav-content collapse"> <li>
                        <a href="{{ route('loans.index') }}">
                            <i class="bi bi-circle"></i><span>loans</span>
                        </a>
                        <a href="{{ route('loan_payments.index') }}">
                            <i class="bi bi-trash2-fill"></i><span>Loans_payments</span>
                        </a>
                    </li>
                </ul>
            </li> -->





            <li class="nav-item">

                @if(Auth::check() && Auth::user()->role_id == 1)

                <a class="nav-link collapsed" data-bs-target="#setting-nav" data-bs-toggle="collapse" href="#">

                    <i class="bi bi-gear"></i><span>Setting</span><i class="bi bi-chevron-down ms-auto"></i>

                </a>

                @endif

                <ul id="setting-nav" class="nav-content collapse">

                    <li>

                        <a href="{{ route('users.index') }}">

                            <i class="bi bi-person-circle"></i><span>Users</span>

                        </a>

                    </li>

                </ul>

            </li>

        </ul>



    </aside>

    </aside>

    <main id="main" class="main">



        @yield('content')



    </main>

    <footer id="footer" class="footer">

        <div class="copyright">

            &copy; 2025.Bi.HAWA.All Rights Reserved

        </div>

        <div class="credits">



        </div>

    </footer>

    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>

    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>

    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://code.jqueryui.com/1.12.1/jquery-ui.min.js"></script>

    <script src="{{ asset('assets/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('assets/js/flatpickr-init.js') }}"></script>



</body>



</html>