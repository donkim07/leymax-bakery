@extends('layouts.app')

@section('title', 'Academy Dashboard')

@section('content')
<div class="row">
    <!-- Date Range Filter -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-range me-2"></i>
                        <select class="form-select form-select-sm" id="dateRange">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="input-group date-picker-range d-none">
                        <input type="text" class="form-control form-control-sm" id="startDate" placeholder="Start Date">
                        <span class="input-group-text">to</span>
                        <input type="text" class="form-control form-control-sm" id="endDate" placeholder="End Date">
                        <button class="btn btn-sm btn-primary" id="applyCustomRange">Apply</button>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary me-2" id="refreshData">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-export="pdf">PDF Report</a></li>
                                <li><a class="dropdown-item" href="#" data-export="excel">Excel Data</a></li>
                                <li><a class="dropdown-item" href="#" data-export="csv">CSV Data</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Left side columns -->
    <div class="col-lg-8">
        <div class="row">
            <!-- Key Performance Metrics -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card students-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Students</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-primary-light">
                                <i class="bi bi-people text-primary"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stats['total_students'] ?? 0 }}</h6>
                                @if(($stats['student_growth'] ?? 0) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['student_growth'] ?? 0, 1) }}%</span>
                                @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['student_growth'] ?? 0), 1) }}%</span>
                                @endif
                                <span class="text-muted small pt-2 ps-1">vs last month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Revenue</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                                <i class="bi bi-currency-dollar text-success"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ money($stats['revenue_month'] ?? 0) }}</h6>
                                @if(($stats['revenue_growth'] ?? 0) > 0)
                                    <span class="text-success small pt-1 fw-bold">+{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</span>
                                @else
                                    <span class="text-danger small pt-1 fw-bold">{{ number_format(abs($stats['revenue_growth'] ?? 0), 1) }}%</span>
                                @endif
                                <span class="text-muted small pt-2 ps-1">vs last month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xxl-4 col-md-6">
                <div class="card info-card classes-card">
                    <div class="card-body">
                        <h5 class="card-title">Active Classes</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-info-light">
                                <i class="bi bi-book text-info"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ $stats['active_classes'] ?? 0 }}</h6>
                                <span class="text-muted small pt-2">{{ $stats['total_classes'] ?? 0 }} total classes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Engagement -->
            <div class="col-xxl-4 col-md-6">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title">Student Engagement</h5>
                        <div class="d-flex align-items-center">
                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning-light">
                                <i class="bi bi-bar-chart text-warning"></i>
                            </div>
                            <div class="ps-3">
                                <h6>{{ number_format($stats['engagement_rate'] ?? 78.5, 1) }}%</h6>
                                <span class="text-muted small pt-2">average completion rate</span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $stats['engagement_rate'] ?? 78.5 }}%" 
                                     aria-valuenow="{{ $stats['engagement_rate'] ?? 78.5 }}" aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Low</small>
                                <small class="text-muted">Average</small>
                                <small class="text-muted">High</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Trend Chart -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Enrollment & Revenue Trends</h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-graph-up"></i> Metrics
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-metric="enrollments">Enrollments</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="revenue">Revenue</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="average">Average Enrollment Value</a></li>
                                    <li><a class="dropdown-item" href="#" data-metric="combined">Combined View</a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="enrollmentChart"></div>
                    </div>
                </div>
            </div>

            <!-- Course Completion Rates -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Course Completion Rates</h5>
                        <div id="courseCompletionChart"></div>
                    </div>
                </div>
            </div>

            <!-- Student Demographics -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Student Demographics</h5>
                        <div id="demographicsChart"></div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <div class="d-flex flex-column align-items-center">
                                        <h6 class="text-muted mb-1">Average Age</h6>
                                        <h4 class="mb-0">{{ $demographics['avg_age'] ?? 32 }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column align-items-center">
                                        <h6 class="text-muted mb-1">Gender Ratio</h6>
                                        <h4 class="mb-0">{{ $demographics['gender_ratio'] ?? '68:32' }}</h4>
                                        <small class="text-muted">F:M</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learning Progress -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Learning Progress Analysis</h5>
                        <div id="learningProgressChart"></div>
                        <div class="mt-3">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-sm-6 text-center mb-3">
                                    <div class="px-3 py-2 border rounded">
                                        <h6 class="text-muted mb-1">Course Enrollment</h6>
                                        <h4 class="mb-0">{{ number_format($learningMetrics['enrollment_rate'] ?? 100.0, 1) }}%</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 text-center mb-3">
                                    <div class="px-3 py-2 border rounded">
                                        <h6 class="text-muted mb-1">Course Completion</h6>
                                        <h4 class="mb-0">{{ number_format($learningMetrics['completion_rate'] ?? 74.8, 1) }}%</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 text-center mb-3">
                                    <div class="px-3 py-2 border rounded">
                                        <h6 class="text-muted mb-1">Attendance Rate</h6>
                                        <h4 class="mb-0">{{ number_format($learningMetrics['attendance_rate'] ?? 82.5, 1) }}%</h4>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 text-center mb-3">
                                    <div class="px-3 py-2 border rounded">
                                        <h6 class="text-muted mb-1">Certification</h6>
                                        <h4 class="mb-0">{{ number_format($learningMetrics['certification_rate'] ?? 42.3, 1) }}%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Courses Table -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Popular Courses</h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" data-view="students">
                                    By Students
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="revenue">
                                    By Revenue
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-view="rating">
                                    By Rating
                                </button>
                            </div>
                        </div>
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th scope="col">Course</th>
                                    <th scope="col">Instructor</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Students</th>
                                    <th scope="col">Revenue</th>
                                    <th scope="col">Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularCourses ?? [] as $course)
                                <tr>
                                    <td><a href="#" class="text-primary fw-bold">{{ $course->name }}</a></td>
                                    <td>{{ $course->instructor->name }}</td>
                                    <td>{{ money($course->price) }}</td>
                                    <td class="fw-bold">{{ number_format($course->students_count) }}</td>
                                    <td>{{ money($course->revenue) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">{{ number_format($course->rating ?? 4.5, 1) }}</div>
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($course->rating ?? 4.5))
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @elseif($i - 0.5 <= ($course->rating ?? 4.5))
                                                        <i class="bi bi-star-half text-warning"></i>
                                                    @else
                                                        <i class="bi bi-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No courses found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right side columns -->
    <div class="col-lg-4">
        <!-- Course Category Distribution -->
        <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Course Categories</h5>
                <div id="categoryDistributionChart"></div>
            </div>
        </div>

        <!-- Student Satisfaction -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Student Satisfaction</h5>
                <div id="satisfactionChart"></div>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-6 border-end">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">NPS Score</h6>
                                <h4 class="mb-0">{{ $satisfaction['nps'] ?? 68 }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex flex-column align-items-center">
                                <h6 class="text-muted mb-1">Average Rating</h6>
                                <h4 class="mb-0">{{ number_format($satisfaction['avg_rating'] ?? 4.7, 1) }}</h4>
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($satisfaction['avg_rating'] ?? 4.7))
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @elseif($i - 0.5 <= ($satisfaction['avg_rating'] ?? 4.7))
                                            <i class="bi bi-star-half text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-warning"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Sources -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Revenue Sources</h5>
                <div id="revenueSourcesChart"></div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Course Sales</span>
                        <span class="badge bg-primary rounded-pill">{{ $revenueSources['course_sales'] ?? 76 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Subscriptions</span>
                        <span class="badge bg-success rounded-pill">{{ $revenueSources['subscriptions'] ?? 14 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Workshop Fees</span>
                        <span class="badge bg-info rounded-pill">{{ $revenueSources['workshops'] ?? 8 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Other</span>
                        <span class="badge bg-secondary rounded-pill">{{ $revenueSources['other'] ?? 2 }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Recent Enrollments</h5>
                <div class="activity">
                    @forelse($recentEnrollments ?? [] as $enrollment)
                    <div class="activity-item d-flex">
                        <div class="activite-label">{{ \Carbon\Carbon::now()->diffForHumans($enrollment->created_at ?? now()) }}</div>
                        <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                        <div class="activity-content">
                            <strong>{{ $enrollment->student->name }}</strong> enrolled in<br>
                            {{ $enrollment->course->name }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No recent enrollments</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Upcoming Classes</h5>
                <div class="news">
                    @forelse($upcomingClasses ?? [] as $class)
                    <div class="post-item clearfix">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-event text-primary me-3 fs-4"></i>
                            <div>
                                <h4><a href="#">{{ $class->name }}</a></h4>
                                <p>{{ $class->start_date->format('h:i A') }} • {{ $class->instructor->name }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted mb-3">No upcoming classes</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date Range Selector
    const dateRangeSelect = document.getElementById('dateRange');
    const datePickerRange = document.querySelector('.date-picker-range');
    
    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            datePickerRange.classList.remove('d-none');
        } else {
            datePickerRange.classList.add('d-none');
            // Here you would typically make an AJAX call to update the data based on the selected range
        }
    });

    // Enrollment Chart
    var enrollmentOptions = {
        series: [{
            name: 'Enrollments',
            type: 'column',
            data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 72, 68, 74]
        }, {
            name: 'Revenue',
            type: 'line',
            data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 115, 90, 121]
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true
            }
        },
        stroke: {
            width: [0, 4]
        },
        dataLabels: {
            enabled: false,
            enabledOnSeries: [1]
        },
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        xaxis: {
            type: 'category'
        },
        yaxis: [{
            title: {
                text: 'Enrollments',
            },
        }, {
            opposite: true,
            title: {
                text: 'Revenue ($)'
            }
        }],
        colors: ['#4154f1', '#2eca6a']
    };

    var enrollmentChart = new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions);
    enrollmentChart.render();

    // Course Completion Chart
    var completionOptions = {
        series: [
            {
                name: 'Completion Rate',
                data: [85, 78, 92, 65, 72, 88, 69, 94, 82]
            }
        ],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
                dataLabels: {
                    position: 'bottom'
                },
            }
        },
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#0dcaf0', '#a566ff', '#6f42c1', '#fd7e14', '#20c997', '#0d6efd'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#fff']
            },
            formatter: function(val, opt) {
                return val + '%';
            },
            offsetX: 0
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        xaxis: {
            categories: ['Cake Decorating Basics', 'Advanced Piping', 'Wedding Cake Design', 'Fondant Mastery', 'Sugar Flowers', 'Chocolate Work', 'Pastry Art', 'Business of Baking', 'Custom Cake Design'],
            labels: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        yaxis: {
            labels: {
                show: true
            }
        },
        tooltip: {
            theme: 'dark',
            x: {
                show: false
            },
            y: {
                title: {
                    formatter: function() {
                        return 'Completion';
                    }
                },
                formatter: function(val) {
                    return val + '%';
                }
            }
        }
    };

    var completionChart = new ApexCharts(document.querySelector("#courseCompletionChart"), completionOptions);
    completionChart.render();

    // Demographics Chart
    var demographicsOptions = {
        series: [44, 55],
        chart: {
            height: 240,
            type: 'donut',
        },
        labels: ['Female', 'Male'],
        colors: ['#ff6384', '#36a2eb'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        },
        legend: {
            position: 'bottom'
        }
    };

    var demographicsChart = new ApexCharts(document.querySelector("#demographicsChart"), demographicsOptions);
    demographicsChart.render();

    // Learning Progress Chart
    var learningProgressOptions = {
        series: [{
            name: 'Average Progress',
            data: [100, 75, 83, 42]
        }],
        chart: {
            height: 350,
            type: 'radar',
        },
        xaxis: {
            categories: ['Course Enrollment', 'Course Completion', 'Attendance Rate', 'Certification']
        },
        yaxis: {
            max: 100,
            min: 0
        },
        markers: {
            size: 5,
            colors: ['#4154f1'],
            strokeColors: '#fff',
            strokeWidth: 2,
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + '%';
                }
            }
        },
        fill: {
            opacity: 0.7
        }
    };

    var learningProgressChart = new ApexCharts(document.querySelector("#learningProgressChart"), learningProgressOptions);
    learningProgressChart.render();

    // Category Distribution Chart
    var categoryOptions = {
        series: [25, 20, 18, 15, 12, 10],
        chart: {
            height: 350,
            type: 'donut',
        },
        labels: ['Cake Decorating', 'Pastry Art', 'Baking Basics', 'Wedding Designs', 'Business Skills', 'Special Techniques'],
        colors: ['#4154f1', '#2eca6a', '#ff771d', '#a566ff', '#0dcaf0', '#ffc107'],
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Courses',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    };

    var categoryDistributionChart = new ApexCharts(document.querySelector("#categoryDistributionChart"), categoryOptions);
    categoryDistributionChart.render();

    // Satisfaction Chart
    var satisfactionOptions = {
        series: [{{ $satisfaction['nps'] ?? 68 }}],
        chart: {
            height: 220,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                },
                dataLabels: {
                    name: {
                        show: false
                    },
                    value: {
                        show: true,
                        fontSize: '28px',
                        fontWeight: 'bold',
                        formatter: function(val) {
                            return val;
                        }
                    }
                }
            }
        },
        fill: {
            colors: ['#2eca6a']
        },
        labels: ['NPS Score'],
    };

    var satisfactionChart = new ApexCharts(document.querySelector("#satisfactionChart"), satisfactionOptions);
    satisfactionChart.render();

    // Revenue Sources Chart
    var revenueSourcesOptions = {
        series: [{{ $revenueSources['course_sales'] ?? 76 }}, {{ $revenueSources['subscriptions'] ?? 14 }}, {{ $revenueSources['workshops'] ?? 8 }}, {{ $revenueSources['other'] ?? 2 }}],
        chart: {
            height: 240,
            type: 'pie',
        },
        labels: ['Course Sales', 'Subscriptions', 'Workshop Fees', 'Other'],
        colors: ['#4154f1', '#2eca6a', '#0dcaf0', '#adb5bd'],
        legend: {
            show: false
        },
        plotOptions: {
            pie: {
                customScale: 0.9,
                donut: {
                    size: '0%'
                }
            }
        }
    };

    var revenueSourcesChart = new ApexCharts(document.querySelector("#revenueSourcesChart"), revenueSourcesOptions);
    revenueSourcesChart.render();

    // Handle filter changes
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.dataset.filter;
            const card = this.closest('.card');
            const titleSpan = card.querySelector('.card-title span');
            if (titleSpan) {
                titleSpan.textContent = `| ${filter.charAt(0).toUpperCase() + filter.slice(1)}`;
            }
            // Here you would typically make an AJAX call to update the data
        });
    });

    // Handle metric changes for enrollment chart
    document.querySelectorAll('.dropdown-item[data-metric]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const metric = this.dataset.metric;
            // Update chart based on selected metric
            if (metric === 'enrollments') {
                enrollmentChart.updateOptions({
                    yaxis: [{
                        title: {
                            text: 'Enrollments'
                        }
                    }]
                });
            } else if (metric === 'revenue') {
                    enrollmentChart.updateOptions({
                    yaxis: [{
                        title: {
                            text: 'Revenue ($)'
                }
                    }]
                });
            }
        });
    });

    // Handle view type changes for courses table
    document.querySelectorAll('.btn-group[role="group"] .btn').forEach(button => {
        button.addEventListener('click', function() {
            const btnGroup = this.closest('.btn-group');
            btnGroup.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            // Here you would update the table based on the selected view
        });
            });

    // Handle refresh button
    document.getElementById('refreshData').addEventListener('click', function() {
        // Add a spinning animation to the refresh icon
        this.querySelector('i').classList.add('rotating');
        
        // Make an AJAX call to refresh data
        setTimeout(() => {
            // Remove spinning animation after data is loaded
            this.querySelector('i').classList.remove('rotating');
        }, 1000);
    });
});
</script>

<style>
.rotating {
    animation: rotate 1s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.stars {
    color: #ffc107;
}
</style>
@endpush 