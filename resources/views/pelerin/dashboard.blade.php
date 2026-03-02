@extends('layouts.app')

@section('page-title', 'Mon espace')
@section('page-description', 'Suivez votre dossier et votre voyage Omra.')

@section('content')
<div class="space-y-6">
    {{-- Carte Live Stream : fond gris clair, texte gris, barre de progression gris foncé, icône minus en haut à droite, plein écran en bas à droite --}}
    <div class="relative rounded-2xl overflow-hidden bg-gray-200 min-h-[200px]">
        <div class="absolute top-3 left-4 px-2 py-1 rounded bg-gray-600 text-white text-xs font-semibold uppercase tracking-wide">Live stream : Makkah</div>
        <button class="absolute top-3 right-4 p-1.5 rounded text-gray-600 hover:bg-gray-300/80" aria-label="Réduire"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
        <div class="p-6 md:p-8 pt-12 pb-20">
            <h1 class="text-2xl md:text-4xl font-bold font-poppins text-gray-600">Assalamu Alaikum, {{ auth()->user()->name }}</h1>
            @if($pilgrim && $pilgrim->status === 'pending')
                <p class="mt-2 text-gray-500 text-sm md:text-base">Votre demande est en cours d'examen par l'agence. Vous serez notifié dès validation.</p>
            @elseif($pilgrim && $pilgrim->package)
                <p class="mt-2 text-gray-500 text-sm md:text-base">Que votre voyage vers les Lieux Saints soit accepté. @if($pilgrim->package->departure_date) Votre départ est prévu dans {{ $pilgrim->package->departure_date->diffInDays(now()) }} jours. @endif</p>
            @else
                <p class="mt-2 text-gray-500 text-sm md:text-base">Parcourez les forfaits disponibles et postulez auprès d'une agence pour démarrer votre procédure.</p>
            @endif
        </div>
        {{-- Barre de progression gris foncé, plein écran en bas à droite --}}
        <div class="absolute bottom-0 left-0 right-0 h-10 bg-gray-700 flex items-center justify-between px-4 text-xs text-gray-200">
            <div class="flex items-center gap-2">
                <button class="text-gray-300 hover:text-white"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 4v16h4V4H6zm4 0v16h4V4h-4zm8 0h-4v16h4V4z"/></svg></button>
                <span>02:45 / 05:00</span>
            </div>
            <button class="text-gray-300 hover:text-white" title="Plein écran"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></button>
        </div>
    </div>

    @if(!$pilgrim)
        {{-- Carte CTA blanche : icône cube 3D filaire centrée, titre, description --}}
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 md:p-12 text-center">
            <div class="flex justify-center mb-6">
                <svg class="w-24 h-24 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/>
                    <path d="M2 7v10"/><path d="M12 2v10"/><path d="M22 7v10"/>
                    <path d="M12 12l10-5"/><path d="M12 12L2 7"/><path d="M12 12v10"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Choisissez un forfait</h2>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">Consultez les forfaits publiés par les agences et postulez à celui de votre choix.</p>
            <a href="{{ route('client.packages.index') }}" class="inline-flex items-center px-6 py-3 bg-primary-green text-white rounded-lg hover:bg-dark-green transition font-medium">Voir les forfaits disponibles <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg></a>
        </div>
    @else
        {{-- Alerte selon le statut de la demande --}}
        @if($pilgrim->status === 'pending')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 flex items-start">
                <svg class="w-10 h-10 text-amber-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h2 class="text-lg font-bold text-amber-900">Demande en attente</h2>
                    <p class="text-amber-800 mt-1">Vous avez postulé pour le forfait <strong>{{ $pilgrim->package?->name }}</strong>. L'agence examinera votre dossier sous peu.</p>
                    <a href="{{ route('client.packages.index') }}" class="inline-block mt-4 text-sm font-medium text-primary-green hover:underline">Voir les forfaits</a>
                </div>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-start">
                <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h2 class="text-sm font-semibold text-green-900">Notification</h2>
                    <p class="text-sm text-green-800 mt-1">
                        Votre demande a été acceptée par l'agence. Statut actuel :
                        <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $pilgrim->status)) }}</span>.
                    </p>
                </div>
            </div>
        @endif

        @php
            $totalAmount = $pilgrim->package ? (float) $pilgrim->package->price : 0;
            $paidAmount = $pilgrim->payments->where('status', 'completed')->sum('amount');
            $balanceDue = max(0, $totalAmount - $paidAmount);
        @endphp

        {{-- Ligne 1 : Visa & Journey Status | An-Noor Assistant --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md border border-gray-100 p-6" id="procedure">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Visa & Journey Status</h2>
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">IN PROGRESS</span>
                </div>
                <div class="flex flex-wrap items-center gap-3 md:gap-6">
                    @foreach($procedureSteps as $idx => $step)
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-9 h-9 rounded-full {{ $step['done'] ? 'bg-primary-green text-white' : ($idx === 2 ? 'bg-gray-300 text-gray-600' : 'bg-gray-200 text-gray-400') }}">
                                @if($step['done'])
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($idx === 2)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                @endif
                            </div>
                            <span class="ml-2 text-sm font-medium {{ $step['done'] ? 'text-gray-900' : 'text-gray-500' }}">{{ strtoupper($step['label']) }}</span>
                            @if(!$loop->last)<span class="ml-2 text-gray-300">—</span>@endif
                        </div>
                    @endforeach
                </div>
                <p class="text-sm text-gray-600 mt-4">@if($pilgrim->visa){{ ucfirst(str_replace('_', ' ', $pilgrim->visa->status ?? 'En traitement')) }}. @else Votre dossier est en cours de traitement. @endif</p>
            </div>
            {{-- Assistant chat intégré (style An-Noor Assistant) --}}
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden flex flex-col"
                 style="min-height: 280px;"
                 x-data="chatbotInline()"
                 x-init="init()">
                {{-- Header vert foncé (couleurs maquette) --}}
                <div class="px-4 py-3 flex items-center justify-between" style="background-color:#0F3F2E;color:#FFFFFF;">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#C9A227;">
                            <svg class="w-5 h-5" style="color:#0F3F2E;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L3 9v11h6v-6h6v6h6V9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">An-Noor Assistant</p>
                            <p class="text-[11px] uppercase tracking-wide text-white/70">Always online</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-white/80">
                        <span class="inline-flex w-2 h-2 rounded-full bg-green-400"></span>
                        <button type="button" class="p-1 rounded hover:bg-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01"/>
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Zone de messages --}}
                {{-- Zone messages : fond très clair, bulles blanches / vertes --}}
                <div class="flex-1 p-4 space-y-3 text-sm" style="background-color:#F4F6FB;max-height:260px;overflow-y:auto;" x-ref="messagesContainer">
                    <template x-if="!hasPilgrim">
                        <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3">
                            Choisissez d'abord un forfait pour activer l'assistant et poser vos questions (visa, paiement, documents).
                        </div>
                    </template>
                    <template x-for="(msg, i) in messages" :key="i">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.role === 'user'
                                          ? 'rounded-2xl rounded-br-sm px-3 py-2 max-w-[80%] text-white'
                                          : 'bg-white text-gray-900 border border-gray-100 rounded-2xl rounded-bl-sm px-3 py-2 max-w-[80%] shadow-sm'"
                                 :style="msg.role === 'user' ? 'background-color:#0F3F2E;' : ''">
                                <p class="text-sm leading-snug" x-text="msg.content"></p>
                            </div>
                        </div>
                    </template>
                    <div x-show="loading" class="flex justify-start">
                        <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 text-gray-600 text-sm">
                            En train d'écrire...
                        </div>
                    </div>
                </div>
                {{-- Zone de saisie + actions rapides --}}
                <div class="px-4 pt-2 pb-3 bg-white border-t border-gray-200" x-show="hasPilgrim">
                    <form @submit.prevent="send" class="flex items-center gap-2">
                        <div class="flex-1 flex items-center bg-gray-50 border border-gray-200 rounded-full px-3 py-1.5">
                            <input type="text"
                                   x-model="input"
                                   placeholder="Type a message..."
                                   class="flex-1 bg-transparent text-sm text-gray-900 placeholder-gray-400 focus:outline-none"
                                   :disabled="loading">
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-primary-green text-white hover:bg-dark-green transition"
                                :disabled="loading || !input.trim()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" class="text-[11px] px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200"
                                @click.prevent="quickAsk('What is my visa status ?')">
                            Visa Status
                        </button>
                        <button type="button" class="text-[11px] px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200"
                                @click.prevent="quickAsk('What documents do I need ?')">
                            Packing List
                        </button>
                        <button type="button" class="text-[11px] px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200"
                                @click.prevent="quickAsk('What are the prayer times ?')">
                            Prayer Times
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte Pay Your Balance (style An-Noor) + Modal paiement --}}
        <div x-data="paiementModal({{ $balanceDue ?? 0 }})" x-cloak>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Pay Your Balance</h2>
            @php
                $canPay = $pilgrim->visa && $pilgrim->visa->status === 'approved';
                $hasPendingPayment = $pilgrim->transactionsDigitales->contains('statut', 'en_attente');
            @endphp
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Remaining Balance</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-1">{{ number_format($balanceDue, 0, ',', ' ') }} FDJ</p>
                    <ul class="mt-4 space-y-2 text-sm text-gray-600">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Hôtel</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Transport</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Frais visa</li>
                    </ul>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Outstanding Amount</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($balanceDue, 0, ',', ' ') }} FDJ</p>
                    @if($balanceDue > 0)
                        @if($canPay)
                            @if($hasPendingPayment)
                            <button type="button" disabled class="mt-4 inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-400 font-semibold rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Paiement en attente de validation
                            </button>
                            <p class="mt-2 text-xs text-gray-500 max-w-xs">Vous avez déjà déclaré un paiement. L'agence le validera sous peu. Consultez « Mes paiements digitaux » ci-dessous.</p>
                            @else
                            <button type="button" @click="open = true; reset()" class="mt-4 inline-flex items-center justify-center px-6 py-3 bg-primary-green text-white font-semibold rounded-lg hover:bg-dark-green transition shadow">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Procéder au paiement
                            </button>
                            @endif
                        @else
                            <button class="mt-4 inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-400 font-semibold rounded-lg cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Paiement disponible après visa
                            </button>
                            <p class="mt-2 text-xs text-gray-500 max-w-xs">
                                Une fois vos documents validés et votre visa approuvé par l'agence, vous pourrez procéder au règlement du solde.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Procéder au paiement (2 étapes) --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="open" @click.self="open = false" class="fixed inset-0 bg-black/50"></div>
                <div x-show="open" x-transition class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl">
                    {{-- En-tête --}}
                    <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl bg-emerald-50 border-b border-emerald-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-green flex items-center justify-center text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" x-text="step === 1 ? 'Procéder au paiement' : 'Confirmation du paiement'"></h3>
                                <p class="text-xs text-gray-600" x-text="'ÉTAPE ' + step + ' SUR 2'"></p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">&times;</button>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-600 mb-4" x-show="step === 1">Veuillez choisir votre mode de paiement préféré pour finaliser votre transaction.</p>

                        {{-- Étape 1 : Choix méthode --}}
                        <div x-show="step === 1" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <template x-for="m in methodes" :key="m.key">
                                    <button type="button" @click="selectMethode(m)"
                                            class="p-4 rounded-xl border-2 text-left transition-all duration-200 hover:shadow-md hover:border-primary-green/50"
                                            :class="selectedCompte && selectedCompte.nom_methode === m.nom ? 'border-primary-green bg-emerald-50/50' : 'border-gray-200'">
                                        <img :src="m.logo" :alt="m.nom" class="w-12 h-12 rounded-full object-cover mx-auto mb-2" onerror="this.style.display='none'">
                                        <div class="font-semibold text-gray-900" x-text="m.nom"></div>
                                        <div class="text-xs text-gray-500 mt-0.5" x-text="m.desc"></div>
                                    </button>
                                </template>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                Paiement sécurisé par cryptage SSL
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="open = false" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</button>
                                <button type="button" @click="goStep2()" :disabled="!selectedCompte" class="px-5 py-2 rounded-lg bg-primary-green text-white font-medium hover:bg-dark-green disabled:opacity-50 disabled:cursor-not-allowed">Continuer →</button>
                            </div>
                        </div>

                        {{-- Étape 2 : Confirmation + formulaire --}}
                        <div x-show="step === 2" class="space-y-4">
                            <div x-show="successMessage" class="p-4 rounded-lg bg-green-100 border border-green-200 flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span class="text-green-800" x-text="successMessage"></span>
                            </div>
                            <template x-if="!successMessage">
                                <div>
                                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-200 space-y-2">
                                        <p class="text-sm"><span class="font-semibold text-gray-700">Nom de l'agence :</span> <span x-text="selectedCompte ? selectedCompte.nom_agence : ''"></span></p>
                                        <p class="text-sm"><span class="font-semibold text-gray-700">Numéro du compte :</span> <span x-text="selectedCompte ? selectedCompte.numero_compte : ''"></span></p>
                                        <p class="text-sm"><span class="font-semibold text-gray-700">Méthode :</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800" x-text="selectedCompte ? selectedCompte.nom_methode : ''"></span>
                                        </p>
                                        <template x-if="selectedCompte && selectedCompte.logo">
                                            <p class="mt-2"><img :src="selectedCompte.logo" alt="" class="h-10 w-auto"></p>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FDJ) *</label>
                                            <input type="number" step="0.01" min="0.01" x-model="montant" placeholder="0.00"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-primary-green focus:border-primary-green">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Référence (facultatif)</label>
                                            <input type="text" x-model="reference" placeholder="Ex: TXN-987654321"
                                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-primary-green focus:border-primary-green">
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-2 mt-4">
                                        <button type="button" @click="submitPaiement()" :disabled="submitting || !montant || parseFloat(montant) < 0.01"
                                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-amber-500 text-white font-semibold hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            J'ai effectué le paiement
                                        </button>
                                        <a href="#" @click.prevent="step = 1" class="text-sm text-primary-green hover:underline text-center sm:text-left">Retourner à l'étape précédente</a>
                                    </div>
                                </div>
                            </template>
                            <template x-if="successMessage && ficheUrl">
                                <div class="pt-2">
                                    <a :href="ficheUrl" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-green text-white font-medium hover:bg-dark-green">
                                        Télécharger la fiche de paiement (PDF)
                                    </a>
                                </div>
                            </template>
                            <div x-show="successMessage" class="flex justify-end">
                                <button type="button" @click="open = false; reset()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        {{-- Section Mes paiements digitaux (fiches PDF) --}}
        @if($pilgrim->transactionsDigitales->isNotEmpty())
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mt-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Mes paiements digitaux</h2>
            <p class="text-sm text-gray-600 mb-4">Historique de vos déclarations de paiement (D-money, Waafi, MyCac). Téléchargez la fiche détaillée pour vos archives.</p>
            <ul class="divide-y divide-gray-200">
                @foreach($pilgrim->transactionsDigitales->sortByDesc('created_at') as $tx)
                <li class="py-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="font-medium text-gray-900">{{ number_format($tx->montant, 0, ',', ' ') }} FDJ</span>
                        <span class="text-sm text-gray-500"> — {{ $tx->compteMarchand->nom_methode ?? '—' }} — {{ $tx->created_at->format('d/m/Y H:i') }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium
                            @if($tx->statut === 'en_attente') bg-amber-100 text-amber-800
                            @elseif($tx->statut === 'valide') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">{{ ucfirst(str_replace('_', ' ', $tx->statut)) }}</span>
                    </div>
                    <a href="{{ route('pelerin.fiche-paiement.pdf', $tx) }}" target="_blank" class="text-sm text-primary-green hover:underline font-medium">Télécharger la fiche PDF</a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Raccourci Étapes Omra/Hajj avec l'agence --}}
        @php
            $stepsOmra = [
                ['key' => 'choose', 'label' => 'Choisir un forfait', 'done' => (bool) $pilgrim->package_id],
                ['key' => 'validation', 'label' => 'Validation agence', 'done' => $pilgrim->status !== 'pending'],
                ['key' => 'dossier', 'label' => 'Dossier complet', 'done' => in_array($pilgrim->status, ['dossier_complete','visa_submitted','visa_approved','departed','returned'])],
                ['key' => 'visa', 'label' => 'Visa', 'done' => in_array($pilgrim->status, ['visa_submitted','visa_approved','departed','returned'])],
                ['key' => 'payment', 'label' => 'Paiement', 'done' => $pilgrim->payments()->where('status','completed')->exists()],
                ['key' => 'travel', 'label' => 'Voyage & retour', 'done' => in_array($pilgrim->status, ['departed','returned'])],
            ];
        @endphp
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 mt-4">
            <h2 class="text-sm font-semibold text-gray-900 mb-3 uppercase tracking-wide">Étapes avec votre agence</h2>
            <p class="text-xs text-gray-500 mb-3">Voici les grandes étapes pour votre Omra/Hajj. Votre agence vous accompagnera à chaque étape.</p>
            <div class="flex flex-wrap gap-3">
                @foreach($stepsOmra as $i => $s)
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold
                            {{ $s['done'] ? 'bg-primary-green text-white' : 'bg-gray-100 text-gray-500' }}">
                            {{ $i+1 }}
                        </div>
                        <span class="text-xs font-medium {{ $s['done'] ? 'text-gray-900' : 'text-gray-500' }}">
                            {{ $s['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Espace documents pour l'agence + documents de voyage (ancre #documents) --}}
        @php
            $passportDoc = $pilgrim->documents->firstWhere('type', 'passport');
            $photoDoc = $pilgrim->documents->firstWhere('type', 'photo');
            $medicalDoc = $pilgrim->documents->firstWhere('type', 'medical_certificate');
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="documents">
            {{-- Espace documents client -> agence --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Mes documents pour l'agence</h2>
                <p class="text-xs text-gray-600 mb-4">
                    Téléversez ici votre passeport, votre photo et votre certificat médical. L'agence utilisera ces pièces pour déposer votre demande de visa.
                </p>
                <form action="{{ route('pelerin.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Passeport</label>
                            <input type="file" name="documents[passport]" accept=".pdf,.jpg,.jpeg,.png"
                                   class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            @error('documents.passport')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($passportDoc)
                                <p class="mt-1 text-[11px] text-emerald-700">
                                    Dernier reçu le {{ $passportDoc->uploaded_at?->format('d/m/Y') }} –
                                    <a href="{{ Storage::url($passportDoc->file_path) }}" target="_blank" class="underline">voir</a>
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Photo</label>
                            <input type="file" name="documents[photo]" accept=".jpg,.jpeg,.png"
                                   class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            @error('documents.photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($photoDoc)
                                <p class="mt-1 text-[11px] text-emerald-700">
                                    Dernière reçue le {{ $photoDoc->uploaded_at?->format('d/m/Y') }} –
                                    <a href="{{ Storage::url($photoDoc->file_path) }}" target="_blank" class="underline">voir</a>
                                </p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1.5">Certificat médical</label>
                            <input type="file" name="documents[medical_certificate]" accept=".pdf,.jpg,.jpeg,.png"
                                   class="block w-full text-xs text-gray-700 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            @error('documents.medical_certificate')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($medicalDoc)
                                <p class="mt-1 text-[11px] text-emerald-700">
                                    Dernier reçu le {{ $medicalDoc->uploaded_at?->format('d/m/Y') }} –
                                    <a href="{{ Storage::url($medicalDoc->file_path) }}" target="_blank" class="underline">voir</a>
                                </p>
                            @endif
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500">
                        Formats acceptés : PDF, JPG, JPEG, PNG. Taille max 5&nbsp;Mo par document.
                    </p>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-primary-green text-white text-xs font-semibold hover:bg-dark-green transition">
                            Envoyer les documents à l'agence
                        </button>
                    </div>
                </form>
            </div>

            {{-- Documents de voyage fournis par l'agence --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Documents de voyage</h2>
                <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-gray-900">Visa</p>
                        <p class="text-xs text-gray-500">
                            {{ $pilgrim->visa && $pilgrim->visa->status === 'approved' ? 'Prêt (approuvé par l\'agence)' : 'En attente de traitement' }}
                        </p>
                    </div>
                    <div class="border rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-gray-900">Billet</p>
                        <p class="text-xs text-gray-500">Sur demande auprès de votre agence</p>
                    </div>
                    <div class="border rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-gray-900">Badge groupe</p>
                        <p class="text-xs text-gray-500">Fourni avant le départ</p>
                    </div>
                    <div class="border rounded-lg p-3 text-center">
                        <p class="text-sm font-medium text-gray-900">Contrat</p>
                        <p class="text-xs text-gray-500">Signé avec votre agence</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ligne 2 : Accommodation | Flight Details | Group Leader --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Accommodation --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="h-28 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="p-4">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Accommodation</h2>
                    @if($pilgrim->package && ($pilgrim->package->hotelMecca || $pilgrim->package->hotelMedina))
                        <p class="font-bold text-gray-900">{{ $pilgrim->package->hotelMecca?->name ?? $pilgrim->package->hotelMedina?->name }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $pilgrim->package->nights_mecca ?? 0 }} nuits La Mecque · {{ $pilgrim->package->nights_medina ?? 0 }} nuits Médine</p>
                    @else
                        <p class="font-bold text-gray-900">—</p>
                        <p class="text-sm text-gray-600">Sur demande</p>
                    @endif
                </div>
            </div>
            {{-- Flight Details --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Flight Details</h2>
                @if($pilgrim->package && $pilgrim->package->departure_date)
                    <p class="font-bold text-gray-900">Départ → Arrivée</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $pilgrim->package->departure_date->translatedFormat('d M Y') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Détails communiqués par l'agence</p>
                @else
                    <p class="font-bold text-gray-900">—</p>
                    <p class="text-sm text-gray-600">Sur demande</p>
                @endif
            </div>
            {{-- Group Leader --}}
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Group Leader</h2>
                @if($pilgrim->guide && $pilgrim->guide->user)
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-primary-green flex items-center justify-center text-white font-bold text-lg">{{ substr($pilgrim->guide->user->name, 0, 2) }}</div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $pilgrim->guide->user->name }}</p>
                            <p class="text-xs text-gray-500 uppercase">Authorized Guide</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="#" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">WhatsApp</a>
                        <a href="#" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Call</a>
                    </div>
                @else
                    <p class="text-gray-600 text-sm">À assigner par l'agence</p>
                @endif
            </div>
        </div>
    @endif
</div>

@if($pilgrim)
    @include('dashboard.partials.chatbot-widget')
@endif

@endsection

@push('scripts')
<script>
function paiementModal(balanceDue) {
    const baseUrl = '{{ url("/pelerin") }}';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return {
        open: false,
        step: 1,
        balanceDue: Number(balanceDue) || 0,
        methodes: [
            { key: 'dmoney', nom: 'D-Money', desc: 'Paiement rapide et sécurisé via mobile', logo: '{{ asset("img/D-money.jpeg") }}' },
            { key: 'waafi', nom: 'Waafi', desc: 'Solution de paiement mobile intégrée', logo: '{{ asset("img/Waafi.jpeg") }}' },
            { key: 'mycac', nom: 'MyCac', desc: 'Transfert d\'argent instantané sécurisé', logo: '{{ asset("img/Mycac.jpeg") }}' }
        ],
        selectedCompte: null,
        loading: false,
        montant: String(balanceDue || 0).replace('.', ','),
        reference: '',
        submitting: false,
        successMessage: null,
        ficheUrl: null,
        reset() {
            this.step = 1;
            this.selectedCompte = null;
            this.montant = String(this.balanceDue || 0).replace('.', ',');
            this.reference = '';
            this.successMessage = null;
            this.ficheUrl = null;
        },
        selectMethode(m) {
            this.loading = true;
            fetch(baseUrl + '/compte-marchand?methode=' + encodeURIComponent(m.nom), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.compte) this.selectedCompte = data.compte;
                    else this.selectedCompte = null;
                })
                .catch(() => { this.selectedCompte = null; })
                .finally(() => { this.loading = false; });
        },
        goStep2() {
            if (this.selectedCompte) this.step = 2;
        },
        submitPaiement() {
            if (this.submitting || !this.selectedCompte || !this.montant || parseFloat(String(this.montant).replace(',', '.')) < 0.01) return;
            this.submitting = true;
            const form = new FormData();
            form.append('_token', csrf);
            form.append('compte_marchand_id', this.selectedCompte.id);
            form.append('montant', String(this.montant).replace(',', '.'));
            if (this.reference) form.append('reference', this.reference);
            fetch(baseUrl + '/transaction-digitale', {
                method: 'POST',
                body: form,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.successMessage = data.message || 'Paiement envoyé avec succès. En attente de validation par l\'agence.';
                        this.ficheUrl = data.fiche_url || null;
                        if (data.fiche_url) setTimeout(() => window.location.reload(), 500);
                    } else {
                        alert(data.message || 'Erreur lors de l\'envoi.');
                    }
                })
                .catch(() => alert('Erreur de connexion. Réessayez.'))
                .finally(() => { this.submitting = false; });
        }
    };
}
function chatbotInline() {
    return {
        messages: [],
        input: '',
        loading: false,
        hasPilgrim: true,
        init() {
            this.fetchSession();
        },
        fetchSession() {
            fetch('{{ route("chatbot.session") }}', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
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
            fetch('{{ route("chatbot.message") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
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
        quickAsk(text) {
            this.input = text;
            this.send();
        },
        scrollBottom() {
            const el = this.$refs.messagesContainer;
            if (el) el.scrollTop = el.scrollHeight;
        }
    };
}
</script>
@endpush
