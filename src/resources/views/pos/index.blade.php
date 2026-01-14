@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-8" x-data="posSystem()">
    <div class="col-span-8">
        <h2 class="text-2xl font-bold mb-6 text-white">Produk Tersedia</h2>
        <div class="grid grid-cols-3 gap-6">
            @foreach($products as $product)
            <div class="bg-[#1e293b] p-5 rounded-2xl border border-slate-700/50 hover:border-indigo-500/50 hover:bg-slate-800 transition-all cursor-pointer group" 
                 @click="addToCart({{ json_encode($product) }})">
                <div class="w-full h-32 bg-slate-900 rounded-xl mb-4 flex items-center justify-center text-4xl group-hover:scale-105 transition">📦</div>
                <h3 class="font-bold text-white group-hover:text-indigo-400 transition">{{ $product->name }}</h3>
                <p class="text-slate-500 text-xs mb-3 font-mono">{{ $product->sku }}</p>
                <div class="flex justify-between items-center mt-auto">
                    <span class="text-emerald-400 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="text-[10px] bg-slate-900 text-slate-400 px-2 py-1 rounded-md uppercase">Stok: {{ $product->stock }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="col-span-4">
        <div class="bg-[#1e293b] rounded-3xl border border-slate-700 p-6 sticky top-24 shadow-2xl">
            <h2 class="text-lg font-bold mb-6 text-white flex items-center justify-between">
                <span>Keranjang</span>
                <span class="bg-indigo-500 text-[10px] px-2 py-0.5 rounded-full" x-text="cart.length"></span>
            </h2>
            
            <div class="space-y-4 mb-6 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                <template x-for="item in cart" :key="item.sku">
                    <div class="flex justify-between items-center bg-slate-900/50 p-3 rounded-xl border border-slate-800">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-white" x-text="item.name"></div>
                            <div class="text-[10px] text-emerald-500 font-bold" x-text="'Rp ' + formatNumber(item.price)"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="decreaseQty(item.sku)" class="w-7 h-7 bg-slate-800 hover:bg-rose-500/20 hover:text-rose-500 border border-slate-700 rounded-lg text-white transition-colors">-</button>
                            <span class="text-sm font-bold w-6 text-center text-white" x-text="item.qty"></span>
                            <button @click="increaseQty(item.sku)" class="w-7 h-7 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-white transition-transform active:scale-90">+</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="border-t border-slate-700 pt-4 space-y-3">
                <div class="flex justify-between font-black text-2xl text-white pt-2">
                    <span>TOTAL</span>
                    <span class="text-indigo-400" x-text="'Rp ' + formatNumber(total)"></span>
                </div>
            </div>

            <button @click="checkout()" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl mt-8 transition-all active:scale-95 shadow-lg shadow-indigo-500/20 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="cart.length === 0 || loading">
                <span x-show="!loading">PROSES TRANSAKSI</span>
                <span x-show="loading">MEMPROSES...</span>
            </button>
        </div>
    </div>

    <div x-show="showSuccessModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        
        <div class="bg-[#1e293b] border border-slate-700 w-full max-w-sm rounded-3xl p-8 text-center shadow-2xl">
            <div class="w-20 h-20 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-500/20 text-4xl">
                ✓
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Berhasil!</h3>
            <p class="text-slate-400 mb-8" x-text="successMessage"></p>
            <button @click="showSuccessModal = false; location.reload()" 
                    class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition-colors border border-slate-700">
                Selesai
            </button>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        cart: [],
        total: 0,
        loading: false,
        showSuccessModal: false,
        successMessage: '',

        addToCart(product) {
            let item = this.cart.find(i => i.sku === product.sku);
            if (item) {
                item.qty++;
            } else {
                this.cart.push({ ...product, qty: 1 });
            }
            this.calculateTotal();
        },

        increaseQty(sku) {
            let item = this.cart.find(i => i.sku === sku);
            if (item) {
                item.qty++;
                this.calculateTotal();
            }
        },

        decreaseQty(sku) {
            let item = this.cart.find(i => i.sku === sku);
            if (item) {
                if (item.qty > 1) {
                    item.qty--;
                } else {
                    this.cart = this.cart.filter(i => i.sku !== sku);
                }
                this.calculateTotal();
            }
        },

        calculateTotal() {
            this.total = this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        async checkout() {
            this.loading = true;
            try {
                const resp = await fetch('/api/checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payment_method: 'cash',
                        items: this.cart
                    })
                });
                
                const data = await resp.json();
                
                if(data.status === 'success') {
                    this.successMessage = `Transaksi ${data.data.invoice} telah diamankan dengan XML Audit Trail.`;
                    this.showSuccessModal = true;
                    this.cart = [];
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection