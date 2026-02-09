{{-- <div>
    <h3>The <code>EmployeeDashboard</code> livewire component is loaded from the <code>HRM</code> module.</h3>
</div> --}}


<div class="row">
    <!-- Total Staff - Blue -->
    <div class="col-md-3">
        <div class="card card-body mx-1 shadow-sm border-0" style="border-left: 5px solid #4e73df;">
            <div class="d-flex align-items-center">
                <i class="icofont icofont-users-social fa-3x txt-primary"></i>
                <div class="ms-3">
                    <p class="mb-0 text-muted fw-bold">Total Staff</p>
                    <h3 class="mb-0 fw-bold">{{ $stats['total_staff'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Missing Documents - Red/Danger -->
    <div class="col-md-3">
        <div class="card card-body mx-1 shadow-sm border-0" style="border-left: 5px solid #e74a3b;">
            <div class="d-flex align-items-center">
                <i class="icofont icofont-file-document fa-3x text-danger"></i>
                <div class="ms-3">
                    <p class="mb-0 text-muted fw-bold">Missing Docs</p>
                    <h3 class="mb-0 fw-bold text-danger">{{ $stats['missing_docs'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Retirements - Warning/Orange -->
    <div class="col-md-3">
        <div class="card card-body mx-1 shadow-sm border-0" style="border-left: 5px solid #f6c23e;">
            <div class="d-flex align-items-center">
                <i class="icofont icofont-clock-time fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-0 text-muted fw-bold">Retiring Soon</p>
                    <h3 class="mb-0 fw-bold">{{ $stats['upcoming_retirements'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Gender Breakdown - Info/Teal -->
    <div class="col-md-3">
        <div class="card card-body mx-1 shadow-sm border-0" style="border-left: 5px solid #36b9cc;">
            <div class="d-flex align-items-center">
                <i class="icofont icofont-restroom fa-3x text-info"></i>
                <div class="ms-3">
                    <p class="mb-0 text-muted fw-bold">M / F Ratio</p>
                    <h3 class="mb-0 fw-bold">
                        {{ $stats['gender_ratio']['male'] ?? 0 }} : {{ $stats['gender_ratio']['female'] ?? 0 }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
