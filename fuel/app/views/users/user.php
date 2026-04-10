<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($target_user['username'], ENT_QUOTES, 'UTF-8'); ?>'s Playlists - Sazanami</title>
    
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

<div id="user-page" class="min-h-screen relative overflow-hidden">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-user" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" strokeWidth="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" strokeWidth="0.5" opacity="0.4" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-user)" />
        </svg>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-12 relative z-10">
        
        <div class="bg-white/60 backdrop-blur-md rounded-3xl p-8 shadow-sm border border-border mb-12 flex flex-col md:flex-row items-center gap-8">
             <div class="w-32 h-32 rounded-full bg-gradient-to-br from-secondary to-accent flex items-center justify-center border-4 border-white shadow-md overflow-hidden flex-shrink-0">
                 <?php if (!empty($target_user['icon'])): ?>
                     <img src="<?php echo htmlspecialchars($target_user['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Icon" class="w-full h-full object-cover">
                 <?php else: ?>
                     <i data-lucide="user" class="w-16 h-16 text-primary"></i>
                 <?php endif; ?>
             </div>
             <div class="text-center md:text-left">
                 <h2 class="text-3xl font-bold text-foreground mb-3"><?php echo htmlspecialchars($target_user['username'], ENT_QUOTES, 'UTF-8'); ?></h2>
                 <?php if (!empty($target_user['bio'])): ?>
                    <p class="text-muted-foreground mb-4 max-w-2xl text-lg"><?php echo nl2br(htmlspecialchars($target_user['bio'], ENT_QUOTES, 'UTF-8')); ?></p>
                 <?php endif; ?>
                 <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-xl shadow-sm border border-border text-sm font-bold text-muted-foreground">
                     <i data-lucide="list-music" class="w-4 h-4 text-primary"></i>
                     <span data-bind="text: userPlaylists().length"></span> Public Playlists
                 </div>
             </div>
        </div>

        <h3 class="text-2xl font-bold text-foreground mb-6">Playlists</h3>

        <div data-bind="visible: userPlaylists().length === 0" style="display: none;" class="text-center py-20 bg-white/40 rounded-3xl border border-dashed border-gray-300">
            <i data-lucide="folder-open" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-xl font-bold text-foreground mb-2">公開されているプレイリストがありません</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-bind="foreach: userPlaylists">
            <div data-bind="component: { name: 'playlist-card', params: $data }"></div>
        </div>

    </main>
</div>

<?php echo \View::forge('shared/playlist_card'); ?>

<script>
    function UserPageViewModel() {
        let self = this;
        let initialPlaylists = <?php echo json_encode($playlists ?? array()); ?>;
        self.userPlaylists = ko.observableArray(initialPlaylists);
    }
    
    setTimeout(function() { lucide.createIcons(); }, 100);
    ko.applyBindings(new UserPageViewModel(), document.getElementById('user-page'));
</script>

</body>
</html>