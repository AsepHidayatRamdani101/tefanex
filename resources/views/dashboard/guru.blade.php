@section('title', 'Dashboard Guru')
@extends('adminlte::page')

@section('content')
    <div class="container pt-4">
        <div class="mb-4">
            <h1>Selamat Datang di Dashboard Guru</h1>
            <p>Ini adalah halaman dashboard untuk guru. Di sini Anda dapat melihat informasi terkait proyek, materi, tugas, dan absensi siswa Anda.</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $totalProjects }}</h3>
                <p>Project Saya</p>
              </div>
              <div class="icon">
                <i class="fas fa-project-diagram"></i>
              </div>
              <a href="{{ route('projects.index') }}" class="small-box-footer">
                View Projects <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ $activeProjects }}</h3>
                <p>Progress Project </p>
              </div>
              <div class="icon">
                <i class="fas fa-tasks"></i>
              </div>
              <a href="#project-progress" class="small-box-footer">
                View Progress <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ $studentActivityCount }}</h3>
                <p>Aktivitas siswa</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="{{ route('project-members.index') }}" class="small-box-footer">
                View Activities <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Project Progress Section -->
        <div class="row" id="project-progress">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Progress Proyek Terkini</h3>
                    </div>
                    <div class="card-body">
                        @if($projects->count() == 0)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Anda belum memiliki proyek. <a href="{{ route('projects.create') }}">Buat proyek baru</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Judul Proyek</th>
                                            <th>Client</th>
                                            <th>Tim</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($projects as $project)
                                            <tr>
                                                <td class="font-weight-bold">{{ $project->judul }}</td>
                                                <td>{{ $project->client ?? '-' }}</td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $project->members_count }} Tim</span>
                                                </td>
                                                <td>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
                                                    </div>
                                                    <small>{{ $project->progress }}%</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'awal' => 'secondary',
                                                            'design_brief' => 'info',
                                                            'mockup' => 'primary',
                                                            'produksi' => 'warning',
                                                            'qc' => 'danger',
                                                            'selesai' => 'success',
                                                        ];
                                                        $statusLabels = [
                                                            'awal' => 'Awal',
                                                            'design_brief' => 'Design Brief',
                                                            'mockup' => 'Mockup',
                                                            'produksi' => 'Produksi',
                                                            'qc' => 'QC',
                                                            'selesai' => 'Selesai',
                                                        ];
                                                    @endphp
                                                    <span class="badge badge-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                                        {{ $statusLabels[$project->status] ?? $project->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Workflow Diagram -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h3 class="card-title">Alur Proyek (Project Workflow)</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <svg width="100%" height="150" viewBox="0 0 1000 150" xmlns="http://www.w3.org/2000/svg">
                                <!-- Stage 1: Awal -->
                                <circle cx="100" cy="75" r="40" fill="#6C757D" opacity="0.7"/>
                                <text x="100" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">Awal</text>
                                <text x="100" y="125" text-anchor="middle" font-size="10" fill="#333">0%</text>

                                <!-- Arrow 1 -->
                                <line x1="140" y1="75" x2="210" y2="75" stroke="#333" stroke-width="2" marker-end="url(#arrowhead)"/>

                                <!-- Stage 2: Design Brief -->
                                <circle cx="260" cy="75" r="40" fill="#17A2B8"/>
                                <text x="260" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">Design Brief</text>
                                <text x="260" y="125" text-anchor="middle" font-size="10" fill="#333">20%</text>

                                <!-- Arrow 2 -->
                                <line x1="300" y1="75" x2="370" y2="75" stroke="#333" stroke-width="2" marker-end="url(#arrowhead)"/>

                                <!-- Stage 3: Mockup -->
                                <circle cx="420" cy="75" r="40" fill="#007BFF"/>
                                <text x="420" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">Mockup</text>
                                <text x="420" y="125" text-anchor="middle" font-size="10" fill="#333">40%</text>

                                <!-- Arrow 3 -->
                                <line x1="460" y1="75" x2="530" y2="75" stroke="#333" stroke-width="2" marker-end="url(#arrowhead)"/>

                                <!-- Stage 4: Produksi -->
                                <circle cx="580" cy="75" r="40" fill="#FFC107"/>
                                <text x="580" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">Produksi</text>
                                <text x="580" y="125" text-anchor="middle" font-size="10" fill="#333">60%</text>

                                <!-- Arrow 4 -->
                                <line x1="620" y1="75" x2="690" y2="75" stroke="#333" stroke-width="2" marker-end="url(#arrowhead)"/>

                                <!-- Stage 5: QC -->
                                <circle cx="740" cy="75" r="40" fill="#DC3545"/>
                                <text x="740" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">QC</text>
                                <text x="740" y="125" text-anchor="middle" font-size="10" fill="#333">80%</text>

                                <!-- Arrow 5 -->
                                <line x1="780" y1="75" x2="850" y2="75" stroke="#333" stroke-width="2" marker-end="url(#arrowhead)"/>

                                <!-- Stage 6: Selesai -->
                                <circle cx="900" cy="75" r="40" fill="#28A745"/>
                                <text x="900" y="80" text-anchor="middle" font-size="12" fill="white" font-weight="bold">Selesai</text>
                                <text x="900" y="125" text-anchor="middle" font-size="10" fill="#333">100%</text>

                                <!-- Arrow marker definition -->
                                <defs>
                                    <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                                        <polygon points="0 0, 10 3.5, 0 7" fill="#333"/>
                                    </marker>
                                </defs>
                            </svg>
                        </div>
                        <div class="mt-3 text-muted text-center">
                            <small>Setiap tahap proyek memiliki persentase progress yang berbeda. Klik "View Progress" untuk melihat detail proyek Anda.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Statistics -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info">
                        <h3 class="card-title">Distribusi Status Proyek</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $statusDistribution = [];
                            foreach($projects as $project) {
                                if(!isset($statusDistribution[$project->status])) {
                                    $statusDistribution[$project->status] = 0;
                                }
                                $statusDistribution[$project->status]++;
                            }
                        @endphp
                        <div class="chart">
                            <canvas id="statusChart" style="height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h3 class="card-title">Statistik Progress</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <h5>Rata-rata Progress</h5>
                                @php
                                    $avgProgress = 0;
                                    if($projects->count() > 0) {
                                        $totalProgress = 0;
                                        foreach($projects as $project) {
                                            $totalProgress += $project->progress;
                                        }
                                        $avgProgress = $totalProgress / $projects->count();
                                    }
                                @endphp
                                <h2 class="text-success">{{ round($avgProgress) }}%</h2>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: {{ $avgProgress }}%"></div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <h5>Proyek Selesai</h5>
                                @php
                                    $completedProjects = 0;
                                    foreach($projects as $project) {
                                        if($project->status === 'selesai') {
                                            $completedProjects++;
                                        }
                                    }
                                @endphp
                                <h2 class="text-info">{{ $completedProjects }}/{{ $projects->count() }}</h2>
                                <small class="text-muted">dari {{ $projects->count() }} proyek</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                const statusDistribution = {!! json_encode($statusDistribution) !!};
                const statusLabels = Object.keys(statusDistribution);
                const statusLabelsFormatted = statusLabels.map(label => {
                    const labels = {
                        'awal': 'Awal',
                        'design_brief': 'Design Brief',
                        'mockup': 'Mockup',
                        'produksi': 'Produksi',
                        'qc': 'QC',
                        'selesai': 'Selesai'
                    };
                    return labels[label] || label;
                });

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabelsFormatted,
                        datasets: [{
                            data: Object.values(statusDistribution),
                            backgroundColor: [
                                '#6C757D',
                                '#17A2B8',
                                '#007BFF',
                                '#FFC107',
                                '#DC3545',
                                '#28A745'
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
@stop
