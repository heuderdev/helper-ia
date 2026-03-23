<div class="min-h-screen bg-slate-950 text-slate-50 flex items-center justify-center">
    <div
        class="w-full max-w-6xl h-[90vh] bg-slate-900/80 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden flex">

        {{-- Sidebar --}}
        <aside
            class="hidden md:flex w-64 bg-gradient-to-b from-slate-950 to-slate-900 border-r border-slate-800 flex-col p-5">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="h-10 w-10 rounded-2xl bg-blue-500/10 flex items-center justify-center border border-blue-500/40">
                    <span class="text-xl">👨‍💻</span>
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-100">AI Dev Senior</div>
                    <div class="text-[11px] text-slate-400">PHP • Laravel • Joomla • JS • SQL</div>
                </div>
            </div>

            <div class="space-y-3 text-xs text-slate-300">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Plano mensal</span>
                    <span
                        class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/40">
                        R$ 100,00
                    </span>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-400">Gasto atual</span>
                        <span class="font-mono text-xs text-yellow-300">
                            R$ {{ number_format($totalCost, 2) }}
                        </span>
                    </div>
                    <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-400 via-blue-500 to-purple-500 transition-all duration-700"
                            style="width: {{ min(($totalCost / 100) * 100, 100) }}%">
                        </div>
                    </div>
                    <div class="flex justify-between mt-1 text-[11px] text-slate-500">
                        <span>0</span>
                        <span>R$ 100</span>
                    </div>
                </div>

                <div class="mt-4 space-y-1 text-[11px] text-slate-400">
                    <div class="flex items-center justify-between">
                        <span>Modelo</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-200 border border-slate-700">
                            Gemini 1.5 Flash
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Limite de uso</span>
                        <span>10 mensagens / minuto</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Visão / Imagens</span>
                        <span class="text-emerald-300">Ativado</span>
                    </div>
                </div>
            </div>

            <div class="mt-auto pt-4 text-[11px] text-slate-500 border-t border-slate-800">
                Feito para acelerar sua rotina de dev, não para “brincar de chat”.
            </div>
        </aside>

        {{-- Main --}}
        <section class="flex-1 flex flex-col bg-slate-950/80">
            {{-- Header topo --}}
            <header class="h-14 border-b border-slate-800 flex items-center justify-between px-5">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-slate-300">Sessão ativa</span>
                    <span class="text-[11px] text-slate-500">| Gemini 1.5 Flash</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Msg atual: <span class="font-mono text-emerald-300">R$ {{ number_format($thisMsgCost, 4) }}</span>
                </div>
            </header>

            {{-- Mensagens --}}
            <div class="flex-1 overflow-y-auto px-5 py-4" x-data="{ messages: @entangle('messages') }">

                <template x-if="messages.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-center text-slate-500">
                        <p class="text-sm mb-2">Comece perguntando algo direto.</p>
                        <p class="text-xs">Ex: “Cria uma migration multi-tenant com soft deletes e índice composto”</p>
                    </div>
                </template>

                <div class="space-y-3" x-show="messages.length > 0">
                    <template x-for="(msg, index) in messages" :key="index">
                        <div class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                            <div class="max-w-[80%] flex flex-col gap-1"
                                :class="msg.role === 'user' ? 'items-end' : 'items-start'">

                                <div class="text-[10px] uppercase tracking-wide text-slate-500"
                                    x-text="msg.role === 'user' ? 'Você' : 'AI Dev Senior'"></div>

                                <div class="rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-md" :class="msg.role === 'user'
                                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-slate-50 rounded-br-sm'
                                        : 'bg-slate-800/90 text-slate-100 border border-slate-700 rounded-bl-sm'">

                                    <template x-if="msg.image && msg.role === 'user'">
                                        <div class="mb-2">
                                            <img :src="msg.image"
                                                class="w-56 max-h-56 object-cover rounded-xl border border-slate-700 shadow" />
                                        </div>
                                    </template>

                                    <div
                                        x-html="msg.content
                                            ?.replace(/\n/g, '<br>')
                                            ?.replace(/```(\w+)?\n([\s\S]*?)```/g, '<pre class=&quot;bg-slate-900 border border-slate-700 p-3 rounded-xl mt-2 text-xs overflow-x-auto&quot;><code>$2</code></pre>')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Input / Upload --}}
            <footer class="border-t border-slate-800 bg-slate-950/95 px-5 py-3 space-y-2">
                {{-- Upload --}}
                <div class="flex items-center gap-3">
                    @if($image)
                    <div class="flex items-center gap-2 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2">
                        <img src="{{ $image->temporaryUrl() }}"
                            class="w-10 h-10 object-cover rounded-lg border border-slate-600" />
                        <div class="text-[11px]">
                            <div class="font-mono truncate max-w-[140px] text-slate-200">
                                {{ $image->getClientOriginalName() }}
                            </div>
                            <div class="text-slate-500">
                                {{ number_format($image->getSize() / 1024, 1) }} KB
                            </div>
                        </div>
                        <button wire:click="removeImage"
                            class="ml-2 text-[11px] text-red-300 hover:text-red-200 px-2 py-1 rounded-lg bg-red-500/10 border border-red-500/30">
                            Remover
                        </button>
                    </div>
                    @else
                    <label
                        class="inline-flex items-center gap-2 text-[11px] text-slate-400 cursor-pointer bg-slate-900/80 border border-dashed border-slate-700 hover:border-blue-500 hover:text-blue-300 px-3 py-2 rounded-xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Adicionar print / screenshot (até 5MB)</span>
                        <input type="file" wire:model="image" accept="image/*,.pdf" class="hidden" />
                    </label>
                    @endif
                </div>

                {{-- Input --}}
                <form wire:submit="sendPrompt" class="flex items-center gap-2">
                    <div class="flex-1 relative">
                        <textarea wire:model.live.debounce.400ms="prompt" rows="1"
                            class="w-full resize-none bg-slate-900 text-slate-50 text-sm rounded-2xl border border-slate-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 px-4 py-3 pr-10 placeholder-slate-500 outline-none"
                            placeholder="Pergunte algo objetivo. Ex: 'Crie seeder para popular tabela de permissões padrão.'"
                            maxlength="2000"></textarea>
                        <div class="absolute right-3 top-2.5 text-[10px] text-slate-500">
                            {{ mb_strlen($prompt) }}/2000
                        </div>
                    </div>

                    <button type="submit"
                        class="h-11 px-5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-sm font-semibold flex items-center gap-2 shadow-lg hover:shadow-blue-500/30 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Enviar</span>
                        <span wire:loading>Gerando...</span>
                        <svg wire:loading.remove class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </form>

                <div class="flex justify-between text-[10px] text-slate-500 mt-1">
                    <span>Gemini 1.5 Flash • Visão ativada • Conversa salva</span>
                    <span>Limite: 10 mensagens/minuto</span>
                </div>
            </footer>
        </section>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', () => {
            const container = document.querySelector('[x-data*="messages"]');
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@endpush