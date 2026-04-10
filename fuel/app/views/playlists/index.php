<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover - Sazanami</title>
    
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

<?php echo \View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

<div id="discover-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-discover" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" strokeWidth="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-discover)" />
        </svg>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-12 space-y-8 relative z-10">
        <div class="text-center space-y-3">
            <h1 class="text-4xl text-primary tracking-wide font-bold">Discover Trending Waves</h1>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                Explore curated playlists from creators around the world, where music spreads softly like ripples on the water
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-bind="foreach: discoverPlaylists">
            <div data-bind="component: { name: 'playlist-card', params: $data }"></div>
        </div>
    </main>
</div>

<?php echo \View::forge('shared/playlist_card'); ?>

<script>
    // プラットフォーム固有のSVGアイコンと色を出力するカスタムバインディング
    ko.bindingHandlers.platformIcon = {
        init: function(element, valueAccessor) {
            let platform = ko.unwrap(valueAccessor());
            let svg = '';
            let colorClass = '';

            switch (platform) {
                case 'youtube':
                    colorClass = 'text-[#FF0000]';
                    svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
                    break;
                case 'niconico':
                    colorClass = 'text-[#231815]';
                    svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M.4 0v24h23.2V0H.4zm4.8 4.8h4.8v14.4H5.2V4.8zm9.6 0h4.8v14.4h-4.8V4.8z"/></svg>';
                    break;
                default:
                    // デフォルトは汎用的な音符アイコン
                    colorClass = 'text-gray-500';
                    svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>';
            }

            // クラスとSVG要素を適用
            element.className += ' ' + colorClass;
            element.innerHTML = svg;
        }
    };

    function DiscoverViewModel() {
        let self = this;

        // PHPのコントローラーから渡された本物のDBデータを受け取る
        let dbPlaylists = <?php echo isset($playlists) ? json_encode($playlists) : '[]'; ?>;
        
        // ObservableArrayにセット
        self.discoverPlaylists = ko.observableArray(dbPlaylists);
    }

    // アイコンの描画とViewModelの適用
    ko.applyBindings(new DiscoverViewModel(), document.getElementById('discover-page'));
    
    // Lucideアイコンの描画
    setTimeout(function() { lucide.createIcons(); }, 100);
</script>

</body>
</html>