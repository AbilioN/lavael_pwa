<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game['name'] ?? 'Jogo' }} - GameStore</title>

    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GameStore">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-192x192.svg">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="description" content="{{ $game['description'] ?? 'Detalhes do jogo' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="GameStore">
    <meta name="msapplication-TileColor" content="#6366f1">
    <meta name="msapplication-TileImage" content="/icons/icon-192x192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#1f2937'
                    }
                }
            }
        }
    </script>
    <style>
        .screenshot {
            transition: all 0.3s ease;
        }
        .screenshot:hover {
            transform: scale(1.05);
        }
        .loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">🎮 GameStore</a>
                </div>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <form action="{{ route('games.search') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Buscar jogos..." 
                            class="w-full px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                        <button type="submit" class="absolute left-3 top-2.5 text-gray-400">
                            🔍
                        </button>
                    </form>
                </div>

                <!-- Install Button -->
                <div id="install-container" class="hidden">
                    <button id="install-btn" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        📱 Instalar App
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(isset($game) && $game)
            <!-- Game Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-8">
                <div class="relative h-96">
                    <img 
                        src="{{ $game['background_image'] ?? '/images/placeholder.jpg' }}" 
                        alt="{{ $game['name'] }}"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6">
                        <h1 class="text-4xl font-bold text-white mb-2">{{ $game['name'] }}</h1>
                        <div class="flex items-center gap-4 text-white">
                            <span class="bg-primary px-3 py-1 rounded-full text-sm font-medium">
                                ⭐ {{ number_format($game['rating'], 1) }}
                            </span>
                            <span class="text-sm">
                                📅 {{ \Carbon\Carbon::parse($game['released'])->format('d/m/Y') }}
                            </span>
                            <span class="text-sm">
                                🎮 {{ $game['platforms'][0]['platform']['name'] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Description -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                        <h2 class="text-2xl font-bold mb-4">Descrição</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $game['description'] ?? 'Descrição não disponível.' !!}
                        </div>
                    </div>

                    <!-- Screenshots -->
                    @if(isset($game['screenshots']) && count($game['screenshots']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8">
                            <h2 class="text-2xl font-bold mb-4">Screenshots</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach(array_slice($game['screenshots'], 0, 6) as $screenshot)
                                    <div class="screenshot cursor-pointer">
                                        <img 
                                            src="{{ $screenshot['image'] }}" 
                                            alt="Screenshot"
                                            class="w-full h-48 object-cover rounded-lg"
                                            onclick="openImageModal('{{ $screenshot['image'] }}')"
                                        >
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Requirements -->
                    @if(isset($game['platforms']) && count($game['platforms']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold mb-4">Plataformas</h2>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($game['platforms'] as $platform)
                                    <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <span class="text-lg">🎮</span>
                                        <span class="font-medium">{{ $platform['platform']['name'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Game Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-xl font-bold mb-4">Informações</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Desenvolvedor:</span>
                                <span class="font-medium">{{ $game['developers'][0]['name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Editora:</span>
                                <span class="font-medium">{{ $game['publishers'][0]['name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Lançamento:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($game['released'])->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Avaliação:</span>
                                <span class="font-medium">⭐ {{ number_format($game['rating'], 1) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Metacritic:</span>
                                <span class="font-medium">{{ $game['metacritic'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Genres -->
                    @if(isset($game['genres']) && count($game['genres']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-bold mb-4">Gêneros</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($game['genres'] as $genre)
                                    <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm">
                                        {{ $genre['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if(isset($game['tags']) && count($game['tags']) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-bold mb-4">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($game['tags'], 0, 10) as $tag)
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-1 rounded text-xs">
                                        {{ $tag['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Loading State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="loading bg-gray-200 dark:bg-gray-700 h-8 mb-4 rounded"></div>
                <div class="loading bg-gray-200 dark:bg-gray-700 h-4 mb-2 rounded"></div>
                <div class="loading bg-gray-200 dark:bg-gray-700 h-4 rounded"></div>
            </div>
        @endif
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl w-full">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                ✕
            </button>
            <img id="modalImage" src="" alt="Screenshot" class="w-full h-auto rounded-lg">
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600 dark:text-gray-400">
                <p>&copy; 2024 GameStore. Todos os direitos reservados.</p>
                <p class="mt-2 text-sm">Dados fornecidos pela RAWG API</p>
            </div>
        </div>
    </footer>

    <!-- PWA Script -->
    @vite(['resources/js/pwa.js'])

    <!-- Custom Script -->
    <script>
        // Image Modal
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on background click
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Install PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('install-container').classList.remove('hidden');
        });

        document.getElementById('install-btn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    console.log('PWA instalada com sucesso!');
                }
                deferredPrompt = null;
                document.getElementById('install-container').classList.add('hidden');
            }
        });
    </script>
</body>
</html> 