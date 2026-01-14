@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-white">Audit Trail System</h2>
            <a href="/api/export-xml"
                class="bg-slate-800 hover:bg-indigo-600 border border-slate-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all flex items-center space-x-2">
                <span>📥</span> <span>Export Full XML</span>
            </a>
        </div>

        <div class="bg-[#1e293b] rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-900/50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">DB Amount</th>
                        <th class="px-6 py-4">Status Integritas</th>
                        <th class="px-6 py-4 text-center">Certificate</th> {{-- Kolom Baru --}}
                        <th class="px-6 py-4 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach ($reports as $report)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="px-6 py-4 text-indigo-400 font-mono font-bold">{{ $report['invoice'] }}</td>
                            <td class="px-6 py-4 text-white">Rp {{ number_format($report['db_total'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                {{-- Status Label --}}
                                @if ($report['integrity_status'] === 'SECURE')
                                    <span class="text-emerald-500 text-[10px] font-black italic">✓ SECURE</span>
                                @else
                                    <span class="text-rose-500 text-[10px] font-black animate-pulse">⚠️ TAMPERED</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{-- Tombol Download Asli --}}
                                <a href="/api/download-snapshot/{{ $report['id_order'] }}"
                                    class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-md text-[10px] border border-slate-700 transition">
                                    <span>⬇️ Original XML</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 text-[11px] font-mono">{{ $report['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bg-slate-900/50 border border-slate-700 p-6 rounded-2xl mb-8">
            <h3 class="text-sm font-bold text-slate-400 mb-4 uppercase tracking-widest">Verify External XML Snapshot</h3>
            <form id="verifyForm" class="flex items-center space-x-4">
                <input type="file" id="xml_file" accept=".xml"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                <button type="button" onclick="uploadAndVerify()"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-full font-bold transition-all text-sm shrink-0">
                    Cek Integritas
                </button>
            </form>
        </div>
    </div>

    <script>
        async function uploadAndVerify() {
            const fileInput = document.getElementById('xml_file');
            if (!fileInput.files[0]) return alert('Pilih file XML dulu, King!');

            const formData = new FormData();
            formData.append('xml_file', fileInput.files[0]);

            const resp = await fetch('/api/verify-xml', {
                method: 'POST',
                body: formData
            });

            const result = await resp.json();
            alert(result.message);
        }
    </script>
@endsection
