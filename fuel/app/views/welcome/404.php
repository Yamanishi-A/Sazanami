<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Not Found - Sazanami</title>
    
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

<div class="min-h-screen relative overflow-hidden flex items-center justify-center p-6">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute w-full h-full opacity-30" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="wave-pattern-error" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                    <path d="M0 50 Q 25 35, 50 50 T 100 50" fill="none" stroke="#5ba3c5" stroke-width="0.5" opacity="0.3" />
                    <path d="M0 60 Q 25 45, 50 60 T 100 60" fill="none" stroke="#9fd4e0" stroke-width="0.5" opacity="0.4" />
                    <path d="M0 70 Q 25 55, 50 70 T 100 70" fill="none" stroke="#b8dfe8" stroke-width="0.5" opacity="0.3" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#wave-pattern-error)" />
        </svg>
    </div>

    <div class="text-center max-w-2xl relative z-10">
        <div class="mb-8 flex justify-center">
            <svg viewBox="0 0 200 200" class="w-64 h-64 text-primary opacity-60" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 140 Q 30 130, 50 140 T 90 140 T 130 140 T 170 140 T 190 140" fill="none" stroke="currentColor" stroke-width="3" opacity="0.4" />
                <path d="M10 150 Q 30 140, 50 150 T 90 150 T 130 150 T 170 150 T 190 150" fill="none" stroke="currentColor" stroke-width="3" opacity="0.3" />
                <path d="M10 160 Q 30 150, 50 160 T 90 160 T 130 160 T 170 160 T 190 160" fill="none" stroke="currentColor" stroke-width="3" opacity="0.2" />
                
                <g transform="translate(100, 100)">
                    <path d="M-25 20 L-30 30 L30 30 L25 20 Z" fill="#7fc8e0" stroke="currentColor" stroke-width="2" />
                    <line x1="0" y1="20" x2="0" y2="-10" stroke="currentColor" stroke-width="2" />
                    <path d="M0 -10 L20 0 L0 10 Z" fill="#9fd4e0" stroke="currentColor" stroke-width="1.5" />
                </g>
                
                <text x="40" y="80" font-size="24" fill="currentColor" opacity="0.5">?</text>
                <text x="150" y="70" font-size="24" fill="currentColor" opacity="0.5">?</text>
            </svg>
        </div>

        <div class="mb-6">
            <h1 class="text-9xl text-muted-foreground/30 font-bold mb-4">404</h1>
        </div>

        <h2 class="text-4xl text-foreground font-bold mb-4">
            Oops! This Page is Lost
        </h2>

        <p class="text-lg text-muted-foreground mb-8 max-w-md mx-auto">
            We can't seem to find the page you're looking for, but you can always sail back to safety.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/">
                <button class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary hover:bg-primary/90 text-primary-foreground font-bold rounded-full shadow-lg hover:shadow-xl transition-all w-full sm:w-auto">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    <span>Back to Home</span>
                </button>
            </a>
            <a href="/playlists/discover">
                <button class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white hover:bg-secondary/50 text-primary font-bold border-2 border-primary/20 rounded-full shadow-md hover:shadow-lg transition-all w-full sm:w-auto">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                    <span>Explore Playlists</span>
                </button>
            </a>
        </div>

        <div class="mt-16 flex items-center justify-center gap-2 opacity-50">
            <?php echo View::forge('shared/logo', array('class' => 'w-6 h-6 text-primary')); ?>
            <span class="text-primary font-bold tracking-wide">Sazanami</span>
        </div>
    </div>
</div>

<script>
    // アイコンの描画
    lucide.createIcons();
</script>

</body>
</html>