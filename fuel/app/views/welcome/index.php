<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sazanami</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5ba3c5',
                        'primary-foreground': '#ffffff',
                        secondary: '#e0f2fe',
                        accent: '#bae6fd',
                        foreground: '#333333',
                        'muted-foreground': '#666666',
                        border: '#e5e7eb'
                    }
                }
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-latest.js"></script>
	<script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50">

<?php $is_logged_in = \Session::get('user_id') ? true : false; ?>
<?php echo View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

<div id="landing-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-landing" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" strokeWidth="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-landing)" />
        </svg>
    </div>

    <section class="max-w-7xl mx-auto px-6 py-20 text-center relative z-10">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="flex justify-center mb-8">
                <div class="relative">
                    <svg viewBox="0 0 120 120" fill="none" class="w-32 h-32 text-primary" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="60" cy="60" r="50" fill="currentColor" opacity="0.1" />
                        <circle cx="60" cy="60" r="35" fill="currentColor" opacity="0.2" />
                        <circle cx="60" cy="60" r="20" fill="currentColor" opacity="0.3" />
                        <circle cx="60" cy="60" r="10" fill="currentColor" />
                    </svg>
                </div>
            </div>
            <h1 class="text-6xl tracking-tight text-foreground font-bold mb-4">Sazanami</h1>
            <p class="text-xl text-muted-foreground max-w-2xl mx-auto leading-relaxed">
                Let your favorite music spread like gentle ripples
            </p>
            <div class="flex items-center justify-center gap-4 pt-6">
                <?php if ($is_logged_in): ?>
                    <a href="/playlists">
                        <button class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-lg hover:shadow-xl transition-all text-lg font-bold">
                            <i data-lucide="compass" class="w-5 h-5"></i>
                            <span>Explore Playlists</span>
                        </button>
                    </a>
                <?php else: ?>
                    <button onclick="window.headerViewModel && window.headerViewModel.openSignUpModal()" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-lg hover:shadow-xl transition-all text-lg font-bold">
                        <span>Sign Up</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16 relative z-10">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl text-foreground font-bold">Trending Playlists</h2>
        </div>

        <div class="overflow-x-auto pb-4 -mx-6 px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-bind="foreach: trendingPlaylists">
                <div data-bind="component: { name: 'playlist-card', params: $data }"></div>
            </div>
        </div>
    </section>

    <footer class="border-t border-border bg-white/50 backdrop-blur-sm mt-20 relative z-10">
        <div class="max-w-7xl mx-auto px-6 py-8 text-center text-muted-foreground">
            <p>© 2026 Sazanami. Let your music flow like gentle ocean ripples.</p>
        </div>
    </footer>
</div>

<?php echo View::forge('shared/playlist_card'); ?>

<script>
    function LandingPageViewModel() {
        let self = this;

        let dbTrending = <?php echo isset($trending_playlists) ? json_encode($trending_playlists) : '[]'; ?>;

        self.trendingPlaylists = ko.observableArray(dbTrending);
    }

    setTimeout(function() { lucide.createIcons(); }, 100);

    // Knockout.jsの起動（HTMLとViewModelを紐付ける）
    ko.applyBindings(new LandingPageViewModel(), document.getElementById('landing-page'));
</script>

</body>
</html>