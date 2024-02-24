@extends('backend.layouts.app')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <!-- DataTables CSS -->

    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-xl-12">
                <button class="btn btn-primary btn-block" data-toggle="collapse" data-target="#demo">Click Here to See
                    Analytics </button>

            </div>
            <div class="col-xl-12">
                <div id="demo" class="collapse">
                    <div class="card">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="m-1"> City</h3>
                                <canvas id="cityChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h3 class="m-1"> Category</h3>
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">

                <div class="card">
                    <div class="card-body">

                        <div class="container">
                            <form action="{{ route('crm.print') }}" method="POST">
                                @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <input id="date" name="date" type="text"
                                            class="aiz-date-range form-control"
                                            placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                            data-separator=" to " data-advanced-range="true" autocomplete="off"
                                            data-live-search="true">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <select id="categorySelect" class="select2 form-control aiz-selectpicker"
                                            name="category_id" data-toggle="select2" data-placeholder="Choose..."
                                            data-live-search="true">
                                            <option value="0"
                                                {{ request()->input('category_id') == 0 ? 'selected' : '' }}>
                                                All category
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request()->input('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group my-2">
                                        <select id="citySelect" class="select2 form-control aiz-selectpicker" name="city_id"
                                            data-toggle="select2" data-placeholder="Choose..." data-live-search="true">
                                            <option value="0" {{ request()->input('city_id') == 0 ? 'selected' : '' }}>
                                                All city
                                            </option>
                                            @foreach ($all_cities as $scity)
                                                <option value="{{ $scity }}"
                                                    {{ request()->input('city_id') == $scity ? 'selected' : '' }}>
                                                    {{ $scity }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group my-2">
                                        <select id="productSelect" class="select2 form-control aiz-selectpicker"
                                            name="product_id" data-toggle="select2" data-placeholder="Choose..."
                                            data-live-search="true">
                                            <option value="0"
                                                {{ request()->input('product_id') == 0 ? 'selected' : '' }}>
                                                All product
                                            </option>
                                            <!-- The options will be dynamically populated here -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group my-2">
                                        <button type="button" id="filter"
                                            class="btn btn-primary btn-block">Filter</button>
                                            <button type="submit"
                                            class="btn btn-secondary btn-block">Print</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                        <div class="card-header">
                            <h5 class="mb-0 h6">Send Bulk SMS</h5>
                        </div>
                        <form id="filterForm" action="{{ route('crm.customer.get') }}" method="post">
                            @csrf
                            <div class="container">
                                <div class="form-group row">
                                    {{-- <label class="col-md-3 col-from-label">SMS Content</label> --}}
                                    <div class="col-md-9">
                                        <textarea name="content" rows="4" class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" name="button"
                                            class="btn btn-warning float-right my-3 btn-block">Send
                                            Sms</button>
                                    </div>
                                    <br>

                                </div>
                                <div class="form-group mt-3 row">
                                    <div class="col-md-3">
                                        <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                                            <div class="px-3 pt-3">
                                                <div class="opacity-50">
                                                    <span class="fs-12 d-block">Total</span>
                                                    Orders
                                                </div>
                                                <div class="h5 fw-700 mb-3"> <span id="sumOfTotalOrders"></span></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                                            <div class="px-3 pt-3">
                                                <div class="opacity-50">
                                                    <span class="fs-12 d-block">Total</span>
                                                    Customers
                                                </div>
                                                <div class="h5 fw-700 mb-3"> <span id="totalCustomers"></span></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                                            <div class="px-3 pt-3">
                                                <div class="opacity-50">
                                                    <span class="fs-12 d-block">Total</span>
                                                    Sale
                                                </div>
                                                <div class="h5 fw-700 mb-3"> <span id="sumOfPurchaseAmount"></span>
                                                </div>

                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                                            <div class="px-3 pt-3">
                                                <div class="opacity-50">
                                                    <span class="fs-12 d-block">Total</span>
                                                    Discount
                                                </div>
                                                <div class="h5 fw-700 mb-3"> <span id="sumOfDiscount"></span></div>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                                                <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                                                    d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">

                                <div class="row">

                                    <div class="col-md-12">
                                        <table id="customerDataTable" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Select</th>
                                                    <th>Name</th>
                                                    <th>Total Orders</th>
                                                    <th>Total Purchase Amount</th>
                                                    <th>Total Discount</th>
                                                    <th>___</th>
                                                    <th>___</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </form>
                        <div class="aiz-pagination mt-3">
                            <!-- Pagination will be handled by DataTables -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- Add BlockUI library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>

    <!-- DataTables Buttons JavaScript -->
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js">
    </script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.print.min.js">
    </script>



    <script>
        $(document).ready(function() {


            $("#categorySelect").on('change', function() {

                let id = $(this).val();
                // let id =  1;
                let url = "{{ url('/admin/productsByCategory') }}/" + id
                console.log(id, url);
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Clear existing options
                        $('#productSelect').empty();

                        // Add the default option
                        $('#productSelect').append(
                            '<option value="0" selected>All product</option>');

                        // Add options based on the response
                        $.each(response, function(productId, product) {
                            $('#productSelect').append('<option value="' + product.id +
                                '">' + product.product + '</option>');
                        });

                        // Initialize Select2 after updating options
                        $('#productSelect').selectpicker('refresh');
                    },
                    error: function(error) {
                        console.error('Error fetching products:', error);
                    }
                });
            });

            // Make an Ajax request


        });
    </script>
    <script>
        $(document).ready(function() {
            var dataTableUrl = "{{ url('datatable/customers') }}";
            var initialDataTableUrl = dataTableUrl + `/0/0/0/0`;
            var dataTable = initializeDataTable(initialDataTableUrl);
            console.log(initialDataTableUrl);
            $('#filter').on('click', function() {
                var selectedCity = $('select[name="city_id"]').val();
                var selectedProduct = $('select[name="product_id"]').val();
                var selectedCategory = $('select[name="category_id"]').val();
                var selectedDate = $('input[name="date"]').val();
                var url =
                    `${dataTableUrl}/${selectedCity}/${selectedProduct}/${selectedCategory}/${selectedDate}`;
                dataTable.ajax.url(url).load();
                // BlockUI: Start loading overlay
                $.blockUI({
                    message: '<h3>Loading...</h3>',
                    css: {
                        border: 'none',
                        padding: '15px',
                        backgroundColor: '#000',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: 0.5,
                        color: '#fff'
                    }
                });

                dataTable.ajax.url(url).load(function() {
                    // BlockUI: Stop loading overlay on DataTable callback
                    $.unblockUI();
                });
            });

            function initializeDataTable(url) {
                var dataTable = $('#customerDataTable').DataTable({
                    retrieve: true,
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pageLength: 25000,
                    dom: 'Bfrtip', // Specify that you want to use DataTables Buttons
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    ajax: url,
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'total_orders',
                            name: 'total_orders'
                        },
                        {
                            data: 'total_purchase_amount',
                            name: 'total_purchase_amount'
                        },
                        {
                            data: 'total_discount',
                            name: 'total_discount'
                        },
                    ],
                    drawCallback: function(settings) {
                        var json = settings.json;
                        var totalCustomers = json.recordsFiltered;
                        var sumOfPurchaseAmount = json.sum_of_purchase_amount;
                        var sumOfDiscount = json.sum_of_discount;
                        var sumOfTotalOrders = json.sum_of_total_orders;

                        $('#totalCustomers').text(totalCustomers);
                        $('#sumOfPurchaseAmount').text(sumOfPurchaseAmount);
                        $('#sumOfDiscount').text(sumOfDiscount);
                        $('#sumOfTotalOrders').text(sumOfTotalOrders);
                    }
                });

                $('#customerDataTable thead th:first-child').html('<input type="checkbox" id="selectAllCheckbox">');

                $('#selectAllCheckbox').on('change', function() {
                    var isChecked = $(this).prop('checked');
                    $('input[name="selected_customers[]"]').prop('checked', isChecked);
                });

                return dataTable;
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctxCity = document.getElementById('cityChart').getContext('2d');
        var cityData = @json($cityData);

        var cityLabels = Object.keys(cityData);
        var totalOrdersData = cityLabels.map(city => cityData[city]?.totalOrders || 0);
        var totalSaleAmountData = cityLabels.map(city => cityData[city]?.totalSaleAmount || 0);
        var totalCustomersData = cityLabels.map(city => cityData[city]?.totalCustomers || 0);

        var cityColors = generateCityColors(cityLabels.length);

        var myCityChart = new Chart(ctxCity, {
            type: 'pie',
            data: {
                labels: cityLabels,
                datasets: [{
                        label: 'Total Orders',
                        data: totalOrdersData,
                        backgroundColor: cityColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Total Sale Amount',
                        data: totalSaleAmountData,
                        backgroundColor: cityColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Total Customers',
                        data: totalCustomersData,
                        backgroundColor: cityColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function generateCityColors(count) {
            var colors = [];
            for (var i = 0; i < count; i++) {
                colors.push(getRandomColor());
            }
            return colors;
        }

        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    </script>

    <script>
        var ctxCategory = document.getElementById('categoryChart').getContext('2d');
        var categoryData = @json($categoryData);

        var categoryLabels = Object.keys(categoryData);
        var totalOrdersData = categoryLabels.map(category => categoryData[category]?.totalOrders || 0);
        var totalSaleAmountData = categoryLabels.map(category => categoryData[category]?.totalSaleAmount || 0);
        var totalCustomersData = categoryLabels.map(category => categoryData[category]?.totalCustomers || 0);

        var categoryColors = generateCategoryColors(categoryLabels.length);

        var myCategoryChart = new Chart(ctxCategory, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                        label: 'Total Orders',
                        data: totalOrdersData,
                        backgroundColor: categoryColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Total Sale Amount',
                        data: totalSaleAmountData,
                        backgroundColor: categoryColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                    {
                        label: 'Total Customers',
                        data: totalCustomersData,
                        backgroundColor: categoryColors,
                        borderColor: '#fff',
                        borderWidth: 1,
                        fill: false
                    },
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function generateCategoryColors(count) {
            var colors = [];
            for (var i = 0; i < count; i++) {
                colors.push(getRandomColor());
            }
            return colors;
        }

        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    </script>
@endsection
