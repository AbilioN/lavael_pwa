<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GameStore - Sua Loja de Jogos</title>

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
    <meta name="description" content="GameStore - Sua loja de jogos eletrônicos">
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
        .game-card {
            transition: all 0.3s ease;
        }
        .game-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
                    <h1 class="text-2xl font-bold text-primary">🎮 GameStore</h1>
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
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Descubra os Melhores Jogos
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Explore nossa coleção de jogos eletrônicos com avaliações, screenshots e informações detalhadas.
            </p>
        </div>

        <!-- Games Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="games-container">
            @if(isset($games) && count($games) > 0)
                @foreach($games as $game)
                    <div class="game-card bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md">
                        <div class="relative">
                            <img 
                                src="{{ $game['background_image'] ?? '/images/placeholder.jpg' }}" 
                                alt="{{ $game['name'] }}"
                                class="w-full h-48 object-cover"
                                loading="lazy"
                            >
                            <div class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded text-xs font-medium">
                                ⭐ {{ number_format($game['rating'], 1) }}
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $game['name'] }}</h3>
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <span>📅 {{ \Carbon\Carbon::parse($game['released'])->format('Y') }}</span>
                                <span>🎮 {{ $game['platforms'][0]['platform']['name'] ?? 'N/A' }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($game['genres'], 0, 3) as $genre)
                                    <span class="bg-primary/10 text-primary px-2 py-1 rounded text-xs">
                                        {{ $genre['name'] }}
                                    </span>
                                @endforeach
                            </div>
                            <a 
                                href="{{ route('games.show', $game['id']) }}" 
                                class="block w-full bg-primary hover:bg-primary/90 text-white text-center py-2 rounded-lg font-medium transition-colors"
                            >
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Loading State -->
                @for($i = 0; $i < 8; $i++)
                    <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md">
                        <div class="loading bg-gray-200 dark:bg-gray-700 h-48"></div>
                        <div class="p-4">
                            <div class="loading bg-gray-200 dark:bg-gray-700 h-6 mb-2 rounded"></div>
                            <div class="loading bg-gray-200 dark:bg-gray-700 h-4 mb-3 rounded"></div>
                            <div class="loading bg-gray-200 dark:bg-gray-700 h-10 rounded"></div>
                        </div>
                    </div>
                @endfor
            @endif
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-12">
            <button id="load-more" class="bg-secondary hover:bg-secondary/90 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Carregar Mais Jogos
            </button>
        </div>
    </main>

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
        let currentPage = 1;
        let isLoading = false;

        // Load more games
        document.getElementById('load-more').addEventListener('click', async function() {
            if (isLoading) return;
            
            isLoading = true;
            this.textContent = 'Carregando...';
            
            try {
                const response = await fetch(`/api/games?page=${currentPage + 1}`);
                const games = await response.json();
                
                if (games.length > 0) {
                    currentPage++;
                    appendGames(games);
                } else {
                    this.textContent = 'Não há mais jogos';
                    this.disabled = true;
                }
            } catch (error) {
                console.error('Erro ao carregar jogos:', error);
                this.textContent = 'Erro ao carregar';
            } finally {
                isLoading = false;
            }
        });

        function appendGames(games) {
            const container = document.getElementById('games-container');
            
            games.forEach(game => {
                const gameCard = createGameCard(game);
                container.appendChild(gameCard);
            });
        }

        function createGameCard(game) {
            const card = document.createElement('div');
            card.className = 'game-card bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md';
            
            const releaseYear = game.released ? new Date(game.released).getFullYear() : 'N/A';
            const platform = game.platforms && game.platforms[0] ? game.platforms[0].platform.name : 'N/A';
            const genres = game.genres ? game.genres.slice(0, 3).map(g => g.name).join(', ') : '';
            
            card.innerHTML = `
                <div class="relative">
                    <img src="${game.background_image || '/images/placeholder.jpg'}" alt="${game.name}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="absolute top-2 right-2 bg-black/70 text-white px-2 py-1 rounded text-xs font-medium">
                        ⭐ ${game.rating ? game.rating.toFixed(1) : 'N/A'}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2 line-clamp-2">${game.name}</h3>
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <span>📅 ${releaseYear}</span>
                        <span>🎮 ${platform}</span>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-3">
                        ${game.genres ? game.genres.slice(0, 3).map(genre => 
                            `<span class="bg-primary/10 text-primary px-2 py-1 rounded text-xs">${genre.name}</span>`
                        ).join('') : ''}
                    </div>
                    <a href="/games/${game.id}" class="block w-full bg-primary hover:bg-primary/90 text-white text-center py-2 rounded-lg font-medium transition-colors">
                        Ver Detalhes
                    </a>
                </div>
            `;
            
            return card;
        }

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