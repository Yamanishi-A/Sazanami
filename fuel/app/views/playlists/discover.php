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

<?php echo View::forge('shared/header', array('user' => isset($user) ? $user : null)); ?>

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

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 pt-4" data-bind="template: { name: 'playlist-card-template', foreach: discoverPlaylists }"></div>
    </main>
</div>

<script type="text/html" id="playlist-card-template">
    <a data-bind="attr: { href: '/playlists/view/' + id }" class="group block cursor-pointer">
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-border/50 hover:border-primary/30 h-full flex flex-col">
            
            <div class="aspect-square w-full overflow-hidden bg-gradient-to-br from-secondary/30 to-accent/30 relative">
                <img data-bind="attr: { src: coverImage, alt: title }" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-secondary/40 to-accent/60 group-hover:from-secondary/60 group-hover:to-accent/80 transition-all">
                    <i data-lucide="list-music" class="w-20 h-20 text-primary/60"></i>
                </div>
                </div>

            <div class="p-4 space-y-3 flex flex-col flex-1">
                <h3 class="text-lg font-semibold text-foreground line-clamp-2 group-hover:text-primary transition-colors" data-bind="text: title"></h3>

                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <img data-bind="attr: { src: creatorAvatar, alt: creatorName }" class="w-full h-full object-cover" />
                        <i data-lucide="user" class="w-3 h-3 text-primary"></i>
                        </div>
                    <p class="text-sm text-muted-foreground truncate" data-bind="text: creatorName"></p>
                </div>

                <div class="mt-auto flex items-center justify-between pt-1">
                    <p class="text-sm text-muted-foreground" data-bind="text: trackCount + (trackCount === 1 ? ' track' : ' tracks')"></p>
                    
                    <div class="flex items-center gap-1.5" data-bind="foreach: platforms">
                        <div class="opacity-70" data-bind="platformIcon: $data"></div>
                    </div>
                </div>
            </div>
            
        </div>
    </a>
</script>

<script>
    // プラットフォーム固有のSVGアイコンと色を出力するカスタムバインディング
    ko.bindingHandlers.platformIcon = {
        init: function(element, valueAccessor) {
            var platform = ko.unwrap(valueAccessor());
            var svg = '';
            var colorClass = '';

            switch (platform) {
                case 'youtube':
                    colorClass = 'text-[#FF0000]';
                    svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
                    break;
                case 'spotify':
                    colorClass = 'text-[#1DB954]';
                    svg = '<svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>';
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
        var self = this;

        // PHPのコントローラーから渡された本物のDBデータを受け取る
        var dbPlaylists = <?php echo isset($playlists) ? json_encode($playlists) : '[]'; ?>;
        
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