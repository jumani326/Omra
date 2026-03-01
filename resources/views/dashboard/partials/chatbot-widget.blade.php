{{-- Widget chatbot fixe en bas à droite (espace client) --}}
<div id="chatbot-widget" class="fixed bottom-6 right-6 z-50" x-data="chatbotWidget()">
    <div x-show="open" x-transition class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden flex flex-col" style="width: 380px; height: 480px;">
        <div class="bg-primary-green text-white px-4 py-3 flex items-center justify-between">
            <span class="font-semibold">Assistant Omra</span>
            <button @click="open = false" class="text-white hover:bg-white/20 rounded p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="messagesContainer">
            <template x-if="!hasPilgrim">
                <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3">
                    Choisissez d'abord un forfait pour activer l'assistant et poser vos questions (visa, paiement, documents).
                </div>
            </template>
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user' ? 'bg-primary-green text-white rounded-lg rounded-br-none px-3 py-2 max-w-[85%]' : 'bg-white border border-gray-200 rounded-lg rounded-bl-none px-3 py-2 max-w-[85%] shadow-sm'">
                        <p class="text-sm" x-text="msg.content"></p>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="bg-white border border-gray-200 rounded-lg rounded-bl-none px-3 py-2 text-gray-500 text-sm">En train d'écrire...</div>
            </div>
        </div>
        <div class="p-3 border-t bg-white" x-show="hasPilgrim">
            <form @submit.prevent="send" class="flex gap-2">
                <input type="text" x-model="input" placeholder="Votre message..." class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-green focus:ring-primary-green text-sm py-2" :disabled="loading">
                <button type="submit" class="bg-primary-green text-white px-4 py-2 rounded-lg hover:bg-dark-green transition text-sm font-medium" :disabled="loading || !input.trim()">
                    Envoyer
                </button>
            </form>
        </div>
    </div>
    <button @click="open = !open" class="w-14 h-14 rounded-full bg-primary-green text-white shadow-lg hover:bg-dark-green transition flex items-center justify-center">
        <svg x-show="!open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        <svg x-show="open" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>
</div>

<script>
function chatbotWidget() {
    return {
        open: false,
        messages: [],
        input: '',
        loading: false,
        hasPilgrim: true,
        init() {
            this.fetchSession();
        },
        fetchSession() {
            fetch('{{ route("chatbot.session") }}', { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    this.hasPilgrim = data.has_pilgrim;
                    this.messages = data.messages || [];
                    this.$nextTick(() => this.scrollBottom());
                });
        },
        send() {
            if (!this.input.trim() || this.loading || !this.hasPilgrim) return;
            const content = this.input.trim();
            this.input = '';
            this.messages.push({ role: 'user', content });
            this.scrollBottom();
            this.loading = true;
            const formData = new FormData();
            formData.append('content', content);
            formData.append('_token', '{{ csrf_token() }}');
            fetch('{{ route("chatbot.message") }}', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.error) {
                        this.messages.push({ role: 'assistant', content: data.error });
                    } else {
                        this.messages.push({ role: 'assistant', content: data.message });
                    }
                    this.$nextTick(() => this.scrollBottom());
                })
                .catch(() => this.messages.push({ role: 'assistant', content: 'Erreur de connexion. Réessayez.' }))
                .finally(() => { this.loading = false; });
        },
        scrollBottom() {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTop = el.scrollHeight;
        }
    };
}
</script>
