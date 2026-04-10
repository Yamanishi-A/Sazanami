<?php $is_logged_in = \Session::get('user_id') ? true : false; ?>

<div id="global-header-component">
    
    <header class="w-full bg-white/80 backdrop-blur-sm border-b border-border shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            
            <a href="<?php echo $is_logged_in ? '/playlists/index' : '/'; ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                <?php echo \View::forge('shared/logo'); ?>
                <h1 class="text-2xl tracking-wide text-primary font-bold">Sazanami</h1>
            </a>

            <div class="flex items-center gap-3">
                <?php if ($is_logged_in): ?>
                    <a href="/playlists/discover" class="text-muted-foreground hover:text-primary transition-colors font-medium mr-2 hidden sm:block">Discover</a>
                    <a href="/playlists/index" class="text-muted-foreground hover:text-primary transition-colors font-medium mr-2 hidden sm:block">My Playlists</a>
                    <a href="/auth/logout" class="text-muted-foreground text-red-500 hover:text-red-600 transition-colors font-medium mr-2 hidden sm:block">Log Out</a>
                    
                    <a href="/playlists/settings" class="w-10 h-10 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center border-2 border-white shadow-lg overflow-hidden" aria-label="Settings">
                        <?php if (!empty($user['icon'])): ?>
                            <img src="<?php echo htmlspecialchars($user['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Icon" class="w-full h-full object-cover">
                        <?php else: ?>
                            <i data-lucide="user" class="w-6 h-6 text-primary"></i>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <button data-bind="click: openLoginModal" class="px-5 py-2 rounded-full text-primary hover:bg-secondary/50 transition-colors font-medium">
                        Log In
                    </button>
                    <button data-bind="click: openSignUpModal" class="px-6 py-2 bg-primary hover:bg-primary/90 text-primary-foreground rounded-full shadow-md hover:shadow-lg transition-all font-bold">
                        Sign Up
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if (!$is_logged_in): ?>
        
        <div data-bind="visible: isLoginModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="absolute inset-0" data-bind="click: closeModals"></div>
            
            <div class="w-full max-w-md bg-white rounded-3xl p-10 shadow-2xl border border-border relative z-10">
                <button data-bind="click: closeModals" class="absolute top-6 right-6 text-muted-foreground hover:text-foreground transition-colors" aria-label="Close modal">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>

                <div class="flex justify-center mb-8">
                    <div class="flex items-center gap-2">
                        <?php echo \View::forge('shared/logo', array('class' => 'w-10 h-10 text-primary')); ?>
                        <h1 class="text-3xl tracking-wide text-primary font-bold">Sazanami</h1>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-foreground mb-2">Welcome back</h2>
                </div>

                <div data-bind="visible: loginError, text: loginError" style="display: none;" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold text-center"></div>

                <form data-bind="submit: handleLogin" class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-foreground">Email<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                            <input type="email" data-bind="value: loginEmail" placeholder="you@example.com" required class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-muted-foreground">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-foreground">Password<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                            <input type="password" data-bind="value: loginPassword" placeholder="Enter your password" required class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-muted-foreground">
                        </div>
                    </div>

                    <button type="submit" data-bind="disable: isLoggingIn" class="w-full py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50">
                        <span data-bind="text: isLoggingIn() ? 'Logging in...' : 'Log In'"></span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        Don't have an account? 
                        <button type="button" data-bind="click: openSignUpModal" class="text-primary font-bold hover:text-primary/80 transition-colors">Sign up</button>
                    </p>
                </div>
            </div>
        </div>

        <div data-bind="visible: isSignUpModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="absolute inset-0" data-bind="click: closeModals"></div>
            <div class="w-full max-w-md bg-white rounded-3xl p-10 shadow-2xl border border-border relative z-10">
                <button data-bind="click: closeModals" class="absolute top-6 right-6 text-muted-foreground hover:text-foreground transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>

                <div class="flex justify-center mb-6">
                    <div class="flex items-center gap-2">
                        <?php echo View::forge('shared/logo'); ?>
                        <h1 class="text-2xl tracking-wide text-primary font-bold">Sazanami</h1>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-foreground mb-2">Create an account</h2>
                </div>

                <form action="/auth/signup" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-foreground">Username<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                            <input type="text" name="username" placeholder="Your name" required class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-foreground">Email<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                            <input type="email" name="email" placeholder="you@example.com" required class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-foreground">Password<span class="text-red-500 ml-1">*</span></label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground"></i>
                            <input type="password" name="password" placeholder="Create a password" required class="w-full pl-12 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all mt-2">
                        Sign Up
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-muted-foreground">
                        Already have an account? 
                        <button type="button" data-bind="click: openLoginModal" class="text-primary font-bold hover:text-primary/80 transition-colors">Log in</button>
                    </p>
                </div>
            </div>
        </div>

        <script>
            // ヘッダー部分だけで独立して動くViewModel
            function HeaderViewModel() {
                var self = this;
                self.isLoginModalOpen = ko.observable(false);
                self.isSignUpModalOpen = ko.observable(false);

                // ▼ 追加: ログインフォーム用のデータ管理
                self.loginEmail = ko.observable("");
                self.loginPassword = ko.observable("");
                self.loginError = ko.observable("");
                self.isLoggingIn = ko.observable(false);

                // ▼ 追加: ログイン処理 (Ajax)
                self.handleLogin = function() {
                    self.loginError("");
                    self.isLoggingIn(true);

                    // ★追加: 現在のページのパスを取得 (クエリパラメータも含める)
                    var currentPath = window.location.pathname + window.location.search;

                    fetch('/auth/login', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            email: self.loginEmail(),
                            password: self.loginPassword(),
                            redirect_to: currentPath // ★追加: サーバーに現在の場所を伝える
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // ★修正: サーバーから返ってきたリダイレクト先（または現在地）へ遷移
                            window.location.href = data.redirect_to || currentPath;
                        } else {
                            self.isLoggingIn(false);
                            self.loginError(data.error || 'ログインに失敗しました。');
                        }
                    })
                    .catch(error => {
                        self.isLoggingIn(false);
                        self.loginError('通信エラーが発生しました。');
                    });
                };

                self.openLoginModal = function() {
                    self.isSignUpModalOpen(false);
                    self.isLoginModalOpen(true);
                    document.body.style.overflow = "hidden";
                };

                self.openSignUpModal = function() {
                    self.isLoginModalOpen(false);
                    self.isSignUpModalOpen(true);
                    document.body.style.overflow = "hidden";
                };

                self.closeModals = function() {
                    self.isLoginModalOpen(false);
                    self.isSignUpModalOpen(false);
                    document.body.style.overflow = "unset";
                };
            }

            document.addEventListener("DOMContentLoaded", function() {
                var headerElement = document.getElementById('global-header-component');
                if (headerElement) {
                    window.headerViewModel = new HeaderViewModel();
                    ko.applyBindings(window.headerViewModel, headerElement);
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons({ root: headerElement });
                    }
                }
            });
        </script>

    <?php endif; ?>
</div>