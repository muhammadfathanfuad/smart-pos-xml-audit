<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#0f172a] text-slate-200 font-sans antialiased">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-[#1e293b] border-r border-slate-700/50 p-6 hidden md:block">
            <div class="flex items-center space-x-3 mb-10">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center font-bold">S</div>
                <h1 class="text-xl font-bold tracking-tight text-white">Smart<span class="text-indigo-400">POS</span></h1>
            </div>
            <nav class="space-y-2">
                <a href="/pos" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-700/50 transition-all text-slate-400 hover:text-white group">
                    <span class="group-hover:scale-110 transition">🛒</span> <span class="font-medium">Kasir</span>
                </a>
                <a href="/report" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-700/50 transition-all text-slate-400 hover:text-white group">
                    <span class="group-hover:scale-110 transition">📊</span> <span class="font-medium">Audit System</span>
                </a>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col">
            <header class="bg-[#0f172a]/80 backdrop-blur-md border-b border-slate-800 p-4 flex justify-between items-center px-8 sticky top-0 z-10">
                <div class="text-sm">
                    <span class="text-slate-500">Operator:</span> 
                    <span class="font-semibold text-indigo-400">Admin</span>
                </div>
                <div class="flex items-center space-x-3 bg-slate-900/50 px-3 py-1.5 rounded-full border border-slate-700">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-500 tracking-wider">XML PROTECTED</span>
                </div>
            </header>
            
            <div class="p-8 flex-1">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>