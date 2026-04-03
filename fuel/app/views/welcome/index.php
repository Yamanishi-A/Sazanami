<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sazanami</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Figmaで設定された独自カラー（primaryなど）をTailwindに設定
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
                    <a href="/playlists/discover">
                        <button class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-lg hover:shadow-xl transition-all text-lg font-bold">
                            <i data-lucide="compass" class="w-5 h-5"></i>
                            <span>Explore Playlists</span>
                        </button>
                    </a>
                <?php else: ?>
                    <button data-bind="click: openSignUpModal" class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-lg hover:shadow-xl transition-all text-lg font-bold">
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

    <div data-bind="visible: isLoginModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
        <div data-bind="click: closeModals" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 m-4">
            <h2 class="text-2xl font-bold text-center mb-6 text-primary">Log In</h2>
            <form action="/auth/login" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <button type="submit" class="w-full py-3 bg-primary text-white rounded-full font-bold hover:bg-primary/90 transition-colors">Log In</button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                アカウントをお持ちでないですか？ <a href="#" data-bind="click: openSignUpModal" class="text-primary font-bold hover:underline">新規登録</a>
            </p>
            <button data-bind="click: closeModals" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
        </div>
    </div>

    <div data-bind="visible: isSignUpModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center">
        <div data-bind="click: closeModals" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 m-4">
            <h2 class="text-2xl font-bold text-center mb-6 text-primary">Sign Up</h2>
            <form action="/auth/signup" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <button type="submit" class="w-full py-3 bg-primary text-white rounded-full font-bold hover:bg-primary/90 transition-colors">Sign Up</button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                すでにアカウントをお持ちですか？ <a href="#" data-bind="click: openLoginModal" class="text-primary font-bold hover:underline">ログイン</a>
            </p>
            <button data-bind="click: closeModals" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
        </div>
    </div>

</div>

<?php echo View::forge('shared/playlist_card'); ?>

<script>
    function LandingPageViewModel() {
        var self = this;

        // モーダルの表示状態を管理する変数（observableにすることで画面と連動する）
        self.isLoginModalOpen = ko.observable(false);
        self.isSignUpModalOpen = ko.observable(false);

        var dbTrending = <?php echo isset($trending_playlists) ? json_encode($trending_playlists) : '[]'; ?>;

        self.trendingPlaylists = ko.observableArray(dbTrending);

        // モーダルを開閉するメソッド
        self.openLoginModal = function() {
            self.isSignUpModalOpen(false);
            self.isLoginModalOpen(true);
        };

        self.openSignUpModal = function() {
            self.isLoginModalOpen(false);
            self.isSignUpModalOpen(true);
        };

        self.closeModals = function() {
            self.isLoginModalOpen(false);
            self.isSignUpModalOpen(false);
        };
    }
    lucide.createIcons();

    // Knockout.jsの起動（HTMLとViewModelを紐付ける）
    ko.applyBindings(new LandingPageViewModel(), document.getElementById('landing-page'));
</script>

</body>
</html>