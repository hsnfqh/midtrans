<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipeline Kolaborasi Sales, Presales & BDM</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .pulse-subtle {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; }
        }
        .verified-seal {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen font-sans">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-sky-500/20">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 tracking-tight leading-none">Enterprise Presales & BDM Hub</h1>
                    <span class="text-xs text-slate-500">Pipeline Sales: <strong>Raiza</strong> &bull; BDM Verification Gatekeeper</span>
                </div>
            </div>

            <!-- Role Switcher for Interactive Live Demo Testing -->
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-semibold">
                <span class="px-2 text-slate-500 flex items-center gap-1.5 hidden sm:inline-flex">
                    <i class="fa-solid fa-user-gear"></i> Simulasi Role:
                </span>
                
                <a href="{{ route('collaboration.index', ['role' => 'sales']) }}" 
                   class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ $activeRole === 'sales' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:bg-white' }}">
                    <i class="fa-solid fa-briefcase"></i> Sales (Raiza)
                </a>

                <a href="{{ route('collaboration.index', ['role' => 'bdm']) }}" 
                   class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ $activeRole === 'bdm' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:bg-white' }}">
                    <i class="fa-solid fa-user-tie"></i> BDM Lead (Approver)
                </a>

                <a href="{{ route('collaboration.index', ['role' => 'presales']) }}" 
                   class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ $activeRole === 'presales' ? 'bg-sky-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:bg-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Pre-Sales (Akbar)
                </a>

                <a href="{{ route('collaboration.index', ['role' => 'sa']) }}" 
                   class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 {{ $activeRole === 'sa' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:bg-white' }}">
                    <i class="fa-solid fa-network-wired"></i> SA (Aris Sadewo)
                </a>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('collaboration.reset', $collaboration->id) }}" method="POST" onsubmit="return confirm('Reset status kolaborasi ke kondisi awal?')">
                    @csrf
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-rotate-left"></i> Reset Demo
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm animate-in fade-in duration-200">
                <div class="text-emerald-600 mt-0.5"><i class="fa-solid fa-circle-check text-lg"></i></div>
                <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 flex items-start gap-3 shadow-sm">
                <div class="text-sky-600 mt-0.5"><i class="fa-solid fa-circle-info text-lg"></i></div>
                <div class="flex-1 text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                <div class="text-rose-600 mt-0.5"><i class="fa-solid fa-circle-exclamation text-lg"></i></div>
                <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Deal Overview & Ownership Banner -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex flex-wrap items-center gap-2.5 mb-2">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $collaboration->project_code }}
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-tag"></i> Sales Owner: <strong>{{ $collaboration->assigned_by }}</strong>
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield"></i> Reviewer BDM: <strong>{{ $collaboration->assigned_bdm_reviewer }}</strong>
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $collaboration->project_name }}</h2>
                <p class="text-xs text-slate-500 mt-1">Klien: <strong class="text-slate-800">{{ $collaboration->client_name }}</strong> &bull; Estimasi Nilai: <strong class="text-slate-800">{{ $collaboration->budget_estimation }}</strong></p>
            </div>

            <!-- Global Pipeline Progress Indicator -->
            <div class="flex items-center gap-3 bg-slate-50 px-4 py-3 rounded-xl border border-slate-200 self-start lg:self-center">
                <!-- Step 1: Penugasan Teknis -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $collaboration->presales_status !== 'pending_upload' && $collaboration->sa_status !== 'pending_upload' ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                        1
                    </div>
                    <span class="text-xs font-medium text-slate-700">Pengerjaan Teknis</span>
                </div>

                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>

                <!-- Step 2: Verifikasi BDM -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold 
                        @if($collaboration->bdm_status === 'approved') bg-emerald-500 text-white 
                        @elseif($collaboration->bdm_status === 'under_review') bg-sky-600 text-white pulse-subtle
                        @elseif($collaboration->bdm_status === 'revision_needed') bg-rose-500 text-white
                        @else bg-slate-300 text-slate-600 @endif">
                        2
                    </div>
                    <span class="text-xs font-semibold 
                        @if($collaboration->bdm_status === 'approved') text-emerald-700 
                        @elseif($collaboration->bdm_status === 'under_review') text-sky-700 font-bold
                        @elseif($collaboration->bdm_status === 'revision_needed') text-rose-700
                        @else text-slate-500 @endif">
                        Verifikasi BDM
                    </span>
                </div>

                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>

                <!-- Step 3: Sales Release -->
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $collaboration->sales_status === 'ready_for_sales' || $collaboration->sales_status === 'sent_to_client' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400' }}">
                        3
                    </div>
                    <span class="text-xs font-medium {{ $collaboration->sales_status === 'ready_for_sales' || $collaboration->sales_status === 'sent_to_client' ? 'text-emerald-700 font-bold' : 'text-slate-400' }}">
                        Rilis ke Sales
                    </span>
                </div>
            </div>
        </div>

        <!-- Role Notification Context Bar -->
        <div class="rounded-xl p-3.5 px-4 text-xs flex items-center justify-between border
            @if($activeRole === 'sales') bg-emerald-50 border-emerald-200 text-emerald-900
            @elseif($activeRole === 'bdm') bg-indigo-50 border-indigo-200 text-indigo-900
            @elseif($activeRole === 'presales') bg-sky-50 border-sky-200 text-sky-900
            @else bg-amber-50 border-amber-200 text-amber-900 @endif">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-dot @if($activeRole === 'sales') text-emerald-600 @elseif($activeRole === 'bdm') text-indigo-600 @elseif($activeRole === 'presales') text-sky-600 @else text-amber-600 @endif"></i>
                <span>
                    Anda sedang melihat sebagai <strong>
                    @if($activeRole === 'sales') Raiza (Sales / Account Executive)
                    @elseif($activeRole === 'bdm') BDM Lead (Approver)
                    @elseif($activeRole === 'presales') Akbar (Pre-Sales Specialist)
                    @else Aris Sadewo (Solution Architect)
                    @endif</strong>.
                </span>
            </div>
            <span class="text-[11px] opacity-75 font-medium">Gunakan switcher di pojok kanan atas untuk berpindah role kapan saja.</span>
        </div>

        <!-- ========================================================================= -->
        <!-- CORE CARD: Kolaborasi Teknis Solusi (Presales & Solution Architect) -->
        <!-- Sesuai dengan screenshot acuan user + dipercantik modern -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            
            <!-- Section Header -->
            <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between bg-white">
                <div class="flex items-center gap-2.5">
                    <span class="w-1.5 h-5 bg-rose-700 rounded-full inline-block"></span>
                    <h3 class="text-base font-bold text-slate-900">Kolaborasi Teknis Solusi (Presales & Solution Architect)</h3>
                </div>
                
                <!-- Button: Ubah Penugasan Tim & Reviewer BDM -->
                <button type="button" onclick="openModal('assignmentModal')" class="text-xs font-bold text-slate-600 hover:text-indigo-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i> Ubah Penugasan Tim & BDM
                </button>
            </div>

            <!-- Two Column Technical Cards -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/40">

                <!-- 1. PRE-SALES SPECIALIST CARD -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between relative transition hover:border-slate-300">
                    
                    <div>
                        <!-- Header & Status Badge -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Pre-Sales Specialist</span>
                                <h4 class="text-base font-bold text-slate-900">{{ $collaboration->presales_name }}</h4>
                            </div>

                            <!-- Dynamic Presales Status Badge -->
                            <div>
                                @if($collaboration->presales_status === 'pending_upload')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Proposal & BoQ
                                    </span>
                                @elseif($collaboration->presales_status === 'submitted_to_bdm')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 pulse-subtle">
                                        <i class="fa-solid fa-spinner fa-spin text-[10px]"></i> Sedang Diverifikasi BDM
                                    </span>
                                @elseif($collaboration->presales_status === 'revision_required')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Perlu Revisi Pre-Sales
                                    </span>
                                @elseif($collaboration->presales_status === 'approved_by_bdm')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check"></i> Disetujui BDM
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 mb-3">Tanggung Jawab: <span class="font-medium text-slate-700">Proposal Teknis & BoQ Komersial.</span></p>

                        <!-- Assignment Instruction Box -->
                        <div class="bg-amber-50/50 border border-amber-200/80 rounded-lg p-3.5 text-xs text-amber-900 mb-4">
                            <p class="italic font-medium">"{{ $collaboration->presales_instructions }}"</p>
                            <span class="block mt-2 text-[11px] text-amber-700/80">
                                Ditugaskan: {{ $collaboration->assigned_at ? $collaboration->assigned_at->format('d M Y H:i') : '24 Sep 2026 09:47' }} (oleh {{ $collaboration->assigned_by }})
                            </span>
                        </div>

                        <!-- Uploaded File Preview if any -->
                        @if($collaboration->presales_file_name)
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-4 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2.5 truncate">
                                    <i class="fa-solid fa-file-pdf text-rose-600 text-lg"></i>
                                    <div class="truncate">
                                        <div class="font-semibold text-slate-800 truncate">{{ $collaboration->presales_file_name }}</div>
                                        <span class="text-[10px] text-slate-500">Disubmit: {{ $collaboration->presales_submitted_at ? $collaboration->presales_submitted_at->format('H:i') : 'Baru saja' }}</span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">Ready</span>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action & Bottom Status Indicator -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="openModal('assignmentModal')" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Ubah Penugasan
                        </button>

                        <div class="flex items-center gap-2">
                            <!-- Bottom Status Button / Badge -->
                            @if($collaboration->presales_status === 'pending_upload')
                                <button onclick="openModal('presalesModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100 transition shadow-sm">
                                    <i class="fa-regular fa-clock"></i> Upload Berkas Pre-Sales
                                </button>
                            @elseif($collaboration->presales_status === 'submitted_to_bdm')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <i class="fa-solid fa-hourglass-half"></i> Sedang Diverifikasi BDM
                                </span>
                            @elseif($collaboration->presales_status === 'revision_required')
                                <button onclick="openModal('presalesModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200 transition">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Ulang Revisi
                                </button>
                            @elseif($collaboration->presales_status === 'approved_by_bdm')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-check-double"></i> Berkas Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 2. SOLUTION ARCHITECT CARD -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between relative transition hover:border-slate-300">
                    
                    <div>
                        <!-- Header & Status Badge -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Solution Architect</span>
                                <h4 class="text-base font-bold text-slate-900">{{ $collaboration->sa_name }}</h4>
                            </div>

                            <!-- Dynamic SA Status Badge -->
                            <div>
                                @if($collaboration->sa_status === 'pending_upload')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Menunggu Desain Topologi
                                    </span>
                                @elseif($collaboration->sa_status === 'submitted_to_bdm')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 pulse-subtle">
                                        <i class="fa-solid fa-spinner fa-spin text-[10px]"></i> Sedang Diverifikasi BDM
                                    </span>
                                @elseif($collaboration->sa_status === 'revision_required')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Perlu Revisi Topologi
                                    </span>
                                @elseif($collaboration->sa_status === 'approved_by_bdm')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-circle-check"></i> Disetujui BDM
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-xs text-slate-500 mb-3">Tanggung Jawab: <span class="font-medium text-slate-700">Desain Arsitektur & Topologi Solusi.</span></p>

                        <!-- Assignment Instruction Box -->
                        <div class="bg-amber-50/50 border border-amber-200/80 rounded-lg p-3.5 text-xs text-amber-900 mb-4">
                            <p class="italic font-medium">"{{ $collaboration->sa_instructions }}"</p>
                            <span class="block mt-2 text-[11px] text-amber-700/80">
                                Ditugaskan: {{ $collaboration->assigned_at ? $collaboration->assigned_at->format('d M Y H:i') : '24 Sep 2026 09:47' }} (oleh {{ $collaboration->assigned_by }})
                            </span>
                        </div>

                        <!-- Uploaded File Preview if any -->
                        @if($collaboration->sa_file_name)
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-4 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2.5 truncate">
                                    <i class="fa-solid fa-diagram-project text-indigo-600 text-lg"></i>
                                    <div class="truncate">
                                        <div class="font-semibold text-slate-800 truncate">{{ $collaboration->sa_file_name }}</div>
                                        <span class="text-[10px] text-slate-500">Disubmit: {{ $collaboration->sa_submitted_at ? $collaboration->sa_submitted_at->format('H:i') : 'Baru saja' }}</span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">Ready</span>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action & Bottom Status Indicator -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="openModal('assignmentModal')" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Ubah Penugasan
                        </button>

                        <div class="flex items-center gap-2">
                            <!-- Bottom Status Button / Badge -->
                            @if($collaboration->sa_status === 'pending_upload')
                                <button onclick="openModal('saModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300 hover:bg-amber-100 transition shadow-sm">
                                    <i class="fa-regular fa-clock"></i> Upload Desain Arsitek
                                </button>
                            @elseif($collaboration->sa_status === 'submitted_to_bdm')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <i class="fa-solid fa-hourglass-half"></i> Sedang Diverifikasi BDM
                                </span>
                            @elseif($collaboration->sa_status === 'revision_required')
                                <button onclick="openModal('saModal')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200 transition">
                                    <i class="fa-solid fa-cloud-arrow-up"></i> Upload Ulang Topologi
                                </button>
                            @elseif($collaboration->sa_status === 'approved_by_bdm')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-check-double"></i> Desain Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAHAP 2: GERBANG VERIFIKASI BDM REVIEWER -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Gerbang Verifikasi BDM Reviewer (Quality Gate)</h3>
                        <p class="text-xs text-slate-500">
                            PIC Reviewer ditunjuk oleh Raiza: <strong class="text-indigo-900">{{ $collaboration->assigned_bdm_reviewer }}</strong> ({{ $collaboration->assigned_bdm_email }})
                        </p>
                    </div>
                </div>

                <!-- BDM Stage Status Badge -->
                <div>
                    @if($collaboration->bdm_status === 'pending_submission')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <i class="fa-regular fa-clock"></i> Menunggu Berkas Tim Teknis
                        </span>
                    @elseif($collaboration->bdm_status === 'under_review')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 border border-sky-300 pulse-subtle">
                            <i class="fa-solid fa-spinner fa-spin text-sky-600"></i> Sedang Diverifikasi BDM
                        </span>
                    @elseif($collaboration->bdm_status === 'revision_needed')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                            <i class="fa-solid fa-circle-xmark text-rose-600"></i> Memerlukan Revisi Teknis
                        </span>
                    @elseif($collaboration->bdm_status === 'approved')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui BDM & Rilis ke Sales
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <!-- If Still Waiting for Presales / SA Upload -->
                @if($collaboration->bdm_status === 'pending_submission')
                    <div class="text-center py-8 px-4 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-hourglass-start text-xl"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">Menunggu Berkas dari Akbar & Aris Sadewo</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mb-4">
                            Saat ini Akbar (Pre-Sales) dan Aris Sadewo (SA) sedang menyusun dokumen. Tombol verifikasi BDM akan otomatis aktif begitu tim mengunggah berkas.
                        </p>
                        <div class="flex items-center justify-center gap-3">
                            <button onclick="openModal('presalesModal')" class="text-xs font-semibold px-4 py-2 rounded-lg bg-sky-600 text-white hover:bg-sky-700 shadow-sm transition">
                                <i class="fa-solid fa-upload mr-1"></i> Simulasi Upload Pre-Sales
                            </button>
                            <button onclick="openModal('saModal')" class="text-xs font-semibold px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                                <i class="fa-solid fa-upload mr-1"></i> Simulasi Upload SA
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Review Summary Deliverables -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Left: Presales Deliverable Review -->
                        <div class="p-4 rounded-xl border {{ $collaboration->presales_file_name ? 'border-sky-200 bg-sky-50/40' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-invoice text-sky-600"></i> Dokumen Pre-Sales (Proposal & BoQ)
                                </span>
                                @if($collaboration->presales_file_name)
                                    <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded">Telah Disubmit</span>
                                @else
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-200 px-2 py-0.5 rounded">Belum Upload</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mb-2">
                                {{ $collaboration->presales_notes ?: 'Belum ada catatan dari Pre-Sales Specialist.' }}
                            </p>
                            @if($collaboration->presales_file_name)
                                <div class="text-[11px] font-semibold text-sky-700 flex items-center gap-1">
                                    <i class="fa-solid fa-paperclip"></i> {{ $collaboration->presales_file_name }}
                                </div>
                            @endif
                        </div>

                        <!-- Right: SA Deliverable Review -->
                        <div class="p-4 rounded-xl border {{ $collaboration->sa_file_name ? 'border-indigo-200 bg-indigo-50/40' : 'border-slate-200 bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-network-wired text-indigo-600"></i> Dokumen SA (Topologi & Arsitektur)
                                </span>
                                @if($collaboration->sa_file_name)
                                    <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">Telah Disubmit</span>
                                @else
                                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-200 px-2 py-0.5 rounded">Belum Upload</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 mb-2">
                                {{ $collaboration->sa_notes ?: 'Belum ada catatan dari Solution Architect.' }}
                            </p>
                            @if($collaboration->sa_file_name)
                                <div class="text-[11px] font-semibold text-indigo-700 flex items-center gap-1">
                                    <i class="fa-solid fa-paperclip"></i> {{ $collaboration->sa_file_name }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- BDM Feedback / Status Note (if already reviewed) -->
                    @if($collaboration->bdm_review_notes)
                        <div class="mb-6 p-4 rounded-xl border {{ $collaboration->bdm_status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                            <div class="flex items-center justify-between text-xs font-bold mb-1">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid {{ $collaboration->bdm_status === 'approved' ? 'fa-circle-check text-emerald-600' : 'fa-triangle-exclamation text-rose-600' }}"></i>
                                    Catatan Evaluasi BDM (oleh {{ $collaboration->bdm_reviewed_by ?: $collaboration->assigned_bdm_reviewer }})
                                </span>
                                <span class="text-[10px] opacity-75">{{ $collaboration->bdm_reviewed_at ? $collaboration->bdm_reviewed_at->format('d M Y H:i') : '' }}</span>
                            </div>
                            <p class="text-xs">{{ $collaboration->bdm_review_notes }}</p>
                        </div>
                    @endif

                    <!-- BDM Decision Action Form -->
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-gavel text-indigo-600"></i> Panel Aksi Keputusan BDM
                        </h4>

                        <form action="{{ route('collaboration.bdmAction', $collaboration->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="current_role" value="{{ $activeRole }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Verifikator BDM (Sesuai Akun Login):</label>
                                    <input type="text" name="bdm_name" value="{{ $collaboration->assigned_bdm_reviewer }}" class="w-full rounded-lg border-slate-300 text-xs p-2.5 border bg-white font-medium text-slate-800" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jika Minta Revisi, targetkan ke:</label>
                                    <select name="revision_target" class="w-full text-xs rounded-lg border-slate-300 border p-2.5 bg-white text-slate-700">
                                        <option value="both">Keduanya (Presales & SA)</option>
                                        <option value="presales">Khusus Pre-Sales (BoQ/Harga)</option>
                                        <option value="sa">Khusus Solution Architect (Topologi)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Evaluasi / Rekomendasi BDM:</label>
                                <textarea name="bdm_notes" rows="2" class="w-full rounded-lg border-slate-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-2.5 border" placeholder="Contoh: Dokumen lengkap dan estimasi BoQ sudah sesuai dengan margin target perusahaan. Siap diajukan ke Raiza (Sales).">{{ old('bdm_notes', $collaboration->bdm_review_notes) }}</textarea>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                                <!-- Reject / Request Revision Button -->
                                <button type="submit" name="action" value="revision" class="px-4 py-2.5 rounded-lg text-xs font-bold bg-white text-rose-700 border border-rose-300 hover:bg-rose-50 shadow-sm transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-rotate-left"></i> Tolak & Minta Revisi
                                </button>

                                <!-- Approve & Release Button -->
                                <button type="submit" name="action" value="approve" class="px-5 py-2.5 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Approve (Setujui & Rilis ke Raiza)
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAHAP 3: DASHBOARD SALES (RAIZA - ACCOUNT EXECUTIVE) -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Dashboard Sales Owner (Raiza - Account Executive)</h3>
                        <p class="text-xs text-slate-500">Proposal teknis & BoQ resmi siap digunakan setelah lulus verifikasi BDM.</p>
                    </div>
                </div>

                <!-- Sales Status Indicator Badge -->
                <div>
                    @if($collaboration->sales_status === 'locked_waiting_bdm')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300">
                            <i class="fa-solid fa-lock text-slate-400"></i> Terkunci (Menunggu BDM {{ $collaboration->assigned_bdm_reviewer }})
                        </span>
                    @elseif($collaboration->sales_status === 'ready_for_sales')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <i class="fa-solid fa-lock-open text-emerald-600"></i> Dokumen Terbuka - Siap Dikirim ke Klien
                        </span>
                    @elseif($collaboration->sales_status === 'sent_to_client')
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                            <i class="fa-solid fa-paper-plane text-blue-600"></i> Sudah Resmi Dikirimkan ke Klien
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                @if($collaboration->sales_status === 'locked_waiting_bdm')
                    <!-- LOCKED STATE VIEW FOR RAIZA (SALES) -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-slate-200 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-lock text-xl"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">Paket Dokumen Proposal Belum Dapat Dirilis</h4>
                        <p class="text-xs text-slate-500 max-w-lg mx-auto mb-4">
                            Halo <strong>Raiza</strong>, berkas teknis dari Akbar dan Aris Sadewo harus melalui verifikasi <strong>{{ $collaboration->assigned_bdm_reviewer }}</strong> terlebih dahulu untuk memastikan validasi margin & arsitektur sebelum Anda serahkan ke klien.
                        </p>
                        
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-medium">
                            <i class="fa-solid fa-circle-info text-sky-600"></i> Status Saat Ini: 
                            <strong class="font-bold">
                                @if($collaboration->bdm_status === 'under_review') 
                                    🟡 Sedang Diverifikasi oleh {{ $collaboration->assigned_bdm_reviewer }}
                                @elseif($collaboration->bdm_status === 'revision_needed') 
                                    🔴 BDM Meminta Revisi Teknis ke Presales/SA
                                @else 
                                    ⏳ Menunggu Akbar & Aris Sadewo Upload Dokumen
                                @endif
                            </strong>
                        </div>
                    </div>
                @else
                    <!-- UNLOCKED / APPROVED STATE FOR RAIZA (SALES) -->
                    <div class="space-y-6">
                        
                        <!-- Official BDM Verification Certificate / Stamp -->
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl font-bold border border-white/30">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-[11px] font-extrabold tracking-wider uppercase px-2 py-0.5 rounded bg-emerald-900/40 border border-white/20">
                                            BDM Quality Assurance Passed
                                        </span>
                                    </div>
                                    <h4 class="text-base font-extrabold">Paket Proposal & BoQ Resmi Disetujui BDM</h4>
                                    <p class="text-xs text-emerald-100">
                                        Diverifikasi oleh <strong>{{ $collaboration->bdm_reviewed_by ?: $collaboration->assigned_bdm_reviewer }}</strong> pada {{ $collaboration->bdm_reviewed_at ? $collaboration->bdm_reviewed_at->format('d M Y H:i') : '' }} WIB.
                                    </p>
                                </div>
                            </div>

                            @if($collaboration->sales_status !== 'sent_to_client')
                                <form action="{{ route('collaboration.salesSend', $collaboration->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-white text-emerald-900 font-bold text-xs hover:bg-emerald-50 shadow-md transition flex items-center gap-2">
                                        <i class="fa-solid fa-paper-plane text-emerald-700"></i> Finalize & Kirim ke Klien
                                    </button>
                                </form>
                            @else
                                <div class="bg-white/20 px-4 py-2 rounded-xl border border-white/30 text-xs font-bold flex items-center gap-2">
                                    <i class="fa-solid fa-check"></i> Sudah Dikirim ke Klien ({{ $collaboration->sales_delivered_at ? $collaboration->sales_delivered_at->format('d M Y H:i') : '' }})
                                </div>
                            @endif
                        </div>

                        <!-- Deliverables Ready for Sales -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- File 1: Proposal & BoQ -->
                            <div class="bg-slate-50 p-4.5 rounded-xl border border-slate-200 flex items-center justify-between shadow-sm hover:bg-white transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">{{ $collaboration->presales_file_name ?: 'Proposal_Teknis_BoQ_Final.pdf' }}</div>
                                        <span class="text-[11px] text-slate-500">Penyusun: Akbar (Pre-Sales Specialist)</span>
                                    </div>
                                </div>
                                <button type="button" class="text-xs px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-download"></i> Unduh
                                </button>
                            </div>

                            <!-- File 2: Topology Design -->
                            <div class="bg-slate-50 p-4.5 rounded-xl border border-slate-200 flex items-center justify-between shadow-sm hover:bg-white transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-diagram-project"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">{{ $collaboration->sa_file_name ?: 'High_Level_Design_Topology.pdf' }}</div>
                                        <span class="text-[11px] text-slate-500">Penyusun: Aris Sadewo (Solution Architect)</span>
                                    </div>
                                </div>
                                <button type="button" class="text-xs px-3.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-download"></i> Unduh
                                </button>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- ========================================================================= -->
    <!-- MODAL: UBAH PENUGASAN TIM & REVIEWER BDM (OLEH SALES / RAIZA) -->
    <!-- ========================================================================= -->
    <div id="assignmentModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users-gear text-indigo-600"></i> Ubah Penugasan Tim Teknis & Reviewer BDM
                    </h3>
                    <span class="text-[11px] text-slate-500">Dikelola oleh Sales Owner: <strong>Raiza (Account Executive)</strong></span>
                </div>
                <button onclick="closeModal('assignmentModal')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form action="{{ route('collaboration.updateAssignment', $collaboration->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="current_role" value="{{ $activeRole }}">

                <!-- 1. BDM Reviewer Selector -->
                <div class="bg-indigo-50/50 border border-indigo-200/80 rounded-xl p-4">
                    <label class="block text-xs font-bold text-indigo-950 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-shield text-indigo-600"></i> Pilih PIC BDM Reviewer:
                    </label>
                    <select name="assigned_bdm_reviewer" class="w-full text-xs font-medium rounded-lg border-slate-300 border p-2.5 bg-white text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($bdmList as $bdm)
                            <option value="{{ $bdm['name'] }} ({{ $bdm['role'] }})" {{ str_contains($collaboration->assigned_bdm_reviewer, $bdm['name']) ? 'selected' : '' }}>
                                {{ $bdm['name'] }} &bull; {{ $bdm['role'] }} ({{ $bdm['email'] }})
                            </option>
                        @endforeach
                    </select>
                    <span class="block mt-1 text-[11px] text-indigo-700/80">BDM yang dipilih akan bertugas memverifikasi dokumen sebelum dirilis ke Sales.</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Presales Specialist -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <label class="block text-xs font-bold text-slate-800">Pre-Sales Specialist:</label>
                        <input type="text" name="presales_name" value="{{ $collaboration->presales_name }}" class="w-full text-xs rounded-lg border-slate-300 border p-2 bg-white" required>
                        <label class="block text-[11px] font-semibold text-slate-600">Instruksi Pre-Sales:</label>
                        <textarea name="presales_instructions" rows="2" class="w-full text-xs rounded-lg border-slate-300 border p-2 bg-white">{{ $collaboration->presales_instructions }}</textarea>
                    </div>

                    <!-- Solution Architect -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <label class="block text-xs font-bold text-slate-800">Solution Architect:</label>
                        <input type="text" name="sa_name" value="{{ $collaboration->sa_name }}" class="w-full text-xs rounded-lg border-slate-300 border p-2 bg-white" required>
                        <label class="block text-[11px] font-semibold text-slate-600">Instruksi SA:</label>
                        <textarea name="sa_instructions" rows="2" class="w-full text-xs rounded-lg border-slate-300 border p-2 bg-white">{{ $collaboration->sa_instructions }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('assignmentModal')" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                        <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: UPLOAD PRE-SALES (AKBAR) -->
    <!-- ========================================================================= -->
    <div id="presalesModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-sky-600"></i> Upload Proposal Teknis & BoQ (Pre-Sales)
                </h3>
                <button onclick="closeModal('presalesModal')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form action="{{ route('collaboration.presalesUpload', $collaboration->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="current_role" value="{{ $activeRole }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pre-Sales untuk BDM Reviewer ({{ $collaboration->assigned_bdm_reviewer }}):</label>
                    <textarea name="presales_notes" rows="3" class="w-full rounded-lg border-slate-300 text-xs focus:ring-sky-500 focus:border-sky-500 p-2.5 border" placeholder="Jelaskan ringkasan proposal dan BoQ yang diajukan...">Proposal Teknis & BoQ v1.0 sudah disesuaikan dengan kebutuhan PT Bank Nusantara Digital Tbk.</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">File Proposal / BoQ (PDF / Excel / Docx):</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-sky-400 transition bg-slate-50/50">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-2"></i>
                        <p class="text-xs text-slate-600">Pilih file proposal atau gunakan simulasi default</p>
                        <input type="file" name="presales_file" class="text-xs text-slate-500 mt-2 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                </div>

                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
                    <span>Setelah di-submit, status akan otomatis berubah menjadi <strong>"Sedang Diverifikasi BDM"</strong>.</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('presalesModal')" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-xs font-bold bg-sky-600 text-white hover:bg-sky-700 shadow-sm transition">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Submit ke BDM
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: UPLOAD SOLUTION ARCHITECT (ARIS SADEWO) -->
    <!-- ========================================================================= -->
    <div id="saModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-indigo-600"></i> Upload Desain Topologi & Arsitektur (SA)
                </h3>
                <button onclick="closeModal('saModal')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form action="{{ route('collaboration.saUpload', $collaboration->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="current_role" value="{{ $activeRole }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Arsitektur untuk BDM Reviewer ({{ $collaboration->assigned_bdm_reviewer }}):</label>
                    <textarea name="sa_notes" rows="3" class="w-full rounded-lg border-slate-300 text-xs focus:ring-indigo-500 focus:border-indigo-500 p-2.5 border" placeholder="Jelaskan ringkasan desain topologi arsitektur...">Desain High Availability Multi-Region dengan Microservices Architecture & sizing kalkulasi 20.000 TPS.</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">File Diagram / Topologi (PDF / Image / Visio):</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-400 transition bg-slate-50/50">
                        <i class="fa-solid fa-diagram-project text-2xl text-slate-400 mb-2"></i>
                        <p class="text-xs text-slate-600">Pilih file diagram arsitektur atau gunakan simulasi default</p>
                        <input type="file" name="sa_file" class="text-xs text-slate-500 mt-2 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="bg-amber-50 p-3 rounded-lg border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
                    <span>Setelah di-submit, status akan otomatis berubah menjadi <strong>"Sedang Diverifikasi BDM"</strong>.</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal('saModal')" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition">
                        <i class="fa-solid fa-paper-plane mr-1"></i> Submit ke BDM
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript Modal Helper -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</body>
</html>
